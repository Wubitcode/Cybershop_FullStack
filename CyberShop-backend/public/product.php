<?php
declare(strict_types=1);

// 1. Debugging & Dependencies
ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/helpers.php';
require_once __DIR__ . '/../app/repos/products_repo.php';

// 2. Fetch Product Data
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
$product = $id ? product_find($id) : null;

// FIX: Checking 'is_active' (common DB naming) or removing the check if not needed
if (!$product) {
    header("Location: index.php");
    exit;
}

// 3. Image Logic - Adjusted to look in your 'uploads' folder where images usually live
$imageName = trim((string)($product['image'] ?? ''));
if ($imageName === '') {
    $imagePath = BASE_URL . '/assets/images/placeholder.png'; 
} else {
    // Ensure this matches your actual folder path (e.g., /public/uploads/)
    $imagePath = BASE_URL . '/public/uploads/' . $imageName;
}

$title = $product['name'] . " | Asset Inspection";
include __DIR__ . '/../app/partials/header.php';
?>

<style>
    body { background-color: #05070a !important; color: #e6edf3 !important; }
    
    /* FIX: Landscape Prevention - Wrap everything in a max-width container */
    .inspection-wrapper {
        max-width: 1100px;
        margin: 0 auto;
    }

    .inspection-card {
        background: #0d1117;
        border: 1px solid rgba(0, 210, 255, 0.2);
        border-radius: 12px;
        padding: 40px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }

    /* FIX: Image Size Control */
    .img-frame {
        border: 1px solid rgba(0, 210, 255, 0.2);
        border-radius: 8px;
        background: #000;
        overflow: hidden;
        height: 450px; /* Fixed height to prevent landscape stretching */
    }

    .img-frame img {
        width: 100%;
        height: 100%;
        object-fit: cover; /* Keeps image from distorting */
    }

    .asset-id-tag { font-family: 'Consolas', monospace; color: #00d2ff; font-size: 0.8rem; letter-spacing: 2px; }
    .price-display { font-family: 'Consolas', monospace; color: #00d2ff; font-size: 2.5rem; font-weight: bold; }
    .full-description { color: #a9b2bb; line-height: 1.8; font-size: 1.05rem; }
    .form-control-cyber { background: #05070a; border: 1px solid #30363d; color: #fff; }
    .btn-cyan { background-color: #00d2ff; color: #000; border: none; }
    .btn-cyan:hover { background-color: #00b4db; color: #000; box-shadow: 0 0 15px rgba(0, 210, 255, 0.4); }
</style>

<div class="container py-5">
    <div class="inspection-wrapper">
        <nav class="mb-4">
            <h6 class="text-info small fw-bold" style="letter-spacing: 1.5px;">
                SECURE_STORE > ONTARIO_NODE > ASSET_INSPECTION
            </h6>
        </nav>

        <div class="inspection-card">
            <div class="row g-5">
                <div class="col-md-5">
                    <div class="img-frame">
                        <img src="<?= e($imagePath) ?>" alt="<?= e($product['name']) ?>">
                    </div>
                </div>

                <div class="col-md-7">
                    <div class="asset-id-tag mb-2">SKU_VERIFICATION: <?= e($product['sku'] ?? '0x'.strtoupper(dechex($id))) ?></div>
                    <h1 class="display-6 fw-bold text-white mb-3"><?= e($product['name']) ?></h1>
                    
                    <div class="price-display mb-4">
                        <?= money((float)$product['price']) ?>
                    </div>

                    <div class="mb-5">
                        <h6 class="text-white text-uppercase small fw-bold border-bottom border-secondary pb-2 mb-3">Technical Description</h6>
                        <p class="full-description">
                            <?= nl2br(e((string)($product['description'] ?? 'No technical data provided.'))) ?>
                        </p>
                    </div>

                    <!-- 🛠️ FIX: Method and Variable Names -->
                    <!-- Changed action to use GET parameters to match your cart_add.php -->
                    <form method="GET" action="<?= BASE_URL ?>/public/cart_add.php">
                        <!-- 'id' matches $productId = (int)$_GET['id'] -->
                        <input type="hidden" name="id" value="<?= (int)$id ?>">

                        <div class="row g-2 align-items-end">
                            <div class="col-3">
                                <label class="form-label small text-muted font-monospace">QUANTITY</label>
                                <input type="number" min="1" max="99" name="qty" class="form-control form-control-cyber" value="1">
                            </div>
                            <div class="col-9">
                                <button class="btn btn-cyan w-100 py-3 fw-bold" type="submit">
                                    <i class="bi bi-cart-plus me-2"></i> AUTHORIZE_ACQUISITION
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="mt-4">
                        <a class="btn btn-outline-secondary btn-sm w-100 border-0" href="<?= BASE_URL ?>/public/index.php">
                            <i class="bi bi-arrow-left me-2"></i> RETURN_TO_REPOSITORY
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../app/partials/footer.php'; ?>