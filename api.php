<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

require_once 'db.php';

$method = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];
$parts = explode('/', trim($requestUri, '/'));
// Для простоты будем использовать параметр action или путь
$action = isset($_GET['action']) ? $_GET['action'] : '';

$input = json_decode(file_get_contents('php://input'), true);

try {
    // Безопасное обновление схемы БД (проверяем наличие колонок перед добавлением)
    $columns = $pdo->query("SHOW COLUMNS FROM card_transactions LIKE 'is_cash_transfer'")->fetch();
    if (!$columns) {
        $pdo->exec("ALTER TABLE card_transactions ADD COLUMN is_cash_transfer BOOLEAN DEFAULT FALSE");
    }
    
    $columns = $pdo->query("SHOW COLUMNS FROM cash_transactions LIKE 'transfer_card_id'")->fetch();
    if (!$columns) {
        $pdo->exec("ALTER TABLE cash_transactions ADD COLUMN transfer_card_id VARCHAR(50) DEFAULT NULL");
    }

    $columns = $pdo->query("SHOW COLUMNS FROM cards LIKE 'section'")->fetch();
    if (!$columns) {
        $pdo->exec("ALTER TABLE cards ADD COLUMN section VARCHAR(20) DEFAULT 'cards'");
    }

    switch ($action) {
        case 'get_cards':
            $stmt = $pdo->query('SELECT * FROM cards');
            $cards = $stmt->fetchAll();
            
            $stmt = $pdo->query('SELECT * FROM card_transactions');
            $transactions = $stmt->fetchAll();
            
            $result = [];
            foreach ($cards as $card) {
                $card['initialBalance'] = (float)$card['initial_balance'];
                $card['section'] = $card['section'] ?? 'cards';
                $card['transactions'] = array_filter($transactions, function($t) use ($card) {
                    return $t['card_id'] === $card['id'];
                });
                $card['transactions'] = array_map(function($t) {
                    $t['amount'] = (float)$t['amount'];
                    return $t;
                }, array_values($card['transactions']));
                $result[] = $card;
            }
            echo json_encode($result);
            break;

        case 'save_card':
            if ($method === 'POST') {
                $stmt = $pdo->prepare('INSERT INTO cards (id, name, initial_balance, section) 
                                     VALUES (:id, :name, :initial_balance, :section) 
                                     ON DUPLICATE KEY UPDATE name = VALUES(name), initial_balance = VALUES(initial_balance), section = VALUES(section)');
                $stmt->execute([
                    ':id' => $input['id'], 
                    ':name' => $input['name'], 
                    ':initial_balance' => $input['initialBalance'],
                    ':section' => isset($input['section']) ? $input['section'] : 'cards'
                ]);
                echo json_encode(['success' => true]);
            }
            break;

        case 'delete_card':
            if ($method === 'DELETE' && isset($_GET['id'])) {
                $stmt = $pdo->prepare('DELETE FROM cards WHERE id = ?');
                $stmt->execute([$_GET['id']]);
                echo json_encode(['success' => true]);
            }
            break;

        case 'add_card_transaction':
            if ($method === 'POST') {
                $stmt = $pdo->prepare('INSERT INTO card_transactions (id, card_id, type, amount, category, date, description, is_cash_transfer) 
                                     VALUES (:id, :card_id, :type, :amount, :category, :date, :description, :is_cash_transfer)');
                $stmt->execute([
                    ':id' => $input['id'], 
                    ':card_id' => $input['cardId'], 
                    ':type' => $input['type'], 
                    ':amount' => $input['amount'], 
                    ':category' => $input['category'], 
                    ':date' => $input['date'], 
                    ':description' => $input['description'],
                    ':is_cash_transfer' => isset($input['isCashTransfer']) ? $input['isCashTransfer'] : 0
                ]);
                echo json_encode(['success' => true]);
            }
            break;

        case 'delete_card_transaction':
            if ($method === 'DELETE' && isset($_GET['id'])) {
                $stmt = $pdo->prepare('DELETE FROM card_transactions WHERE id = ?');
                $stmt->execute([$_GET['id']]);
                echo json_encode(['success' => true]);
            }
            break;

        case 'get_cash_transactions':
            $stmt = $pdo->query('SELECT * FROM cash_transactions ORDER BY date ASC');
            $transactions = $stmt->fetchAll();
            $transactions = array_map(function($t) {
                $t['amount'] = (float)$t['amount'];
                $t['is_cash_transfer'] = isset($t['is_cash_transfer']) ? (bool)$t['is_cash_transfer'] : false;
                return $t;
            }, $transactions);
            echo json_encode($transactions);
            break;

        case 'add_cash_transaction':
            if ($method === 'POST') {
                $stmt = $pdo->prepare('INSERT INTO cash_transactions (id, type, amount, recipient, date, description, transfer_card_id) 
                                     VALUES (:id, :type, :amount, :recipient, :date, :description, :transfer_card_id)');
                $stmt->execute([
                    ':id' => $input['id'], 
                    ':type' => $input['type'], 
                    ':amount' => $input['amount'], 
                    ':recipient' => $input['recipient'], 
                    ':date' => $input['date'], 
                    ':description' => $input['description'],
                    ':transfer_card_id' => isset($input['transferCardId']) ? $input['transferCardId'] : null
                ]);
                echo json_encode(['success' => true]);
            }
            break;

        case 'delete_cash_transaction':
            if ($method === 'DELETE' && isset($_GET['id'])) {
                $stmt = $pdo->prepare('DELETE FROM cash_transactions WHERE id = ?');
                $stmt->execute([$_GET['id']]);
                echo json_encode(['success' => true]);
            }
            break;

        default:
            http_response_code(404);
            echo json_encode(['error' => 'Action not found']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
