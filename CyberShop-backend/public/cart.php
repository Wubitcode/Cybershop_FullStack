<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/helpers.php';
require_once __DIR__ . '/../app/repos/products_repo.php';

// session_start() is typically handled in auth.php
$cart = $_SESSION['cart'] ?? [];
$items = [];
$subtotal = 0.0;

foreach ($cart as $pid => $qty) {
    $p = product_find((int)$pid);
    if (!$p) continue;

    $line = ((float)$p['price']) * ((int)$qty);
    $subtotal += $line;

    $imageName = trim((string)($p['image'] ?? ''));
    $image = ($imageName === '') 
        ? BASE_URL . '/assets/images/cyberShoplogo.png' 
        : BASE_URL . '/assets/images/' . ltrim($imageName, '/');

    $items[] = ['p' => $p, 'qty' => (int)$qty, 'line' => $line, 'image' => $image];
}

$title = "Procurement Manifest | CyberShop";
include __DIR__ . '/../app/partials/header.php';
?>

<style>
    /* 1. Global Visibility Fixes */
    body { 
        background-color: #05070a !important; 
        color: #e6edf3 !important; 
    }
    
    h1, h2, h3, h4, h5, h6 { 
        color: #ffffff !important; 
    }

    .text-glow { 
        text-shadow: 0 0 15px rgba(0, 210, 255, 0.6); 
    }

    .text-cyan {
        color: #00d2ff !important;
    }

    /* 2. Table & Card Styling */
    .manifest-card { 
        background: #0d1117; 
        border: 1px solid rgba(255, 255, 255, 0.1); 
        border-radius: 12px; 
    }

    .table { 
        color: #e6edf3 !important; 
        border-color: rgba(255, 255, 255, 0.05);
    }

    .table-dark-custom thead { 
        background: rgba(0, 210, 255, 0.1); 
        color: #00d2ff !important; 
        font-family: 'Consolas', monospace; 
    }

    /* 3. Summary Box (Footer Area) */
    .cart-summary-box {
        background: #0d1117; 
        border: 2px solid #00d2ff; 
        border-radius: 12px;
        padding: 2rem;
        margin-top: 2rem;
    }

    .total-amount {
        color: #00d2ff;
        text-shadow: 0 0 10px rgba(0, 210, 255, 0.8);
        font-family: 'Consolas', monospace;
    }

    /* 4. Buttons */
    .btn-checkout { 
        background: #00d2ff; 
        color: #000 !important; 
        font-weight: 800; 
        border: none;
    }

    .btn-checkout:hover { 
        background: #00b4db; 
        box-shadow: 0 0 20px rgba(0, 210, 255, 0.6); 
    }

    /* 5. Page Footer Visibility Fix */
    footer, .footer-text {
        color: #ffffff !important;
        opacity: 0.8;
    }
</style>

<div class="container py-5">
    <!-- Header Section -->
    <div class="mb-5 border-bottom border-secondary border-opacity-25 pb-4">
        <h6 class="text-info small fw-bold" style="letter-spacing: 2px;">SECURE_STORE > PROCURE_MANIFEST</h6>
        <h1 class="h2 text-white fw-bold text-glow">
            <i class="bi bi-shield-lock me-2 text-info"></i>Selected Assets
        </h1>
    </div>

    <?php if (count($items) === 0): ?>
      <div class="manifest-card p-5 text-center border-secondary shadow-lg">
        <i class="bi bi-cpu text-info display-1 mb-4"></i>
        <h4 class="text-white">Manifest Empty: No assets flagged.</h4>
        <a href="index.php" class="btn btn-outline-info mt-3 px-4 fw-bold">RETURN TO REPOSITORY</a>
      </div>
    <?php else: ?>
      
      <!-- Table Section -->
      <div class="manifest-card shadow-lg mb-4 overflow-hidden">
        <div class="table-responsive">
          <table class="table table-dark-custom align-middle mb-0">
            <thead>
              <tr class="small text-uppercase">
                <th class="ps-4 py-3">Asset Specification</th>
                <th class="py-3 text-center">Qty</th>
                <th class="py-3">Unit Price</th>
                <th class="py-3">Subtotal</th>
                <th class="py-3 pe-4 text-end">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($items as $it): $p = $it['p']; ?>
                <tr>
                  <td class="ps-4 py-3">
                    <div class="d-flex gap-3 align-items-center">
                      <img src="<?= e($it['image']) ?>" 
                           style="width:55px; height:55px; object-fit:cover" 
                           class="rounded border border-info border-opacity-25"
                           onerror="this.src='<?= BASE_URL ?>/assets/images/cyberShoplogo.png'">
                      <div>
                        <div class="fw-bold text-white"><?= e($p['name']) ?></div>
                        <div class="font-monospace text-info small">ID: #<?= (int)$p['product_id'] ?></div>
                      </div>
                    </div>
                  </td>
                  <td class="text-center font-monospace"><?= (int)$it['qty'] ?></td>
                  <td class="font-monospace text-white-50"><?= money((float)$p['price']) ?></td>
                  <td class="fw-bold text-cyan font-monospace"><?= money((float)$it['line']) ?></td>
                  <td class="text-end pe-4">
                    <form method="post" action="<?= BASE_URL ?>/public/cart_remove.php">
                      <input type="hidden" name="productId" value="<?= (int)$p['product_id'] ?>">
                      <button class="btn btn-sm btn-outline-danger border-0" 
                              type="submit" 
                              onclick="return confirm('DE-AUTHORIZE_ASSET: Are you sure you want to remove this from the manifest?');">
                        <i class="bi bi-trash3-fill"></i>
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- High-Visibility Summary Box -->
      <div class="cart-summary-box shadow-lg">
        <div class="row align-items-center">
            <div class="col-md-6 mb-4 mb-md-0">
                <a class="btn btn-outline-light px-4 py-2" href="index.php">
                  <i class="bi bi-arrow-left me-2"></i> CONTINUE BROWSING
                </a>
            </div>
            <div class="col-md-6 text-md-end">
                <div class="mb-3">
                  <span class="text-info text-uppercase small font-monospace fw-bold d-block">Grand Total:</span>
                  <h2 class="fw-bold total-amount mb-0"><?= money((float)$subtotal) ?></h2>
                </div>
                <a class="btn btn-checkout btn-lg px-5 py-3 shadow-sm" href="checkout.php">
                  FINALIZE ORDER <i class="bi bi-shield-check ms-2"></i>
                </a>
            </div>
        </div>
      </div>
    <?php endif; ?>

    <!-- Forced Visibility Footer Text -->
    <div class="mt-5 pt-4 text-center border-top border-secondary border-opacity-25">
        <p class="footer-text font-monospace small">
            &copy; <?= date('Y') ?> <span class="text-info fw-bold">CyberShop</span> | ONTARIO_NODE | SECURE_SESSION_ACTIVE
        </p>
    </div>
</div>

<?php include __DIR__ . '/../app/partials/footer.php'; ?>