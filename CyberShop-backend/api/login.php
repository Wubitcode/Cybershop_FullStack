
<?php
// ================================
// 1. DEBUG + SECURITY HEADERS
// ================================
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Allow Angular frontend
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

// Handle preflight request (CORS fix)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ================================
// 2. LOAD DEPENDENCIES
// ================================
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/auth.php';

// ================================
// 3. CONNECT DATABASE
// ================================
try {
    $pdo = db();

    // Read JSON input from Angular
    $data = json_decode(file_get_contents("php://input"));

    // ================================
    // 4. VALIDATE INPUT
    // ================================
    if (!$data || empty($data->email) || empty($data->password)) {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => "EMAIL_OR_PASSWORD_MISSING"
        ]);
        exit();
    }

    // ================================
    // 5. FIND USER IN DATABASE
    // ================================
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$data->email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // ================================
    // 6. VERIFY PASSWORD
    // ================================
    if ($user && password_verify($data->password, $user['password'])) {

        // ================================
        // 7. SUCCESS RESPONSE (IMPORTANT FIX)
        // ================================
        echo json_encode([
            "status" => "success",
            "message" => "LOGIN_SUCCESS",
            "user" => [
                "id" => $user['id'],
                "name" => $user['name'],
                "email" => $user['email'],
                "role" => $user['role']  // 🔥 CRITICAL FOR ROUTING
            ]
        ]);

    } else {

        // ================================
        // 8. INVALID LOGIN
        // ================================
        http_response_code(401);
        echo json_encode([
            "status" => "error",
            "message" => "INVALID_CREDENTIALS"
        ]);
    }

} catch (Exception $e) {

    // ================================
    // 9. SERVER ERROR
    // ================================
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "SERVER_ERROR",
        "debug" => $e->getMessage()
    ]);
}