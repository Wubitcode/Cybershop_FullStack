<?php
declare(strict_types=1);

// 1. Correct relative paths for your folder structure
require_once __DIR__ . '/../../app/config.php'; 
require_once __DIR__ . '/../../app/auth.php';
require_once __DIR__ . '/../../app/repos/orders_repo.php';

require_admin();

// 2. Get the ID - Ensure this matches the name="order_id" in your view.php form
$id = filter_input(INPUT_POST, 'order_id', FILTER_VALIDATE_INT) ?: 0;

// 3. Clean and Validate Status
$status = strtolower(trim($_POST['status'] ?? ''));
$allowed = ['pending', 'paid', 'shipped', 'cancelled', 'completed']; // Added 'completed' to match your button

if ($id && in_array($status, $allowed)) {
    order_update_status($id, $status);
}

// 4. Redirect back to the specific order view
// Note: Check if your view page is 'view.php' or 'index.php'
header("Location: " . BASE_URL . "/admin/orders/view.php?id=" . $id);
exit;