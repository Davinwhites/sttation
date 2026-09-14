<?php
// client/orders.php
require_once '../includes/header.php';

if (!is_customer_logged_in()) {
    redirect('login.php');
}

$customer_id = $_SESSION['customer_id'];

// Fetch Orders
$stmt = $pdo->prepare("SELECT * FROM orders WHERE customer_id = ? ORDER BY id DESC");
$stmt->execute([$customer_id]);
$orders = $stmt->fetchAll();
?>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="list-group shadow-sm">
            <a href="profile.php" class="list-group-item list-group-item-action">My Profile</a>
            <a href="orders.php" class="list-group-item list-group-item-action active">My Orders</a>
            <a href="logout.php" class="list-group-item list-group-item-action text-danger">Logout</a>
        </div>
    </div>
    
    <div class="col-md-9">
        <h4 class="mb-4">My Orders</h4>
        
        <?php if(count($orders) > 0): ?>
            <?php foreach($orders as $order): ?>
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Order #<?php echo $order['order_number']; ?></span>
                        <span class="text-muted small">Placed on <?php echo date('d M Y', strtotime($order['created_at'])); ?></span>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-sm-4">
                                <p class="mb-1 text-muted small">Total Amount</p>
                                <p class="fw-bold mb-0 text-primary"><?php echo format_price($order['total_amount']); ?></p>
                            </div>
                            <div class="col-sm-4">
                                <p class="mb-1 text-muted small">Order Status</p>
                                <?php 
                                    $badgeClass = 'secondary';
                                    if($order['order_status'] == 'Pending') $badgeClass = 'warning';
                                    if($order['order_status'] == 'Approved') $badgeClass = 'info';
                                    if($order['order_status'] == 'Completed') $badgeClass = 'success';
                                    if($order['order_status'] == 'Rejected' || $order['order_status'] == 'Cancelled') $badgeClass = 'danger';
                                ?>
                                <span class="badge bg-<?php echo $badgeClass; ?>"><?php echo $order['order_status']; ?></span>
                            </div>
                            <div class="col-sm-4">
                                <p class="mb-1 text-muted small">Payment Status</p>
                                <?php 
                                    $payClass = 'warning';
                                    if($order['payment_status'] == 'Paid') $payClass = 'success';
                                    if($order['payment_status'] == 'Failed') $payClass = 'danger';
                                ?>
                                <span class="badge bg-<?php echo $payClass; ?>"><?php echo $order['payment_status']; ?></span>
                                <small class="d-block text-muted mt-1">(<?php echo $order['payment_method']; ?>)</small>
                            </div>
                        </div>
                        
                        <!-- Order Items -->
                        <?php
                            $items_stmt = $pdo->prepare("SELECT oi.*, p.product_name, p.image FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
                            $items_stmt->execute([$order['id']]);
                            $items = $items_stmt->fetchAll();
                        ?>
                        <hr>
                        <h6 class="fw-bold mb-3">Items in this order:</h6>
                        <ul class="list-group list-group-flush">
                            <?php foreach($items as $item): ?>
                                <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <?php if($item['image']): ?>
                                            <img src="../assets/images/products/<?php echo $item['image']; ?>" width="40" class="img-thumbnail me-2">
                                        <?php endif; ?>
                                        <span><?php echo htmlspecialchars($item['product_name']); ?></span>
                                    </div>
                                    <span class="text-muted"><?php echo $item['quantity']; ?> x <?php echo format_price($item['price']); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-info">You have not placed any orders yet.</div>
        <?php endif; ?>
    </div>
</div>

<?php
require_once '../includes/footer.php';
?>
