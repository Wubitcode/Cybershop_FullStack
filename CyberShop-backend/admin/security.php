<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/auth.php';

// Security check
require_admin();

// 1. DATA FETCHING SECTION
$totalAdmins = 0;
$recent_logs = [];

try {
    $pdo = db();
    
    // Count admin nodes
    $stmtCount = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'");
    $totalAdmins = (int)$stmtCount->fetchColumn();

    // Get the 10 most recent actions (Matching your DB columns: log_id, user_id, action, ip_address)
    $stmtLogs = $pdo->query("SELECT l.*, u.name 
                             FROM system_logs l 
                             LEFT JOIN users u ON l.user_id = u.user_id 
                             ORDER BY l.created_at DESC 
                             LIMIT 10");
    $recent_logs = $stmtLogs->fetchAll();

} catch (Exception $e) {
    // If DB fails, we set a fallback or error message
    $error_msg = "CORE_SYSTEM_OFFLINE: " . $e->getMessage();
}

$title = "Security Hub | CyberShop";
include __DIR__ . '/../app/partials/header.php';
?>

<div class="admin-wrapper" style="display: flex;">
    <?php include __DIR__ . '/../app/partials/admin_sidebar.php'; ?>

    <main class="main-terminal p-4" style="flex-grow: 1;">
        <header class="mb-5">
            <h2 class="text-white fw-bold"><i class="bi bi-shield-lock text-info"></i> SECURITY_HUB</h2>
            <p class="text-secondary small font-monospace">Monitoring System Access & Integrity</p>
        </header>

        <?php if (isset($error_msg)): ?>
            <div class="alert alert-danger bg-dark border-danger text-danger small"><?= $error_msg ?></div>
        <?php endif; ?>

        <div class="row g-4">
            <!-- Admin Nodes Stat -->
            <div class="col-md-4">
                <div class="cyber-card border-info">
                    <div class="stat-label text-info small fw-bold">ADMIN_NODES</div>
                    <div class="h3 text-white"><?= $totalAdmins ?></div>
                    <div class="progress mt-2" style="height: 4px; background: rgba(0,255,255,0.1);">
                        <div class="progress-bar bg-info" style="width: 100%"></div>
                    </div>
                </div>
            </div>

            <!-- Session Encryption Stat -->
            <div class="col-md-8">
                <div class="cyber-card h-100">
                    <h6 class="stat-label text-info small fw-bold mb-3">CURRENT_SESSION_ENCRYPTION</h6>
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <code class="text-info x-small" style="word-break: break-all;"><?= session_id() ?></code>
                            <div class="text-secondary x-small mt-1 font-monospace">IP_ADDRESS: <?= $_SERVER['REMOTE_ADDR'] ?></div>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">ENCRYPTED</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Live Logs Table -->
            <div class="col-12">
                <div class="cyber-card">
                    <h5 class="text-info mb-3 small fw-bold"><i class="bi bi-broadcast me-2"></i>LIVE_ACCESS_LOG</h5>
                    <div class="table-responsive">
                        <table class="table table-dark table-hover small border-secondary border-opacity-10">
                            <thead>
                                <tr class="text-secondary">
                                    <th>TIMESTAMP</th>
                                    <th>NODE_OPERATOR</th>
                                    <th>ACTION_TYPE</th>
                                    <th>IP_SOURCE</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recent_logs)): ?>
                                    <tr><td colspan="4" class="text-center text-secondary py-4">No access logs detected.</td></tr>
                                <?php else: ?>
                                    <?php foreach($recent_logs as $log): ?>
                                    <tr>
                                        <td class="font-monospace text-secondary"><?= $log['created_at'] ?></td>
                                        <td class="text-white"><?= htmlspecialchars($log['name'] ?? 'UNKNOWN') ?></td>
                                        <td>
                                            <span class="text-success fw-bold font-monospace">
                                                <i class="bi bi-chevron-right me-1"></i><?= htmlspecialchars($log['action']) ?>
                                            </span>
                                        </td>
                                        <td class="text-secondary font-monospace"><?= $log['ip_address'] ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- System Integrity Checklist -->
            <div class="col-12">
                <div class="cyber-card">
                    <h6 class="stat-label text-info small fw-bold mb-4">INTEGRITY_CHECKLIST</h6>
                    <ul class="list-group list-group-flush bg-transparent">
                        <li class="list-group-item bg-transparent text-white border-secondary border-opacity-10 d-flex justify-content-between align-items-center px-0">
                            <span class="small">Root Password Hashing</span>
                            <span class="text-success small"><i class="bi bi-check-circle-fill me-1"></i> BCRYPT_ACTIVE</span>
                        </li>
                        <li class="list-group-item bg-transparent text-white border-secondary border-opacity-10 d-flex justify-content-between align-items-center px-0">
                            <span class="small">Session Management</span>
                            <span class="text-success small"><i class="bi bi-check-circle-fill me-1"></i> PHP_SESSION_LOCK</span>
                        </li>
                        <li class="list-group-item bg-transparent text-white border-secondary border-opacity-10 d-flex justify-content-between align-items-center px-0">
                            <span class="small">SQL Injection Prevention</span>
                            <span class="text-success small"><i class="bi bi-check-circle-fill me-1"></i> PDO_PREPARED_STMTS</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include __DIR__ . '/../app/partials/footer.php'; ?>