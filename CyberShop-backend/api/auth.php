<?php
// ===============================
// BASIC SETUP (IMPORTANT)
// ===============================
header("Content-Type: application/json");

// CORS (fixes Angular errors)
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // Added GET
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight request (VERY IMPORTANT for Angular)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ===============================
// DATABASE CONNECTION
// ===============================
$conn = new mysqli("localhost", "root", "", "cybershop");

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Database connection failed"
    ]);
    exit();
}

// ===============================
// READ INPUT
// ===============================
$action = $_GET['action'] ?? '';
$data = json_decode(file_get_contents("php://input"), true);

// ===============================
// REGISTER
// ===============================
if ($action === "register") {

    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $password = trim($data['password'] ?? '');

    if ($name === '' || $email === '' || $password === '') {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => "All fields are required"
        ]);
        exit();
    }

    // check if user exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        http_response_code(409);
        echo json_encode([
            "status" => "error",
            "message" => "User already exists"
        ]);
        exit();
    }

    // hash password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // default role = user
    $role = "user";

    $stmt = $conn->prepare("
        INSERT INTO users (name, email, password, role)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->bind_param("ssss", $name, $email, $hashedPassword, $role);

    if ($stmt->execute()) {
        echo json_encode([
            "status" => "success",
            "message" => "Account created successfully"
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Registration failed"
        ]);
    }
}

// ===============================
// LOGIN
// ===============================
elseif ($action === "login") {

    $email = trim($data['email'] ?? '');
    $password = trim($data['password'] ?? '');

    if ($email === '' || $password === '') {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => "Email and password required"
        ]);
        exit();
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        http_response_code(401);
        echo json_encode([
            "status" => "error",
            "message" => "User not found"
        ]);
        exit();
    }

    if (password_verify($password, $user['password'])) {

        // remove password before sending to frontend
        unset($user['password']);

        echo json_encode([
            "status" => "success",
            "message" => "Login successful",
            "user" => $user
        ]);

    } else {
        http_response_code(401);
        echo json_encode([
            "status" => "error",
            "message" => "Invalid password"
        ]);
    }
}

// ===============================
// INVALID ACTION
// ===============================
else {
    http_response_code(404);
    echo json_encode([
        "status" => "error",
        "message" => "Invalid action"
    ]);
}