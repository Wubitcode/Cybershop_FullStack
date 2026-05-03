<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/helpers.php';
require_once __DIR__ . '/../app/repos/orders_repo.php';

// Force login
require_login(BASE_URL . '/public/login.php');

$user = current_user();

/** 
 * SYNC POINT: 
 * We must use 'user_id' as defined in your login_user() function.
 */
$currentUserId = (int)($user['user_id'] ?? 0);

// Use the function name that matches your repository
$orders = orders_by_user($currentUserId); 

$title = "Mission History | CyberShop";
include __DIR__ . '/../app/partials/header.php';
?>

<style>
    body { background-color: #05070a !important; color: #e6edf3 !important; }
    .terminal-card { background: #0d1117; border: 1px solid rgba(0, 210, 255, 0.2); border-radius: 12px; overflow: hidden; }
    .table-cyber { color: #e6edf3 !important; border-color: rgba(255,255,255,0.05) !important; }
    .table-cyber thead { background: rgba(0, 210, 255, 0.05); color: #00d2ff; font-family: 'Consolas', monospace; }
    .status-badge { border: 1px solid #00d2ff; padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; background: rgba(0, 210, 255, 0.1); color: #00d2ff; }
    .text-cyan { color: #00d2ff !important; }
</style>

<div class="container py-5">
    <div class="mb-4 border-bottom border-secondary pb-3">
        <h6 class="text-info small fw-bold">SECURE_STORE > ACQUISITION_LOGS</h6>
        <h1 class="h2 text-white fw-bold">My Orders</h1>
        <small class="text-muted">OPERATOR_ID: <?= $currentUserId ?></small>
    </div>

    <?php if (empty($orders)): ?>
      <div class="terminal-card p-5 text-center">
        <i class="bi bi-shield-slash text-muted display-1 mb-3"></i>
        <h4 class="text-white">No Records Found</h4>
        <p class="text-secondary">No orders associated with this account in the database.</p>
        <a href="index.php" class="btn btn-outline-info mt-3">RETURN TO REPOSITORY</a>
      </div>
    <?php else: ?>
      <div class="terminal-card shadow-lg">
        <div class="table-responsive">
          <table class="table table-cyber align-middle mb-0">
            <thead>
              <tr>
                <th class="ps-4 py-3">ID</th>
                <th>STATUS</th>
                <th>TOTAL</th>
                <th>TIMESTAMP</th>
                <th class="pe-4 text-end">ACTION</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($orders as $o): ?>
                <tr>
                  <td class="ps-4 font-monospace text-info">#<?= e((string)$o['id']) ?></td>
                  <td><span class="status-badge"><?= strtoupper(e($o['status'])) ?></span></td>
                  <td class="fw-bold text-cyan"><?= money((float)$o['total']) ?></td>
                  <td class="text-secondary small"><?= e($o['createdAt']) ?></td>
                  <td class="pe-4 text-end">
                    <a class="btn btn-sm btn-outline-info" href="order_details.php?id=<?= (int)$o['id'] ?>">
                      INSPECT
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../app/partials/footer.php'; ?>