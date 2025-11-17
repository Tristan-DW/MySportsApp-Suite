<?php
declare(strict_types=1);

namespace MySportsApp\Controllers;

use MySportsApp\Services\AnalyticsService;
use PDO;

class DashboardController
{
    public function index(): void
    {
        require_login();

        $analytics = new AnalyticsService();
        $agg       = $analytics->aggregate(analytics_dbs());

        $totals    = $agg['totals'] ?? [];
        $provinces = $agg['provinces'] ?? [];

        // open tickets
        $openTickets = (int)db()->query("SELECT COUNT(*) FROM tickets WHERE status IN ('open','pending')")->fetchColumn();

        // income last 30 days from paystack_transactions
        $stmtIncome = db()->prepare("
            SELECT COALESCE(SUM(amount),0) AS total
            FROM paystack_transactions
            WHERE paid_at IS NOT NULL
              AND paid_at >= (NOW() - INTERVAL 30 DAY)
        ");
        $stmtIncome->execute();
        $last30Income = (float)$stmtIncome->fetchColumn() / 100.0; // Paystack amounts are in kobo/cents typically

        $metrics = [
            'players'      => $totals['players']  ?? 0,
            'clubs'        => $totals['clubs']    ?? 0,
            'coaches'      => $totals['coaches']  ?? 0,
            'refs'         => $totals['referees'] ?? 0,
            'reos'         => $totals['reos']     ?? 0,
            'openTickets'  => $openTickets,
            'last30Income' => $last30Income,
        ];

        render('pages/dashboard', [
            'metrics'   => $metrics,
            'provinces' => $provinces,
        ]);
    }
}
