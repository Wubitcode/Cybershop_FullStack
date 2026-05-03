<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/helpers.php';

// Ensure only admins can access the system config
require_admin();

$user = current_user();
$title = "System Configuration | CyberShop";

include __DIR__ . '/../app/partials/header.php';
?>

<style>
    body, html { margin: 0; padding: 0; background-color: #000 !important; }
    .config-label { font-size: 0.7rem; color: var(--accent-cyan); letter-spacing: 1px; font-weight: bold; }
    .form-control-cyber { 
        background: rgba(255,255,255,0.03); 
        border: 1px solid rgba(255,255,255,0.1); 
        color: #fff; 
        font-family: 'Consolas', monospace;
    }
    .form-control-cyber:focus { 
        background: rgba(255,255,255,0.05); 
        border-color: var(--accent-cyan); 
        box-shadow: 0 0 10px rgba(0, 210, 255, 0.2);
        color: #fff;
    }
</style>

<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin-theme.css">

<div class="admin-wrapper">
    <?php include __DIR__ . '/../app/partials/admin_sidebar.php'; ?>

    <main class="main-terminal">
        <header class="mb-4">
            <h6 class="text-info small fw-bold mb-1">SYSTEM_LAYER > CONFIGURATION</h6>
            <h4 class="fw-bold mb-0 text-white text-glow"><i class="bi bi-sliders me-2"></i>GLOBAL_SETTINGS</h4>
        </header>

        <div class="row g-4">
            <!-- Left Column: Operator Profile -->
            <div class="col-lg-6">
                <div class="cyber-card">
                    <h5 class="fw-bold mb-4 text-white border-bottom border-secondary border-opacity-25 pb-3">
                        <i class="bi bi-person-badge me-2"></i>OPERATOR_PROFILE
                    </h5>
                    <form action="settings_update.php" method="POST">
                        <div class="mb-3">
                            <label class="config-label d-block mb-2">FULL_NAME_STRING</label>
                            <input type="text" name="name" class="form-control form-control-cyber" value="<?= e($user['fullName']) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="config-label d-block mb-2">AUTH_EMAIL_NODE</label>
                            <input type="email" name="email" class="form-control form-control-cyber" value="<?= e($user['email']) ?>">
                        </div>
                        <div class="mb-4">
                            <label class="config-label d-block mb-2">ACCESS_LEVEL</label>
                            <input type="text" class="form-control form-control-cyber opacity-50" value="<?= strtoupper($user['role']) ?>" readonly>
                            <small class="text-muted mt-1 d-block">Permissions are locked by Root Administrator.</small>
                        </div>
                        <button type="submit" class="btn btn-cyan btn-sm px-4 fw-bold">UPDATE_IDENTITY</button>
                    </form>
                </div>
            </div>

            <!-- Right Column: System Security -->
            <div class="col-lg-6">
                <div class="cyber-card mb-4">
                    <h5 class="fw-bold mb-4 text-white border-bottom border-secondary border-opacity-25 pb-3">
                        <i class="bi bi-shield-lock me-2"></i>SECURITY_PARAMETERS
                    </h5>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input bg-dark border-secondary" type="checkbox" id="twoFactor" checked>
                        <label class="form-check-label text-white small" for="twoFactor">Enable Multi-Factor Authentication</label>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input bg-dark border-secondary" type="checkbox" id="ipLock" checked>
                        <label class="form-check-label text-white small" for="ipLock">Strict IP Address Lockdown</label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input bg-dark border-secondary" type="checkbox" id="debugMode">
                        <label class="form-check-label text-white small" for="debugMode">Developer Verbose Logging</label>
                    </div>
                </div>

                <div class="cyber-card border-danger border-opacity-25">
                    <h5 class="fw-bold mb-3 text-danger"><i class="bi bi-exclamation-triangle me-2"></i>CRITICAL_ZONE</h5>
                    <p class="text-dim small">Permanently clear all cached session tokens across the entire network.</p>
                    <button class="btn btn-outline-danger btn-sm fw-bold">FLUSH_ALL_SESSIONS</button>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include __DIR__ . '/../app/partials/footer.php'; ?>