<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/helpers.php';
require_once __DIR__ . '/../app/repos/orders_repo.php';

// Ensure the user is authenticated
require_login(BASE_URL . '/public/login.php');

// 1. Capture the Order ID from the URL
$orderId = (int)($_GET['id'] ?? 0);
$order = order_find($orderId);

// 2. Security Check: Does the order exist and belong to this user?
if (!$order || (int)$order['user_id'] !== (int)current_user()['user_id']) {
    header("Location: " . BASE_URL . "/public/index.php");
    exit;
}

// 3. Handle the Payment Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Simulate a successful credit transfer by updating status to 'paid'
    $success = order_update_status($orderId, 'paid');

    if ($success) {
        // 🚀 FIX: Redirect to receipt.php (not order_details.php)
        header("Location: receipt.php?id=$orderId&status=confirmed");
        exit;
    } else {
        $error = "ENCRYPTION_ERROR: Payment node rejected the authorization.";
    }
}

$title = "Payment Terminal | CyberShop";
include __DIR__ . '/../app/partials/header.php';
?>

<style>
    body { background-color: #05070a !important; color: #e6edf3 !important; }
    .payment-terminal {
        background: #0d1117;
        border: 2px solid #00d2ff;
        border-radius: 12px;
        box-shadow: 0 0 20px rgba(0, 210, 255, 0.1);
        max-width: 450px;
        margin: 0 auto;
    }
    .card-input {
        background: #000 !important;
        border: 1px solid #30363d !important;
        color: #00d2ff !important;
        font-family: 'Consolas', monospace;
        letter-spacing: 2px;
    }
    .card-input:focus {
        border-color: #00d2ff !important;
        box-shadow: 0 0 10px rgba(0, 210, 255, 0.2);
    }
    .text-cyan { color: #00d2ff !important; }
</style>

<div class="container py-5">
    <div class="row justify-content-center mt-4">
        <div class="col-md-6">
            <div class="payment-terminal p-4">
                <h5 class="text-white fw-bold mb-4">
                    <i class="bi bi-shield-lock-fill me-2 text-info"></i>SECURE_PAYMENT_GATEWAY
                </h5>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger bg-dark border-danger text-danger small mb-4">
                        <i class="bi bi-exclamation-triangle me-2"></i> <?= e($error) ?>
                    </div>
                <?php endif; ?>

                <div class="bg-dark p-3 rounded mb-4 border border-secondary border-opacity-25 text-center">
                    <span class="text-secondary small font-monospace">TRANSFER_TOTAL:</span>
                    <h2 class="text-info fw-bold font-monospace"><?= money((float)$order['total']) ?></h2>
                </div>

                <form id="payForm" method="POST">
                    <div class="mb-3">
                        <label class="text-secondary small fw-bold font-monospace">CARD_NUMBER</label>
                        <input type="password" name="card_num" class="form-control card-input" 
                               placeholder="•••• •••• •••• ••••" required autocomplete="off">
                    </div>

                    <div class="row mb-4">
                        <div class="col-6">
                            <label class="text-secondary small fw-bold font-monospace">EXPIRY</label>
                            <input type="text" name="expiry" class="form-control card-input" placeholder="MM/YY" required>
                        </div>
                        <div class="col-6">
                            <label class="text-secondary small fw-bold font-monospace">CVC / CVV</label>
                            <input type="password" name="cvc" class="form-control card-input" 
                                   placeholder="•••" maxlength="4" required>
                        </div>
                    </div>
                    
                    <button type="submit" id="payBtn" class="btn btn-info w-100 fw-bold py-3 text-dark">
                        AUTHORIZE_CREDIT_TRANSFER
                    </button>
                </form>

                <div class="mt-3 text-center">
                    <a href="checkout.php" class="text-secondary small text-decoration-none">
                        <i class="bi bi-arrow-left"></i> BACK_TO_CHECKOUT
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('payForm').onsubmit = function() {
    const btn = document.getElementById('payBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-arrow-repeat spin-anim me-2"></i>PROCESS_TRANSFER...';
    return true; 
};
</script>

<?php include __DIR__ . '/../app/partials/footer.php'; ?>