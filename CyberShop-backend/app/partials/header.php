<?php
// Ensure session is active for cart and user status
if (session_status() === PHP_SESSION_NONE) { session_start(); }

require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../helpers.php';

// 1. Detect if we are in the admin section to hide the public nav
$is_admin_page = (strpos($_SERVER['PHP_SELF'], '/admin/') !== false);

// 2. Get data for the dynamic navbar
$currentUser = current_user(); 
$cartCount = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title ?? "CyberShop") ?></title>
  
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
  <!--  Link to  Admin Specific Theme -->
  <?php if ($is_admin_page): ?>
  <link rel="stylesheet" href="<?= BASE_URL ?>/admin/assets/css/admin-theme.css">
 <?php endif; ?>
  <style>
    /* Dark Theme Overrides for Public Side */
    body.cyber-bg { background-color: #05070a !important; color: #e1e1e1; }
    
    .navbar-cyber { 
        background-color: #0d1117 !important; 
        border-bottom: 1px solid rgba(0, 210, 255, 0.3); 
        padding: 0.8rem 0;
    }
    
    .nav-link-cyber { 
        color: #8b949e !important; 
        font-weight: 600; 
        font-size: 0.75rem; 
        text-transform: uppercase; 
        letter-spacing: 1px;
    }
    
    .nav-link-cyber:hover, .nav-link-cyber.active { 
        color: #00d2ff !important; 
    }

    /* Logic to hide standard nav on Admin pages */
    <?php if ($is_admin_page): ?>
      body { 
        background-color: #000 !important; 
        color: #e1e1e1 !important; 
        margin: 0; 
        padding: 0; 
      }
      .standard-nav { display: none !important; }
    <?php endif; ?>
  </style>
</head>
<body class="<?= $is_admin_page ? '' : 'cyber-bg' ?>">

<?php 
// ONLY show this navbar if we are NOT in an admin folder
if (!$is_admin_page): 
?>
<nav class="navbar navbar-expand-lg navbar-dark navbar-cyber standard-nav">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center fw-bold text-info" href="<?= BASE_URL ?>/public/index.php">
      <img src="<?= BASE_URL ?>/assets/images/cyberShoplogo.png" alt="Logo" height="30" class="me-2">
      <span style="letter-spacing: 2px;">CYBER_SHOP</span>
    </a>

    <button class="navbar-toggler border-info" type="button" data-bs-toggle="collapse" data-bs-target="#cyberNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="cyberNav">
      <ul class="navbar-nav ms-auto align-items-center">
        
        <li class="nav-item">
          <a class="nav-link nav-link-cyber" href="<?= BASE_URL ?>/public/index.php">Market</a>
        </li>
        
        <li class="nav-item">
          <a class="nav-link nav-link-cyber position-relative px-3" href="<?= BASE_URL ?>/public/cart.php">
            <i class="bi bi-cart3 fs-5"></i>
            <?php if ($cartCount > 0): ?>
              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-info text-dark" style="font-size: 0.6rem;">
                <?= $cartCount ?>
              </span>
            <?php endif; ?>
          </a>
        </li>

        <div class="vr mx-3 bg-secondary opacity-25 d-none d-lg-block" style="height: 20px;"></div>

        <?php if ($currentUser): ?>
          <?php if ($currentUser['role'] === 'admin'): ?>
            <li class="nav-item">
                <a class="nav-link nav-link-cyber text-info border border-info border-opacity-25 rounded px-2 me-2" href="<?= BASE_URL ?>/admin/dashboard.php">
                    <i class="bi bi-shield-lock me-1"></i> ADMIN_PANEL
                </a>
            </li>
          <?php endif; ?>
          
          <li class="nav-item">
            <a class="nav-link nav-link-cyber" href="<?= BASE_URL ?>/public/logout.php">
                <i class="bi bi-power me-1"></i> LOGOUT
            </a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link nav-link-cyber" href="<?= BASE_URL ?>/public/login.php">Login</a>
          </li>
          <li class="nav-item ms-lg-2">
            <a class="btn btn-outline-info btn-sm fw-bold px-3" href="<?= BASE_URL ?>/public/register.php">REGISTER</a>
          </li>
        <?php endif; ?>

      </ul>
    </div>
  </div>
</nav>

<div class="container py-4">
<?php endif; ?>