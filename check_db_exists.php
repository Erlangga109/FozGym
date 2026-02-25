<?php
// Check if database exists
require_once __DIR__ . '/config.php';

try {
    // First, connect without specifying the database
    $pdo = new PDO("mysql:host=$DB_HOST;charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    // Check if the specific database exists
    $stmt = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$DB_NAME'");
    $result = $stmt->fetch();

    if ($result) {
        echo "<h2>Database '$DB_NAME' exists!</h2>";
        echo "<p>✓ Database connection is working properly</p>";
    } else {
        echo "<h2>Database '$DB_NAME' does not exist!</h2>";
        echo "<p>Creating the database...</p>";

        // Create the database
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$DB_NAME` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "<p style='color: green;'>✓ Database '$DB_NAME' created successfully!</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color: red;'>Database connection error: " . $e->getMessage() . "</p>";
}

echo "<p>Now run: <a href='init_db.php'>init_db.php</a> to create the tables.</p>";