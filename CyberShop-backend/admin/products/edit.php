<?php
declare(strict_types=1);

require_once __DIR__ . '/../../app/config.php';
require_once __DIR__ . '/../../app/auth.php';
require_once __DIR__ . '/../../app/helpers.php';
require_once __DIR__ . '/../../app/repos/products_repo.php';

require_admin();

// 1. Validate ID and Fetch Asset
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
$product = $id ? product_find($id) : null;

if (!$product) {
    header("Location: index.php?error=not_found");
    exit;
}

$error = null;
$user = current_user();

// 2. Handle Patch Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sku = trim($_POST['sku'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $imageUrl = trim($_POST['imageUrl'] ?? 'placeholder.png');
    $isActive = isset($_POST['isActive']) ? 1 : 0;

    if ($sku === '' || $name === '' || $price <= 0) {
        $error = "VALIDATION_FAILURE: SKU, Name, and Price are required.";
    } else {
        try {
            product_update($id, [
                'sku' => $sku,
                'name' => $name,
                'description' => $description,
                'price' => $price,
                'stock' => $stock,
                'image' => $imageUrl, // Fixed the variable name here
                'isActive' => $isActive
            ]);
            header("Location: index.php?success=updated");
            exit;
        } catch (Throwable $e) {
            $error = "KERNEL_ERROR: " . $e->getMessage();
        }
    }
}

$title = "Edit Asset | CyberShop";
include __DIR__ . '/../../app/partials/header.php';
?>

<style>
    body, html { margin: 0 !important; padding: 0 !important; background-color: #000 !important; }
    .cyber-input {
        background: rgba(255,255,255,0.05) !important;
        border: 1px solid rgba(255,255,255,0.1) !important;
        color: #fff !important;
    }
    .cyber-input:focus {
        border-color: var(--accent-cyan) !important;
        box-shadow: 0 0 10px rgba(0, 210, 255, 0.2);
    }
</style>

<link rel="stylesheet" href="/cybershop/assets/css/admin-theme.css">

<div class="admin-wrapper">
    <?php include __DIR__ . '/../../app/partials/admin_sidebar.php'; ?>

    <main class="main-terminal">
        <header class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0 text-white">PATCH_PROTOCOL: ASSET_<?= $id ?></h4>
                <small class="text-cyan"><i class="bi bi-pencil-square"></i> Modifying: <?= e($product['name']) ?></small>
            </div>
            <a href="index.php" class="btn btn-dark btn-sm border-secondary text-dim">
                <i class="bi bi-x-lg"></i> ABORT_EXIT
            </a>
        </header>

        <?php if ($error): ?>
            <div class="alert alert-danger bg-danger bg-opacity-10 border-danger text-danger py-2 small mb-4">
                <i class="bi bi-shield-slash"></i> <?= e($error) ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-9">
                <div class="cyber-card">
                    <form method="post">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="stat-label mb-2">Internal_SKU</label>
                                <input class="form-control cyber-input" name="sku" required maxlength="30" value="<?= e($product['sku']) ?>">
                            </div>
                            <div class="col-md-8">
                                <label class="stat-label mb-2">Asset_Name</label>
                                <input class="form-control cyber-input" name="name" required maxlength="255" value="<?= e($product['name']) ?>">
                            </div>

                            <div class="col-12">
                                <label class="stat-label mb-2">Technical_Description</label>
                                <textarea class="form-control cyber-input" name="description" rows="4"><?= e((string)($product['description'] ?? '')) ?></textarea>
                            </div>

                            <div class="col-md-4">
                                <label class="stat-label mb-2">Valuation ($)</label>
                                <input class="form-control cyber-input" type="number" step="0.01" name="price" required value="<?= e((string)$product['price']) ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="stat-label mb-2">Stock_Level</label>
                                <input class="form-control cyber-input" type="number" name="stock" value="<?= (int)$product['stock'] ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="stat-label mb-2">Image_Pointer</label>
                                <input class="form-control cyber-input" name="imageUrl" value="<?= e((string)($product['image'] ?? '')) ?>">
                            </div>

                            <div class="col-12 mt-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="isActive" id="isActive" <?= ((int)$product['isActive'] === 1) ? 'checked' : '' ?>>
                                    <label class="form-check-label text-dim small" for="isActive">Keep node active in public repository</label>
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-top border-secondary border-opacity-25">
                                <button class="btn btn-cyan fw-bold px-5" type="submit">
                                    <i class="bi bi-cpu"></i> EXECUTE_PATCH
                                </button>
                                <a class="btn btn-link text-dim text-decoration-none ms-3" href="index.php">Return_Without_Saving</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="cyber-card text-center">
                    <h6 class="stat-label mb-3">CURRENT_VISUAL</h6>
                    <img src="/cybershop/assets/images/<?= e($product['image'] ?? 'placeholder.png') ?>" class="img-fluid rounded border border-secondary mb-3 shadow" alt="Preview">
                    <div class="text-dim x-small">LAST_MODIFIED: <?= date('Y-m-d H:i') ?></div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include __DIR__ . '/../../app/partials/footer.php'; ?>