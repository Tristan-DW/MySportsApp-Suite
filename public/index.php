<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/src/bootstrap.php';

use MySportsApp\Controllers\AuthController;
use MySportsApp\Controllers\DashboardController;
use MySportsApp\Controllers\KnowledgeController;
use MySportsApp\Controllers\TicketController;
use MySportsApp\Controllers\FinanceController;
use MySportsApp\Controllers\AdminController;

$route = $_GET['route'] ?? null;

// Public routes
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

// Auth-only routes
if ($route === 'logout' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    (new AuthController())->logout();
    exit;
}

switch ($route) {
    case null:
    case 'dashboard':
        require_login();
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
