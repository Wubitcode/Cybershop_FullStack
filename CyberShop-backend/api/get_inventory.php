<?php
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Content-Type: application/json; charset=UTF-8");

// Points to the 'app' folder which is one level up from 'api'
require_once __DIR__ . '/../app/db.php';

try {
    $db = db();
    
    // We use product_id and sku to match your SQL dump exactly
    $stmt = $db->query("SELECT product_id, sku, name, price, stock, category FROM products WHERE isActive = 1");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "count" => count($products),
        "data" => $products
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $e->getMessage()
    ]);
}