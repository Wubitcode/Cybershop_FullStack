<?php
$current_page = basename($_SERVER['PHP_SELF']);
$uri_path = $_SERVER['REQUEST_URI'];
?>

<nav class="sidebar" style="width: 260px; min-width: 260px; height: 100vh; position: sticky; top: 0; z-index: 9999; background: #050505; border-right: 1px solid rgba(0, 255, 255, 0.1);">
    <div class="sidebar-brand p-4">
        <span class="text-info fw-bold"><i class="bi bi-terminal-fill me-2"></i>NODE_01_ADMIN</span>
    </div>
    
    <div class="nav-links-container d-flex flex-column h-100">
        <!-- Dashboard -->
        <a href="<?= BASE_URL ?>/admin/dashboard.php" 
           class="nav-link-custom <?= ($current_page == 'dashboard.php') ? 'active' : '' ?>">
            <i class="bi bi-grid-1x2 me-2"></i> DASHBOARD
        </a>
        
        <!-- Inventory -->
        <a href="<?= BASE_URL ?>/admin/products/index.php" 
           class="nav-link-custom <?= (strpos($uri_path, 'products') !== false) ? 'active' : '' ?>">
            <i class="bi bi-cpu me-2"></i> INVENTORY
        </a>
        
        <!-- Security -->
        <a href="<?= BASE_URL ?>/admin/security.php" 
           class="nav-link-custom <?= ($current_page == 'security.php') ? 'active' : '' ?>">
            <i class="bi bi-shield-lock me-2"></i> SECURITY_HUB
        </a>

        <!-- Settings (The Missing Link) -->
        <a href="<?= BASE_URL ?>/admin/settings.php" 
           class="nav-link-custom <?= ($current_page == 'settings.php') ? 'active' : '' ?>">
            <i class="bi bi-sliders me-2"></i> GLOBAL_SETTINGS
        </a>

        <!-- Deauthorize (Bottom) -->
        <div class="mt-auto border-top border-secondary border-opacity-10">
            <a href="<?= BASE_URL ?>/logout.php" class="nav-link-custom text-danger fw-bold py-3">
                <i class="bi bi-lock-fill me-2"></i> DEAUTHORIZE
            </a>
        </div>
    </div>
</nav>