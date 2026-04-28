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
    // Инициализация таблицы продаж
    $pdo->exec("CREATE TABLE IF NOT EXISTS arenda_sales (
        id INT AUTO_INCREMENT PRIMARY KEY,
        client_name VARCHAR(255) NOT NULL,
        items TEXT NOT NULL,
        total_amount DECIMAL(15, 2) DEFAULT 0,
        paid_amount DECIMAL(15, 2) DEFAULT 0,
        date_sale DATE NOT NULL,
        phone VARCHAR(50),
        comment TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    switch ($action) {
        case 'get_sales':
            $stmt = $pdo->query("SELECT * FROM arenda_sales ORDER BY date_sale DESC");
            $sales = $stmt->fetchAll();
            $sales = array_map(function($s) {
                $s['total_amount'] = (float)$s['total_amount'];
                $s['paid_amount'] = (float)$s['paid_amount'];
                return $s;
            }, $sales);
            echo json_encode($sales);
            break;

        case 'save_sale':
            if ($method === 'POST') {
                if (isset($input['id']) && !empty($input['id'])) {
                    $stmt = $pdo->prepare("UPDATE arenda_sales SET 
                        client_name = :client_name, items = :items, total_amount = :total_amount, 
                        paid_amount = :paid_amount, date_sale = :date_sale, phone = :phone, 
                        comment = :comment WHERE id = :id");
                } else {
                    $stmt = $pdo->prepare("INSERT INTO arenda_sales (
                        client_name, items, total_amount, paid_amount, date_sale, phone, comment
                    ) VALUES (
                        :client_name, :items, :total_amount, :paid_amount, :date_sale, :phone, :comment
                    )");
                }
                
                $params = [
                    ':client_name' => $input['client_name'],
                    ':items' => $input['items'],
                    ':total_amount' => $input['total_amount'] ?? 0,
                    ':paid_amount' => $input['paid_amount'] ?? 0,
                    ':date_sale' => $input['date_sale'],
                    ':phone' => $input['phone'] ?? '',
                    ':comment' => $input['comment'] ?? ''
                ];
                if (isset($input['id']) && !empty($input['id'])) {
                    $params[':id'] = $input['id'];
                }
                
                $stmt->execute($params);
                echo json_encode(['success' => true, 'id' => $input['id'] ?? $pdo->lastInsertId()]);
            }
            break;

        case 'delete_sale':
            if ($method === 'DELETE' && isset($_GET['id'])) {
                $stmt = $pdo->prepare("DELETE FROM arenda_sales WHERE id = ?");
                $stmt->execute([$_GET['id']]);
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