</main>

<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-3">
                <h5>Deco&Mat Stationers</h5>
                <p>Your one-stop shop for all school and office supplies. We offer the best prices for wholesale and retail.</p>
            </div>
            <div class="col-md-4 mb-3">
                <h5>Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="<?php echo BASE_URL; ?>/client/home.php" class="text-white text-decoration-none">Home</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/client/shop.php" class="text-white text-decoration-none">Shop</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/client/cart.php" class="text-white text-decoration-none">Cart</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/admin/login.php" class="text-white text-decoration-none">Admin Login</a></li>
                </ul>
            </div>
            <div class="col-md-4 mb-3">
                <h5>Contact Us</h5>
                <p><i class="fas fa-map-marker-alt"></i> Fortportal, Uganda</p>
                <p><i class="fas fa-phone"></i> +256 771018311</p>
                <p><i class="fab fa-whatsapp"></i> <a href="https://wa.me/<?php echo WHATSAPP_ORDER_NUMBER; ?>" target="_blank" rel="noopener" class="text-white text-decoration-none">Order on WhatsApp: 0772 616 006</a></p>
                <p><i class="fas fa-envelope"></i> support@decomatstationers.com</p>
                <p><a href="<?php echo BASE_URL; ?>/privacy-policy.html" class="text-white text-decoration-none"><i class="fas fa-shield-alt"></i> Privacy Policy</a></p>
            </div>
        </div>
        <hr class="mt-2 mb-3 border-secondary">
        <div class="text-center">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> Deco&Mat Stationers. All rights reserved.Made by Ayesiga Davin</p>
        </div>
    </div>
</footer>

<!-- Bootstrap 5 JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Registers the PWA service worker so the site can be "installed" to a
// phone's home screen. Safe to leave in even before HTTPS is live -
// browsers simply won't register it over plain HTTP.
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
        navigator.serviceWorker.register('<?php echo BASE_URL; ?>/service-worker.js').catch(function () {
            // Silently ignore - e.g. running over http:// during local dev.
        });
    });
}
</script>
</body>
</html>
