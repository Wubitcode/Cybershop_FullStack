<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>PHP is working!</h1>";

$host = "localhost";
$user = "root";
$pass = ""; // Try changing to "root" if empty fails
$db = "cybershop";

echo "Attempting to connect to database...<br>";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo "<b style='color:red;'>Connection Failed:</b> " . $conn->connect_error;
} else {
    echo "<b style='color:green;'>Connection Successful!</b><br>";
    
    $result = $conn->query("SELECT COUNT(*) as total FROM products");
    $row = $result->fetch_assoc();
    echo "Total products in database: " . $row['total'];
}
?>