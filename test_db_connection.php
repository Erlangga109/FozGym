<?php
// Simple database connection test
require_once __DIR__ . '/config.php';

echo "Testing database connection...\n";

try {
    $dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4";
    echo "DSN: " . $dsn . "\n";
    echo "DB_HOST: " . $DB_HOST . "\n";
    echo "DB_NAME: " . $DB_NAME . "\n";
    echo "DB_USER: " . $DB_USER . "\n";
    
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    
    echo "Connection successful!\n";
    
    // Test a simple query
    $stmt = $pdo->query("SELECT 1 as test");
    $result = $stmt->fetch();
    echo "Test query result: " . $result['test'] . "\n";
    
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
    echo "Please make sure XAMPP Apache and MySQL are running.\n";
    echo "1. Start XAMPP Control Panel\n";
    echo "2. Click 'Start' button next to Apache and MySQL\n";
    echo "3. Then try accessing your website again.\n";
}