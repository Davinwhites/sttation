<?php
// client/shop.php
require_once '../includes/header.php';

// Fetch Categories for sidebar
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

// Build query for products based on filters
$query = "SELECT * FROM products WHERE status = 'Active'";
$params = [];

if (isset($_GET['q']) && !empty($_GET['q'])) {
    $query .= " AND product_name LIKE ?";
    $params[] = '%' . $_GET['q'] . '%';
}

if (isset($_GET['category']) && !empty($_GET['category'])) {
    $query .= " AND category_id = ?";
    $params[] = $_GET['category'];
}

$query .= " ORDER BY id DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>

<div class="row">
    <!-- Sidebar -->
    <div class="col-md-3 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Categories</h5>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <a href="shop.php" class="text-decoration-none text-dark <?php echo (!isset($_GET['category']) ? 'fw-bold text-primary' : ''); ?>">All Products</a>
                </li>
                <?php foreach($categories as $cat): ?>
                <li class="list-group-item d-flex align-items-center">
                    <?php if (!empty($cat['image'])): ?>
                        <img src="../assets/images/categories/<?php echo $cat['image']; ?>" class="me-2 rounded-circle" style="width: 24px; height: 24px; object-fit: cover;">
                    <?php else: ?>
                        <i class="fas fa-folder text-muted me-2" style="width: 24px; text-align: center;"></i>
                    <?php endif; ?>
                    <a href="shop.php?category=<?php echo $cat['id']; ?>" class="text-decoration-none text-dark <?php echo (isset($_GET['category']) && $_GET['category'] == $cat['id'] ? 'fw-bold text-primary' : ''); ?>">
                        <?php echo htmlspecialchars($cat['category_name']); ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="col-md-9">
        <h3 class="mb-4">Shop Products</h3>
        
        <?php if(isset($_GET['q']) && !empty($_GET['q'])): ?>
            <p class="text-muted">Search results for: "<strong><?php echo htmlspecialchars($_GET['q']); ?></strong>"</p>
        <?php endif; ?>

        <div class="row">
            <?php foreach($products as $prod): ?>
            <div class="col-6 col-lg-4 mb-4">
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
            
            <?php if(count($products) == 0): ?>
            <div class="col-12 text-center py-5">
                <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                <h4>No products found</h4>
                <p class="text-muted">Try adjusting your filters or search query.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php';
?>
