<?php
declare(strict_types=1);

final class AntikvarSyncService {
    /** @var PDO */
    private $pdo;
    /** @var SettingsRepo */
    private $settings;

    public function __construct(PDO $pdo, SettingsRepo $settings) {
        $this->pdo = $pdo;
        $this->settings = $settings;
    }

    public function syncAll(): array {
        $apiKey = $this->settings->get('meshok_api_key');
        if (!$apiKey) {
            throw new RuntimeException('Meshok API key is not set (settings: meshok_api_key).');
        }

        $client = new MeshokClient($apiKey);

        $stats = [
            'products_upserted' => 0,
            'orders_upserted' => 0,
            'order_items_upserted' => 0,
            'transactions_upserted' => 0,
            'notifications_created' => 0,
        ];

        $stats['products_upserted'] += $this->syncOnSaleLots($client);
        $stats['products_upserted'] += $this->syncFinishedLots($client);
        $soldStats = $this->syncSoldFinishedLots($client);
        foreach ($soldStats as $k => $v) $stats[$k] = ($stats[$k] ?? 0) + $v;

        $this->settings->set('last_sync_at', (string)time());
        return $stats;
    }

    private function syncOnSaleLots(MeshokClient $client): int {
        $resp = $client->post('getItemList', []);
        if (($resp['success'] ?? 0) !== 1) {
            throw new RuntimeException((string)($resp['error'] ?? 'Meshok getItemList failed'));
        }
        $lots = $resp['result'] ?? [];
        if (!is_array($lots)) $lots = [];

        $count = 0;
        foreach ($lots as $lot) {
            if (!is_array($lot) || !isset($lot['id'])) continue;
            $count += $this->upsertProductFromLotSummary($lot);
        }
        return $count;
    }

