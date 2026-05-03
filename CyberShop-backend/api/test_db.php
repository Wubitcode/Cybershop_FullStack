<?php
// Force PHP to show errors on the screen
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
// 1. Include your database connection function
require_once __DIR__ . '/../app/db.php';
try {
    // 2. Try to connect
    $conn = db(); 
    
    // 3. If it gets here, it worked!
    echo "<h1 style='color: green;'>✔ Connection Successful!</h1>";
    echo "<p>Your PHP is now talking to the MySQL database.</p>";
    
} catch (Exception $e) {
    // 4. If there is an error, show it
    echo "<h1 style='color: red;'>✘ Connection Failed</h1>";
    echo "Error message: " . $e->getMessage();
}
?>