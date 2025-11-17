<?php
declare(strict_types=1);

namespace MySportsApp\Services;

use PDO;

class AnalyticsService
{
    public function aggregate(array $analyticsDbs): array
    {
        $totals = [
            'members'  => 0,
            'players'  => 0,
            'clubs'    => 0,
            'coaches'  => 0,
            'referees' => 0,
            'reos'     => 0,
        ];
        $provinceMap = [];

        foreach ($analyticsDbs as $label => $pdo) {
            if (!$pdo instanceof PDO) continue;

            // clubs count
            $clubCount = (int)$pdo->query("SELECT COUNT(*) FROM clubs")->fetchColumn();
            $totals['clubs'] += $clubCount;

            $sql = "
                SELECT
                    p.id as province_id,
                    p.name as province_name,
                    p.code as province_code,
                    r.id as region_id,
                    r.name as region_name,
                    COUNT(m.id) as members,
                    SUM(m.type = 'player') as players,
                    SUM(m.type = 'coach') as coaches,
                    SUM(m.type = 'referee') as referees,
                    SUM(m.type = 'reo') as reos
                FROM members m
                JOIN clubs c   ON m.club_id = c.id
                JOIN regions r ON c.region_id = r.id
                JOIN provinces p ON r.province_id = p.id
                GROUP BY p.id, r.id
                ORDER BY p.name, r.name
            ";

            $stmt = $pdo->query($sql);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $pKey = $row['province_code'];

                if (!isset($provinceMap[$pKey])) {
                    $provinceMap[$pKey] = [
                        'name'     => $row['province_name'],
                        'code'     => $row['province_code'],
                        'members'  => 0,
                        'players'  => 0,
                        'coaches'  => 0,
                        'referees' => 0,
                        'reos'     => 0,
                        'regions'  => [],
                    ];
                }

                $provinceMap[$pKey]['members']  += (int)$row['members'];
                $provinceMap[$pKey]['players']  += (int)$row['players'];
                $provinceMap[$pKey]['coaches']  += (int)$row['coaches'];
                $provinceMap[$pKey]['referees'] += (int)$row['referees'];
                $provinceMap[$pKey]['reos']     += (int)$row['reos'];

                $provinceMap[$pKey]['regions'][] = [
                    'name'     => $row['region_name'],
                    'members'  => (int)$row['members'],
                    'players'  => (int)$row['players'],
                    'coaches'  => (int)$row['coaches'],
                    'referees' => (int)$row['referees'],
                    'reos'     => (int)$row['reos'],
                ];

                $totals['members']  += (int)$row['members'];
                $totals['players']  += (int)$row['players'];
                $totals['coaches']  += (int)$row['coaches'];
                $totals['referees'] += (int)$row['referees'];
                $totals['reos']     += (int)$row['reos'];
            }
        }

        return [
            'totals'    => $totals,
            'provinces' => array_values($provinceMap),
        ];
    }
}
