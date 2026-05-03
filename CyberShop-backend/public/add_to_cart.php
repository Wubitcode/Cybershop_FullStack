<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/repos/products_repo.php';

// 1. Validate Inputs
// Make sure your form uses name="productId"
$productId = filter_input(INPUT_POST, 'productId', FILTER_VALIDATE_INT) ?: 0;
$qty = filter_input(INPUT_POST, 'qty', FILTER_VALIDATE_INT) ?: 1;

$qty = max(1, min(99, $qty));

// 2. Find Product
$product = $productId ? product_find((int)$productId) : null;

/** 
 * SYNC FIX: 
 * Your SQL dump uses 'isActive' (1 or 0), NOT 'status'.
 * If we check for 'status', it returns NULL, and redirects you away.
 */
if (!$product || (int)$product['isActive'] !== 1) {
    // This is why your cart was "empty" - it was redirecting here every time!
    header("Location: " . BASE_URL . "/public/index.php?error=inactive_asset");
    exit;
}

// 3. Update the Session Cart
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$_SESSION['cart'] ??= [];

// Add the quantity
$_SESSION['cart'][$productId] = ($_SESSION['cart'][$productId] ?? 0) + $qty;

// 4. Success redirect
header("Location: " . BASE_URL . "/public/cart.php");
exit;