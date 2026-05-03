<?php
declare(strict_types=1);

// Show errors for debugging
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Include config and auth
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/auth.php';

// 1️⃣ Logout user safely
if (function_exists('logout_user')) {
    logout_user();
} else {
    // Fallback: manually destroy session
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION = [];
    if (session_id() !== "" || isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    session_destroy();
}

// 2️⃣ Determine login URL
$loginUrl = (defined('BASE_URL') ? BASE_URL : '') . '/public/login.php';

// 3️⃣ Redirect safely
if (!headers_sent()) {
    header("Location: $loginUrl");
    exit;
} else {
    // Fallback: show link if headers already sent
    echo "You have been logged out. <a href='$loginUrl'>Click here to login again.</a>";
    exit;
}