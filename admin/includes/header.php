<?php
// admin/includes/header.php
session_start();
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';

// Check if it's the login page, if not, require login
$current_page = basename($_SERVER['PHP_SELF']);
if ($current_page != 'login.php' && !is_admin_logged_in()) {
    redirect(BASE_URL . '/admin/login.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Deco&Mat Stationers Admin</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <!-- AdminLTE css -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  
  <style>
    .brand-link .brand-image { float: none; max-height: 33px; margin-right: 5px; }
  </style>
</head>
<body class="hold-transition sidebar-mini <?php echo ($current_page == 'login.php') ? 'login-page' : ''; ?>">

<?php if ($current_page != 'login.php'): ?>
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="<?php echo BASE_URL; ?>/admin/dashboard.php" class="nav-link">Home</a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="nav-link" href="<?php echo BASE_URL; ?>/admin/logout.php" role="button">
          <i class="fas fa-sign-out-alt"></i> Logout
        </a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?php echo BASE_URL; ?>/admin/dashboard.php" class="brand-link">
      <span class="brand-text font-weight-light">Deco&Mat Stationers</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="info">
          <a href="#" class="d-block">Admin User</a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          
          <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>/admin/dashboard.php" class="nav-link <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>/admin/categories/categories.php" class="nav-link">
              <i class="nav-icon fas fa-list"></i>
              <p>Categories</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>/admin/products/view-products.php" class="nav-link">
              <i class="nav-icon fas fa-box"></i>
              <p>Products</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>/admin/orders/all-orders.php" class="nav-link">
              <i class="nav-icon fas fa-shopping-cart"></i>
              <p>Orders</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>/admin/customers/customers.php" class="nav-link">
              <i class="nav-icon fas fa-users"></i>
              <p>Customers</p>
            </a>
          </li>
          
          <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>/admin/reports/sales.php" class="nav-link">
              <i class="nav-icon fas fa-chart-line"></i>
              <p>Reports</p>
            </a>
          </li>

          <li class="nav-header">WEBSITE</li>
          <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>/client/home.php" class="nav-link text-info">
              <i class="nav-icon fas fa-store"></i>
              <p>Back to Client Site</p>
            </a>
          </li>

        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
<?php endif; ?>
