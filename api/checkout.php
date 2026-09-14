<?php
// api/checkout.php - POST multipart/form-data
// fields: payment_method (Pickup|Online), pickup_date (YYYY-MM-DD), proof_image (file, required if Online)
require_once 'bootstrap.php';

$customer_id = require_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Method not allowed', 405);
}

$stmt = $pdo->prepare("SELECT c.quantity, p.id as product_id, p.product_name, p.retail_price, p.stock
                       FROM cart c
                       JOIN products p ON c.product_id = p.id
                       WHERE c.customer_id = ?");
$stmt->execute([$customer_id]);
$cart_items = $stmt->fetchAll();

if (count($cart_items) === 0) {
    json_error('Your cart is empty.');
}

$payment_method = $_POST['payment_method'] ?? '';
$pickup_date = $_POST['pickup_date'] ?? '';

if (!in_array($payment_method, ['Online', 'Pickup'], true)) {
    json_error('Please choose a valid payment method.');
}
if ($pickup_date === '') {
    json_error('Please select a pickup date.');
}

// Verify stock is still available
foreach ($cart_items as $item) {
    if ($item['quantity'] > $item['stock']) {
        json_error('"' . $item['product_name'] . '" only has ' . $item['stock'] . ' left in stock.');
    }
}

$total_amount = 0;
foreach ($cart_items as $item) {
    $total_amount += $item['retail_price'] * $item['quantity'];
}

$order_number = 'ORD-' . date('Ymd') . '-' . rand(1000, 9999);
$proof_image = null;

if ($payment_method === 'Online') {
    if (!isset($_FILES['proof_image']) || $_FILES['proof_image']['error'] !== 0) {
        json_error('Please upload proof of payment.');
    }

    $tmp_path = $_FILES['proof_image']['tmp_name'];
    $size = $_FILES['proof_image']['size'];
    $max_bytes = 5 * 1024 * 1024; // 5MB cap

    if ($size <= 0 || $size > $max_bytes) {
        json_error('Proof image must be smaller than 5MB.');
    }

    // Validate the file is actually an image (not just a renamed .php etc.)
    // by checking its real MIME type rather than trusting the extension.
    $allowed_mimes = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $real_mime = finfo_file($finfo, $tmp_path);
    finfo_close($finfo);

    if (!isset($allowed_mimes[$real_mime]) || @getimagesize($tmp_path) === false) {
        json_error('Invalid proof image. Only real JPG or PNG photos are allowed.');
    }

    $ext = $allowed_mimes[$real_mime];
    // Random, non-guessable filename - never trust the client-supplied name.
    $proof_image = bin2hex(random_bytes(16)) . '.' . $ext;
    $dest = __DIR__ . '/../assets/images/payments/' . $proof_image;
    if (!move_uploaded_file($tmp_path, $dest)) {
        json_error('Failed to upload proof image.', 500);
    }
}

try {
    $pdo->beginTransaction();

    $order_stmt = $pdo->prepare("INSERT INTO orders (customer_id, order_number, total_amount, payment_method, payment_status, order_status, pickup_date) VALUES (?, ?, ?, ?, 'Pending', 'Pending', ?)");
    $order_stmt->execute([$customer_id, $order_number, $total_amount, $payment_method, $pickup_date]);
    $order_id = $pdo->lastInsertId();

    foreach ($cart_items as $item) {
        $item_stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, price, quantity) VALUES (?, ?, ?, ?)");
        $item_stmt->execute([$order_id, $item['product_id'], $item['retail_price'], $item['quantity']]);

        $stock_stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        $stock_stmt->execute([$item['quantity'], $item['product_id']]);
    }

    if ($payment_method === 'Online' && $proof_image) {
        $txn_stmt = $pdo->prepare("INSERT INTO transactions (customer_id, reference, amount, status, proof_image) VALUES (?, ?, ?, 'Pending', ?)");
        $txn_stmt->execute([$customer_id, $order_number, $total_amount, $proof_image]);
    }

    $clear_cart = $pdo->prepare("DELETE FROM cart WHERE customer_id = ?");
    $clear_cart->execute([$customer_id]);

    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    json_error('Order processing failed. Please try again.', 500);
}

json_response([
    'order_number' => $order_number,
    'total_amount' => (float)$total_amount,
], 201);
