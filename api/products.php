<?php
// api/products.php - GET ?q=search&category=ID
require_once 'bootstrap.php';

$query = "SELECT * FROM products WHERE status = 'Active'";
$params = [];

if (!empty($_GET['q'])) {
    $query .= " AND product_name LIKE ?";
    $params[] = '%' . $_GET['q'] . '%';
}

if (!empty($_GET['category'])) {
    $query .= " AND category_id = ?";
    $params[] = $_GET['category'];
}

$query .= " ORDER BY id DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$rows = $stmt->fetchAll();

$products = array_map(function ($p) {
    return [
        'id' => (int)$p['id'],
        'category_id' => (int)$p['category_id'],
        'name' => $p['product_name'],
        'description' => $p['description'],
        'retail_price' => (float)$p['retail_price'],
        'wholesale_price' => (float)$p['wholesale_price'],
        'stock' => (int)$p['stock'],
        'image' => image_url('products', $p['image']),
    ];
}, $rows);

json_response($products);
