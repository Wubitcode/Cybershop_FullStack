<?php
require_once __DIR__ . '/../../app/config.php';
require_once __DIR__ . '/../../app/db.php';
require_once __DIR__ . '/../../app/auth.php';

require_admin();

$orderId = $_GET['id'] ?? null;

if (!$orderId) {
    header("Location: index.php");
    exit;
}

try {
    $pdo = db();
    
    // 1. Fetch Order & User Info
    $stmt = $pdo->prepare("SELECT o.*, u.name as customer_name, u.email 
                           FROM orders o 
                           JOIN users u ON o.user_id = u.user_id 
                           WHERE o.order_id = ?");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();

    if (!$order) {
        die("ORDER_NOT_FOUND: Hash ID $orderId does not exist in the ledger.");
    }

    // 2. Fetch items + JOIN products to get the PRICE (since it's missing in order_items)
    $itemStmt = $pdo->prepare("SELECT oi.*, p.name as product_name, p.price 
                               FROM order_items oi 
                               JOIN products p ON oi.product_id = p.product_id 
                               WHERE oi.order_id = ?");
    $itemStmt->execute([$orderId]);
    $items = $itemStmt->fetchAll();

} catch (Exception $e) {
    die("DATABASE_ERROR: " . $e->getMessage());
}

$title = "Order #$orderId | CyberShop";
include __DIR__ . '/../../app/partials/header.php';
?>

<div class="admin-wrapper" style="display: flex;">
    <?php include __DIR__ . '/../../app/partials/admin_sidebar.php'; ?>

    <main class="main-terminal p-4" style="flex-grow: 1;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h6 class="text-info small fw-bold mb-1">SALES_LOG > ORDER_INSPECTION</h6>
                <h4 class="text-white fw-bold">ORDER_ID: #<?= $order['order_id'] ?></h4>
            </div>
            <a href="index.php" class="btn btn-outline-info btn-sm">BACK_TO_LEDGER</a>
        </div>

        <div class="row g-4">
            <!-- Customer Info Card -->
            <div class="col-md-4">
                <div class="cyber-card h-100">
                    <h6 class="text-info small fw-bold mb-3">CUSTOMER_PROFILE</h6>
                    <p class="text-white mb-1"><?= htmlspecialchars($order['customer_name']) ?></p>
                    <p class="text-secondary x-small mb-3"><?= htmlspecialchars($order['email']) ?></p>
                    <div class="badge bg-info bg-opacity-10 text-info border border-info px-3">
                        STATUS: <?= strtoupper($order['status'] ?? 'PENDING') ?>
                    </div>
                </div>
            </div>

            <!-- Manifest / Items Card -->
            <div class="col-md-8">
                <div class="cyber-card h-100">
                    <h6 class="text-info small fw-bold mb-3">TRANSACTION_MANIFEST</h6>
                    
                    <?php if (empty($items)): ?>
                        <div class="alert alert-danger bg-dark border-danger text-danger x-small">
                            <i class="bi bi-exclamation-triangle"></i> DATABASE_DESYNC: No items linked to Order #<?= $orderId ?>.
                        </div>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-dark table-borderless small">
                            <thead>
                                <tr class="text-secondary border-bottom border-secondary border-opacity-25">
                                    <th>ASSET_NAME</th>
                                    <th class="text-center">QTY</th>
                                    <th class="text-end">UNIT_PRICE</th>
                                    <th class="text-end">SUBTOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $calculatedTotal = 0;
                                foreach ($items as $item): 
                                    $subtotal = $item['price'] * $item['quantity'];
                                    $calculatedTotal += $subtotal;
                                ?>
                                <tr>
                                    <td class="text-white"><?= htmlspecialchars($item['product_name']) ?></td>
                                    <td class="text-center"><?= $item['quantity'] ?></td>
                                    <td class="text-end text-secondary">$<?= number_format((float)$item['price'], 2) ?></td>
                                    <td class="text-end text-info fw-bold">$<?= number_format($subtotal, 2) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="border-top border-secondary">
                                    <td colspan="3" class="text-end text-secondary pt-3">TOTAL_CREDITS:</td>
                                    <td class="text-end text-white h5 pt-3 fw-bold">
                                        <?php 
                                        // Use calculated total if database total is 0
                                        $finalTotal = ($order['total_amount'] > 0) ? $order['total_amount'] : $calculatedTotal;
                                        echo '$' . number_format((float)$finalTotal, 2); 
                                        ?>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include __DIR__ . '/../../app/partials/footer.php'; ?>