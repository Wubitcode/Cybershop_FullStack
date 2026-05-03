<?php
declare(strict_types=1);
require_once __DIR__ . '/../../app/config.php';
require_once __DIR__ . '/../../app/db.php';
require_once __DIR__ . '/../../app/auth.php';
require_once __DIR__ . '/../../app/repos/products_repo.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id    = (int)($_POST['product_id'] ?? 0);
    $name  = $_POST['name'] ?? '';
    $price = (float)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);

    // This calls the function you just added to the repo!
    if ($id > 0 && product_update($id, $name, $price, $stock)) {
        header("Location: " . BASE_URL . "/admin/products/index.php?updated=1");
        exit;
    }
}

die("CRITICAL_ERROR: DATA_NOT_WRITTEN_TO_DATABASE");