<?php
declare(strict_types=1);

namespace MySportsApp\Controllers;

use MySportsApp\Services\PaystackClient;
use MySportsApp\Services\PaystackSyncService;
use PDO;

class FinanceController
{
    public function index(): void
    {
        require_login();
        require_role(['super_admin', 'accounting', 'support']);

        $db = db();

        $totalIncome = (float)$db->query("SELECT COALESCE(SUM(amount),0) FROM paystack_transactions")->fetchColumn() / 100.0;
        $totalPayouts = (float)$db->query("SELECT COALESCE(SUM(total_amount),0) FROM paystack_settlements")->fetchColumn() / 100.0;

        $stmt = $db->query("SELECT * FROM paystack_settlements ORDER BY settled_at DESC, id DESC");
        $settlements = $stmt->fetchAll(PDO::FETCH_ASSOC);

        render('pages/finance', [
            'totalIncome'   => $totalIncome,
            'totalPayouts'  => $totalPayouts,
            'settlements'   => $settlements,
        ]);
    }

    public function payoutDetail(): void
    {
        require_login();
        require_role(['super_admin', 'accounting', 'support']);

        $db = db();
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if (!$id) {
            header('Location: /index.php?route=finance');
            exit;
        }

        $stmt = $db->prepare("SELECT * FROM paystack_settlements WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $settlement = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$settlement) {
            header('Location: /index.php?route=finance');
            exit;
        }

        $stmt = $db->prepare("SELECT * FROM paystack_transactions WHERE settlement_id = :id");
        $stmt->execute([':id' => $id]);
        $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $nationalTotal = 0;
        $provMap = [];

        foreach ($transactions as $t) {
            $amount = (int)$t['amount'];
            $nationalTotal += $amount;

            $province = $t['province'] ?: 'UNKNOWN';
            $region   = $t['region']   ?: 'UNKNOWN';

            if (!isset($provMap[$province])) {
                $provMap[$province] = [
                    'province' => $province,
                    'total'    => 0,
                    'regions'  => [],
                ];
            }
            if (!isset($provMap[$province]['regions'][$region])) {
                $provMap[$province]['regions'][$region] = [
                    'region' => $region,
                    'total'  => 0,
                ];
            }

            $provMap[$province]['total']               += $amount;
            $provMap[$province]['regions'][$region]['total'] += $amount;
        }

        $provinces = array_values(array_map(function ($p) {
            $p['regions'] = array_values($p['regions']);
            return $p;
        }, $provMap));

        render('pages/finance_payout', [
            'settlement'    => $settlement,
            'transactions'  => $transactions,
            'nationalTotal' => $nationalTotal / 100.0,
            'provinces'     => $provinces,
        ]);
    }

    public function settings(): void
    {
        require_login();
        require_role(['super_admin']);

        $error   = null;
        $saved   = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            set_setting('paystack_secret_key', trim($_POST['paystack_secret_key'] ?? ''));
            set_setting('paystack_public_key', trim($_POST['paystack_public_key'] ?? ''));
            set_setting('xero_client_id', trim($_POST['xero_client_id'] ?? ''));
            set_setting('xero_client_secret', trim($_POST['xero_client_secret'] ?? ''));
            set_setting('xero_redirect_uri', trim($_POST['xero_redirect_uri'] ?? ''));
            $saved = true;
        }

        $data = [
            'paystack_secret_key' => setting('paystack_secret_key', ''),
            'paystack_public_key' => setting('paystack_public_key', ''),
            'xero_client_id'      => setting('xero_client_id', ''),
            'xero_client_secret'  => setting('xero_client_secret', ''),
            'xero_redirect_uri'   => setting('xero_redirect_uri', ''),
            'saved'               => $saved,
            'error'               => $error,
        ];

        render('pages/finance_settings', $data);
    }

    public function syncPaystack(): void
    {
        require_login();
        require_role(['super_admin']);

        $secret = setting('paystack_secret_key');
        if (!$secret) {
            $error = 'Paystack secret key not configured.';
            render('pages/finance_sync_result', ['error' => $error, 'ok' => false]);
            return;
        }

        try {
            $client = new PaystackClient($secret);
            $sync   = new PaystackSyncService(db(), $client);
            $sync->syncSettlements(1);
            render('pages/finance_sync_result', ['error' => null, 'ok' => true]);
        } catch (\Throwable $e) {
            render('pages/finance_sync_result', ['error' => $e->getMessage(), 'ok' => false]);
        }
    }
}
