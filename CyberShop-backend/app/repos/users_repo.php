<?php
declare(strict_types=1);

require_once __DIR__ . '/../db.php';

/**
 * 1. Finds a user by email (used for LOGIN)
 */
function user_find_by_email(string $email): ?array {
    $stmt = db()->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    return $user ?: null;
}

/**
 * 2. Finds a user by ID
 */
function user_find(int $id): ?array {
    $stmt = db()->prepare("SELECT * FROM users WHERE user_id = ? LIMIT 1");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

/**
 * 3. Creates a new user node (used for REGISTER)
 */
function user_create(string $name, string $email, string $password, string $role = 'user'): int {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = db()->prepare("
        INSERT INTO users (name, email, password, role)
        VALUES (:name, :email, :password, :role)
    ");
    
    $stmt->execute([
        ':name'     => $name,
        ':email'    => $email,
        ':password' => $hash,
        ':role'     => $role,
    ]);
    
    return (int)db()->lastInsertId();
}

/**
 * 4. ADMIN_ONLY: Fetches all users for the Management Terminal
 */
function users_all(): array {
    try {
        // We select only needed columns for security
        $stmt = db()->query("SELECT user_id, name, email, role, created_at FROM users ORDER BY created_at DESC");
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("User Repo Error: " . $e->getMessage());
        return [];
    }
}

/**
 * 5. ADMIN_ONLY: Counts total registered nodes
 */
function users_count(): int {
    try {
        $stmt = db()->query("SELECT COUNT(*) FROM users");
        return (int)$stmt->fetchColumn();
    } catch (Exception $e) {
        return 0;
    }
}