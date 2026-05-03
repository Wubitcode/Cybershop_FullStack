<?php

$is_admin = str_contains($_SERVER['PHP_SELF'], '/admin/');
?>

<?php if (!$is_admin): ?>
    </div> 
    <footer class="py-3 bg-dark text-white-50 mt-auto">
        <div class="container text-center">
            <small>CyberShop &copy; 2026 | Secure Operations</small>
        </div>
    </footer>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/app.js"></script>

</body>
</html>