<?php
declare(strict_types=1);

session_start();

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/src/Services/DatabaseManager.php';
require_once BASE_PATH . '/src/Services/AnalyticsService.php';
require_once BASE_PATH . '/src/Services/PaystackClient.php';
require_once BASE_PATH . '/src/Services/PaystackSyncService.php';
require_once BASE_PATH . '/src/Services/XeroService.php';

$appConfig = require BASE_PATH . '/config/app.php';

use MySportsApp\Services\DatabaseManager;
use MySportsApp\Services\AnalyticsService;
use MySportsApp\Services\PaystackClient;
use MySportsApp\Services\PaystackSyncService;
use MySportsApp\Services\XeroService;

DatabaseManager::init([]); // config embedded in class or via env

function db(): PDO {
    return DatabaseManager::primary();
}

function analytics_dbs(): array {
    return DatabaseManager::analytics();
}

function app_name(): string {
    global $appConfig;
    return $appConfig['name'] ?? 'MySportsApp Suite';
}

// ---- settings helpers ----
function setting(string $key, ?string $default = null): ?string {
    static $cache = null;
    if ($cache === null) {
        $rows = db()->query("SELECT `key`, `value` FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR);
        $cache = $rows;
    }
    return $cache[$key] ?? $default;
}

function set_setting(string $key, string $value): void {
    $stmt = db()->prepare("
        INSERT INTO settings (`key`, `value`, created_at, updated_at)
        VALUES (:k, :v, NOW(), NOW())
        ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), updated_at = NOW()
    ");
    $stmt->execute([':k' => $key, ':v' => $value]);
}

// ---- auth helpers ----
function current_user(): ?array {
    if (!isset($_SESSION['user_id'])) return null;
    static $cached = null;
    if ($cached !== null) return $cached;

    $stmt = db()->prepare("SELECT id, name, email, role FROM users WHERE id = :id");
    $stmt->execute([':id' => $_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    $cached = $user;
    return $user;
}

function require_login(): void {
    if (!current_user()) {
        header('Location: /index.php?route=login');
        exit;
    }
}

function require_role(array $roles): void {
    $user = current_user();
    if (!$user || !in_array($user['role'], $roles, true)) {
        http_response_code(403);
        echo "Forbidden";
        exit;
    }
}

function render(string $view, array $data = []): void {
    extract($data);
    $viewFile = BASE_PATH . '/views/' . $view . '.php';
    if (!is_file($viewFile)) {
        http_response_code(500);
        echo "View not found: " . htmlspecialchars($view);
        exit;
    }
    include $viewFile;
}
