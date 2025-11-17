<?php
declare(strict_types=1);

namespace MySportsApp\Services;

use PDO;

class PaystackSyncService
{
    private PaystackClient $client;
    private PDO $db;

    public function __construct(PDO $db, PaystackClient $client)
    {
        $this->db     = $db;
        $this->client = $client;
    }

    private function parseReference(?string $reference): array
    {
        if (!$reference) {
            return ['province' => null, 'region' => null, 'club' => null, 'unique' => null];
        }

        $parts = explode('-', $reference);
        if (count($parts) < 4) {
            return ['province' => null, 'region' => null, 'club' => null, 'unique' => null];
        }

        $province = array_shift($parts);
        $region   = array_shift($parts);
        $unique   = array_pop($parts);
        $club     = implode('-', $parts);

        return [
            'province' => strtoupper($province),
            'region'   => $region,
            'club'     => $club,
            'unique'   => $unique,
        ];
    }

    public function syncSettlements(int $maxPages = 1): void
    {
        $this->db->beginTransaction();
        try {
            $page    = 1;
            $perPage = 50;

            while ($page <= $maxPages) {
                $res  = $this->client->listSettlements($page, $perPage);
                $data = $res['data'] ?? [];
                $meta = $res['meta'] ?? [];
                $hasNext = !empty($meta['next_page']);

                foreach ($data as $s) {
                    $settlementId = (int)$s['id'];

                    $stmt = $this->db->prepare("
                        INSERT INTO paystack_settlements
                        (paystack_id, integration_id, total_amount, transaction_volume, status, currency, settled_at, paid_at, created_at, updated_at)
                        VALUES (:paystack_id, :integration_id, :total_amount, :transaction_volume, :status, :currency, :settled_at, :paid_at, NOW(), NOW())
                        ON DUPLICATE KEY UPDATE
                          integration_id = VALUES(integration_id),
                          total_amount   = VALUES(total_amount),
                          transaction_volume = VALUES(transaction_volume),
                          status         = VALUES(status),
                          currency       = VALUES(currency),
                          settled_at     = VALUES(settled_at),
                          paid_at        = VALUES(paid_at),
                          updated_at     = NOW()
                    ");

                    $stmt->execute([
                        ':paystack_id'        => $settlementId,
                        ':integration_id'     => $s['integration'] ?? null,
                        ':total_amount'       => $s['total_amount'] ?? 0,
                        ':transaction_volume' => $s['transaction_volume'] ?? 0,
                        ':status'             => $s['status'] ?? '',
                        ':currency'           => $s['currency'] ?? 'ZAR',
                        ':settled_at'         => $s['settled_at'] ?? null,
                        ':paid_at'            => $s['paid_at'] ?? null,
                    ]);

                    $this->syncSettlementTransactions($settlementId);
                }

                if (!$hasNext) break;
                $page++;
            }

            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    private function syncSettlementTransactions(int $settlementPaystackId, int $maxPages = 3): void
    {
        $settlementStmt = $this->db->prepare("SELECT id FROM paystack_settlements WHERE paystack_id = :pid");
        $settlementStmt->execute([':pid' => $settlementPaystackId]);
        $settlementLocalId = (int)$settlementStmt->fetchColumn();
        if (!$settlementLocalId) return;

        $page    = 1;
        $perPage = 200;

        while ($page <= $maxPages) {
            $res  = $this->client->settlementTransactions($settlementPaystackId, $page, $perPage);
            $data = $res['data'] ?? [];
            $meta = $res['meta'] ?? [];
            $hasNext = !empty($meta['next_page']);

            foreach ($data as $t) {
                $parsed = $this->parseReference($t['reference'] ?? null);

                $stmt = $this->db->prepare("
                    INSERT INTO paystack_transactions
                    (paystack_id, settlement_id, reference, amount, currency, status, paid_at,
                     customer_email, province, region, club_slug, unique_key, raw_json, created_at, updated_at)
                    VALUES
                    (:paystack_id, :settlement_id, :reference, :amount, :currency, :status, :paid_at,
                     :customer_email, :province, :region, :club_slug, :unique_key, :raw_json, NOW(), NOW())
                    ON DUPLICATE KEY UPDATE
                      settlement_id  = VALUES(settlement_id),
                      reference      = VALUES(reference),
                      amount         = VALUES(amount),
                      currency       = VALUES(currency),
                      status         = VALUES(status),
                      paid_at        = VALUES(paid_at),
                      customer_email = VALUES(customer_email),
                      province       = VALUES(province),
                      region         = VALUES(region),
                      club_slug      = VALUES(club_slug),
                      unique_key     = VALUES(unique_key),
                      raw_json       = VALUES(raw_json),
                      updated_at     = NOW()
                ");

                $stmt->execute([
                    ':paystack_id'    => $t['id'],
                    ':settlement_id'  => $settlementLocalId,
                    ':reference'      => $t['reference'] ?? '',
                    ':amount'         => $t['amount'] ?? 0,
                    ':currency'       => $t['currency'] ?? 'ZAR',
                    ':status'         => $t['status'] ?? '',
                    ':paid_at'        => $t['paid_at'] ?? null,
                    ':customer_email' => $t['customer']['email'] ?? null,
                    ':province'       => $parsed['province'],
                    ':region'         => $parsed['region'],
                    ':club_slug'      => $parsed['club'],
                    ':unique_key'     => $parsed['unique'],
                    ':raw_json'       => json_encode($t),
                ]);
            }

            if (!$hasNext) break;
            $page++;
        }
    }
}
