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

// Debug mode check
$debug = isset($_ENV['PHP_DEBUG']) && $_ENV['PHP_DEBUG'] === 'true';

log_message("Health check starting at " . date('Y-m-d H:i:s'));
log_message("Environment variables: MYSQL_DATABASE=" . (isset($_ENV['MYSQL_DATABASE']) ? $_ENV['MYSQL_DATABASE'] : 'not set'));
log_message("Environment variables: MYSQL_USER=" . (isset($_ENV['MYSQL_USER']) ? $_ENV['MYSQL_USER'] : 'not set'));
log_message("Environment variables: MYSQL_PASSWORD=" . (isset($_ENV['MYSQL_PASSWORD']) ? 'is set' : 'not set'));
log_message("Environment variables: PHP_DEBUG=" . ($debug ? 'true' : 'false'));

// Try to connect to the database with multiple attempts
$max_attempts = 3;
$attempt = 0;
$connected = false;
$pdo = null;
$last_error = '';

while ($attempt < $max_attempts && !$connected) {
    $attempt++;
    try {
        log_message("Attempting to connect to database (attempt $attempt/$max_attempts)...");
        
        // Try with hostname 'db' first
        $dsn = "mysql:host=db;dbname={$_ENV['MYSQL_DATABASE']};charset=utf8mb4";
        if ($debug) {
            log_message("Using DSN: $dsn");
        }
        
        $pdo = new PDO(
            $dsn,
            $_ENV['MYSQL_USER'],
            $_ENV['MYSQL_PASSWORD'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_TIMEOUT => 8, // Increased timeout
                PDO::ATTR_PERSISTENT => false, // Ensure we get a fresh connection
            ]
        );
        
        // Test the connection with a simple query
        $pdo->query("SELECT 1");
        
        log_message("Database connection successful");
        $connected = true;
    } catch (PDOException $e) {
        $last_error = $e->getMessage();
        log_message("Database connection failed (attempt $attempt/$max_attempts): " . $last_error);
        
        // If we've tried with 'db' and failed, try with IP address
        if ($attempt == 1) {
            try {
                log_message("Trying alternative connection with IP 127.0.0.1...");
                $dsn = "mysql:host=127.0.0.1;dbname={$_ENV['MYSQL_DATABASE']};charset=utf8mb4";
                $pdo = new PDO(
                    $dsn,
                    $_ENV['MYSQL_USER'],
                    $_ENV['MYSQL_PASSWORD'],
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_TIMEOUT => 8,
                    ]
                );
                $pdo->query("SELECT 1");
                log_message("Alternative connection successful");
                $connected = true;
            } catch (PDOException $e2) {
                log_message("Alternative connection also failed: " . $e2->getMessage());
            }
        }
        
        if (!$connected && $attempt < $max_attempts) {
            log_message("Waiting 2 seconds before next attempt...");
            sleep(2);
        }
    }
}

if (!$connected) {
    log_message("All database connection attempts failed. Last error: $last_error");
    
    // Diagnostic information
    log_message("Diagnostic information:");
    log_message("PHP version: " . phpversion());
    log_message("PDO drivers: " . implode(", ", PDO::getAvailableDrivers()));
    
    // Try to ping the database host
    log_message("Attempting to ping db host...");
    $ping_output = shell_exec("ping -c 1 db 2>&1");
    log_message("Ping result: " . ($ping_output ? $ping_output : "No output"));
    
    exit(1);
}

// Check if the users table exists and has data
try {
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
        if ($debug) {
            log_message("Checking table: $table");
        }
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
    log_message("Health check failed during table verification: " . $e->getMessage());
    exit(1);
} catch (Exception $e) {
    log_message("Health check failed with unexpected error: " . $e->getMessage());
    exit(1);
}