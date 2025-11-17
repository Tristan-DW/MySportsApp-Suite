<?php
/**
 * Health check script for the PHP application container
 * 
 * This script checks:
 * 1. If the database connection is working
 * 2. If the required tables exist
 */

// Log function to write to both stdout and system log
function log_message($message) {
    echo $message . "\n";
    error_log($message);
}

log_message("Health check starting at " . date('Y-m-d H:i:s'));
log_message("Environment variables: MYSQL_DATABASE=" . (isset($_ENV['MYSQL_DATABASE']) ? $_ENV['MYSQL_DATABASE'] : 'not set'));
log_message("Environment variables: MYSQL_USER=" . (isset($_ENV['MYSQL_USER']) ? $_ENV['MYSQL_USER'] : 'not set'));
log_message("Environment variables: MYSQL_PASSWORD=" . (isset($_ENV['MYSQL_PASSWORD']) ? 'is set' : 'not set'));

// Try to connect to the database
try {
    log_message("Attempting to connect to database...");
    $dsn = "mysql:host=db;dbname={$_ENV['MYSQL_DATABASE']};charset=utf8mb4";
    $pdo = new PDO(
        $dsn,
        $_ENV['MYSQL_USER'],
        $_ENV['MYSQL_PASSWORD'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 5, // 5 second timeout
        ]
    );
    log_message("Database connection successful");
    
    // Check if the users table exists and has data
    log_message("Checking if users table exists and has data...");
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $userCount = $stmt->fetchColumn();
    log_message("Users table exists with $userCount records");
    
    if ($userCount < 1) {
        log_message("Health check failed: users table exists but has no data");
        exit(1);
    }
    
    // Check if all required tables exist
    log_message("Checking if all required tables exist...");
    $requiredTables = [
        'users', 'analytics_sources', 'knowledge_articles', 'tickets', 
        'ticket_notes', 'settings', 'paystack_settlements', 
        'paystack_transactions', 'xero_connections'
    ];
    
    $missingTables = [];
    foreach ($requiredTables as $table) {
        log_message("Checking table: $table");
        $stmt = $pdo->prepare("SHOW TABLES LIKE :table");
        $stmt->execute([':table' => $table]);
        if ($stmt->rowCount() === 0) {
            $missingTables[] = $table;
            log_message("Table $table is missing");
        }
    }
    
    if (!empty($missingTables)) {
        log_message("Health check failed: missing tables: " . implode(', ', $missingTables));
        exit(1);
    }
    
    log_message("Health check passed: database connection working and all tables exist");
    exit(0);
    
} catch (PDOException $e) {
    log_message("Health check failed: database connection error: " . $e->getMessage());
    exit(1);
} catch (Exception $e) {
    log_message("Health check failed: " . $e->getMessage());
    exit(1);
}