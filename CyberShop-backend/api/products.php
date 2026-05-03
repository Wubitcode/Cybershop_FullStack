<?php
/**
 * 1. CORS HEADERS 
 * These must be sent BEFORE any other output.
 */
ob_start(); // Buffer output to prevent "headers already sent" errors

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

/**
 * 2. HANDLE PREFLIGHT (OPTIONS)
 * Angular sends an OPTIONS request before POST/PUT/DELETE. 
 * We must return a 200 OK and exit immediately.
 */
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

/**
 * 3. DATABASE CONNECTION
 */
$host = "localhost";
$db_name = "cybershop";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $db_name);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

/**
 * 4. ROUTING LOGIC
 */
$method = $_SERVER['REQUEST_METHOD'];
$inputData = json_decode(file_get_contents("php://input"), true) ?? [];

switch ($method) {
    case 'GET':
        handleGet($conn);
        break;
    case 'POST':
        handlePost($conn, $inputData);
        break;
    case 'DELETE':
        handleDelete($conn);
        break;
    default:
        http_response_code(405);
        echo json_encode(["message" => "Method Not Allowed"]);
        break;
}

/**
 * FUNCTIONS
 */

function handleGet($conn) {
    // If an ID is passed in the URL (?id=5), get one product, else get all
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;
    
    if ($id) {
        $sql = "SELECT * FROM products WHERE product_id = ? AND isActive = 1 LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
    } else {
        $sql = "SELECT * FROM products WHERE isActive = 1 ORDER BY product_id DESC";
        $stmt = $conn->prepare($sql);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $products = [];

    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    echo json_encode($products);
}

function handlePost($conn, $data) {
    // Determine if we are Creating or Updating
    $action = $data['action'] ?? 'create';

    if ($action === 'create') {
        $sql = "INSERT INTO products (sku, name, category, image, price, description, stock, low_stock_threshold, isActive) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssdsii", 
            $data['sku'], $data['name'], $data['category'], $data['image'], 
            $data['price'], $data['description'], $data['stock'], $data['low_stock_threshold']
        );
    } else {
        // Update existing
        $sql = "UPDATE products SET sku=?, name=?, category=?, image=?, price=?, description=?, stock=?, low_stock_threshold=? 
                WHERE product_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssdsiii", 
            $data['sku'], $data['name'], $data['category'], $data['image'], 
            $data['price'], $data['description'], $data['stock'], $data['low_stock_threshold'], $data['product_id']
        );
    }

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Product saved successfully"]);
    } else {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => $stmt->error]);
    }
}

function handleDelete($conn) {
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;
    if (!$id) {
        echo json_encode(["success" => false, "message" => "No ID provided"]);
        return;
    }

    // Soft delete by setting isActive to 0
    $stmt = $conn->prepare("UPDATE products SET isActive = 0 WHERE product_id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => $stmt->error]);
    }
}

$conn->close();