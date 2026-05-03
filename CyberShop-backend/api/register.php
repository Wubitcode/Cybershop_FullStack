<?php
declare(strict_types=1);
require_once __DIR__ . '/_bootstrap.php';

$data = read_json_body();

$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$password = (string)($data['password'] ?? '');

// ================= VALIDATION =================
if ($name === '' || $email === '' || $password === '') {
    json_response(['ok' => false, 'error' => 'Missing fields'], 400);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_response(['ok' => false, 'error' => 'Invalid email'], 400);
}

if (strlen($password) < 6) {
    json_response(['ok' => false, 'error' => 'Password too short'], 400);
}

// ================= DUPLICATE CHECK =================
if (user_find_by_email($email)) {
    json_response(['ok' => false, 'error' => 'Email exists'], 409);
}

// ================= CREATE USER =================
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$id = user_create($name, $email, $hashedPassword, 'user');

// fetch user
$u = user_find($id);

// auto login
login_user($u);

json_response([
    'ok' => true,
    'user' => current_user()
]);