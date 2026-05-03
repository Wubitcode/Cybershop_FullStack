<?php
declare(strict_types=1);

// 1. Dependencies
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/auth.php'; 
require_once __DIR__ . '/../app/helpers.php';
require_once __DIR__ . '/../app/repos/users_repo.php';

// 2. Redirect if already logged in (Don't let them see the login screen twice)
if (is_logged_in()) {
    $user = current_user();
    $target = ($user['role'] === 'admin') ? "/admin/dashboard.php" : "/public/index.php";
    header("Location: " . BASE_URL . $target);
    exit;
}

$error = null;

// 3. Process Login Attempt
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = (string)($_POST['password'] ?? ''); // Added fallback empty string

    // Use your repo function to find the user
    $u = user_find_by_email($email);

    if ($u && password_verify($password, $u['password'])) {
        // Authenticate the session
        login_user($u);

        // Redirect based on role (Admin goes to Dashboard, Customer goes to Shop)
        $target = ($u['role'] === 'admin') ? "/admin/dashboard.php" : "/public/index.php";
        header("Location: " . BASE_URL . $target);
        exit;
    } else {
        $error = "ACCESS_DENIED: Invalid Credentials or Node Mismatch.";
    }
}

$title = "CyberShop | Secure Gateway";
include __DIR__ . '/../app/partials/header.php';
?>

<style>
    /* High-contrast Cyber Theme */
    body { background-color: #05070a !important; color: #ffffff; }
    
    .login-card { 
        background: #0d1117; 
        border: 1px solid rgba(175, 23, 99, 0.3); 
        border-radius: 4px; 
        box-shadow: 0 0 20px rgba(175, 23, 99, 0.1);
    }
    
    .form-control { 
        background-color: #05070a !important; 
        border: 1px solid #30363d !important; 
        color: white !important;
        font-family: 'JetBrains Mono', monospace;
    }
    
    .form-control:focus { 
        border-color: #AF1763 !important; 
        box-shadow: 0 0 10px rgba(175, 23, 99, 0.3); 
    }
    
    .btn-cyber { 
        background-color: #AF1763; 
        border: none; 
        color: white; 
        font-weight: bold; 
        letter-spacing: 2px;
        text-transform: uppercase;
    }
    
    .btn-cyber:hover { background-color: #8a124e; color: white; transform: translateY(-1px); }
    .text-magenta { color: #AF1763; }
    .x-small { font-size: 0.75rem; }
</style>

<div class="container d-flex align-items-center justify-content-center" style="min-height: 80vh;">
    <div class="col-md-5 col-lg-4">
        <div class="card login-card p-4">
            <div class="card-body">
                <div class="text-center mb-4">
                    <h2 class="fw-bold text-magenta"><i class="bi bi-shield-lock"></i> CYBERSHOP</h2>
                    <p class="text-secondary x-small text-uppercase fw-bold">Secure Terminal Access_v2.6</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger py-2 x-small border-0 mb-4" style="background: rgba(175, 23, 99, 0.1); color: #ff6666;">
                        <i class="bi bi-exclamation-octagon me-2"></i><?= e($error) ?>
                    </div>
                <?php endif; ?>

                <form method="post">
                    <div class="mb-3">
                        <label class="form-label text-secondary x-small fw-bold">IDENT_EMAIL</label>
                        <input class="form-control" name="email" type="email" required placeholder="name@example.com" value="<?= e($email ?? '') ?>">
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label text-secondary x-small fw-bold">VERIFY_PASS</label>
                        <input class="form-control" name="password" type="password" required placeholder="••••••••">
                    </div>
                    
                    <button class="btn btn-cyber btn-lg w-100 mb-3 py-2" type="submit">INITIALIZE_SESSION</button>
                    
                    <div class="text-center mt-4 border-top border-secondary border-opacity-25 pt-3">
                        <span class="text-secondary x-small">New Operator?</span> 
                        <a href="<?= BASE_URL ?>/public/register.php" class="text-info text-decoration-none x-small ms-1 fw-bold italic">
                            PROVISION_NEW_IDENTITY
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="text-center mt-3">
            <p class="text-secondary italic" style="font-size: 0.6rem; letter-spacing: 1px;">
                ONTARIO_NODE // ENCRYPTION_ACTIVE // SESSION_ID: <?= session_id() ?>
            </p>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../app/partials/footer.php'; ?>