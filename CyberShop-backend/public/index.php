<?php
declare(strict_types=1);

// 1. Debugging & Error Reporting
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// 2. Load System Dependencies
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/helpers.php';
require_once __DIR__ . '/../app/repos/products_repo.php';

$title = "CyberShop | Hardware Repository";

/**
 * 3. Data Acquisition
 * Uses products_all() which filters by isActive = 1
 */
$products = products_all();

include __DIR__ . '/../app/partials/header.php';
?>

<style>
    :root { 
        --accent-cyan: #00d2ff; 
        --bg-dark: #05070a; 
        --card-bg: #0d1117; 
        --border-color: rgba(255, 255, 255, 0.1);
    }
    
    body { background-color: var(--bg-dark) !important; color: #e6edf3 !important; }
    
    .text-glow { color: #fff; text-shadow: 0 0 10px rgba(0, 210, 255, 0.4); }
    
    .asset-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        transition: all 0.3s ease;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .asset-card:hover {
        border-color: var(--accent-cyan);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        transform: translateY(-5px);
    }

    .price-tag { 
        background: rgba(0, 210, 255, 0.1); 
        color: var(--accent-cyan); 
        border: 1px solid rgba(0, 210, 255, 0.2);
        font-family: 'Consolas', monospace;
        font-weight: 700;
        padding: 0.5rem 1rem;
    }

    .btn-view {
        border: 1px solid #30363d;
        color: #8b949e;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .btn-view:hover {
        background: rgba(255, 255, 255, 0.05);
        color: #fff;
    }

    .btn-add-cart {
        background-color: var(--accent-cyan);
        color: #000;
        font-weight: 700;
        border: none;
        text-transform: uppercase;
        font-size: 0.75rem;
    }

    .btn-add-cart:hover {
        background-color: #00b4db;
        box-shadow: 0 0 15px rgba(0, 210, 255, 0.3);
    }

    .img-wrapper {
        height: 220px;
        background: #000;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .asset-description {
        color: #a9b2bb !important;
        font-size: 0.85rem;
        line-height: 1.5;
        margin-bottom: 1.5rem;
        height: 65px;
        overflow: hidden;
    }

    .asset-card:hover .asset-description {
        color: #fff !important;
        transition: color 0.3s ease;
    }

    .img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.9;
    }
</style>

<div class="container py-5">
    <div class="d-flex align-items-center justify-content-between mb-5 border-bottom border-secondary border-opacity-25 pb-4">
        <div>
            <h6 class="text-info small fw-bold mb-1" style="letter-spacing: 1.5px; font-size: 0.7rem;">
                SECURE_STORE > ONTARIO_NODE
            </h6>
            <h1 class="h2 mb-0 text-white text-glow fw-bold font-monospace">
                CyberShop Assets
            </h1>
        </div>

        <a class="btn btn-outline-info btn-sm px-4 fw-bold" href="<?= BASE_URL ?>/public/cart.php">
            <i class="bi bi-cart3 me-2"></i> SHOPPING CART
        </a>
    </div>

    <?php if (empty($products)): ?>
        <div class="alert bg-dark border border-secondary text-muted py-5 text-center shadow-sm">
            <i class="bi bi-cpu display-4 mb-3 d-block"></i>
            <p class="mb-0">No active security assets are currently listed in the inventory.</p>
        </div>
    <?php else: ?>

    <div class="row g-4">
        <?php foreach ($products as $p): ?>
            <?php
            $imageName = trim((string)($p['image'] ?? ''));
            // Use the image if it exists, otherwise use a fallback
            if ($imageName === '' || !file_exists(__DIR__ . '/../assets/images/' . $imageName)) {
                $imagePath = BASE_URL . '/assets/images/placeholder.png'; // Make sure this exists or use a generic one
            } else {
                $imagePath = BASE_URL . '/assets/images/' . $imageName;
            }
            ?>

            <div class="col-12 col-md-6 col-lg-4">
                <div class="card asset-card h-100 shadow-lg">
                    <div class="img-wrapper">
                        <img src="<?= e($imagePath) ?>" alt="<?= e($p['name']) ?>" loading="lazy">
                    </div>

                    <div class="card-body d-flex flex-column p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h2 class="h6 mb-0 fw-bold text-white"><?= e($p['name']) ?></h2>
                            <span class="badge price-tag">
                                <?= money((float)$p['price']) ?>
                            </span>
                        </div>

                        <p class="asset-description">
                          <?php
                          $desc = (string)($p['description'] ?? '');
                          echo e(strlen($desc) > 90 ? substr($desc, 0, 90) . '...' : $desc);
                          ?>
                        </p>

                        <div class="mt-auto d-flex gap-2">
                            <a class="btn btn-view w-100 py-2" href="<?= BASE_URL ?>/public/product.php?id=<?= (int)$p['product_id'] ?>">
                                <i class="bi bi-search me-1"></i> VIEW
                            </a>
     
                            <!-- UPDATED ACTION TO add_to_cart.php -->
                            <form method="post" action="<?= BASE_URL ?>/public/add_to_cart.php" class="w-100">
                                <input type="hidden" name="productId" value="<?= (int)$p['product_id'] ?>">
                                <input type="hidden" name="qty" value="1">
                                <button class="btn btn-add-cart w-100 py-2" type="submit">
                                    <i class="bi bi-cart-plus me-1"></i> ADD TO CART
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../app/partials/footer.php'; ?>