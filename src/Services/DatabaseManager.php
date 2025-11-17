<?php
declare(strict_types=1);

namespace MySportsApp\Services;

use PDO;
use PDOException;

class DatabaseManager
{
    private static array $config = [];
    private static ?PDO $primary = null;
    private static array $analytics = [];

    public static function init(array $config): void
    {
        self::$config = $config;
    }

    public static function primary(): PDO
    {
        if (self::$primary instanceof PDO) {
            return self::$primary;
        }

        $cfg = self::$config['primary'] ?? [
            'dsn'      => 'mysql:host=db;dbname=mysportsapp_suite;charset=utf8mb4',
            'user'     => 'mysportsapp',
            'password' => 'mysportsapp_password',
        ];

        self::$primary = self::makeConnection($cfg);
        return self::$primary;
    }

    public static function analytics(): array
    {
        if (!empty(self::$analytics)) {
            return self::$analytics;
        }

        $primary = self::primary();
        $rows = $primary->query("SELECT * FROM analytics_sources WHERE is_active = 1")->fetchAll(PDO::FETCH_ASSOC);

        $out = [];
        foreach ($rows as $row) {
            $label = $row['name'];
            $out[$label] = self::makeConnection([
                'dsn'      => $row['dsn'],
                'user'     => $row['db_user'],
                'password' => $row['db_password'],
            ]);
        }

        self::$analytics = $out;
        return $out;
    }

    private static function makeConnection(array $cfg): PDO
    {
        try {
            $pdo = new PDO(
                $cfg['dsn'],
                $cfg['user'],
                $cfg['password'],
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
            return $pdo;
        } catch (PDOException $e) {
            throw new \RuntimeException("DB connection failed: " . $e->getMessage());
        }
    }
}
