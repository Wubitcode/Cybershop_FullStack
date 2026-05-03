<?php
declare(strict_types=1);

// 1. Load System Requirements
require_once __DIR__ . '/../../app/config.php';
require_once __DIR__ . '/../../app/db.php';
require_once __DIR__ . '/../../app/auth.php';
require_once __DIR__ . '/../../app/repos/products_repo.php';

require_admin();

// 2. Change INPUT_POST to INPUT_GET
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;

if ($id > 0) {
    try {
        // 3. Execute Deletion
        product_delete($id);
        
        // 4. Redirect with a success flag
        header("Location: " . BASE_URL . "/admin/products/index.php?status=erased");
        exit;
    } catch (Throwable $e) {
        // Log the error if the database blocks the delete (e.g., product is in an order)
        error_log("DECONSTRUCTION_ERROR: " . $e->getMessage());
        header("Location: " . BASE_URL . "/admin/products/index.php?error=db_constraint");
        exit;
    }
}

// If no ID, just go back
header("Location: " . BASE_URL . "/admin/products/index.php");
exit;