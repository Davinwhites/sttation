<?php
// client/product.php
require_once '../includes/header.php';

if (!isset($_GET['id'])) {
    redirect('shop.php');
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT p.*, c.category_name, bc.class_name, s.subject_name 
                       FROM products p 
                       LEFT JOIN categories c ON p.category_id = c.id
                       LEFT JOIN book_classes bc ON p.class_id = bc.id
                       LEFT JOIN subjects s ON p.subject_id = s.id
                       WHERE p.id = ? AND p.status = 'Active'");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    echo "<div class='alert alert-danger'>Product not found.</div>";
    require_once '../includes/footer.php';
    exit();
}

// Fetch related products (same category)
$related_stmt = $pdo->prepare("SELECT * FROM products WHERE category_id = ? AND id != ? AND status = 'Active' LIMIT 4");
$related_stmt->execute([$product['category_id'], $id]);
$related_products = $related_stmt->fetchAll();
?>

<div class="row mb-5">
    <!-- Product Image -->
    <div class="col-md-5 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center p-0">
                <?php if($product['image']): ?>
                    <img src="../assets/images/products/<?php echo $product['image']; ?>" class="img-fluid rounded" alt="<?php echo htmlspecialchars($product['product_name']); ?>" style="max-height: 400px; object-fit: contain;">
                <?php else: ?>
                    <div class="bg-light d-flex align-items-center justify-content-center rounded" style="height: 400px;">
                        <i class="fas fa-image fa-6x text-muted"></i>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Product Details -->
    <div class="col-md-7">
        <h2 class="fw-bold mb-2"><?php echo htmlspecialchars($product['product_name']); ?></h2>
        <p class="text-muted mb-3">Category: <a href="shop.php?category=<?php echo $product['category_id']; ?>"><?php echo htmlspecialchars($product['category_name']); ?></a></p>
        
        <h3 class="text-primary fw-bold mb-3" id="totalPriceDisplay"><?php echo format_price($product['retail_price']); ?></h3>
        <p class="text-muted d-none" id="basePrice"><?php echo $product['retail_price']; ?></p>
        
        <?php if($product['wholesale_price'] > 0): ?>
            <p class="text-muted"><small>Wholesale Price: <?php echo format_price($product['wholesale_price']); ?></small></p>
        <?php endif; ?>

        <div class="mb-4">
            <?php if($product['stock'] > 0): ?>
                <span class="badge bg-success mb-2">In Stock</span>
            <?php else: ?>
                <span class="badge bg-danger mb-2">Out of Stock</span>
            <?php endif; ?>
        </div>

        <div class="card border-0 bg-light mb-4">
            <div class="card-body">
                <p class="mb-0"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            </div>
        </div>

        <?php if($product['class_name'] || $product['subject_name']): ?>
        <ul class="list-group mb-4">
            <?php if($product['class_name']): ?>
                <li class="list-group-item"><strong>Class:</strong> <?php echo htmlspecialchars($product['class_name']); ?></li>
            <?php endif; ?>
            <?php if($product['subject_name']): ?>
                <li class="list-group-item"><strong>Subject:</strong> <?php echo htmlspecialchars($product['subject_name']); ?></li>
            <?php endif; ?>
        </ul>
        <?php endif; ?>

        <?php if($product['stock'] > 0): ?>
        <form action="cart.php" method="POST" class="d-flex align-items-center mb-4">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
            
            <div class="input-group me-3" style="width: 130px;">
                <span class="input-group-text">Qty</span>
                <input type="number" name="quantity" id="quantityInput" class="form-control text-center" value="1" min="1" max="<?php echo $product['stock']; ?>" onchange="updateTotal()">
            </div>
            
            <button type="submit" class="btn btn-primary btn-lg flex-grow-1"><i class="fas fa-cart-plus"></i> Add to Cart</button>
        </form>
        <?php endif; ?>

        <a href="<?php echo whatsapp_order_link($product['product_name'], $product['retail_price']); ?>"
           target="_blank" rel="noopener"
           class="btn btn-lg w-100 text-white mb-4" style="background-color:#25D366;">
            <i class="fab fa-whatsapp"></i> Order via WhatsApp
        </a>
    </div>
</div>

<!-- Related Products -->
<?php if(count($related_products) > 0): ?>
<hr>
<h3 class="mb-4 mt-5 text-center">Related Products</h3>
<div class="row">
    <?php foreach($related_products as $prod): ?>
    <div class="col-sm-6 col-md-3 mb-4">
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
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<script>
function updateTotal() {
    var basePrice = parseFloat(document.getElementById('basePrice').innerText);
    var quantity = parseInt(document.getElementById('quantityInput').value);
    
    // Safety checks
    var maxStock = parseInt(document.getElementById('quantityInput').getAttribute('max'));
    if (quantity > maxStock) {
        quantity = maxStock;
        document.getElementById('quantityInput').value = maxStock;
    } else if (quantity < 1) {
        quantity = 1;
        document.getElementById('quantityInput').value = 1;
    }
    
    var total = basePrice * quantity;
    // Format to currency UGX
    document.getElementById('totalPriceDisplay').innerText = 'UGX ' + total.toLocaleString();
}
</script>

<?php
require_once '../includes/footer.php';
?>
