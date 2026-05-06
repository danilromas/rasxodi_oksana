<?php
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

if ($action !== 'export_rentals_excel') {
    header('Content-Type: application/json');
}

try {
    // Инициализация таблиц
    $pdo->exec("CREATE TABLE IF NOT EXISTS arenda_rentals (
        id INT AUTO_INCREMENT PRIMARY KEY,
        type ENUM('ramnye', 'vyshka') NOT NULL,
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
    $pdo->exec("ALTER TABLE arenda_rentals MODIFY COLUMN type ENUM('ramnye', 'vyshka') NOT NULL");

    $pdo->exec("CREATE TABLE IF NOT EXISTS arenda_inventory (
        id INT AUTO_INCREMENT PRIMARY KEY,
        category ENUM('ramnye', 'vyshka') NOT NULL,
        name VARCHAR(255) NOT NULL,
        quantity INT DEFAULT 0,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // Обновляем ENUM для category в arenda_inventory
    $pdo->exec("ALTER TABLE arenda_inventory MODIFY COLUMN category ENUM('ramnye', 'vyshka') NOT NULL");

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
            ['vyshka', 'База с колесами']
        ];
        $insert = $pdo->prepare("INSERT INTO arenda_inventory (category, name, quantity) VALUES (?, ?, 0)");
        foreach ($inventory_items as $item) {
            $insert->execute($item);
        }
    } else {
        // Удаляем записи klinovye и lestnicy из инвентаря если они есть
        $pdo->exec("DELETE FROM arenda_inventory WHERE category IN ('klinovye', 'lestnicy')");
    }

    $pdo->exec("CREATE TABLE IF NOT EXISTS arenda_rental_adjustments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        rental_id INT NOT NULL,
        date_change DATE NOT NULL,
        new_daily_rate DECIMAL(10, 2),
        new_square_meters DECIMAL(10, 2),
        comment TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (rental_id) REFERENCES arenda_rentals(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    switch ($action) {
        case 'get_rentals':
            $stmt = $pdo->query("SELECT *, DATEDIFF(CURDATE(), date_start) + 1 as days FROM arenda_rentals ORDER BY date_start DESC");
            $rentals = $stmt->fetchAll();
            
            // Получаем все корректировки для каждой аренды
            foreach ($rentals as &$r) {
                $adjStmt = $pdo->prepare("SELECT * FROM arenda_rental_adjustments WHERE rental_id = ? ORDER BY date_change ASC");
                $adjStmt->execute([$r['id']]);
                $r['adjustments'] = $adjStmt->fetchAll();
                
                $r['daily_rate'] = (float)$r['daily_rate'];
                $r['deposit'] = (float)$r['deposit'];
                $r['paid_rent'] = (float)$r['paid_rent'];
                $r['square_meters'] = (float)$r['square_meters'];
                $r['days'] = (int)$r['days'];
            }
            echo json_encode($rentals);
            break;

        case 'save_adjustment':
            if ($method === 'POST') {
                $stmt = $pdo->prepare("INSERT INTO arenda_rental_adjustments (
                    rental_id, date_change, new_daily_rate, new_square_meters, comment
                ) VALUES (
                    :rental_id, :date_change, :new_daily_rate, :new_square_meters, :comment
                )");
                
                $stmt->execute([
                    ':rental_id' => $input['rental_id'],
                    ':date_change' => $input['date_change'],
                    ':new_daily_rate' => $input['new_daily_rate'],
                    ':new_square_meters' => $input['new_square_meters'],
                    ':comment' => $input['comment'] ?? ''
                ]);
                echo json_encode(['success' => true]);
            }
            break;

        case 'delete_adjustment':
            if ($method === 'DELETE' && isset($_GET['id'])) {
                $stmt = $pdo->prepare("DELETE FROM arenda_rental_adjustments WHERE id = ?");
                $stmt->execute([$_GET['id']]);
                echo json_encode(['success' => true]);
            }
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
                $date_end = !empty($input['date_end']) ? $input['date_end'] : date('Y-m-d');
                $stmt = $pdo->prepare("UPDATE arenda_rentals SET status = 'closed', date_end = ? WHERE id = ?");
                $stmt->execute([$date_end, $input['id']]);
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

        case 'export_rentals_excel':
            if ($method === 'GET') {
                $dateFrom = isset($_GET['date_from']) ? trim($_GET['date_from']) : '';
                $dateTo = isset($_GET['date_to']) ? trim($_GET['date_to']) : '';

                if (!$dateFrom || !$dateTo) {
                    http_response_code(400);
                    header('Content-Type: application/json');
                    echo json_encode(['error' => 'Параметры date_from и date_to обязательны']);
                    break;
                }

                $stmt = $pdo->prepare("
                    SELECT 
                        r.*
                    FROM arenda_rentals r
                    WHERE r.date_start BETWEEN :date_from AND :date_to
                    ORDER BY r.date_start ASC, r.id ASC
                ");
                $stmt->execute([
                    ':date_from' => $dateFrom,
                    ':date_to' => $dateTo
                ]);
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $fileName = 'arenda_buh_' . $dateFrom . '_to_' . $dateTo . '.xls';
                header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
                header('Content-Disposition: attachment; filename="' . $fileName . '"');
                header('Pragma: no-cache');
                header('Expires: 0');

                echo "\xEF\xBB\xBF";
                echo '<html><head><meta charset="UTF-8">';
                echo '<style>
                    table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; font-size: 12px; }
                    th, td { border: 1px solid #d9d9d9; padding: 6px 8px; vertical-align: top; }
                    .title { background: #1f4e78; color: #fff; font-size: 14px; font-weight: bold; text-align: left; }
                    .header { background: #ddebf7; font-weight: bold; text-align: center; }
                    .num { text-align: right; white-space: nowrap; }
                    .center { text-align: center; white-space: nowrap; }
                    .text { text-align: left; }
                    .summary { background: #e2f0d9; font-weight: bold; }
                    .closed { background: #f2f2f2; }
                    .active { background: #ffffff; }
                </style></head><body>';

                echo '<table>';
                echo '<colgroup>
                        <col style="width: 50px;">
                        <col style="width: 90px;">
                        <col style="width: 220px;">
                        <col style="width: 120px;">
                        <col style="width: 120px;">
                        <col style="width: 95px;">
                        <col style="width: 95px;">
                        <col style="width: 95px;">
                        <col style="width: 110px;">
                        <col style="width: 110px;">
                        <col style="width: 130px;">
                        <col style="width: 90px;">
                        <col style="width: 130px;">
                        <col style="width: 120px;">
                        <col style="width: 100px;">
                        <col style="width: 80px;">
                        <col style="width: 280px;">
                        <col style="width: 140px;">
                        <col style="width: 140px;">
                    </colgroup>';

                echo '<tr><th class="title" colspan="19">Аренда и Склад - выгрузка для бухгалтерии | Период: ' 
                    . htmlspecialchars($dateFrom) . ' - ' . htmlspecialchars($dateTo) . '</th></tr>';

                echo '<tr>';
                foreach ([
                    'ID',
                    'Тип',
                    'Клиент',
                    'Договор',
                    'Акт',
                    'Дата начала',
                    'Дата завершения',
                    'Статус',
                    'Цена за сутки',
                    'Залог',
                    'Оплачено за аренду',
                    'Площадь (м2)',
                    'Телефон',
                    'Дата последнего платежа',
                    'Дата звонка',
                    'Должник',
                    'Комментарий',
                    'Создано',
                    'Обновлено'
                ] as $h) {
                    echo '<th class="header">' . htmlspecialchars($h) . '</th>';
                }
                echo '</tr>';

                $sumDaily = 0.0;
                $sumDeposit = 0.0;
                $sumPaid = 0.0;
                $sumM2 = 0.0;

                foreach ($rows as $r) {
                    $typeLabel = $r['type'] === 'ramnye' ? 'Рамные' : 'Вышка';
                    $statusLabel = $r['status'] === 'closed' ? 'Завершена' : 'Активна';
                    $isDebtorLabel = ((int)$r['is_debtor'] === 1) ? 'Да' : 'Нет';
                    $rowClass = $r['status'] === 'closed' ? 'closed' : 'active';

                    $dailyRate = (float)$r['daily_rate'];
                    $deposit = (float)$r['deposit'];
                    $paidRent = (float)$r['paid_rent'];
                    $squareMeters = (float)$r['square_meters'];

                    $sumDaily += $dailyRate;
                    $sumDeposit += $deposit;
                    $sumPaid += $paidRent;
                    $sumM2 += $squareMeters;

                    echo '<tr class="' . $rowClass . '">';
                    echo '<td class="center">' . (int)$r['id'] . '</td>';
                    echo '<td class="center">' . htmlspecialchars($typeLabel) . '</td>';
                    echo '<td class="text">' . htmlspecialchars((string)$r['client_name']) . '</td>';
                    echo '<td class="text">' . htmlspecialchars((string)$r['dogovor']) . '</td>';
                    echo '<td class="text">' . htmlspecialchars((string)$r['akt']) . '</td>';
                    echo '<td class="center">' . htmlspecialchars((string)$r['date_start']) . '</td>';
                    echo '<td class="center">' . htmlspecialchars((string)$r['date_end']) . '</td>';
                    echo '<td class="center">' . htmlspecialchars($statusLabel) . '</td>';
                    echo '<td class="num">' . number_format($dailyRate, 2, '.', ' ') . '</td>';
                    echo '<td class="num">' . number_format($deposit, 2, '.', ' ') . '</td>';
                    echo '<td class="num">' . number_format($paidRent, 2, '.', ' ') . '</td>';
                    echo '<td class="num">' . number_format($squareMeters, 2, '.', ' ') . '</td>';
                    echo '<td class="text">' . htmlspecialchars((string)$r['phone']) . '</td>';
                    echo '<td class="center">' . htmlspecialchars((string)$r['last_payment_date']) . '</td>';
                    echo '<td class="center">' . htmlspecialchars((string)$r['call_date']) . '</td>';
                    echo '<td class="center">' . htmlspecialchars($isDebtorLabel) . '</td>';
                    echo '<td class="text">' . htmlspecialchars(preg_replace("/[\r\n\t]+/", ' ', (string)$r['comment'])) . '</td>';
                    echo '<td class="center">' . htmlspecialchars((string)$r['created_at']) . '</td>';
                    echo '<td class="center">' . htmlspecialchars((string)$r['updated_at']) . '</td>';
                    echo '</tr>';
                }

                echo '<tr class="summary">';
                echo '<td class="center" colspan="8">ИТОГО (' . count($rows) . ' записей)</td>';
                echo '<td class="num">' . number_format($sumDaily, 2, '.', ' ') . '</td>';
                echo '<td class="num">' . number_format($sumDeposit, 2, '.', ' ') . '</td>';
                echo '<td class="num">' . number_format($sumPaid, 2, '.', ' ') . '</td>';
                echo '<td class="num">' . number_format($sumM2, 2, '.', ' ') . '</td>';
                echo '<td colspan="7"></td>';
                echo '</tr>';

                echo '</table></body></html>';
                exit;
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