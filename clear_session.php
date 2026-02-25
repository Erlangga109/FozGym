<?php
// Clear all sessions to fix potential session-related loading issues
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Destroy all session data
$_SESSION = array();

// Delete the session cookie if it exists
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finally, destroy the session
session_destroy();

echo "<h1>Session cleared successfully!</h1>";
echo "<p>All session data has been cleared. This might resolve the loading issue.</p>";
echo "<p><a href='/FozGym/' class='btn btn-primary'>Try accessing FozGym again</a></p>";
echo "<p><a href='/FozGym/login.php' class='btn btn-success'>Go to Login Page</a></p>";
echo "<p><a href='/FozGym/simple_test.php' class='btn btn-info'>Run Simple Test</a></p>";