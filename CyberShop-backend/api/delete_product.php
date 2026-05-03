<?php
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

include_once 'db_connection.php'; 

$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data['product_id'])) {
    $id = intval($data['product_id']);

    // 🛡️ CYBERSECURITY: Using Prepared Statements to prevent SQL Injection
    $query = "UPDATE products SET isActive = 0 WHERE product_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Asset decommissioned."]);
    } else {
        echo json_encode(["success" => false, "message" => "Database error."]);
    }
    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid ID packet."]);
}
$conn->close();
?>