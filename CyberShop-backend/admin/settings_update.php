<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/auth.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newName = trim($_POST['name'] ?? '');
    $newEmail = trim($_POST['email'] ?? '');
    $userId = current_user()['user_id'];

    if (!empty($newName) && filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        try {
            $stmt = db()->prepare("UPDATE users SET name = ?, email = ? WHERE user_id = ?");
            $stmt->execute([$newName, $newEmail, $userId]);
            
            // Success: Update the session data so the sidebar reflects the new name
            $_SESSION['user']['name'] = $newName;
            $_SESSION['user']['email'] = $newEmail;

            header("Location: settings.php?success=1");
            exit;
        } catch (Exception $e) {
            header("Location: settings.php?error=db");
            exit;
        }
    }
}

header("Location: settings.php?error=invalid");
exit;