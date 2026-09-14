<?php
// admin/orders/all-orders.php
$current_page = 'all-orders.php';
require_once '../includes/header.php';

$status_filter = $_GET['status'] ?? '';
$where = "";
$params = [];
if ($status_filter) {
    $where = "WHERE o.order_status = ?";
    $params[] = $status_filter;
}

// Handle Status Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    $order_status = $_POST['order_status'];
    $payment_status = $_POST['payment_status'];
    
    $update = $pdo->prepare("UPDATE orders SET order_status = ?, payment_status = ? WHERE id = ?");
    if ($update->execute([$order_status, $payment_status, $order_id])) {
        redirect("all-orders.php" . ($status_filter ? "?status=$status_filter&msg=updated" : "?msg=updated"));
    }
}

$query = "SELECT o.*, c.fullname, c.phone 
          FROM orders o 
          JOIN customers c ON o.customer_id = c.id 
          $where 
          ORDER BY o.id DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$orders = $stmt->fetchAll();
?>

    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"><?php echo $status_filter ? $status_filter . ' Orders' : 'All Orders'; ?></h1>
          </div>
        </div>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">
        
        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
            <div class="alert alert-success">Order updated successfully.</div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body p-0 table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Type</th>
                            <th>Payment Status</th>
                            <th>Order Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($orders as $order): ?>
                        <tr>
                            <td><?php echo $order['order_number']; ?></td>
                            <td>
                                <?php echo htmlspecialchars($order['fullname']); ?><br>
                                <small class="text-muted"><?php echo htmlspecialchars($order['phone']); ?></small>
                            </td>
                            <td><?php echo format_price($order['total_amount']); ?></td>
                            <td><?php echo $order['payment_method']; ?></td>
                            <td>
                                <span class="badge badge-<?php echo ($order['payment_status'] == 'Paid' ? 'success' : ($order['payment_status'] == 'Pending' ? 'warning' : 'danger')); ?>">
                                    <?php echo $order['payment_status']; ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-<?php echo ($order['order_status'] == 'Completed' ? 'success' : ($order['order_status'] == 'Pending' ? 'warning' : ($order['order_status'] == 'Approved' ? 'info' : 'danger'))); ?>">
                                    <?php echo $order['order_status']; ?>
                                </span>
                            </td>
                            <td><?php echo date('Y-m-d H:i', strtotime($order['created_at'])); ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#orderModal<?php echo $order['id']; ?>">
                                    <i class="fas fa-edit"></i> Manage
                                </button>
                            </td>
                        </tr>
                        
                        <!-- Modal -->
                        <div class="modal fade" id="orderModal<?php echo $order['id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                          <div class="modal-dialog" role="document">
                            <div class="modal-content">
                              <form action="" method="POST">
                                  <div class="modal-header">
                                    <h5 class="modal-title">Manage Order #<?php echo $order['order_number']; ?></h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                    </button>
                                  </div>
                                  <div class="modal-body">
                                      <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                      
                                      <div class="form-group">
                                          <label>Payment Status</label>
                                          <select name="payment_status" class="form-control">
                                              <option value="Pending" <?php if($order['payment_status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                                              <option value="Paid" <?php if($order['payment_status'] == 'Paid') echo 'selected'; ?>>Paid</option>
                                              <option value="Failed" <?php if($order['payment_status'] == 'Failed') echo 'selected'; ?>>Failed</option>
                                          </select>
                                      </div>
                                      
                                      <div class="form-group">
                                          <label>Order Status</label>
                                          <select name="order_status" class="form-control">
                                              <option value="Pending" <?php if($order['order_status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                                              <option value="Approved" <?php if($order['order_status'] == 'Approved') echo 'selected'; ?>>Approved</option>
                                              <option value="Rejected" <?php if($order['order_status'] == 'Rejected') echo 'selected'; ?>>Rejected</option>
                                              <option value="Completed" <?php if($order['order_status'] == 'Completed') echo 'selected'; ?>>Completed</option>
                                              <option value="Cancelled" <?php if($order['order_status'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                                          </select>
                                      </div>
                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                  </div>
                              </form>
                            </div>
                          </div>
                        </div>
                        <?php endforeach; ?>
                        
                        <?php if(count($orders) == 0): ?>
                        <tr><td colspan="8" class="text-center">No orders found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

      </div>
    </section>

<?php
require_once '../includes/footer.php';
?>
