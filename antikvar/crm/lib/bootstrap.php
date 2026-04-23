<?php
declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '0'); // never emit HTML errors into JSON API

try {
    require_once __DIR__ . '/../../../db2.php';
} catch (Throwable $e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'error' => 'DB connection failed',
        'details' => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function antikvar_ensure_schema(PDO $pdo): void {
    $exists = $pdo->query("SHOW TABLES LIKE 'antikvar_settings'")->fetchColumn();
    if ($exists) return;

    $sqlPath = __DIR__ . '/../sql/init.sql';
    $sql = file_get_contents($sqlPath);
    if ($sql === false) {
        throw new RuntimeException("Cannot read schema file: {$sqlPath}");
    }
    $pdo->exec($sql);
}

function antikvar_json($data, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

function antikvar_read_json_body(): array {
    $raw = file_get_contents('php://input');
    if ($raw === false || trim($raw) === '') return [];
    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : [];
}

