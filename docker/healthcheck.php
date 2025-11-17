<?php
/**
 * Health check script for the PHP application container
 * 
 * This script checks:
 * 1. If the database connection is working
 * 2. If the required tables exist
 */

// Try to connect to the database
try {
    $dsn = "mysql:host=db;dbname={$_ENV['MYSQL_DATABASE']};charset=utf8mb4";
    $pdo = new PDO(
        $dsn,
        $_ENV['MYSQL_USER'],
        $_ENV['MYSQL_PASSWORD'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
    
    // Check if the users table exists and has data
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $userCount = $stmt->fetchColumn();
    
    if ($userCount < 1) {
        echo "Health check failed: users table exists but has no data\n";
        exit(1);
    }
    
    // Check if all required tables exist
    $requiredTables = [
        'users', 'analytics_sources', 'knowledge_articles', 'tickets', 
        'ticket_notes', 'settings', 'paystack_settlements', 
        'paystack_transactions', 'xero_connections'
    ];
    
    $missingTables = [];
    foreach ($requiredTables as $table) {
        $stmt = $pdo->prepare("SHOW TABLES LIKE :table");
        $stmt->execute([':table' => $table]);
        if ($stmt->rowCount() === 0) {
            $missingTables[] = $table;
        }
    }
    
    if (!empty($missingTables)) {
        echo "Health check failed: missing tables: " . implode(', ', $missingTables) . "\n";
        exit(1);
    }
    
    echo "Health check passed: database connection working and all tables exist\n";
    exit(0);
    
} catch (PDOException $e) {
    echo "Health check failed: database connection error: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "Health check failed: " . $e->getMessage() . "\n";
    exit(1);
}