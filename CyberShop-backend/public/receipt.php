<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/helpers.php';
require_once __DIR__ . '/../app/repos/orders_repo.php';

// 1. Capture the Order ID from the URL (?id=XX)
$id = (int)($_GET['id'] ?? 0);
$order = order_find($id);

// 2. Security Check: Only the owner (or an admin) can see this receipt
if (!$order || (int)$order['user_id'] !== (int)current_user()['user_id']) {
    die("ACCESS_DENIED: UNAUTHORIZED_RECEIPT_REQUEST");
}

// 3. Fetch the individual items for this order
// This uses the order_get_items function we added to your repo
$items = order_get_items($id);

$title = "Acquisition Receipt #" . $id;
include __DIR__ . '/../app/partials/header.php';
?>

<style>
    body { background-color: #05070a !important; color: #e6edf3 !important; }
    .receipt-card {
        background: #0d1117;
        border: 1px solid rgba(0, 210, 255, 0.2);
        border-radius: 12px;
        padding: 40px;
        max-width: 800px;
        margin: 0 auto;
    }
    .status-confirmed {
        color: #00ff88;
        border: 1px solid #00ff88;
        padding: 4px 12px;
        font-size: 0.8rem;
        text-transform: uppercase;
        font-family: 'Consolas', monospace;
    }
    .table-cyber { color: #e6edf3; border-color: #30363d; }
    .text-cyan { color: #00d2ff !important; }
</style>

<div class="container py-5">
    <div class="receipt-card shadow-lg">
        
        <div class="d-flex justify-content-between align-items-start mb-5 border-bottom border-secondary border-opacity-25 pb-4">
            <div>
                <h6 class="text-info small fw-bold font-monospace">TRANSACTION_RECEIPT</h6>
                <h2 class="text-white fw-bold mb-0">Order #<?= $id ?></h2>
                <div class="text-secondary small mt-1">Processed: <?= e($order['order_date']) ?></div>
            </div>
            <span class="status-confirmed">AUTHORIZED_PAID</span>
        </div>

        <div class="row mb-5">
            <div class="col-md-6">
                <label class="text-info small fw-bold font-monospace">DEPLOYMENT_TARGET</label>
                <div class="text-white fw-bold fs-5"><?= e($order['customer_name'] ?? current_user()['fullName']) ?></div>
                <div class="text-secondary"><?= e($order['shipping_address']) ?></div>
                <div class="text-secondary"><?= e($order['postal_code']) ?></div>
            </div>
            <div class="col-md-6 text-md-end">
                <label class="text-info small fw-bold font-monospace">FINANCIAL_TOTAL</label>
                <div class="h2 text-cyan fw-bold font-monospace"><?= money((float)$order['total']) ?></div>
            </div>
        </div>

        <div class="table-responsive mb-5">
            <table class="table table-cyber align-middle">
                <thead>
                    <tr class="text-info font-monospace small">
                        <th>ASSET_NAME</th>
                        <th>QTY</th>
                        <th class="text-end">UNIT_PRICE</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $it): ?>
                    <tr>
                        <td class="text-white fw-bold"><?= e($it['product_name']) ?></td>
                        <td class="font-monospace text-secondary"><?= (int)$it['quantity'] ?></td>
                        <td class="text-end font-monospace text-cyan"><?= money((float)$it['price_at_purchase']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between">
            <a href="index.php" class="btn btn-outline-info px-4">
                <i class="bi bi-arrow-left me-2"></i> RETURN_TO_STORE
            </a>
            <button onclick="window.print()" class="btn btn-dark border-secondary px-4">
                <i class="bi bi-printer me-2"></i> PRINT_HARD_COPY
            </button>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../app/partials/footer.php'; ?>