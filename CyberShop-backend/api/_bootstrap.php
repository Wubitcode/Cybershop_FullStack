<?php

// Allow Angular to access the API
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE, PUT");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// Handle preflight 'OPTIONS' requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit;
}

require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/db.php';
declare(strict_types=1);
require_once __DIR__ . '/_bootstrap.php';

$data = read_json_body();
$fullName = trim($data['fullName'] ?? '');
$email = trim($data['email'] ?? '');
$password = (string)($data['password'] ?? '');

if ($fullName === '' || $email === '' || $password === '') {
    json_response(['ok'=>false,'error'=>'Missing fields'], 400);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_response(['ok'=>false,'error'=>'Invalid email'], 400);
}
if (strlen($password) < 6) {
    json_response(['ok'=>false,'error'=>'Password too short'], 400);
}
if (user_find_by_email($email)) {
    json_response(['ok'=>false,'error'=>'Email exists'], 409);
}

$id = user_create($fullName, $email, $password, 'user');
$u = user_find($id);
login_user($u);

json_response(['ok'=>true,'user'=>current_user()]);
?>