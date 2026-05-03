<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php'; 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function current_user(): ?array {
    return $_SESSION['user'] ?? null;
}

// FIX: Check for 'user_id' instead of 'id'
function is_logged_in(): bool {
    return isset($_SESSION['user']) && !empty($_SESSION['user']['user_id']);
}



function require_login(string $redirect = '/cybershop/public/login.php'): void {
    if (!is_logged_in()) {
        header("Location: $redirect");
        exit;
    }
}

/**
 * FIXED: Now uses 'user_id' consistently
 */
function login_user(array $userRow): void {
    session_regenerate_id(true);

    $_SESSION['user'] = [
        'user_id'  => (int)$userRow['user_id'], // Changed from 'id' to 'user_id'
        'fullName' => $userRow['name'],         
        'email'    => $userRow['email'],        
        'role'     => strtolower(trim((string)$userRow['role'])), 
    ];

    try {
        $stmt = db()->prepare("INSERT INTO system_logs (user_id, action, ip_address) VALUES (?, ?, ?)");
        $stmt->execute([
            (int)$userRow['user_id'], 
            'User Login',
            $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ]);
    } catch (Exception $e) {
        error_log("Audit Log Error: " . $e->getMessage());
    }
}
/**
 * SECURITY GATE: Only allows users with 'admin' role.
 * Redirects everyone else to the login page.
 */
function require_admin() {
    // 1. Check if user is even logged in
    if (!isset($_SESSION['user'])) {
        header("Location: " . BASE_URL . "/public/login.php");
        exit;
    }

    // 2. Check if the role is 'admin'
    // NOTE: We use 'user_id' and 'role' based on our previous fixes
    if (($_SESSION['user']['role'] ?? '') !== 'admin') {
        http_response_code(403);
        die("403: Administrative Clearance Required. Current Role: " . ($_SESSION['user']['role'] ?? 'None'));
    }
}

/**
 * Helper to check admin status without redirecting
 */
function is_admin(): bool {
    return isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
}