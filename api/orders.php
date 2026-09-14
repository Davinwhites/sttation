<?php
// api/orders.php - GET the logged-in customer's orders (with items)
require_once 'bootstrap.php';

$customer_id = require_auth();

$stmt = $pdo->prepare("SELECT * FROM orders WHERE customer_id = ? ORDER BY id DESC");
$stmt->execute([$customer_id]);
$orders = $stmt->fetchAll();

$items_stmt = $pdo->prepare("SELECT oi.*, p.product_name, p.image FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");

$result = [];
foreach ($orders as $order) {
    $items_stmt->execute([$order['id']]);
    $items = array_map(function ($i) {
        return [
            'product_name' => $i['product_name'],
            'image' => image_url('products', $i['image']),
            'price' => (float)$i['price'],
            'quantity' => (int)$i['quantity'],
        ];
    }, $items_stmt->fetchAll());

    $result[] = [
        'id' => (int)$order['id'],
        'order_number' => $order['order_number'],
        'total_amount' => (float)$order['total_amount'],
        'payment_method' => $order['payment_method'],
        'payment_status' => $order['payment_status'],
        'order_status' => $order['order_status'],
        'pickup_date' => $order['pickup_date'],
        'created_at' => $order['created_at'],
        'items' => $items,
    ];
}

json_response($result);
