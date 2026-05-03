<?php
declare(strict_types=1);

// Force error reporting to catch any final path issues
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/helpers.php';
require_once __DIR__ . '/../app/repos/orders_repo.php';

// 1. Force Login
require_login(BASE_URL . '/public/login.php');

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
$order = $id ? order_find($id) : null;

// 2. Handle Missing Orders
if (!$order) {
    http_response_code(404);
    $title = "Order Not Found";
    include __DIR__ . '/../app/partials/header.php';
    echo '<div class="container py-5"><div class="alert alert-danger border-0 bg-dark text-danger font-monospace">PROTOCOL_ERROR: Order ID not found in central repository.</div></div>';
    include __DIR__ . '/../app/partials/footer.php';
    exit;
}

$me = current_user();

/**
 * 3. SECURITY GATE
 * Ensure 'user_id' matches between session and order record
 */
$orderOwnerId = (int)($order['user_id'] ?? 0);
$currentUserId = (int)($me['user_id'] ?? 0);

if (!is_admin() && $orderOwnerId !== $currentUserId) {
    http_response_code(403);
    $title = "Access Denied";
    include __DIR__ . '/../app/partials/header.php';
    echo '<div class="container py-5">
            <div class="alert alert-danger border-0 bg-dark text-danger shadow-lg p-5">
                <h2 class="fw-bold font-monospace"><i class="bi bi-shield-slash-fill me-2"></i>403: Security Breach</h2>
                <hr class="border-danger opacity-25">
                <p>Access Denied: Your node credentials do not match the authorization key for Order #' . (int)$id . '</p>
                <a href="index.php" class="btn btn-outline-danger mt-3 font-monospace">BACK_TO_SAFETY</a>
            </div>
          </div>';
    include __DIR__ . '/../app/partials/footer.php';
    exit;
}

/** 
 * 4. FETCH ITEMS
 * Fetches the specific products attached to this order
 */
$items = order_get_items((int)$order['id']);
$title = "Asset Receipt #" . $order['id'];

include __DIR__ . '/../app/partials/header.php';
?>

<style>
    body { background-color: #05070a !important; color: #e6edf3 !important; }
    .terminal-card {
        background: #0d1117;
        border: 1px solid rgba(0, 210, 255, 0.2);
        border-radius: 12px;
        padding: 30px;
        margin-bottom: 2rem;
    }
    .status-badge {
        background: rgba(0, 210, 255, 0.1);
        color: #00d2ff;
        border: 1px solid #00d2ff;
        padding: 5px 15px;
        font-family: 'Consolas', monospace;
        text-transform: uppercase;
        font-size: 0.8rem;
    }
    .text-cyan { color: #00d2ff !important; }
    .table-cyber { color: #e6edf3 !important; border-color: #30363d; }
    .table-cyber thead { background: rgba(255, 255, 255, 0.05); color: #00d2ff; font-family: 'Consolas', monospace; }
</style>

<div class="container py-5">
    <?php if (isset($_GET['status']) && $_GET['status'] === 'confirmed'): ?>
        <div class="alert alert-success bg-dark border-success text-success mb-4">
            <i class="bi bi-shield-check me-2"></i> TRANSACTION_AUTHORIZED: Funds cleared and assets deployed to your node.
        </div>
    <?php endif; ?>

    <div class="d-flex align-items-center justify-content-between mb-4 border-bottom border-secondary border-opacity-25 pb-3">
        <div>
            <h6 class="text-info small fw-bold font-monospace" style="letter-spacing: 2px;">SECURE_STORE > ACQUISITION_RECEIPT</h6>
            <h1 class="h3 mb-0 text-white fw-bold">Order #<?= (int)$order['id'] ?></h1>
        </div>
        <span class="status-badge"><?= e($order['status'] ?? 'PROCESSED') ?></span>
    </div>

    <div class="terminal-card shadow-lg">
        <div class="row g-4">
            <div class="col-md-6 border-end border-secondary border-opacity-25">
                <label class="text-info small fw-bold mb-2 d-block font-monospace">DEPLOYMENT_TARGET</label>
                <div class="fs-5 text-white fw-bold"><?= e($order['customer_name'] ?? $me['fullName']) ?></div>
                <div class="text-secondary small"><?= e($me['email']) ?></div>
                <div class="text-secondary small mt-2">Date: <?= $order['createdAt'] ?></div>
            </div>
            <div class="col-md-6 text-md-end">
                <label class="text-info small fw-bold mb-2 d-block font-monospace">FINANCIAL_SUMMARY</label>
                <div class="h2 text-cyan fw-bold mt-2 font-monospace"><?= money((float)($order['total'] ?? 0)) ?></div>
                <div class="text-muted small">Tax & Encryption Fees Included</div>
            </div>
        </div>
    </div>

    <div class="terminal-card p-0 overflow-hidden shadow-lg border-info border-opacity-25">
        <div class="table-responsive">
            <table class="table table-cyber align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">ASSET_SPECIFICATION</th>
                        <th>QTY</th>
                        <th class="pe-4 text-end">UNIT_PRICE</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($items)): ?>
                        <tr><td colspan="3" class="text-center py-4 text-muted">No line items found for this record.</td></tr>
                    <?php else: ?>
                        <?php foreach ($items as $it): 
                            // FALLBACK: Checks for 'unit_price' OR 'price' to prevent crashes
                            $displayPrice = (float)($it['unit_price'] ?? $it['price'] ?? 0);
                            $displayQty   = (int)($it['quantity'] ?? $it['qty'] ?? 0);
                        ?>
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="fw-bold text-white"><?= e($it['product_name'] ?? 'Unknown Item') ?></div>
                                <div class="text-muted small font-monospace">ID: 0x<?= dechex((int)($it['product_id'] ?? 0)) ?></div>
                            </td>
                            <td class="font-monospace text-silver"><?= $displayQty ?></td>
                            <td class="font-monospace text-cyan fw-bold pe-4 text-end"><?= money($displayPrice) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-4">
        <a href="index.php" class="btn btn-outline-info px-4 font-monospace">
            <i class="bi bi-arrow-left me-2"></i> RETURN_TO_TERMINAL
        </a>
        <button onclick="window.print()" class="btn btn-dark text-info border-info px-4 font-monospace">
            <i class="bi bi-printer me-2"></i> GENERATE_HARD_COPY
        </button>
    </div>
</div>

<?php include __DIR__ . '/../app/partials/footer.php'; ?>