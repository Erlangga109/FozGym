<?php
// Disable any existing sessions to prevent conflicts
if (session_status() == PHP_SESSION_ACTIVE) {
    session_destroy();
}

// Simple test without database or session dependencies
echo "<h1>Basic PHP Test</h1>";
echo "<p>PHP is working: " . (function_exists('phpversion') ? 'Yes' : 'No') . "</p>";
echo "<p>Current time: " . date('Y-m-d H:i:s') . "</p>";
echo "<p>MySQL connection test:</p>";

// Test database connection directly without session
$host = 'localhost';
$dbname = 'fozgym';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    echo "<p style='color: green;'>✓ Database connection successful!</p>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Database connection failed: " . $e->getMessage() . "</p>";
}

echo "<p><a href='?clear_session=1'>Try clearing session and redirect</a></p>";

if (isset($_GET['clear_session'])) {
    // Clear any problematic session
    session_start();
    session_destroy();
    session_write_close();
    echo "<p>Session cleared. Try accessing the main site again.</p>";
}