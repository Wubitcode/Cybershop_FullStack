<?php
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/auth.php';
require_admin();

$title = "Inject Asset | CyberShop";
include __DIR__ . '/../app/partials/header.php';
?>

<div class="admin-wrapper" style="display: flex;">
    <?php include __DIR__ . '/../app/partials/admin_sidebar.php'; ?>

    <main class="main-terminal" style="flex-grow: 1; padding: 2rem;">
        <header class="mb-4">
            <h6 class="text-info small fw-bold mb-1">SYSTEM_INVENTORY > UPLOAD_PROTOCOL</h6>
            <h4 class="fw-bold text-white"><i class="bi bi-cpu me-2"></i>ADD_NEW_ASSET</h4>
        </header>

        <div class="cyber-card col-lg-8">
            <form action="products_store.php" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="config-label d-block mb-2">ASSET_NAME</label>
                        <input type="text" name="name" class="form-control form-control-cyber" placeholder="Neural Link v2.1" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="config-label d-block mb-2">CREDITS_PRICE</label>
                        <input type="number" name="price" step="0.01" class="form-control form-control-cyber" placeholder="0.00" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="config-label d-block mb-2">TECHNICAL_SPECS</label>
                    <textarea name="description" class="form-control form-control-cyber" rows="3" placeholder="Enter hardware details..."></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="config-label d-block mb-2">QUANTITY_IN_STOCK</label>
                        <input type="number" name="stock" class="form-control form-control-cyber" value="1" required>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="config-label d-block mb-2">VISUAL_UPLINK (IMAGE)</label>
                        <input type="file" name="image" class="form-control form-control-cyber">
                    </div>
                </div>

                <div class="border-top border-secondary border-opacity-10 pt-4">
                    <button type="submit" class="btn btn-info fw-bold px-4">COMMIT_TO_DATABASE</button>
                    <a href="index.php" class="btn btn-outline-secondary btn-sm ms-3">ABORT</a>
                </div>
            </form>
        </div>
    </main>
</div>

<?php include __DIR__ . '/../app/partials/footer.php'; ?>