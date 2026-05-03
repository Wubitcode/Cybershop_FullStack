<?php
declare(strict_types=1);

require_once __DIR__ . '/../../app/config.php';
require_once __DIR__ . '/../../app/auth.php';
require_once __DIR__ . '/../../app/helpers.php';
require_once __DIR__ . '/../../app/repos/orders_repo.php';

require_admin();

$orders = orders_all();
$title = "Transaction Logs | CyberShop";
$user = current_user();

include __DIR__ . '/../../app/partials/header.php';
?>

<style>
    /* Reset margins for Zero-Gap */
    body, html { margin: 0 !important; padding: 0 !important; background-color: #000 !important; }
    
    /* Your Custom Cyber Styles */
    :root { --cyber-blue: #00d2ff; --dark-card: #161b22; }
    .text-glow { color: var(--cyber-blue); text-shadow: 0 0 8px rgba(0, 210, 255, 0.4); }
    .btn-inspect { border: 1px solid var(--cyber-blue); color: var(--cyber-blue); font-size: 0.75rem; font-weight: bold; }
    .btn-inspect:hover { background: var(--cyber-blue); color: #000; box-shadow: 0 0 10px var(--cyber-blue); }
    
    /* Table Overrides for Terminal look */
    .table-terminal-orders { background: rgba(255,255,255,0.02); border-radius: 4px; }
</style>

<link rel="stylesheet" href="/cybershop/assets/css/admin-theme.css">

<div class="admin-wrapper">
    <!-- Sidebar stays flush to the left -->
    <?php include __DIR__ . '/../../app/partials/admin_sidebar.php'; ?>

    <!-- Content fills the rest -->
    <main class="main-terminal">
        <header class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h6 class="text-info text-uppercase small fw-bold mb-1">SYSTEM_ADMINISTRATION</h6>
                <h4 class="fw-bold mb-0 text-white text-glow"><i class="bi bi-terminal-fill me-2"></i>ORDER_MANIFEST_LOGS</h4>
            </div>
            <div class="text-end">
                <span class="badge bg-dark border border-success text-success p-2 small">
                    NODE_ACCESS: <?= strtoupper($user['role'] ?? 'GUEST') ?>
                </span>
            </div>
        </header>

        <div class="cyber-card p-0 overflow-hidden">
            <div class="table-responsive">
                <table class="table-terminal mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>CUSTOMER_ENTITY</th>
                            <th>STATUS</th>
                            <th>VALUATION</th>
                            <th>TIMESTAMP</th>
                            <th class="text-end pe-4">COMMAND</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $o): ?>
                            <tr>
                                <td class="ps-4 text-dim">#<?= (int)$o['order_id'] ?></td>
                                <td>
                                    <div class="fw-bold text-white"><?= e($o['customer_name']) ?></div>
                                    <div class="text-dim x-small"><?= e($o['email'] ?? 'HIDDEN_USER') ?></div>
                                </td>
                                <td>
                                    <?php $s = strtolower($o['status']); ?>
                                    <span class="badge bg-opacity-10 border <?= $s === 'paid' || $s === 'completed' ? 'bg-success text-success border-success' : 'bg-warning text-warning border-warning' ?>" style="font-size: 0.65rem;">
                                        <?= strtoupper($s) ?>
                                    </span>
                                </td>
                                <td class="text-cyan fw-bold"><?= money((float)$o['total']) ?></td>
                                <td class="text-dim small"><?= date('Y-m-d H:i', strtotime($o['order_date'] ?? $o['created_at'])) ?></td>
                                <td class="text-end pe-4">
                                    <a href="view.php?id=<?= $o['order_id'] ?>" class="btn btn-sm btn-inspect">
                                        <i class="bi bi-search"></i> INSPECT
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-dim small">
                                    <i class="bi bi-exclamation-triangle d-block mb-2 h4"></i>
                                    NO TRANSACTIONS DETECTED IN ENCRYPTED DATABASE
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<?php include __DIR__ . '/../../app/partials/footer.php'; ?>