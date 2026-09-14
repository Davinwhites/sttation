<?php
require_once '../includes/header.php';

// Fetch all completed orders for sales report
$stmt = $pdo->query("SELECT o.*, c.fullname FROM orders o 
                     JOIN customers c ON o.customer_id = c.id 
                     WHERE o.payment_status = 'Paid' OR o.order_status = 'Completed'
                     ORDER BY o.created_at DESC");
$sales = $stmt->fetchAll();

$total_revenue = 0;
foreach ($sales as $sale) {
    $total_revenue += $sale['total_amount'];
}
?>

<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Sales Report</h1>
      </div>
    </div>
  </div>
</div>
<!-- /.content-header -->

<!-- Main content -->
<div class="content">
  <div class="container-fluid">
    
    <div class="row">
      <div class="col-lg-4 col-6">
        <!-- small box -->
        <div class="small-box bg-success">
          <div class="inner">
            <h3>UGX <?= number_format($total_revenue) ?></h3>
            <p>Total Revenue</p>
          </div>
          <div class="icon">
            <i class="ion ion-stats-bars"></i>
          </div>
        </div>
      </div>
    </div>

    <div class="card card-success card-outline">
      <div class="card-header">
        <h3 class="card-title"><i class="fas fa-chart-line"></i> Successful Transactions</h3>
      </div>
      <div class="card-body">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>Order No.</th>
              <th>Customer</th>
              <th>Amount (UGX)</th>
              <th>Payment Method</th>
              <th>Status</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($sales) > 0): ?>
              <?php foreach ($sales as $sale): ?>
                <tr>
                  <td><?= htmlspecialchars($sale['order_number']) ?></td>
                  <td><?= htmlspecialchars($sale['fullname']) ?></td>
                  <td><?= number_format($sale['total_amount']) ?></td>
                  <td><?= htmlspecialchars($sale['payment_method']) ?></td>
                  <td><span class="badge badge-success">Paid</span></td>
                  <td><?= date('M d, Y h:i A', strtotime($sale['created_at'])) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" class="text-center">No sales records found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div><!-- /.container-fluid -->
</div>
<!-- /.content -->

<?php require_once '../includes/footer.php'; ?>
