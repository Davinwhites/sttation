<?php
// client/cart.php
require_once '../includes/header.php';

if (!is_customer_logged_in()) {
    $_SESSION['redirect_after_login'] = 'cart.php';
    redirect('login.php');
}

$customer_id = $_SESSION['customer_id'];

// Handle Add/Update/Remove
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action == 'add') {
        $product_id = $_POST['product_id'];
        $quantity = (int)$_POST['quantity'];
        
        // check if already in cart
        $check = $pdo->prepare("SELECT id, quantity FROM cart WHERE customer_id = ? AND product_id = ?");
        $check->execute([$customer_id, $product_id]);
        $existing = $check->fetch();
        
        if ($existing) {
            $new_qty = $existing['quantity'] + $quantity;
            $update = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
            $update->execute([$new_qty, $existing['id']]);
        } else {
            $insert = $pdo->prepare("INSERT INTO cart (customer_id, product_id, quantity) VALUES (?, ?, ?)");
            $insert->execute([$customer_id, $product_id, $quantity]);
        }
        redirect('cart.php');
        
    } elseif ($action == 'update') {
        $cart_id = $_POST['cart_id'];
        $quantity = (int)$_POST['quantity'];
        
        if ($quantity > 0) {
            $update = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND customer_id = ?");
            $update->execute([$quantity, $cart_id, $customer_id]);
        } else {
            $delete = $pdo->prepare("DELETE FROM cart WHERE id = ? AND customer_id = ?");
            $delete->execute([$cart_id, $customer_id]);
        }
        redirect('cart.php');
        
    } elseif ($action == 'remove') {
        $cart_id = $_POST['cart_id'];
        $delete = $pdo->prepare("DELETE FROM cart WHERE id = ? AND customer_id = ?");
        $delete->execute([$cart_id, $customer_id]);
        redirect('cart.php');
    }
}

// Fetch Cart Items
$stmt = $pdo->prepare("SELECT c.id as cart_id, c.quantity, p.id as product_id, p.product_name, p.retail_price, p.image, p.stock 
                       FROM cart c 
                       JOIN products p ON c.product_id = p.id 
                       WHERE c.customer_id = ?");
$stmt->execute([$customer_id]);
$cart_items = $stmt->fetchAll();

$total_amount = 0;
?>

<div class="row">
    <div class="col-12">
        <h3 class="mb-4">Shopping Cart</h3>
    </div>
</div>

<?php if(count($cart_items) > 0): ?>
<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3">Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($cart_items as $item): 
                                $subtotal = $item['retail_price'] * $item['quantity'];
                                $total_amount += $subtotal;
                            ?>
                            <tr>
                                <td class="ps-3">
                                    <div class="d-flex align-items-center">
                                        <?php if($item['image']): ?>
                                            <img src="../assets/images/products/<?php echo $item['image']; ?>" width="60" class="img-thumbnail me-3">
                                        <?php else: ?>
                                            <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                        <a href="product.php?id=<?php echo $item['product_id']; ?>" class="text-decoration-none text-dark fw-bold">
                                            <?php echo htmlspecialchars($item['product_name']); ?>
                                        </a>
                                    </div>
                                </td>
                                <td><?php echo format_price($item['retail_price']); ?></td>
                                <td style="width: 150px;">
                                    <form action="cart.php" method="POST" class="d-flex">
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                        <input type="number" name="quantity" class="form-control form-control-sm text-center me-1" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock']; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-primary"><i class="fas fa-sync-alt"></i></button>
                                    </form>
                                </td>
                                <td><strong class="text-primary"><?php echo format_price($subtotal); ?></strong></td>
                                <td>
                                    <form action="cart.php" method="POST">
                                        <input type="hidden" name="action" value="remove">
                                        <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h5 class="mb-0">Order Summary</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <span>Subtotal</span>
                    <strong><?php echo format_price($total_amount); ?></strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-4">
                    <span class="fs-5">Total</span>
                    <strong class="fs-5 text-primary"><?php echo format_price($total_amount); ?></strong>
                </div>
                <a href="checkout.php" class="btn btn-primary w-100 btn-lg py-3 fw-bold">Proceed to Checkout</a>
                <a href="shop.php" class="btn btn-outline-secondary w-100 mt-2">Continue Shopping</a>
            </div>
        </div>
    </div>
</div>
<?php else: ?>
<div class="text-center py-5">
    <i class="fas fa-shopping-cart fa-5x text-muted mb-4"></i>
    <h2>Your cart is empty</h2>
    <p class="text-muted mb-4">Looks like you haven't added anything to your cart yet.</p>
    <a href="shop.php" class="btn btn-primary btn-lg px-5">Start Shopping</a>
</div>
<?php endif; ?>

<?php
require_once '../includes/footer.php';
?>
