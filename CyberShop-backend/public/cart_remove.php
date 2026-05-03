<?php
declare(strict_types=1);

// 1. Load config so the script knows what BASE_URL is
require_once __DIR__ . '/../app/config.php';

// 2. Ensure session is active (using your existing check)
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 3. Get the Product ID from the POST request
$productId = filter_input(INPUT_POST, 'productId', FILTER_VALIDATE_INT) ?: 0;

// 4. If ID is valid and exists in the cart, remove it
if ($productId > 0 && isset($_SESSION['cart'][$productId])) {
    unset($_SESSION['cart'][$productId]);
}

// 5. Secure redirect back to the cart view
header("Location: " . BASE_URL . "/public/cart.php");
exit;