    private function syncFinishedLots(MeshokClient $client): int {
        $resp = $client->post('getFinishedItemList', []);
        if (($resp['success'] ?? 0) !== 1) {
            throw new RuntimeException((string)($resp['error'] ?? 'Meshok getFinishedItemList failed'));
        }
        $lots = $resp['result'] ?? [];
        if (!is_array($lots)) $lots = [];

        $count = 0;
        foreach ($lots as $lot) {
            if (!is_array($lot) || !isset($lot['id'])) continue;
            $id = (int)$lot['id'];
            $stmt = $this->pdo->prepare(
                "UPDATE antikvar_products
                 SET status = 'finished', end_datetime = :end_dt, tz = :tz, raw_json = COALESCE(raw_json, :raw)
                 WHERE meshok_item_id = :id"
            );
            $stmt->execute([
                ':id' => $id,
                ':end_dt' => isset($lot['endDateTime']) ? (string)$lot['endDateTime'] : null,
                ':tz' => isset($lot['TZ']) ? (string)$lot['TZ'] : null,
                ':raw' => json_encode($lot, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ]);
            if ($stmt->rowCount() > 0) $count++;
        }
        return $count;
    }

    private function syncSoldFinishedLots(MeshokClient $client): array {
        $resp = $client->post('getSoldFinishedItemList', []);
        if (($resp['success'] ?? 0) !== 1) {
            throw new RuntimeException((string)($resp['error'] ?? 'Meshok getSoldFinishedItemList failed'));
        }
        $rows = $resp['result'] ?? [];
        if (!is_array($rows)) $rows = [];

        $byOrder = [];
        foreach ($rows as $r) {
            if (!is_array($r) || !isset($r['orderId']) || !isset($r['id'])) continue;
            $orderId = (int)$r['orderId'];
            $byOrder[$orderId][] = $r;
        }

        $stats = [
            'products_upserted' => 0,
            'orders_upserted' => 0,
            'order_items_upserted' => 0,
            'transactions_upserted' => 0,
            'notifications_created' => 0,
        ];

        foreach ($byOrder as $orderId => $items) {
            $this->pdo->beginTransaction();
            try {
                $isNew = $this->upsertOrder($orderId, $items);
                $stats['orders_upserted'] += 1;
                if ($isNew) {
                    $stats['notifications_created'] += $this->createNotification(
                        'new_order',
                        "Новый заказ Meshok #{$orderId}",
                        'order',
                        (string)$orderId
                    );
                }

                $total = 0.0;
                $currencyId = null;

                foreach ($items as $r) {
                    $lotId = (int)$r['id'];
                    $qty = isset($r['quantity']) ? (int)$r['quantity'] : 1;

                    $infoResp = $client->post('getItemInfo', ['id' => $lotId]);
                    if (($infoResp['success'] ?? 0) !== 1) {
                        throw new RuntimeException((string)($infoResp['error'] ?? "Meshok getItemInfo failed for {$lotId}"));
                    }

                    $lot = $infoResp['result'] ?? null;
                    if (!is_array($lot)) $lot = ['id' => $lotId];

                    $stats['products_upserted'] += $this->upsertProductFromLotInfo($lot);

                    $lineCurrency = isset($lot['curencyId']) ? (int)$lot['curencyId'] : null;
                    if ($currencyId === null) $currencyId = $lineCurrency;

                    $price = $this->extractLotPrice($lot);
                    $total += ($price ?? 0) * max(1, $qty);

                    $stats['order_items_upserted'] += $this->upsertOrderItem($orderId, $lotId, $qty, $price);
                }

                $this->updateOrderTotal($orderId, $total, $currencyId);
                $stats['transactions_upserted'] += $this->upsertTransactionForOrder($orderId, $total, $currencyId);

                $this->pdo->commit();
            } catch (Throwable $e) {
                $this->pdo->rollBack();
                throw $e;
            }
        }

        return $stats;
    }

    private function extractLotPrice(array $lot): ?float {
        // Auction: prefer currentPrice, then strikePrice, then startPrice
        if (($lot['saleType'] ?? null) === 'Auction') {
            foreach (['currentPrice', 'strikePrice', 'startPrice'] as $k) {
                if (isset($lot[$k]) && is_numeric($lot[$k])) return (float)$lot[$k];
            }
        }
        // Sale: prefer price
        if (isset($lot['price']) && is_numeric($lot['price'])) return (float)$lot['price'];
        if (isset($lot['currentPrice']) && is_numeric($lot['currentPrice'])) return (float)$lot['currentPrice'];
        return null;
    }

    private function upsertOrder(int $orderId, array $rawItems): bool {
        $exists = $this->pdo->prepare("SELECT 1 FROM antikvar_orders WHERE meshok_order_id = ?");
        $exists->execute([$orderId]);
        $isNew = $exists->fetchColumn() === false;

        $stmt = $this->pdo->prepare(
            "INSERT INTO antikvar_orders (meshok_order_id, status, raw_json)
             VALUES (:id, 'new', :raw)
             ON DUPLICATE KEY UPDATE raw_json = :raw"
        );
        $stmt->execute([
            ':id' => $orderId,
            ':raw' => json_encode($rawItems, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);

        return $isNew;
    }

    private function updateOrderTotal(int $orderId, float $total, ?int $currencyId): void {
        $stmt = $this->pdo->prepare(
            "UPDATE antikvar_orders SET total_amount = :t, currency_id = :c WHERE meshok_order_id = :id"
        );
        $stmt->execute([':t' => $total, ':c' => $currencyId, ':id' => $orderId]);
    }

    private function upsertTransactionForOrder(int $orderId, float $amount, ?int $currencyId): int {
        // idempotency: one "sale" transaction per order
        $stmt = $this->pdo->prepare("SELECT id FROM antikvar_transactions WHERE order_id = ? AND type='sale' LIMIT 1");
        $stmt->execute([$orderId]);
        $id = $stmt->fetchColumn();

        if ($id) {
            $u = $this->pdo->prepare("UPDATE antikvar_transactions SET amount=:a, currency_id=:c WHERE id=:id");
            $u->execute([':a' => $amount, ':c' => $currencyId, ':id' => (int)$id]);
            return 1;
        }

        $i = $this->pdo->prepare(
            "INSERT INTO antikvar_transactions (order_id, type, amount, currency_id) VALUES (:o,'sale',:a,:c)"
        );
        $i->execute([':o' => $orderId, ':a' => $amount, ':c' => $currencyId]);
        return 1;
    }

    private function upsertOrderItem(int $orderId, int $productId, int $qty, ?float $price): int {
        $stmt = $this->pdo->prepare(
            "INSERT INTO antikvar_order_items (order_id, product_id, quantity, price)
             VALUES (:o, :p, :q, :pr)
             ON DUPLICATE KEY UPDATE quantity = VALUES(quantity), price = VALUES(price)"
        );
        $stmt->execute([':o' => $orderId, ':p' => $productId, ':q' => $qty, ':pr' => $price]);
        return 1;
    }

    private function upsertProductFromLotSummary(array $lot): int {
        $id = (int)$lot['id'];
        $saleType = isset($lot['saleType']) ? (string)$lot['saleType'] : 'Sale';

        $stmt = $this->pdo->prepare(
            "INSERT INTO antikvar_products (meshok_item_id, name, sale_type, status, currency_id, quantity, sold, price, current_price, bids, raw_json)
             VALUES (:id, :name, :sale_type, 'listed', :currency_id, :quantity, :sold, :price, :current_price, :bids, :raw)
             ON DUPLICATE KEY UPDATE
               sale_type = VALUES(sale_type),
               status = 'listed',
               currency_id = VALUES(currency_id),
               quantity = VALUES(quantity),
               sold = VALUES(sold),
               price = VALUES(price),
               current_price = VALUES(current_price),
               bids = VALUES(bids),
               raw_json = :raw"
        );

        $stmt->execute([
            ':id' => $id,
            ':name' => (string)($lot['internalId'] ?? ('Lot #' . $id)),
            ':sale_type' => $saleType === 'Auction' ? 'Auction' : 'Sale',
            ':currency_id' => isset($lot['curencyId']) ? (int)$lot['curencyId'] : null,
            ':quantity' => isset($lot['quantity']) ? (int)$lot['quantity'] : null,
            ':sold' => isset($lot['sold']) ? (int)$lot['sold'] : null,
            ':price' => isset($lot['price']) && is_numeric($lot['price']) ? (float)$lot['price'] : null,
            ':current_price' => isset($lot['currentPrice']) && is_numeric($lot['currentPrice']) ? (float)$lot['currentPrice'] : null,
            ':bids' => isset($lot['bids']) ? (int)$lot['bids'] : null,
            ':raw' => json_encode($lot, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);

        return 1;
    }

    private function upsertProductFromLotInfo(array $lot): int {
        $id = isset($lot['id']) ? (int)$lot['id'] : 0;
        if ($id <= 0) return 0;

        $stmt = $this->pdo->prepare(
            "INSERT INTO antikvar_products
              (meshok_item_id, internal_id, name, sale_type, status, currency_id, quantity, sold, price, current_price, bids, end_datetime, tz, raw_json)
             VALUES
              (:id, :internal_id, :name, :sale_type, :status, :currency_id, :quantity, :sold, :price, :current_price, :bids, :end_dt, :tz, :raw)
             ON DUPLICATE KEY UPDATE
              internal_id = VALUES(internal_id),
              name = VALUES(name),
              sale_type = VALUES(sale_type),
              status = VALUES(status),
              currency_id = VALUES(currency_id),
              quantity = VALUES(quantity),
              sold = VALUES(sold),
              price = VALUES(price),
              current_price = VALUES(current_price),
              bids = VALUES(bids),
              end_datetime = VALUES(end_datetime),
              tz = VALUES(tz),
              raw_json = :raw"
        );

        $stmt->execute([
            ':id' => $id,
            ':internal_id' => isset($lot['internalId']) ? (string)$lot['internalId'] : null,
            ':name' => (string)($lot['name'] ?? ('Lot #' . $id)),
            ':sale_type' => (($lot['saleType'] ?? null) === 'Auction') ? 'Auction' : 'Sale',
            ':status' => isset($lot['status']) ? (string)$lot['status'] : 'listed',
            ':currency_id' => isset($lot['curencyId']) ? (int)$lot['curencyId'] : null,
            ':quantity' => isset($lot['quantity']) ? (int)$lot['quantity'] : null,
            ':sold' => isset($lot['sold']) ? (int)$lot['sold'] : null,
            ':price' => isset($lot['price']) && is_numeric($lot['price']) ? (float)$lot['price'] : null,
            ':current_price' => isset($lot['currentPrice']) && is_numeric($lot['currentPrice']) ? (float)$lot['currentPrice'] : null,
            ':bids' => isset($lot['bids']) ? (int)$lot['bids'] : null,
            ':end_dt' => isset($lot['endDateTime']) ? (string)$lot['endDateTime'] : null,
            ':tz' => isset($lot['TZ']) ? (string)$lot['TZ'] : null,
            ':raw' => json_encode($lot, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);

        return 1;
    }

    private function createNotification(string $type, string $message, ?string $entityType, ?string $entityId): int {
        $stmt = $this->pdo->prepare(
            "INSERT INTO antikvar_notifications (type, message, entity_type, entity_id) VALUES (:t,:m,:et,:eid)"
        );
        $stmt->execute([':t' => $type, ':m' => $message, ':et' => $entityType, ':eid' => $entityId]);
        return 1;
    }
}

