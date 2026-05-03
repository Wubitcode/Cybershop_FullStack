<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/auth.php';

// Security check: Only admins can trigger this script
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $description = trim($_POST['description'] ?? '');

    // 1. Basic Image Handling (We'll use a placeholder if empty)
    $image_url = 'default.jpg';
    if (!empty($_FILES['image']['name'])) {
        $image_url = time() . '_' . $_FILES['image']['name'];
        // Ensure the folder 'assets/images/products/' exists!
        move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . "/../assets/images/products/" . $image_url);
    }

    try {
        // 2. THE REAL DEAL: Prepared Statement to block SQL Injection
        $pdo = db();
        $sql = "INSERT INTO products (name, description, price, stock, image_url) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $description, $price, $stock, $image_url]);
     
        // Inside your products_store.php after the execute()
          $action = "ASSET_CREATED";
          $details = "Product: " . $name;
           $log_stmt = db()->prepare("INSERT INTO system_logs (user_id, action, ip_address) VALUES (?, ?, ?)");
           $log_stmt->execute([$_SESSION['user']['user_id'], $action, $_SERVER['REMOTE_ADDR']]);
        // 3. SUCCESS REDIRECT
        header("Location: index.php?success=added");
        exit;

    } catch (Exception $e) {
        // Log the error and alert the operator
        die("CRITICAL_DATABASE_FAILURE: " . $e->getMessage());
    }
} else {
    // If someone tries to access this file directly without POSTing data
    header("Location: products_add.php");
    exit;
}