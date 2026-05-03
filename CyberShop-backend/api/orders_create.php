<?php
declare(strict_types=1);

/**
 * 🌐 STEP 1: CORS & SECURITY HEADERS
 * These headers define who can talk to your API.
 */
header("Access-Control-Allow-Origin: http://localhost:4200"); // Only allow your Angular app
header("Access-Control-Allow-Methods: POST, OPTIONS");        // Only allow POST for orders
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Allow Auth tokens
header("Content-Type: application/json");                     // Always respond with JSON

/**
 * 🛡️ STEP 2: HANDLE PRE-FLIGHT (OPTIONS)
 * Browsers send an OPTIONS request first to check if the API is safe.
 */
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

/**
 * 🥾 STEP 3: BOOTSTRAP & AUTHENTICATION
 * Load database connections and check if the user is logged in.
 */
require_once __DIR__ . '/_bootstrap.php';
require_login(); // 🔐 SECURITY: If no valid token is found, this kills the script immediately.

/**
 * 📥 STEP 4: INPUT COLLECTION
 * Get the raw JSON data sent from Angular's checkout() function.
 */
$data = json_decode(file_get_contents("php://input"), true);
$items = $data['items'] ?? [];
$shippingAddress = $data['location'] ?? 'Ajax, Ontario';

/**
 * ❌ STEP 5: BASIC VALIDATION
 * Don't proceed if the cart is empty or the data is malformed.
 */
if (!is_array($items) || count($items) === 0) {
    http_response_code(400);
    echo json_encode(["ok" => false, "error" => "Cart is empty"]);
    exit;
}

/**
 * 👤 STEP 6: USER IDENTIFICATION
 * Get the User ID from the session/token decoded in _bootstrap.php.
 */
$currentUser = current_user();
$userId = (int)($currentUser['id'] ?? $currentUser['user_id'] ?? 0);

if ($userId <= 0) {
    http_response_code(401);
    echo json_encode(["ok" => false, "error" => "Invalid session"]);
    exit;
}

$db = db(); // Initialize PDO database connection

try {
    /**
     * 🏗️ STEP 7: START TRANSACTION
     * This is CRITICAL for data integrity. If one part of the order fails, 
     * the whole things rolls back (no partial orders in the database).
     */
    $db->beginTransaction();

    // Create the "Order Header" record
    $stmt = $db->prepare("INSERT INTO orders (user_id, total, status, shipping_address) VALUES (?, 0, 'pending', ?)");
    $stmt->execute([$userId, $shippingAddress]);
    $orderId = (int)$db->lastInsertId();

    $total = 0;
    $validItems = [];

    /**
     * 🕵️‍♂️ STEP 8: SERVER-SIDE VALIDATION LOOP
     * Never trust the price or stock data sent from the frontend.
     */
    foreach ($items as $item) {
        $pid = (int)($item['productId'] ?? 0);
        $qty = (int)($item['qty'] ?? 0);

        if ($pid <= 0 || $qty <= 0) continue;

        // Fetch fresh product data directly from the DB
        $p = product_find($pid); 

        // Check if product exists and is active
        if (!$p || (int)$p['isActive'] !== 1) continue;

        // 🛡️ SECURITY: Prevent overselling
        if ((int)$p['stock'] < $qty) {
            throw new Exception("Insufficient stock for " . $p['name']);
        }

        // 💰 SECURITY: Always use the price from your SQL, not from the browser request.
        $price = (float)$p['price'];
        $total += $price * $qty;

        $validItems[] = ['pid' => $pid, 'qty' => $qty, 'price' => $price];
    }

    if (count($validItems) === 0) {
        throw new Exception("No valid products in cart");
    }

    /**
     * 📝 STEP 9: SAVE ITEMS & DEDUCT STOCK
     * Loop through the validated items to record them and update inventory.
     */
    $insertItem = $db->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)");
    $updateStock = $db->prepare("UPDATE products SET stock = stock - ? WHERE product_id = ?");

    foreach ($validItems as $item) {
        // Record the item in order_items (price_at_purchase keeps a record of the price today)
        $insertItem->execute([$orderId, $item['pid'], $item['qty'], $item['price']]);
        
        // Subtract the purchased quantity from the products table
        $updateStock->execute([$item['qty'], $item['pid']]);
    }

    /**
     * 🏁 STEP 10: FINALIZE
     * Update the total price in the order header and commit the transaction.
     */
    $db->prepare("UPDATE orders SET total = ? WHERE order_id = ?")->execute([$total, $orderId]);

    $db->commit(); // Save everything to the database permanently

    echo json_encode([
        "ok" => true,
        "orderId" => $orderId,
        "total" => $total,
        "message" => "Order created successfully"
    ]);

} catch (Exception $e) {
    /**
     * 🚨 STEP 11: ERROR HANDLING
     * If ANYTHING went wrong, undo all database changes made during this script.
     */
    $db->rollBack();
    http_response_code(500);
    echo json_encode([
        "ok" => false, 
        "error" => $e->getMessage()
    ]);
}