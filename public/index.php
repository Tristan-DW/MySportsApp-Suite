<?php
declare(strict_types=1);

/**
 * Minimal PSR-4 autoloader for the MySportsApp namespace.
 * This MUST run before bootstrap so bootstrap can also use namespaced classes.
 */
spl_autoload_register(function (string $class): void {
    $prefix  = 'MySportsApp\\';
    $baseDir = dirname(__DIR__) . '/src/';

    // Debug information to help diagnose the issue
    error_log("Autoloader: Looking for class {$class}");
    error_log("Autoloader: Base directory is {$baseDir}");

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // Not our namespace.
        error_log("Autoloader: Class {$class} is not in our namespace");
        return;
    }

    $relative = substr($class, $len); // e.g. "Controllers\AuthController"
    $file = $baseDir . str_replace('\\', '/', $relative) . '.php'; // src/Controllers/AuthController.php
    
    error_log("Autoloader: Looking for file {$file}");
    
    // Check if the file exists
    if (file_exists($file)) {
        error_log("Autoloader: File {$file} exists, requiring it");
        require $file;
    } else {
        error_log("Autoloader: File {$file} does not exist");
        
        // Try an alternative path (assuming src directory might be at a different location)
        $altBaseDir = '/var/www/html/src/';
        $altFile = $altBaseDir . str_replace('\\', '/', $relative) . '.php';
        error_log("Autoloader: Trying alternative path {$altFile}");
        
        if (file_exists($altFile)) {
            error_log("Autoloader: Alternative file {$altFile} exists, requiring it");
            require $altFile;
        } else {
            error_log("Autoloader: Alternative file {$altFile} does not exist");
        }
    }
});

// Bootstrap (config, DB, session helpers, require_login, etc.)
// Try to load bootstrap.php from the expected location
$bootstrapFile = dirname(__DIR__) . '/src/bootstrap.php';
if (file_exists($bootstrapFile)) {
    require_once $bootstrapFile;
} else {
    // Try an alternative path if the primary path fails
    $altBootstrapFile = '/var/www/html/src/bootstrap.php';
    if (file_exists($altBootstrapFile)) {
        require_once $altBootstrapFile;
    } else {
        die('Fatal error: Could not find bootstrap.php file');
    }
}

use MySportsApp\Controllers\AuthController;
use MySportsApp\Controllers\DashboardController;
use MySportsApp\Controllers\KnowledgeController;
use MySportsApp\Controllers\TicketController;
use MySportsApp\Controllers\FinanceController;
use MySportsApp\Controllers\AdminController;

$route = $_GET['route'] ?? null;

// ------- Public routes (no login required) -------

if ($route === 'login') {
    (new AuthController())->showLogin();
    exit;
}

if ($route === 'login_submit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    (new AuthController())->loginSubmit();
    exit;
}

if ($route === 'faq') {
    (new KnowledgeController())->publicFaq();
    exit;
}

if ($route === 'ticket_public') {
    (new TicketController())->publicForm();
    exit;
}

// ------- Auth-only routes -------

if ($route === 'logout' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    (new AuthController())->logout();
    exit;
}

// Everything below this point requires a logged-in user
require_login();

switch ($route) {
    case null:
    case 'dashboard':
        (new DashboardController())->index();
        break;

    case 'knowledge':
        (new KnowledgeController())->adminList();
        break;

    case 'knowledge_edit':
        (new KnowledgeController())->edit();
        break;

    case 'tickets':
        (new TicketController())->adminList();
        break;

    case 'ticket_view':
        (new TicketController())->view();
        break;

    case 'finance':
        (new FinanceController())->index();
        break;

    case 'finance_payout':
        (new FinanceController())->payoutDetail();
        break;

    case 'finance_settings':
        (new FinanceController())->settings();
        break;

    case 'finance_sync_paystack':
        (new FinanceController())->syncPaystack();
        break;

    case 'analytics_sources':
        (new AdminController())->analyticsSources();
        break;

    default:
        http_response_code(404);
        echo "Not found";
        break;
}
