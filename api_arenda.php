<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

require_once 'db1.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';
$input = json_decode(file_get_contents('php://input'), true);

try {
    // Инициализация таблиц
    $pdo->exec("CREATE TABLE IF NOT EXISTS arenda_rentals (
        id INT AUTO_INCREMENT PRIMARY KEY,
        type ENUM('ramnye', 'vyshka', 'lestnicy') NOT NULL,
        client_name VARCHAR(255) NOT NULL,
        dogovor VARCHAR(100),
        akt VARCHAR(100),
        date_start DATE NOT NULL,
        daily_rate DECIMAL(10, 2) DEFAULT 0,
        deposit DECIMAL(10, 2) DEFAULT 0,
        paid_rent DECIMAL(10, 2) DEFAULT 0,
        square_meters DECIMAL(10, 2) DEFAULT 0,
        phone VARCHAR(50),
        comment TEXT,
        last_payment_date DATE,
        call_date DATE,
        status ENUM('active', 'closed') DEFAULT 'active',
        is_debtor BOOLEAN DEFAULT FALSE,
        date_end DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // Проверяем наличие колонки is_debtor в существующей таблице
    $columns = $pdo->query("SHOW COLUMNS FROM arenda_rentals LIKE 'is_debtor'")->fetch();
    if (!$columns) {
        $pdo->exec("ALTER TABLE arenda_rentals ADD COLUMN is_debtor BOOLEAN DEFAULT FALSE");
    }

    // Проверяем наличие колонки date_end
    $columns = $pdo->query("SHOW COLUMNS FROM arenda_rentals LIKE 'date_end'")->fetch();
    if (!$columns) {
        $pdo->exec("ALTER TABLE arenda_rentals ADD COLUMN date_end DATE DEFAULT NULL");
    }

    // Обновляем ENUM для type, если нужно (удаляем klinovye)
    // В MySQL это делается через MODIFY COLUMN
    $pdo->exec("ALTER TABLE arenda_rentals MODIFY COLUMN type ENUM('ramnye', 'vyshka', 'lestnicy') NOT NULL");

    $pdo->exec("CREATE TABLE IF NOT EXISTS arenda_inventory (
        id INT AUTO_INCREMENT PRIMARY KEY,
        category ENUM('ramnye', 'vyshka', 'lestnicy') NOT NULL,
        name VARCHAR(255) NOT NULL,
        quantity INT DEFAULT 0,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // Обновляем ENUM для category в arenda_inventory
    $pdo->exec("ALTER TABLE arenda_inventory MODIFY COLUMN category ENUM('ramnye', 'vyshka', 'lestnicy') NOT NULL");

    // Начальные данные для склада, если таблица пуста
    $stmt = $pdo->query("SELECT COUNT(*) FROM arenda_inventory");
    if ($stmt->fetchColumn() == 0) {
        $inventory_items = [
            ['ramnye', 'Рама с лестницей'],
            ['ramnye', 'Рама проходная'],
            ['ramnye', 'Диагональ'],
            ['ramnye', 'Горизонталь'],
            ['ramnye', 'Ригель'],
            ['ramnye', 'Настил'],
            ['vyshka', 'Секция 1.2х2.0'],
            ['vyshka', 'Секция 0.7х1.6'],
            ['vyshka', 'База с колесами'],
            ['lestnicy', 'Лестница 3х7'],
            ['lestnicy', 'Лестница 3х10']
        ];
        $insert = $pdo->prepare("INSERT INTO arenda_inventory (category, name, quantity) VALUES (?, ?, 0)");
        foreach ($inventory_items as $item) {
            $insert->execute($item);
        }
    } else {
        // Удаляем записи klinovye из инвентаря если они есть
        $pdo->exec("DELETE FROM arenda_inventory WHERE category = 'klinovye'");
    }

    switch ($action) {
        case 'get_rentals':
            $stmt = $pdo->query("SELECT *, DATEDIFF(CURDATE(), date_start) + 1 as days FROM arenda_rentals ORDER BY date_start DESC");
            $rentals = $stmt->fetchAll();
            $rentals = array_map(function($r) {
                $r['daily_rate'] = (float)$r['daily_rate'];
                $r['deposit'] = (float)$r['deposit'];
                $r['paid_rent'] = (float)$r['paid_rent'];
                $r['square_meters'] = (float)$r['square_meters'];
                $r['days'] = (int)$r['days'];
                return $r;
            }, $rentals);
            echo json_encode($rentals);
            break;

        case 'save_rental':
            if ($method === 'POST') {
                if (isset($input['id']) && !empty($input['id'])) {
                    $stmt = $pdo->prepare("UPDATE arenda_rentals SET 
                        type = :type, client_name = :client_name, dogovor = :dogovor, akt = :akt, 
                        date_start = :date_start, daily_rate = :daily_rate, deposit = :deposit, 
                        paid_rent = :paid_rent, square_meters = :square_meters, phone = :phone, 
                        comment = :comment, last_payment_date = :last_payment_date, call_date = :call_date, 
                        status = :status, is_debtor = :is_debtor, date_end = :date_end WHERE id = :id");
                } else {
                    $stmt = $pdo->prepare("INSERT INTO arenda_rentals (
                        type, client_name, dogovor, akt, date_start, daily_rate, deposit, 
                        paid_rent, square_meters, phone, comment, last_payment_date, call_date, status, is_debtor, date_end
                    ) VALUES (
                        :type, :client_name, :dogovor, :akt, :date_start, :daily_rate, :deposit, 
                        :paid_rent, :square_meters, :phone, :comment, :last_payment_date, :call_date, :status, :is_debtor, :date_end
                    )");
                }
                
                $params = [
                    ':type' => $input['type'],
                    ':client_name' => $input['client_name'],
                    ':dogovor' => $input['dogovor'] ?? '',
                    ':akt' => $input['akt'] ?? '',
                    ':date_start' => $input['date_start'],
                    ':daily_rate' => $input['daily_rate'] ?? 0,
                    ':deposit' => $input['deposit'] ?? 0,
                    ':paid_rent' => $input['paid_rent'] ?? 0,
                    ':square_meters' => $input['square_meters'] ?? 0,
                    ':phone' => $input['phone'] ?? '',
                    ':comment' => $input['comment'] ?? '',
                    ':last_payment_date' => !empty($input['last_payment_date']) ? $input['last_payment_date'] : null,
                    ':call_date' => !empty($input['call_date']) ? $input['call_date'] : null,
                    ':status' => $input['status'] ?? 'active',
                    ':is_debtor' => isset($input['is_debtor']) ? (int)$input['is_debtor'] : 0,
                    ':date_end' => !empty($input['date_end']) ? $input['date_end'] : null
                ];
                if (isset($input['id']) && !empty($input['id'])) {
                    $params[':id'] = $input['id'];
                }
                
                $stmt->execute($params);
                echo json_encode(['success' => true, 'id' => $input['id'] ?? $pdo->lastInsertId()]);
            }
            break;

        case 'toggle_debtor':
            if ($method === 'POST' && isset($input['id'])) {
                $stmt = $pdo->prepare("UPDATE arenda_rentals SET is_debtor = !is_debtor WHERE id = ?");
                $stmt->execute([$input['id']]);
                echo json_encode(['success' => true]);
            }
            break;

        case 'delete_rental':
            if ($method === 'DELETE' && isset($_GET['id'])) {
                $stmt = $pdo->prepare("DELETE FROM arenda_rentals WHERE id = ?");
                $stmt->execute([$_GET['id']]);
                echo json_encode(['success' => true]);
            }
            break;

        case 'close_rental':
            if ($method === 'POST' && isset($input['id'])) {
                $stmt = $pdo->prepare("UPDATE arenda_rentals SET status = 'closed', date_end = CURDATE() WHERE id = ?");
                $stmt->execute([$input['id']]);
                echo json_encode(['success' => true]);
            }
            break;

        case 'get_inventory':
            $stmt = $pdo->query("SELECT * FROM arenda_inventory ORDER BY category, name");
            $inventory = $stmt->fetchAll();
            $inventory = array_map(function($i) {
                $i['quantity'] = (int)$i['quantity'];
                return $i;
            }, $inventory);
            echo json_encode($inventory);
            break;

        case 'update_inventory':
            if ($method === 'POST') {
                $stmt = $pdo->prepare("UPDATE arenda_inventory SET quantity = :quantity WHERE id = :id");
                $stmt->execute([
                    ':quantity' => $input['quantity'],
                    ':id' => $input['id']
                ]);
                echo json_encode(['success' => true]);
            }
            break;

        default:
            echo json_encode(['error' => 'Unknown action']);
            break;
    }

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>