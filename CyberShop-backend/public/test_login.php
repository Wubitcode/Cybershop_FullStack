<?php
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/repos/users_repo.php';

$email_to_test = 'admin@cybershop.com';
$password_to_test = 'admin123'; // Change this to what you are typing

echo "<h2>Login Debugger</h2>";

// 1. Test Database Connection
try {
    $database = db();
    echo "✅ Database Connected.<br>";
} catch (Exception $e) {
    die("❌ Database Connection Failed: " . $e->getMessage());
}

// 2. Test User Fetching
$user = user_find_by_email($email_to_test);

if (!$user) {
    echo "❌ User '$email_to_test' NOT found in database.<br>";
    echo "<i>Check if the email in phpMyAdmin is exactly '$email_to_test' (no spaces).</i>";
} else {
    echo "✅ User found in database.<br>";
    echo "Full Name: " . ($user['name'] ?? 'NULL') . "<br>";
    echo "Role: " . ($user['role'] ?? 'NULL') . "<br>";
    
    // 3. Test Password Hash
    $hash_in_db = $user['password'] ?? 'NOT_FOUND';
    echo "Hash in DB: <code>$hash_in_db</code><br>";

    if (password_verify($password_to_test, $hash_in_db)) {
        echo "<h3 style='color:green'>✅ SUCCESS: Password matches!</h3>";
        echo "Your login.php should be working. Clear your browser cookies and try again.";
    } else {
        echo "<h3 style='color:red'>❌ FAILURE: Password does NOT match the hash.</h3>";
        echo "The password you are typing does not match the encrypted string in the DB.";
    }
}