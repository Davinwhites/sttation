<?php
// includes/header.php
require_once __DIR__ . '/config.php';
app_start_session();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

// Calculate cart count
$cart_count = 0;
if (is_customer_logged_in()) {
    $stmt = $pdo->prepare("SELECT SUM(quantity) as count FROM cart WHERE customer_id = ?");
    $stmt->execute([$_SESSION['customer_id']]);
    $cart_count = $stmt->fetch()['count'] ?? 0;
} else if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['quantity'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deco&Mat Stationers</title>
    <!-- PWA: lets the site be "installed" to a phone home screen -->
    <link rel="manifest" href="<?php echo BASE_URL; ?>/manifest.json">
    <meta name="theme-color" content="#1E5C3A">
    <link rel="apple-touch-icon" href="<?php echo BASE_URL; ?>/assets/images/icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>/assets/images/icons/icon-192.png">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa; }
        .navbar-brand { font-weight: bold; font-size: 1.5rem; color: #0d6efd !important; }
        .product-card { transition: transform 0.2s, box-shadow 0.2s; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .product-image { height: 200px; object-fit: contain; padding: 10px; background: #fff; }
        .footer { background-color: #343a40; color: #fff; padding: 40px 0 20px; margin-top: 40px; }
        .hero-section { background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%); color: white; padding: 60px 0; border-radius: 10px; margin-bottom: 40px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
  <div class="container">
    <a class="navbar-brand" href="<?php echo BASE_URL; ?>/client/home.php">
        <i class="fas fa-pencil-ruler"></i> Deco&Mat Stationers
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNav">
        <form class="d-flex mx-auto" action="<?php echo BASE_URL; ?>/client/shop.php" method="GET" style="width: 100%; max-width: 400px;">
            <input class="form-control me-2" type="search" name="q" placeholder="Search products..." aria-label="Search">
            <button class="btn btn-outline-primary" type="submit"><i class="fas fa-search"></i></button>
        </form>

      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" href="<?php echo BASE_URL; ?>/client/home.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo BASE_URL; ?>/client/shop.php">Shop</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-danger fw-bold" href="<?php echo BASE_URL; ?>/admin/login.php"><i class="fas fa-user-shield"></i> Admin Panel</a>
        </li>
        
        <?php if(is_customer_logged_in()): ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['customer_name']); ?>
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/client/profile.php">Profile</a></li>
                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/client/orders.php">My Orders</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/client/logout.php">Logout</a></li>
                </ul>
            </li>
        <?php else: ?>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL; ?>/client/login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
            </li>
        <?php endif; ?>
        
        <li class="nav-item">
          <a class="nav-link position-relative" href="<?php echo BASE_URL; ?>/client/cart.php">
            <i class="fas fa-shopping-cart"></i> Cart
            <?php if($cart_count > 0): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                <?php echo $cart_count; ?>
                </span>
            <?php endif; ?>
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
<main class="container py-4">
