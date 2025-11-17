<?php
declare(strict_types=1);

session_start();

define('BASE_PATH', dirname(__DIR__));

// Function to safely require a file with fallback to absolute path
function safe_require_once($relativePath) {
    $file = BASE_PATH . $relativePath;
    if (file_exists($file)) {
        require_once $file;
    } else {
        // Try absolute path as fallback
        $absolutePath = '/var/www/html' . $relativePath;
        if (file_exists($absolutePath)) {
            require_once $absolutePath;
        } else {
            error_log("Could not find file: {$relativePath}");
            die("Fatal error: Could not find required file: {$relativePath}");
        }
    }
}

// Function to safely require a configuration file with fallback to absolute path
function safe_require_config($relativePath) {
    $file = BASE_PATH . $relativePath;
    if (file_exists($file)) {
        return require $file;
    } else {
        // Try absolute path as fallback
        $absolutePath = '/var/www/html' . $relativePath;
        if (file_exists($absolutePath)) {
            return require $absolutePath;
        } else {
            error_log("Could not find config file: {$relativePath}");
            die("Fatal error: Could not find required config file: {$relativePath}");
        }
    }
}

// Require service files with fallback
safe_require_once('/src/Services/DatabaseManager.php');
safe_require_once('/src/Services/AnalyticsService.php');
safe_require_once('/src/Services/PaystackClient.php');
safe_require_once('/src/Services/PaystackSyncService.php');
safe_require_once('/src/Services/XeroService.php');

// Load configuration with fallback
$appConfig = safe_require_config('/config/app.php');

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
    
    // Try to find the view file using BASE_PATH
    $viewFile = BASE_PATH . '/views/' . $view . '.php';
    
    // If the file doesn't exist at the expected location, try an alternative path
    if (!file_exists($viewFile)) {
        $altViewFile = '/var/www/html/views/' . $view . '.php';
        
        if (file_exists($altViewFile)) {
            $viewFile = $altViewFile;
        } else {
            error_log("View not found: {$view}");
            error_log("Tried paths: {$viewFile} and {$altViewFile}");
            http_response_code(500);
            echo "View not found: " . htmlspecialchars($view);
            exit;
        }
    }
    
    include $viewFile;
}
