<?php
require_once __DIR__ . '/../app/config.php';
session_start();

// If they aren't even logged in, kick them out
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pin = $_POST['pin'] ?? '';
    
    // For your Demo/Capstone, use a "System Override" PIN: 123456
    if ($pin === "123456") {
        $_SESSION['mfa_verified'] = true;
        unset($_SESSION['mfa_pending']);
        header("Location: index.php");
        exit;
    } else {
        $error = "ACCESS_DENIED: INVALID_SECURE_TOKEN";
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card bg-dark border-info p-4 text-center">
                <h4 class="text-info font-monospace mb-4">MFA_REQUIRED</h4>
                <p class="text-secondary small">Enter the 6-digit authorization token sent to your secure device.</p>
                
                <?php if($error): ?>
                    <div class="alert alert-danger small py-1"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST">
                    <input type="text" name="pin" class="form-control bg-black border-info text-info text-center fs-2 mb-4" 
                           placeholder="000000" maxlength="6" autofocus required>
                    <button class="btn btn-info w-100 fw-bold">VERIFY_IDENTITY</button>
                </form>
            </div>
        </div>
    </div>
</div>