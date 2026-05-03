<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/config.php'; 
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/helpers.php';
require_once __DIR__ . '/../app/repos/products_repo.php';
require_once __DIR__ . '/../app/repos/orders_repo.php';

require_login('/public/login.php');

if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$cart = $_SESSION['cart'] ?? [];

// Redirect if the manifest is empty
if (count($cart) === 0) {
    header("Location: " . BASE_URL . "/public/cart.php");
    exit;
}

$items = [];
$totalAmount = 0.0;

// Prepare the data package from the session cart
foreach ($cart as $pid => $qty) {
    $p = product_find((int)$pid);
    if (!$p) continue;
    
    $lineTotal = ((float)$p['price'] * (int)$qty);
    $totalAmount += $lineTotal;

    $items[] = [
        'productId' => (int)$p['product_id'], 
        'name'      => $p['name'],
        'qty'       => (int)$qty, 
        'unitPrice' => (float)$p['price']
    ];
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $user = current_user();
        $uid = (int)($user['user_id'] ?? 0);
        
        // 1. Capture the new Delivery Coordinates
        $address = trim($_POST['address'] ?? '');
        $postal  = trim($_POST['postal_code'] ?? '');
        $phone   = trim($_POST['phone'] ?? ''); // Matches <input name="phone">

        if ($uid === 0) throw new Exception("SESSION_EXPIRED");
        if (empty($address)) throw new Exception("DELIVERY_COORDINATES_REQUIRED");

        // 2. Authorize the Order Creation
        // This sends the data to order_create in orders_repo.php
        $newId = order_create($uid, $items, $address, $postal, $phone);
        
        if ($newId > 0) {
            // Clear the manifest only AFTER successful database insertion
            $_SESSION['cart'] = []; 
            header("Location: " . BASE_URL . "/public/payment.php?id=" . $newId);
            exit;
        } else {
            throw new Exception("DATABASE_REJECTED_MANIFEST_CHECK_SQL_LOGS");
        }
    } catch (Throwable $e) {
        $error = "DEPLOYMENT_FAILED: " . $e->getMessage();
    }
}

$title = "Authorize Deployment | CyberShop";
include __DIR__ . '/../app/partials/header.php';
?>

<style>
    body { background-color: #05070a !important; color: #e6edf3 !important; }
    .checkout-terminal { background: #0d1117; border: 1px solid rgba(0, 210, 255, 0.2); border-radius: 12px; padding: 40px; }
    .text-cyan { color: #00d2ff !important; }
    .form-control-cyber { background: #000 !important; border: 1px solid #30363d !important; color: #00d2ff !important; font-family: 'Consolas', monospace; }
    .form-control-cyber:focus { border-color: #00d2ff !important; box-shadow: 0 0 10px rgba(0, 210, 255, 0.2); }
    .btn-authorize { background: #00d2ff; color: #000; font-weight: 800; border: none; padding: 15px 30px; text-transform: uppercase; letter-spacing: 1.5px; }
    .btn-authorize:hover { background: #00b4db; box-shadow: 0 0 20px rgba(0, 210, 255, 0.5); color: #000; }
</style>

<div class="container py-5">
    <div class="mb-4">
        <h6 class="text-info small fw-bold" style="letter-spacing: 2px;">SECURE_STORE > AUTHORIZE_ACQUISITION</h6>
        <h1 class="h2 text-white fw-bold"><i class="bi bi-shield-check me-2 text-info"></i>Final Authorization</h1>
    </div>

    <?php if ($error): ?>
      <div class="alert alert-danger bg-dark border-danger text-danger shadow-sm mb-4">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= e($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST" id="checkoutForm">
        <div class="row g-4">
            <div class="col-md-8">
                <div class="checkout-terminal shadow-lg mb-4">
                    <h5 class="text-white mb-4 border-bottom border-secondary pb-2">DELIVERY_COORDINATES</h5>
                    <div class="mb-3">
                        <label class="small text-secondary fw-bold">PHYSICAL_STREET_ADDRESS</label>
                        <input type="text" name="address" class="form-control form-control-cyber" placeholder="e.g. 123 Cyber St, Ajax, ON" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="small text-secondary fw-bold">POSTAL_CODE</label>
                            <input type="text" name="postal_code" class="form-control form-control-cyber" placeholder="L1S 3Z4" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-secondary fw-bold">CONTACT_PHONE</label>
                            <input type="text" name="phone" class="form-control form-control-cyber" placeholder="905-000-0000" required>
                        </div>
                    </div>
                </div>

                <div class="checkout-terminal shadow-lg">
                    <h5 class="text-white mb-4 border-bottom border-secondary pb-2">MANIFEST REVIEW</h5>
                    <?php foreach ($items as $item): ?>
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary border-opacity-10">
                            <div>
                                <span class="text-white fw-bold d-block"><?= e($item['name']) ?></span>
                                <span class="text-secondary small">QTY: <?= $item['qty'] ?></span>
                            </div>
                            <span class="text-cyan fw-bold font-monospace"><?= money($item['qty'] * $item['unitPrice']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="col-md-4">
                <div class="checkout-terminal border-info sticky-top" style="top: 20px;">
                    <h6 class="text-info font-monospace small mb-2">TOTAL_COST</h6>
                    <h2 class="text-white fw-bold mb-4"><?= money($totalAmount) ?></h2>

                    <button type="submit" class="btn btn-authorize w-100 mb-3" id="authBtn">
                        <i class="bi bi-lock-fill me-2"></i> PLACE ORDER
                    </button>
                    
                    <a class="btn btn-outline-secondary w-100" href="<?= BASE_URL ?>/public/cart.php">
                        <i class="bi bi-arrow-left"></i> BACK TO CART
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.getElementById('checkoutForm').onsubmit = function() {
    const btn = document.getElementById('authBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-gear-fill spin-anim me-2"></i>AUTHORIZING...';
    return true; 
};
</script>

<?php include __DIR__ . '/../app/partials/footer.php'; ?>