<?php
declare(strict_types=1);

// Make fatal PHP errors return JSON (not blank/HTML).
error_reporting(E_ALL);
ini_set('display_errors', '0');
header('Content-Type: application/json; charset=utf-8');

register_shutdown_function(function (): void {
    $err = error_get_last();
    if (!$err) return;
    $type = (int)($err['type'] ?? 0);
    $isFatal = in_array($type, [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true);
    if (!$isFatal) return;

    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
    }
    echo json_encode([
        'error' => 'PHP fatal error',
        'details' => ($err['message'] ?? '') . ' in ' . ($err['file'] ?? '') . ':' . ($err['line'] ?? 0),
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
});

require_once __DIR__ . '/lib/bootstrap.php';
require_once __DIR__ . '/lib/SettingsRepo.php';
require_once __DIR__ . '/lib/MeshokClient.php';
require_once __DIR__ . '/lib/AntikvarSyncService.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

$action = isset($_GET['action']) ? (string)$_GET['action'] : 'health';
$method = $_SERVER['REQUEST_METHOD'];
$body = antikvar_read_json_body();

$settings = new SettingsRepo($pdo);

try {
    // Ensure schema inside try/catch so SQL errors return JSON (not HTML/blank 500)
    antikvar_ensure_schema($pdo);

    switch ($action) {
        case 'health':
            antikvar_json([
                'ok' => true,
                'time' => time(),
                'last_sync_at' => $settings->get('last_sync_at'),
            ]);
            break;

        case 'debug_db':
            // Quick diagnostics: shows DB name/version and presence of core tables
            $dbName = $pdo->query("SELECT DATABASE()")->fetchColumn();
            $version = $pdo->query("SELECT VERSION()")->fetchColumn();
            $tables = [];
            foreach (['antikvar_settings','antikvar_orders','antikvar_products','antikvar_order_items','antikvar_transactions','antikvar_notifications'] as $t) {
                $tables[$t] = (bool)$pdo->query("SHOW TABLES LIKE " . $pdo->quote($t))->fetchColumn();
            }
            antikvar_json([
                'ok' => true,
                'database' => $dbName,
                'version' => $version,
                'tables' => $tables,
            ]);
            break;

        case 'debug_env':
            antikvar_json([
                'ok' => true,
                'php' => PHP_VERSION,
                'extensions' => [
                    'curl' => extension_loaded('curl'),
                    'openssl' => extension_loaded('openssl'),
                    'mbstring' => extension_loaded('mbstring'),
                    'json' => extension_loaded('json'),
                ],
                'meshok_api_key_set' => (bool)$settings->get('meshok_api_key'),
            ]);
            break;

        case 'settings_get':
            antikvar_json([
                'meshok_api_key_set' => (bool)$settings->get('meshok_api_key'),
                'sync_interval_minutes' => (int)($settings->get('sync_interval_minutes', '15') ?? 15),
                'last_sync_at' => $settings->get('last_sync_at'),
            ]);
            break;

        case 'settings_set':
            if ($method !== 'POST') throw new RuntimeException('Method not allowed');
            if (array_key_exists('meshok_api_key', $body)) {
                $settings->set('meshok_api_key', trim((string)$body['meshok_api_key']) ?: null);
            }
            if (array_key_exists('sync_interval_minutes', $body)) {
                $v = max(1, (int)$body['sync_interval_minutes']);
                $settings->set('sync_interval_minutes', (string)$v);
            }
            antikvar_json(['ok' => true]);
            break;

        case 'sync_now':
            if ($method !== 'POST') throw new RuntimeException('Method not allowed');
            $svc = new AntikvarSyncService($pdo, $settings);
            $stats = $svc->syncAll();
            antikvar_json(['ok' => true, 'stats' => $stats]);
            break;

        case 'sync_now_debug':
            // Allows running sync from browser without POST (for troubleshooting).
            $svc = new AntikvarSyncService($pdo, $settings);
            $stats = $svc->syncAll();
            antikvar_json(['ok' => true, 'stats' => $stats]);
            break;

        case 'orders_list': {
            $q = trim((string)($_GET['q'] ?? ''));
            $status = trim((string)($_GET['status'] ?? ''));
            $from = trim((string)($_GET['from'] ?? ''));
            $to = trim((string)($_GET['to'] ?? ''));

            $where = [];
            $params = [];
            if ($q !== '') {
                if (ctype_digit($q)) {
                    $where[] = 'o.meshok_order_id = :oid';
                    $params[':oid'] = (int)$q;
                } else {
                    $where[] = 'o.buyer_username LIKE :bu';
                    $params[':bu'] = '%' . $q . '%';
                }
            }
            if ($status !== '') {
                $where[] = 'o.status = :st';
                $params[':st'] = $status;
            }
            if ($from !== '') {
                $where[] = 'o.created_at >= :from';
                $params[':from'] = $from . ' 00:00:00';
            }
            if ($to !== '') {
                $where[] = 'o.created_at <= :to';
                $params[':to'] = $to . ' 23:59:59';
            }

            $sql = "SELECT o.meshok_order_id, o.status, o.buyer_username, o.total_amount, o.currency_id, o.created_at, o.updated_at
                    FROM antikvar_orders o";
            if ($where) $sql .= " WHERE " . implode(' AND ', $where);
            $sql .= " ORDER BY o.created_at DESC LIMIT 500";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            antikvar_json(['orders' => $stmt->fetchAll()]);
            break;
        }

        case 'order_get': {
            $id = (int)($_GET['id'] ?? 0);
            if ($id <= 0) throw new RuntimeException('Missing id');

            $o = $pdo->prepare("SELECT * FROM antikvar_orders WHERE meshok_order_id = ?");
            $o->execute([$id]);
            $order = $o->fetch();
            if (!$order) {
                antikvar_json(['error' => 'Not found'], 404);
                break;
            }

            $items = $pdo->prepare(
                "SELECT i.product_id, i.quantity, i.price, p.name, p.internal_id, p.sale_type, p.status
                 FROM antikvar_order_items i
                 JOIN antikvar_products p ON p.meshok_item_id = i.product_id
                 WHERE i.order_id = ?
                 ORDER BY p.name"
            );
            $items->execute([$id]);

            antikvar_json([
                'order' => $order,
                'items' => $items->fetchAll(),
            ]);
            break;
        }

        case 'order_update_status': {
            if ($method !== 'POST') throw new RuntimeException('Method not allowed');
            $id = (int)($body['id'] ?? 0);
            $status = (string)($body['status'] ?? '');
            if ($id <= 0) throw new RuntimeException('Missing id');
            if (!in_array($status, ['new','paid','shipped','completed'], true)) throw new RuntimeException('Invalid status');

            $stmt = $pdo->prepare("UPDATE antikvar_orders SET status = :s WHERE meshok_order_id = :id");
            $stmt->execute([':s' => $status, ':id' => $id]);
            antikvar_json(['ok' => true]);
            break;
        }

        case 'products_list': {
            $status = trim((string)($_GET['status'] ?? ''));
            $q = trim((string)($_GET['q'] ?? ''));

            $where = [];
            $params = [];
            if ($status !== '') {
                $where[] = 'p.status = :st';
                $params[':st'] = $status;
            }
            if ($q !== '') {
                $where[] = '(p.name LIKE :q OR p.internal_id LIKE :q)';
                $params[':q'] = '%' . $q . '%';
            }

            $sql = "SELECT p.meshok_item_id, p.internal_id, p.name, p.sale_type, p.status, p.price, p.current_price, p.currency_id,
                           p.quantity, p.sold, p.bids, p.end_datetime, p.updated_at
                    FROM antikvar_products p";
            if ($where) $sql .= " WHERE " . implode(' AND ', $where);
            $sql .= " ORDER BY p.updated_at DESC LIMIT 1000";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            antikvar_json(['products' => $stmt->fetchAll()]);
            break;
        }

        case 'users_list': {
            // At this stage, buyers are taken from orders. Later you can enrich via another Meshok method if available.
            $stmt = $pdo->query(
                "SELECT buyer_username AS username,
                        COUNT(*) AS orders_count,
                        SUM(total_amount) AS total_amount,
                        MAX(created_at) AS last_order_at
                 FROM antikvar_orders
                 WHERE buyer_username IS NOT NULL AND buyer_username <> ''
                 GROUP BY buyer_username
                 ORDER BY total_amount DESC
                 LIMIT 1000"
            );
            antikvar_json(['users' => $stmt->fetchAll()]);
            break;
        }

        case 'analytics_summary': {
            $from = trim((string)($_GET['from'] ?? ''));
            $to = trim((string)($_GET['to'] ?? ''));
            $where = [];
            $params = [];
            if ($from !== '') { $where[] = 'created_at >= :from'; $params[':from'] = $from . ' 00:00:00'; }
            if ($to !== '') { $where[] = 'created_at <= :to'; $params[':to'] = $to . ' 23:59:59'; }
            $w = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

            $stmt = $pdo->prepare("SELECT COUNT(*) AS orders_count, COALESCE(SUM(total_amount),0) AS revenue FROM antikvar_orders {$w}");
            $stmt->execute($params);
            $row = $stmt->fetch() ?: ['orders_count' => 0, 'revenue' => 0];

            $ordersCount = (int)$row['orders_count'];
            $revenue = (float)$row['revenue'];
            $avgCheck = $ordersCount > 0 ? $revenue / $ordersCount : 0.0;

            antikvar_json([
                'orders_count' => $ordersCount,
                'revenue' => $revenue,
                'avg_check' => $avgCheck,
            ]);
            break;
        }

        case 'analytics_sales_series': {
            $period = (string)($_GET['period'] ?? 'day'); // day|week|month
            $valid = ['day','week','month'];
            if (!in_array($period, $valid, true)) $period = 'day';

            if ($period === 'day') {
                $sql = "SELECT DATE(created_at) AS label, SUM(total_amount) AS value
                        FROM antikvar_orders
                        GROUP BY DATE(created_at)
                        ORDER BY label ASC
                        LIMIT 365";
            } elseif ($period === 'week') {
                $sql = "SELECT YEARWEEK(created_at, 1) AS label, SUM(total_amount) AS value
                        FROM antikvar_orders
                        GROUP BY YEARWEEK(created_at, 1)
                        ORDER BY label ASC
                        LIMIT 260";
            } else {
                $sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') AS label, SUM(total_amount) AS value
                        FROM antikvar_orders
                        GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                        ORDER BY label ASC
                        LIMIT 120";
            }

            $stmt = $pdo->query($sql);
            antikvar_json(['series' => $stmt->fetchAll()]);
            break;
        }

        case 'analytics_top_products': {
            $stmt = $pdo->query(
                "SELECT p.meshok_item_id, p.name,
                        SUM(i.quantity) AS qty,
                        SUM(COALESCE(i.price, 0) * i.quantity) AS revenue
                 FROM antikvar_order_items i
                 JOIN antikvar_products p ON p.meshok_item_id = i.product_id
                 GROUP BY p.meshok_item_id, p.name
                 ORDER BY revenue DESC
                 LIMIT 50"
            );
            antikvar_json(['top' => $stmt->fetchAll()]);
            break;
        }

        case 'notifications_list': {
            $sinceId = (int)($_GET['since_id'] ?? 0);
            if ($sinceId > 0) {
                $stmt = $pdo->prepare("SELECT * FROM antikvar_notifications WHERE id > ? ORDER BY id DESC LIMIT 200");
                $stmt->execute([$sinceId]);
            } else {
                $stmt = $pdo->query("SELECT * FROM antikvar_notifications ORDER BY id DESC LIMIT 200");
            }
            antikvar_json(['notifications' => $stmt->fetchAll()]);
            break;
        }

        case 'notifications_mark_read': {
            if ($method !== 'POST') throw new RuntimeException('Method not allowed');
            $ids = $body['ids'] ?? [];
            if (!is_array($ids) || !$ids) { antikvar_json(['ok' => true]); break; }
            $ids = array_map('intval', $ids);
            $ids = array_values(array_filter($ids, function ($x) { return $x > 0; }));
            if (!$ids) { antikvar_json(['ok' => true]); break; }

            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $pdo->prepare("UPDATE antikvar_notifications SET read_at = NOW() WHERE id IN ({$placeholders})");
            $stmt->execute($ids);
            antikvar_json(['ok' => true]);
            break;
        }

        default:
            antikvar_json(['error' => 'Action not found'], 404);
            break;
    }
} catch (Throwable $e) {
    antikvar_json(['error' => $e->getMessage()], 500);
}

