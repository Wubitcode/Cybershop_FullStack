<?php
// 1. Security Headers for Angular Communication
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

// Handle Preflight (Internal browser check)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// 2. Database Connection
include_once 'db_connection.php'; 

// 3. Capture the JSON Input from Angular
$data = json_decode(file_get_contents("php://input"), true);

// 4. Validation & Execution
if (
    !empty($data['name']) && 
    !empty($data['sku']) && 
    isset($data['price']) && 
    isset($data['stock'])
) {
    // Sanitize and Map
    $name = $data['name'];
    $sku = $data['sku'];
    $price = (double)$data['price'];
    $stock = (int)$data['stock'];
    $category = !empty($data['category']) ? $data['category'] : 'Uncategorized';

    // 🛡️ CYBERSECURITY: Prepared Statement to block SQL Injection
    $query = "INSERT INTO products (name, sku, price, stock, category, isActive) VALUES (?, ?, ?, ?, ?, 1)";
    $stmt = $conn->prepare($query);

    // "ssdis" = string, string, double, integer, string
    $stmt->bind_param("ssdis", $name, $sku, $price, $stock, $category);

    if ($stmt->execute()) {
        http_response_code(201); // Created
        echo json_encode(["success" => true, "message" => "Asset synchronized successfully."]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Critical Error: Unable to write to node database."]);
    }
    
    $stmt->close();
} else {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Incomplete data packet. Initialization failed."]);
}

$conn->close();
?>