<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/db.php'; 
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/helpers.php';
require_once __DIR__ . '/../app/repos/products_repo.php';
require_once __DIR__ . '/../app/repos/orders_repo.php';

require_admin();

$title = "Cyber Intelligence | Dashboard";
$user = current_user(); 

try {
    $totalProducts = products_count();
    $pendingOrders = orders_count_pending(); 
    $totalRevenue  = orders_total_revenue();
    $latestOrders  = orders_latest_limit(5); 

    // Chart Data
    $chartLabels = ['01', '05', '10', '15', '20', '25', '30'];
    $chartData   = [5000, 15000, 12000, 28000, 20000, 32000, 33700]; 
} catch (Exception $e) {
    $totalProducts = $pendingOrders = $totalRevenue = 0;
}

include __DIR__ . '/../app/partials/header.php';
?>

<style>
    :root {
        --bg-deep: #000000;
        --sidebar-bg: #191c24;
        --card-bg: #191c24;
        --accent-cyan: #00d2ff;
        --accent-green: #00d25b;
        --accent-red: #ff3e3e;
        --text-dim: #6c7293;
    }

    body {
        background-color: var(--bg-deep) !important;
        color: #ffffff !important;
        font-family: 'Inter', 'Segoe UI', sans-serif;
    }

    /* GRID SETUP */
    .wrapper {
        display: flex;
        min-height: 100vh;
    }

    /* SIDEBAR - EXACT MATCH */
    .sidebar {
        width: 240px;
        background: var(--sidebar-bg);
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
    }

    .sidebar-header {
        padding: 1.5rem;
        font-size: 1.25rem;
        font-weight: 800;
        letter-spacing: 1px;
    }

    .nav-group-title {
        padding: 1.5rem 1.5rem 0.5rem;
        font-size: 0.7rem;
        color: var(--text-dim);
        text-transform: uppercase;
        letter-spacing: 2px;
    }

    .nav-item {
        padding: 0.75rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 15px;
        color: #ffffff;
        text-decoration: none;
        font-size: 0.9rem;
        transition: 0.2s;
    }

    .nav-item:hover, .nav-item.active {
        background: #0f1015;
        color: var(--accent-cyan);
    }

    .nav-item i { font-size: 1.1rem; width: 20px; }

    /* MAIN AREA */
    .main-content {
        flex-grow: 1;
        padding: 2rem;
        background: #000000;
    }

    /* FLOATING GLASS CARDS */
    .cyber-card {
        background: var(--card-bg);
        border-radius: 4px; /* Image uses slightly sharp corners */
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .stat-label { color: var(--text-dim); font-size: 0.8rem; text-transform: uppercase; }
    .stat-val { font-size: 1.5rem; font-weight: 700; margin: 5px 0; }

    /* PROGRESS CIRCLE */
    .integrity-circle {
        width: 120px;
        height: 120px;
        border: 8px solid #2c2e33;
        border-top: 8px solid var(--accent-green);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 20px auto;
        position: relative;
    }

    /* STATUS DOTS */
    .dot { height: 8px; width: 8px; border-radius: 50%; display: inline-block; margin-right: 5px; }
    .dot-online { background-color: var(--accent-green); box-shadow: 0 0 10px var(--accent-green); }

    /* TABLE */
    .table-custom { color: #fff; width: 100%; }
    .table-custom thead th { color: var(--text-dim); border-bottom: 1px solid #2c2e33; font-size: 0.75rem; padding: 10px; }
    .table-custom td { padding: 15px 10px; border-bottom: 1px solid #2c2e33; font-size: 0.9rem; }

</style>

<div class="wrapper">
    <nav class="sidebar">
        <div class="sidebar-header">
            <span style="color: var(--accent-cyan);">CYBER</span>SHOP
        </div>
        
        <div class="nav-group-title">Navigation</div>
        <a href="#" class="nav-item active"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <a href="products/index.php" class="nav-item"><i class="bi bi-cpu"></i> Inventory</a>
        <a href="orders/index.php" class="nav-item"><i class="bi bi-receipt"></i> Orders</a>
        
        <div class="nav-group-title">Analytics & Tools</div>
        <a href="#" class="nav-item"><i class="bi bi-bar-chart-steps"></i> Analytics</a>
        <a href="#" class="nav-item"><i class="bi bi-shield-lock"></i> Security Hub</a>
        
        <div class="nav-group-title">System</div>
        <a href="#" class="nav-item"><i class="bi bi-gear"></i> Settings</a>
        <a href="../public/logout.php" class="nav-item text-danger"><i class="bi bi-power"></i> Log Out</a>
    </nav>

    <main class="main-content">
        <header class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">WELCOME, <?= e($user['fullName']) ?></h4>
                <small class="text-dim"><span class="dot dot-online"></span> System Online | Durham Region Node</small>
            </div>
            <div class="d-flex gap-4">
                <div class="text-end">
                    <div class="small text-dim">Total Balance</div>
                    <div class="fw-bold text-glow"><?= money($totalRevenue) ?></div>
                </div>
            </div>
        </header>

        <div class="row">
            <div class="col-md-3">
                <div class="cyber-card">
                    <div class="stat-label">Potential Growth</div>
                    <div class="stat-val text-white"><?= money($totalRevenue) ?></div>
                    <div class="text-success small">+11% this month</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="cyber-card">
                    <div class="stat-label">Active Sessions</div>
                    <div class="stat-val">333</div>
                    <div class="text-dim small">Live connections</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="cyber-card">
                    <div class="stat-label">Pending Orders</div>
                    <div class="stat-val"><?= $pendingOrders ?></div>
                    <div class="text-info small">Awaiting verification</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="cyber-card">
                    <div class="stat-label">Security Threat</div>
                    <div class="stat-val text-success">LOW</div>
                    <div class="text-dim small">No breaches detected</div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="cyber-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h6 class="fw-bold m-0">SALES OVERVIEW</h6>
                        <select class="bg-dark text-dim border-0 small outline-none">
                            <option>Last 30 Days</option>
                        </select>
                    </div>
                    <div style="height: 300px;">
                        <canvas id="ultimateChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="cyber-card text-center">
                    <h6 class="fw-bold text-start mb-3">SYSTEM INTEGRITY</h6>
                    <div class="integrity-circle">
                        <span class="h3 fw-bold mb-0">99.8%</span>
                    </div>
                    <p class="text-dim small">Encryption: AES-256 Active</p>
                    <button class="btn btn-outline-info btn-sm w-100 mt-2">View Server Logs</button>
                </div>
            </div>
        </div>

        <div class="cyber-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="fw-bold m-0">TRANSACTION HISTORY</h6>
                <a href="orders/index.php" class="text-dim text-decoration-none small">View All <i class="bi bi-chevron-right"></i></a>
            </div>
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>CLIENT</th>
                            <th>ORDER ID</th>
                            <th>AMOUNT</th>
                            <th>STATUS</th>
                            <th class="text-end">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($latestOrders as $row): ?>
                        <tr>
                            <td><?= e($row['customer_name']) ?></td>
                            <td class="text-dim">#<?= $row['order_id'] ?></td>
                            <td class="fw-bold"><?= money((float)$row['total']) ?></td>
                            <td><span class="badge bg-opacity-10 bg-success text-success border border-success"><?= strtoupper($row['status']) ?></span></td>
                            <td class="text-end"><a href="orders/view.php?id=<?= $row['order_id'] ?>" class="btn btn-sm btn-dark border-secondary">Manage</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('ultimateChart').getContext('2d');
    
    const neonGradient = ctx.createLinearGradient(0, 0, 0, 400);
    neonGradient.addColorStop(0, 'rgba(0, 210, 255, 0.4)');
    neonGradient.addColorStop(1, 'rgba(0, 210, 255, 0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($chartLabels) ?>,
            datasets: [{
                data: <?= json_encode($chartData) ?>,
                borderColor: '#00d2ff',
                borderWidth: 3,
                backgroundColor: neonGradient,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#00d2ff',
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#6c7293' } },
                x: { grid: { display: false }, ticks: { color: '#6c7293' } }
            }
        }
    });
</script>

<?php include __DIR__ . '/../app/partials/footer.php'; ?>
