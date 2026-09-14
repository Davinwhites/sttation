<?php
// client/checkout.php
require_once '../includes/header.php';

if (!is_customer_logged_in()) {
    $_SESSION['redirect_after_login'] = 'checkout.php';
    redirect('login.php');
}

$customer_id = $_SESSION['customer_id'];

// Fetch Cart
$stmt = $pdo->prepare("SELECT c.quantity, p.id as product_id, p.product_name, p.retail_price, p.stock 
                       FROM cart c 
                       JOIN products p ON c.product_id = p.id 
                       WHERE c.customer_id = ?");
$stmt->execute([$customer_id]);
$cart_items = $stmt->fetchAll();

if (count($cart_items) == 0) {
    redirect('shop.php');
}

$total_amount = 0;
foreach($cart_items as $item) {
    $total_amount += $item['retail_price'] * $item['quantity'];
}

// Fetch Payment Settings
$payment_settings = $pdo->query("SELECT * FROM payment_settings LIMIT 1")->fetch();

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_csrf();
    $payment_method = $_POST['payment_method'] ?? '';
    $pickup_date = $_POST['pickup_date'] ?? '';
    if (!in_array($payment_method, ['Pickup', 'Online'], true) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $pickup_date) || $pickup_date < date('Y-m-d')) {
        $error = 'Please choose a valid payment method and pickup date.';
    }
    
    // Generate Order Number
    $order_number = 'ORD-' . date('Ymd') . '-' . rand(1000, 9999);
    
    $proof_image = null;
    $payment_status = 'Pending';
    
    if ($payment_method == 'Online') {
        if (isset($_FILES['proof_image']) && $_FILES['proof_image']['error'] == 0) {
            $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
            $mime = (new finfo(FILEINFO_MIME_TYPE))->file($_FILES['proof_image']['tmp_name']);
            if ($_FILES['proof_image']['size'] > 5 * 1024 * 1024) {
                $error = 'Payment proof must be 5 MB or smaller.';
            } elseif (isset($allowed[$mime])) {
                $proof_image = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
                if (!move_uploaded_file($_FILES['proof_image']['tmp_name'], __DIR__ . '/../assets/images/payments/' . $proof_image)) $error = 'Unable to save payment proof.';
            } else {
                $error = "Invalid proof image format. Only JPG, PNG are allowed.";
            }
        } else {
            $error = "Please upload proof of payment.";
        }
    }

    if (empty($error)) {
        try {
            $pdo->beginTransaction();
            
            // Insert Order
            $order_stmt = $pdo->prepare("INSERT INTO orders (customer_id, order_number, total_amount, payment_method, payment_status, order_status, pickup_date) VALUES (?, ?, ?, ?, ?, 'Pending', ?)");
            $order_stmt->execute([$customer_id, $order_number, $total_amount, $payment_method, $payment_status, $pickup_date]);
            $order_id = $pdo->lastInsertId();
            
            // Insert Order Items and Update Stock
            foreach ($cart_items as $item) {
                $item_stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, price, quantity) VALUES (?, ?, ?, ?)");
                $item_stmt->execute([$order_id, $item['product_id'], $item['retail_price'], $item['quantity']]);
                
                // reduce stock
                $stock_stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
                $stock_stmt->execute([$item['quantity'], $item['product_id']]);
            }
            
            // Insert Transaction if Online
            if ($payment_method == 'Online' && $proof_image) {
                $txn_stmt = $pdo->prepare("INSERT INTO transactions (customer_id, reference, amount, status, proof_image) VALUES (?, ?, ?, 'Pending', ?)");
                $txn_stmt->execute([$customer_id, $order_number, $total_amount, $proof_image]);
            }
            
            // Clear Cart
            $clear_cart = $pdo->prepare("DELETE FROM cart WHERE customer_id = ?");
            $clear_cart->execute([$customer_id]);
            
            $pdo->commit();
            $success = true;
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Order processing failed: " . $e->getMessage();
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <?php if($success): ?>
            <div class="card shadow-sm border-0 text-center py-5">
                <div class="card-body">
                    <i class="fas fa-check-circle fa-5x text-success mb-4"></i>
                    <h2>Order Placed Successfully!</h2>
                    <p class="lead text-muted">Your order number is: <strong><?php echo $order_number; ?></strong></p>
                    <p>We will review your order and update the status shortly.</p>
                    <a href="orders.php" class="btn btn-primary mt-3">View My Orders</a>
                </div>
            </div>
        <?php else: ?>
        
            <h3 class="mb-4">Checkout</h3>
            
            <?php if(!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form action="checkout.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                <div class="row">
                    <!-- Order Details -->
                    <div class="col-md-7 mb-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Payment Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="fw-bold">Payment Method</label>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="radio" name="payment_method" id="pay_pickup" value="Pickup" checked onchange="togglePaymentInfo()">
                                        <label class="form-check-label" for="pay_pickup">Pay on Pickup</label>
                                    </div>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="radio" name="payment_method" id="pay_online" value="Online" onchange="togglePaymentInfo()">
                                        <label class="form-check-label" for="pay_online">Pay Online (Mobile Money / Bank)</label>
                                    </div>
                                </div>
                                
                                <!-- Online Payment Instructions -->
                                <div id="online_payment_info" style="display:none;" class="alert alert-info mt-3">
                                    <h6><i class="fas fa-info-circle"></i> Payment Instructions</h6>
                                    <?php if($payment_settings): ?>
                                        <p><?php echo nl2br(htmlspecialchars($payment_settings['instructions'])); ?></p>
                                        <p class="mb-1"><strong>Provider:</strong> <?php echo htmlspecialchars($payment_settings['payment_type']); ?></p>
                                        <p class="mb-1"><strong>Phone/Account:</strong> <?php echo htmlspecialchars($payment_settings['phone_number']); ?></p>
                                        <p class="mb-0"><strong>Name:</strong> <?php echo htmlspecialchars($payment_settings['account_name']); ?></p>
                                    <?php else: ?>
                                        <p>Please contact admin for payment details.</p>
                                    <?php endif; ?>
                                    
                                    <div class="mt-3">
                                        <label class="form-label">Upload Payment Screenshot <span class="text-danger">*</span></label>
                                        <input class="form-control" type="file" name="proof_image" accept="image/*">
                                    </div>
                                </div>

                                <div class="mb-3 mt-4">
                                    <label class="fw-bold">Select Pickup Date</label>
                                    <input type="date" name="pickup_date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Summary -->
                    <div class="col-md-5 mb-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Order Summary</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush mb-3">
                                    <?php foreach($cart_items as $item): ?>
                                    <li class="list-group-item d-flex justify-content-between lh-sm px-0">
                                        <div>
                                            <h6 class="my-0"><?php echo htmlspecialchars($item['product_name']); ?></h6>
                                            <small class="text-muted">Qty: <?php echo $item['quantity']; ?></small>
                                        </div>
                                        <span class="text-muted"><?php echo format_price($item['retail_price'] * $item['quantity']); ?></span>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                                <hr>
                                <div class="d-flex justify-content-between fw-bold fs-5">
                                    <span>Total (UGX)</span>
                                    <span class="text-primary"><?php echo format_price($total_amount); ?></span>
                                </div>
                                <button class="btn btn-primary w-100 mt-4 btn-lg" type="submit">Submit Order</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<script>
function togglePaymentInfo() {
    var onlineInfo = document.getElementById('online_payment_info');
    var isOnline = document.getElementById('pay_online').checked;
    if (isOnline) {
        onlineInfo.style.display = 'block';
    } else {
        onlineInfo.style.display = 'none';
    }
}
</script>

<?php
require_once '../includes/footer.php';
?>
