<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include_once 'db_connection.php'; 

$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data['name'])) {
    $name = $data['name'];
    $sku = $data['sku'] ?? '';
    $price = $data['price'] ?? 0;
    $stock = $data['stock'] ?? 0;
    $category = $data['category'] ?? 'General';

    $stmt = $conn->prepare("INSERT INTO products (name, sku, price, stock, category, isActive) VALUES (?, ?, ?, ?, ?, 1)");
    $stmt->bind_param("ssdis", $name, $sku, $price, $stock, $category);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Synchronized"]);
    } else {
        echo json_encode(["success" => false, "message" => "Database Write Error"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Incomplete Data"]);
}
?>