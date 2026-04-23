<?php
declare(strict_types=1);

final class SettingsRepo {
    /** @var PDO */
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function get(string $key, ?string $default = null): ?string {
        $stmt = $this->pdo->prepare("SELECT `value` FROM antikvar_settings WHERE `key` = ?");
        $stmt->execute([$key]);
        $v = $stmt->fetchColumn();
        return $v === false ? $default : (string)$v;
    }

    public function set(string $key, ?string $value): void {
        $stmt = $this->pdo->prepare(
            "INSERT INTO antikvar_settings (`key`, `value`) VALUES (:k, :v)
             ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)"
        );
        $stmt->execute([':k' => $key, ':v' => $value]);
    }
}

