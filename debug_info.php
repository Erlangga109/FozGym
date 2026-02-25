<?php
// File debugging untuk menganalisis perbedaan antara lokal dan hosting
require_once __DIR__ . '/config.php';

echo "<h1>Debug Information</h1>";

echo "<h2>Environment Information:</h2>";
echo "<p>Current URL: " . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p>Base URL Function: " . get_base_url() . "</p>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</p>";

echo "<h2>Configuration Values:</h2>";
echo "<p>DB_HOST: $DB_HOST</p>";
echo "<p>DB_NAME: $DB_NAME</p>";
echo "<p>DB_USER: $DB_USER</p>";
echo "<p>DB_PASS: " . (empty($DB_PASS) ? '(empty)' : str_repeat('*', strlen($DB_PASS))) . "</p>";

echo "<h2>File Structure Check:</h2>";
$files_to_check = [
    'config.php',
    'index.php',
    'partials/header.php',
    'partials/footer.php',
    'assets/style.css',
    'img/1.png',
    'img/2.png',
    'img/3.png',
    'img/4.jpeg',
    'img/5.jpeg',
    'img/6.jpeg'
];

foreach ($files_to_check as $file) {
    $full_path = __DIR__ . '/' . $file;
    if (file_exists($full_path)) {
        echo "<p style='color: green;'>✓ $file exists</p>";
    } else {
        echo "<p style='color: red;'>✗ $file NOT FOUND</p>";
    }
}

echo "<h2>Database Connection Test:</h2>";
try {
    require_once __DIR__ . '/db.php';
    $pdo = get_pdo();
    echo "<p style='color: green;'>✓ Database connection successful</p>";
    
    // Test some key tables
    $tables = ['users', 'classes', 'members', 'schedules', 'enrollments'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            // Check if table has records
            $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
            echo "<p style='color: green;'>✓ Table '$table' exists with $count records</p>";
        } else {
            echo "<p style='color: red;'>✗ Table '$table' does not exist</p>";
        }
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database connection failed: " . $e->getMessage() . "</p>";
}

echo "<h2>URL Structure Analysis:</h2>";
echo "<pre>";
echo "SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'N/A') . "\n";
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'N/A') . "\n";
echo "SCRIPT_FILENAME: " . ($_SERVER['SCRIPT_FILENAME'] ?? 'N/A') . "\n";
echo "PATH_INFO: " . ($_SERVER['PATH_INFO'] ?? 'N/A') . "\n";
echo "</pre>";

echo "<h2>Session Information:</h2>";
echo "<p>Session status: " . (session_status() === PHP_SESSION_ACTIVE ? 'Active' : 'Not Active') . "</p>";
if (isset($_SESSION['user'])) {
    echo "<p>User logged in: " . ($_SESSION['user']['name'] ?? $_SESSION['user']['email'] ?? 'Unknown') . " (Role: " . ($_SESSION['user']['role'] ?? 'N/A') . ")</p>";
} else {
    echo "<p>No user logged in</p>";
}
?>