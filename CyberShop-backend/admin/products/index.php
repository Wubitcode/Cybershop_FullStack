<?php
declare(strict_types=1);

require_once __DIR__ . '/../../app/config.php';
require_once __DIR__ . '/../../app/db.php';
require_once __DIR__ . '/../../app/auth.php';
require_once __DIR__ . '/../../app/helpers.php';
require_once __DIR__ . '/../../app/repos/products_repo.php';

require_admin(); 

$title = "Inventory Terminal | CyberShop";
$products = products_all();
$user = current_user();

include __DIR__ . '/../../app/partials/header.php';
?>

<style>
    /* FORCED CYBER THEME */
    :root {
        --terminal-black: #000000;
        --card-bg: #11141b; /* Slightly lighter than black for depth */
        --sidebar-bg: #080a0c;
        --accent-cyan: #00d2ff;
        --border-color: rgba(0, 210, 255, 0.1);
        --text-dim: #6c7293;
    }

    body {
        background-color: var(--terminal-black) !important;
        color: #ffffff !important;
        font-family: 'Inter', 'Segoe UI', monospace;
    }

    .wrapper {
        display: flex;
        min-height: 100vh;
        background: var(--terminal-black);
    }

    /* SIDEBAR STYLING */
    .sidebar {
        width: 260px;
        background: var(--sidebar-bg);
        border-right: 1px solid var(--border-color);
        display: flex;
        flex-direction: column;
    }

    .nav-item {
        padding: 12px 25px;
        color: var(--text-dim);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: 0.3s;
        border-left: 3px solid transparent;
    }

    .nav-item:hover, .nav-item.active {
        background: rgba(0, 210, 255, 0.05);
        color: var(--accent-cyan);
        border-left: 3px solid var(--accent-cyan);
    }

    /* MAIN CONTENT */
    .main-content {
        flex-grow: 1;
        padding: 30px;
        background: radial-gradient(circle at top right, #0d1117, #000000);
    }

    .cyber-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 4px;
        padding: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }

    /* TABLE STYLING */
    .table-terminal {
        width: 100%;
        color: #ffffff;
        border-collapse: collapse;
    }

    .table-terminal thead th {
        color: var(--accent-cyan);
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 1px;
        padding: 15px;
        border-bottom: 1px solid var(--border-color);
    }

    .table-terminal td {
        padding: 15px;
        border-bottom: 1px solid rgba(255,255,255,0.03);
        font-size: 0.9rem;
    }

    .btn-cyber {
        background: transparent;
        border: 1px solid var(--accent-cyan);
        color: var(--accent-cyan);
        font-size: 0.75rem;
        padding: 5px 15px;
        text-transform: uppercase;
        transition: 0.3s;
    }

    .btn-cyber:hover {
        background: var(--accent-cyan);
        color: #000;
        box-shadow: 0 0 15px var(--accent-cyan);
    }
</style>

<div class="wrapper">
    <nav class="sidebar">
        <div class="p-4 mb-4">
            <h5 class="fw-bold mb-0" style="letter-spacing: 2px;">
                <span style="color: var(--accent-cyan);">NODE</span>_01
            </h5>
            <small class="text-dim">OPERATOR: <?= strtoupper($user['name'] ?? 'ROOT') ?></small>
        </div>

        <a href="<?= BASE_URL ?>/admin/dashboard.php" class="nav-item">
            <i class="bi bi-grid-1x2"></i> DASHBOARD
        </a>
        <a href="<?= BASE_URL ?>/admin/products/index.php" class="nav-item active">
            <i class="bi bi-cpu"></i> INVENTORY
        </a>
        <a href="<?= BASE_URL ?>/admin/orders/index.php" class="nav-item">
            <i class="bi bi-shield-lock"></i> ORDER_TRAFFIC
        </a>
        
        <div class="mt-auto p-4 border-top border-secondary border-opacity-10">
            <a href="<?= BASE_URL ?>/public/logout.php" class="text-danger text-decoration-none small fw-bold">
                <i class="bi bi-power"></i> DEAUTHORIZE_SESSION
            </a>
        </div>
    </nav>

    <main class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="fw-bold mb-0 text-white" style="letter-spacing: -1px;">INVENTORY_DATABASE</h2>
                <div class="text-info small fw-bold mt-1">
                    <i class="bi bi-hdd-network"></i> 127.0.0.1 // SECURE_ACCESS
                </div>
            </div>
            <a href="<?= BASE_URL ?>/admin/products/create.php" class="btn btn-cyber">
                + ADD_PRODUCT
            </a>
        </div>

        <div class="cyber-card">
            <table class="table-terminal">
                <thead>
                    <tr>
                        <th>IDENTIFIER</th>
                        <th>PRODUCT_NAME</th>
                        <th>MARKET_PRICE</th>
                        <th>STOCK_LEVEL</th>
                        <th class="text-end">SYSTEM_COMMANDS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $p): ?>
                    <tr>
                        <td class="text-dim">#<?= $p['product_id'] ?></td>
                        <td class="fw-bold text-white"><?= htmlspecialchars($p['name']) ?></td>
                        <td class="text-info"><?= money((float)$p['price']) ?></td>
                        <td>
                            <span class="<?= $p['stock'] < 5 ? 'text-danger fw-bold' : 'text-success' ?>">
                                <?= $p['stock'] ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="<?= BASE_URL ?>/admin/products/edit.php?id=<?= $p['product_id'] ?>" class="text-info me-3 text-decoration-none small">
                                [ EDIT ]
                            </a>
                            <a href="<?= BASE_URL ?>/admin/products/delete.php?id=<?= $p['product_id'] ?>" 
                       class="text-danger text-decoration-none small"
                    onclick="return confirm('CRITICAL_WARNING: ERASE_NODE_#<?= $p['product_id'] ?>?')">
                 [ DELETE ]
                    </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<?php include __DIR__ . '/../../app/partials/footer.php'; ?>