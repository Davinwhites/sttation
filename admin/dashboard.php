<?php
// admin/dashboard.php
require_once 'includes/header.php';

// Fetch some basic metrics for the dashboard
try {
    $total_sales_stmt = $pdo->query("SELECT SUM(total_amount) as total FROM orders WHERE payment_status = 'Paid'");
    $total_sales = $total_sales_stmt->fetch()['total'] ?? 0;

    $today_orders_stmt = $pdo->query("SELECT COUNT(*) as count FROM orders WHERE DATE(created_at) = CURDATE()");
    $today_orders = $today_orders_stmt->fetch()['count'];

    $pending_orders_stmt = $pdo->query("SELECT COUNT(*) as count FROM orders WHERE order_status = 'Pending'");
    $pending_orders = $pending_orders_stmt->fetch()['count'];

    $customers_stmt = $pdo->query("SELECT COUNT(*) as count FROM customers");
    $total_customers = $customers_stmt->fetch()['count'];

} catch (PDOException $e) {
    // Basic error handling for dashboard
    $total_sales = 0;
    $today_orders = 0;
    $pending_orders = 0;
    $total_customers = 0;
}
?>

    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Dashboard</h1>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3><?php echo format_price($total_sales); ?></h3>
                <p>Total Revenue (Paid)</p>
              </div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3><?php echo $today_orders; ?></h3>
                <p>Today's Orders</p>
              </div>
              <div class="icon">
                <i class="ion ion-stats-bars"></i>
              </div>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3><?php echo $pending_orders; ?></h3>
                <p>Pending Orders</p>
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3><?php echo $total_customers; ?></h3>
                <p>Total Customers</p>
              </div>
              <div class="icon">
                <i class="ion ion-pie-graph"></i>
              </div>
            </div>
          </div>
          <!-- ./col -->
        </div>
        <!-- /.row -->
        
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Welcome to Deco&Mat Stationers Admin Panel</h3>
                    </div>
                    <div class="card-body">
                        Use the sidebar to navigate through Categories, Products, Orders, and Customers.
                    </div>
                </div>
            </div>
        </div>

      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->

<?php
require_once 'includes/footer.php';
?>
