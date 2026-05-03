<?php
declare(strict_types=1);

require_once __DIR__ . '/../../app/config.php';
require_once __DIR__ . '/../../app/db.php'; 
require_once __DIR__ . '/../../app/auth.php';
require_once __DIR__ . '/../../app/helpers.php';
require_once __DIR__ . '/../../app/repos/products_repo.php';

require_admin();

$error = null;

// Your original Logic - Integrated
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sku = trim($_POST['sku'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $imageUrl = trim($_POST['imageUrl'] ?? 'placeholder.png');
    $isActive = isset($_POST['isActive']) ? 1 : 0;

    if ($sku === '' || $name === '' || $price <= 0) {
        $error = "CRITICAL: SKU, name, and price are required for database entry.";
    } else {
        try {
            product_create([
                'sku' => $sku,
                'name' => $name,
                'description' => $description,
                'price' => $price,
                'stock' => $stock,
                'image' => $imageUrl,
                'isActive' => $isActive
            ]);
            header("Location: index.php?success=created");
            exit;
        } catch (Throwable $e) {
            $error = "SYSTEM_ERROR: " . $e->getMessage();
        }
    }
}

$title = "Asset Registration | CyberShop";
$user = current_user();

include __DIR__ . '/../../app/partials/header.php';
?>

<!-- Professional Layout Reset -->
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
                <h4 class="fw-bold mb-0 text-white">PROTOCOL: REGISTER_NEW_ASSET</h4>
                <small class="text-info">Authorized Operator: <?= e($user['fullName']) ?></small>
            </div>
            <a href="index.php" class="btn btn-dark btn-sm border-secondary text-dim">
                <i class="bi bi-arrow-left"></i> BACK_TO_LIST
            </a>
        </header>

        <?php if ($error): ?>
            <div class="alert alert-danger bg-danger bg-opacity-10 border-danger text-danger py-2 small mb-4">
                <i class="bi bi-exclamation-triangle-fill"></i> <?= e($error) ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-9">
                <div class="cyber-card">
                    <form method="post">
                        <div class="row g-3">
                            <!-- SKU and Name -->
                            <div class="col-md-4">
                                <label class="stat-label mb-2">Internal_SKU</label>
                                <input type="text" name="sku" class="form-control cyber-input" placeholder="e.g. CYB-101" required value="<?= e($_POST['sku'] ?? '') ?>">
                            </div>
                            <div class="col-md-8">
                                <label class="stat-label mb-2">Asset_Name</label>
                                <input type="text" name="name" class="form-control cyber-input" placeholder="e.g. Flipper Zero" required value="<?= e($_POST['name'] ?? '') ?>">
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <label class="stat-label mb-2">Technical_Description</label>
                                <textarea name="description" class="form-control cyber-input" rows="4" placeholder="Enter specifications..."><?= e($_POST['description'] ?? '') ?></textarea>
                            </div>

                            <!-- Price and Stock -->
                            <div class="col-md-4">
                                <label class="stat-label mb-2">Valuation ($)</label>
                                <input type="number" step="0.01" name="price" class="form-control cyber-input" required value="<?= e($_POST['price'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="stat-label mb-2">Initial_Stock</label>
                                <input type="number" name="stock" class="form-control cyber-input" value="<?= e($_POST['stock'] ?? '0') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="stat-label mb-2">Image_Pointer</label>
                                <input type="text" name="imageUrl" class="form-control cyber-input" placeholder="filename.png" value="<?= e($_POST['imageUrl'] ?? '') ?>">
                            </div>

                            <!-- Visibility Switch -->
                            <div class="col-12 mt-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="isActive" id="isActive" checked>
                                    <label class="form-check-label text-dim small" for="isActive">Broadcast to Public Site Immediately</label>
                                </div>
                            </div>

                            <!-- Submit -->
                            <div class="col-12 mt-4 pt-3 border-top border-secondary border-opacity-25">
                                <button type="submit" class="btn btn-cyan fw-bold px-5">
                                    <i class="bi bi-shield-check"></i> COMMIT_ASSET
                                </button>
                                <a href="index.php" class="btn btn-link text-dim text-decoration-none ms-3">Cancel_Abort</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Side Sidebar for Tips -->
            <div class="col-lg-3">
                <div class="cyber-card">
                    <h6 class="text-info fw-bold small mb-3">INTEGRITY_CHECK</h6>
                    <ul class="list-unstyled mb-0" style="font-size: 0.75rem;">
                        <li class="mb-2 text-dim"><i class="bi bi-check2 text-success"></i> SKU must be unique.</li>
                        <li class="mb-2 text-dim"><i class="bi bi-check2 text-success"></i> Valuation must be > 0.</li>
                        <li class="text-dim"><i class="bi bi-check2 text-success"></i> Images must exist in <code>/assets/images/</code>.</li>
                    </ul>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include __DIR__ . '/../../app/partials/footer.php'; ?>