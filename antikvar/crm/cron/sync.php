<?php
declare(strict_types=1);

require_once __DIR__ . '/../lib/bootstrap.php';
require_once __DIR__ . '/../lib/SettingsRepo.php';
require_once __DIR__ . '/../lib/MeshokClient.php';
require_once __DIR__ . '/../lib/AntikvarSyncService.php';

antikvar_ensure_schema($pdo);

$settings = new SettingsRepo($pdo);
$svc = new AntikvarSyncService($pdo, $settings);

try {
    $stats = $svc->syncAll();
    echo "[OK] " . date('c') . " " . json_encode($stats, JSON_UNESCAPED_UNICODE) . PHP_EOL;
} catch (Throwable $e) {
    fwrite(STDERR, "[ERROR] " . date('c') . " " . $e->getMessage() . PHP_EOL);
    exit(1);
}

