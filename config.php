<?php
// FozGym configuration
session_start();
date_default_timezone_set('Asia/Jakarta');

// Database config (adjust if needed)
$DB_HOST = 'localhost';
$DB_NAME = 'dbfozgym';
$DB_USER = 'root';
$DB_PASS = '';

// Optional: AI API Keys for AI Coach feature
// Set these to enable real AI responses; leave empty to use local tips fallback
$OPENAI_API_KEY = '';
$OPENAI_MODEL = 'gpt-4o-mini'; // Change model if needed

$GEMINI_API_KEY = 'AIzaSyA99v_w9s0XPvyemlRzhovuWx-7_Gv8fIM'; // Gemini API key for free usage
$GEMINI_MODEL = 'gemini-2.0-flash'; // Change model if needed

// Choose which AI provider to use: 'openai' or 'gemini'
$AI_PROVIDER = 'gemini'; // Default to gemini for free usage

// Base URL configuration
$BASE_URL = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$BASE_URL .= $_SERVER['HTTP_HOST'];
if (strpos($_SERVER['REQUEST_URI'], '/FozGym') === 0) {
    $BASE_URL .= '/FozGym';
} elseif (strpos($_SERVER['REQUEST_URI'], '/public_html') !== false) {
    // For subdirectory hosting
    $subdir = dirname($_SERVER['SCRIPT_NAME']);
    if ($subdir !== '/') {
        $BASE_URL .= rtrim($subdir, '/');
    }
}

// App helpers
function app_name() { return 'FozGym'; }

function get_base_url() {
    global $BASE_URL;
    return $BASE_URL;
}

function is_logged_in() {
    return isset($_SESSION['user']);
}

function current_user() {
    return $_SESSION['user'] ?? null;
}

function require_login() {
    if (!is_logged_in()) {
        $login_url = get_base_url() . '/login.php';
        header('Location: ' . $login_url);
        exit;
    }
}

function flash($key, $message = null) {
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return;
    }
    if (isset($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    return null;
}

function has_role(string $role): bool {
    return is_logged_in() && (current_user()['role'] ?? '') === $role;
}

function is_owner(): bool { return has_role('owner'); }
function is_trainer(): bool { return has_role('trainer'); }
function is_customer(): bool { return has_role('customer'); }

function require_roles(array $roles) {
    if (!is_logged_in() || !in_array(current_user()['role'] ?? '', $roles, true)) {
        flash('error', 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        $redirect_url = get_base_url() . '/';
        header('Location: ' . $redirect_url);
        exit;
    }
}