<?php
// client/home.php
require_once '../includes/header.php';

// Fetch some categories for display
$categories = $pdo->query("SELECT * FROM categories LIMIT 8")->fetchAll();

// Fetch latest products
$latest_products = $pdo->query("SELECT * FROM products WHERE status='Active' ORDER BY id DESC LIMIT 8")->fetchAll();
?>

<!-- Hero Section -->
<div class="hero-section text-center">
    <h1 class="display-4 fw-bold">Welcome to Deco&Mat Stationers</h1>
    <p class="lead">Your one-stop shop for school and office supplies at the best prices.</p>
    <a href="shop.php" class="btn btn-light btn-lg mt-3 fw-bold text-primary">Shop Now</a>
</div>

<!-- Get Our App -->
<div class="card border-0 shadow-sm mb-5" style="background: linear-gradient(135deg, #1E5C3A, #2E7D4F);">
    <div class="card-body p-4 text-center text-white">
        <i class="fas fa-mobile-alt fa-2x mb-2"></i>
        <h3 class="fw-bold">Get the Deco&Mat Stationers App</h3>
        <p class="mb-3">Shop faster, track your orders, and get order updates right from your phone.</p>
        <a href="<?php echo PLAY_STORE_URL; ?>" target="_blank" rel="noopener" class="btn btn-light fw-bold me-2 mb-2">
            <i class="fab fa-google-play"></i> Get it on Google Play
        </a>
        <a href="<?php echo APK_DOWNLOAD_URL; ?>" class="btn btn-outline-light fw-bold mb-2">
            <i class="fas fa-download"></i> Download APK directly
        </a>
        <p class="small mb-0 mt-2 opacity-75">Or tap "Add to Home Screen" from your phone browser's menu to install this website as an app.</p>
    </div>
</div>

<!-- Categories -->
<h3 class="mb-4 text-center">Shop by Category</h3>
<div class="row mb-5">
    <?php foreach($categories as $cat): ?>
    <div class="col-6 col-md-3 mb-3">
        <a href="shop.php?category=<?php echo $cat['id']; ?>" class="text-decoration-none text-dark">
            <div class="card h-100 text-center product-card border-0 shadow-sm">
                <div class="card-body">
                    <?php if (!empty($cat['image'])): ?>
                        <img src="../assets/images/categories/<?php echo $cat['image']; ?>" class="mb-3" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                    <?php else: ?>
                        <i class="fas fa-folder-open fa-3x text-primary mb-3"></i>
                    <?php endif; ?>
                    <h5 class="card-title"><?php echo htmlspecialchars($cat['category_name']); ?></h5>
                </div>
            </div>
        </a>
    </div>
    <?php endforeach; ?>
</div>

<!-- Latest Products -->
<h3 class="mb-4 text-center">Latest Arrivals</h3>
<div class="row">
    <?php foreach($latest_products as $prod): ?>
    <div class="col-6 col-md-4 col-lg-3 mb-4">
        <div class="card h-100 product-card shadow-sm border-0">
            <?php if($prod['image']): ?>
                <img src="../assets/images/products/<?php echo $prod['image']; ?>" class="card-img-top product-image" alt="<?php echo htmlspecialchars($prod['product_name']); ?>">
            <?php else: ?>
                <div class="card-img-top product-image d-flex align-items-center justify-content-center bg-light">
                    <i class="fas fa-image fa-4x text-muted"></i>
                </div>
            <?php endif; ?>
            <div class="card-body d-flex flex-column">
                <h5 class="card-title fs-6"><?php echo htmlspecialchars($prod['product_name']); ?></h5>
                <p class="card-text text-primary fw-bold mb-3"><?php echo format_price($prod['retail_price']); ?></p>
                <div class="mt-auto">
                    <a href="product.php?id=<?php echo $prod['id']; ?>" class="btn btn-outline-primary w-100 mb-2">View Details</a>
                    <form action="cart.php" method="POST" class="mb-2">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?php echo $prod['id']; ?>">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-cart-plus"></i> Add to Cart</button>
                    </form>
                    <a href="<?php echo whatsapp_order_link($prod['product_name'], $prod['retail_price']); ?>"
                       target="_blank" rel="noopener"
                       class="btn w-100 text-white" style="background-color:#25D366;">
                        <i class="fab fa-whatsapp"></i> Order via WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php
require_once '../includes/footer.php';
?>
