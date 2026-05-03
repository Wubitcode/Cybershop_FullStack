<?php
declare(strict_types=1);
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/helpers.php';
require_once __DIR__ . '/../app/repos/users_repo.php';

// Redirect if already authenticated
if (is_logged_in()) {
    header("Location: " . BASE_URL . "/public/index.php");
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['fullName'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = (string)($_POST['password'] ?? '');

    if ($fullName === '' || $email === '' || $password === '') {
        $error = "CRITICAL_ERROR: All data fields required for uplink.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "INVALID_PROTOCOL: Email format rejected.";
    } elseif (strlen($password) < 6) {
        $error = "SECURITY_BREACH: Password must be at least 6 characters.";
    } elseif (user_find_by_email($email)) {
        $error = "IDENTITY_CONFLICT: Email already registered in Ontario Node.";
    } else {
        // Create user with default 'user' role
        $id = user_create($fullName, $email, $password, 'user');
        $u = user_find($id);
        login_user($u);
        header("Location: " . BASE_URL . "/public/index.php?status=provisioned");
        exit;
    }
}

$title = "Identity Provisioning | CyberShop";
include __DIR__ . '/../app/partials/header.php';
?>

<div class="row justify-content-center mt-5">
  <div class="col-md-6 col-lg-4">
    <div class="cyber-card shadow-lg border-info border-opacity-25">
      <div class="card-body p-4">
        
        <!-- Header Section -->
        <div class="text-center mb-4">
            <h6 class="text-info small fw-bold mb-1" style="letter-spacing: 2px;">SECURE_UPLINK</h6>
            <h2 class="text-white fw-bold italic">CREATE_IDENTITY</h2>
            <hr class="border-info border-opacity-25 w-25 mx-auto">
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger bg-dark border-danger text-danger x-small py-2 mb-4">
                <i class="bi bi-exclamation-triangle me-2"></i><?= e($error) ?>
            </div>
        <?php endif; ?>

        <form method="post">
          <div class="mb-3">
            <label class="form-label text-secondary x-small fw-bold">LEGAL_NAME</label>
            <input class="form-control bg-dark text-white border-secondary border-opacity-50" 
                   name="fullName" 
                   placeholder="Enter full name..." 
                   value="<?= e($fullName ?? '') ?>" required>
          </div>

          <div class="mb-3">
            <label class="form-label text-secondary x-small fw-bold">EMAIL_COMM</label>
            <input class="form-control bg-dark text-white border-secondary border-opacity-50" 
                   name="email" 
                   type="email" 
                   placeholder="name@domain.com"
                   value="<?= e($email ?? '') ?>" required>
          </div>

          <div class="mb-4">
            <label class="form-label text-secondary x-small fw-bold">ACCESS_KEY</label>
            <input class="form-control bg-dark text-white border-secondary border-opacity-50" 
                   name="password" 
                   type="password" 
                   placeholder="Min 6 characters"
                   minlength="6" required>
          </div>

          <button class="btn btn-info w-100 fw-bold py-2 mb-3 shadow-glow" type="submit">
            INITIALIZE_PROVISIONING
          </button>

          <div class="text-center mt-4 border-top border-secondary border-opacity-25 pt-3">
            <span class="text-secondary small">Identity already exists?</span>
            <a href="<?= BASE_URL ?>/public/login.php" class="text-info small ms-1 text-decoration-none fw-bold">
                BYPASS_TO_LOGIN
            </a>
          </div>
        </form>
      </div>
    </div>
    
    <!-- Footer Tag -->
    <div class="text-center mt-3">
        <p class="text-secondary" style="font-size: 0.6rem; letter-spacing: 1px;">
            ONTARIO_NODE // ENCRYPTION_ACTIVE // AES-256
        </p>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../app/partials/footer.php'; ?>