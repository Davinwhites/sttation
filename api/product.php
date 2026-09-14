<?php
// api/product.php - GET ?id=ID
require_once 'bootstrap.php';

$id = $_GET['id'] ?? null;
if (!$id || !ctype_digit((string)$id)) {
    json_error('Product id is required.');
}

$stmt = $pdo->prepare("SELECT p.*, c.category_name, bc.class_name, s.subject_name
                       FROM products p
                       LEFT JOIN categories c ON p.category_id = c.id
                       LEFT JOIN book_classes bc ON p.class_id = bc.id
                       LEFT JOIN subjects s ON p.subject_id = s.id
                       WHERE p.id = ? AND p.status = 'Active'");
$stmt->execute([$id]);
$p = $stmt->fetch();

if (!$p) {
    json_error('Product not found.', 404);
}

$related_stmt = $pdo->prepare("SELECT * FROM products WHERE category_id = ? AND id != ? AND status = 'Active' LIMIT 4");
$related_stmt->execute([$p['category_id'], $id]);
$related_rows = $related_stmt->fetchAll();

$mapProduct = function ($row) {
    return [
        'id' => (int)$row['id'],
        'category_id' => (int)$row['category_id'],
        'name' => $row['product_name'],
        'description' => $row['description'],
        'retail_price' => (float)$row['retail_price'],
        'wholesale_price' => (float)$row['wholesale_price'],
        'stock' => (int)$row['stock'],
        'image' => image_url('products', $row['image']),
    ];
};

$product = $mapProduct($p);
$product['category_name'] = $p['category_name'];
$product['class_name'] = $p['class_name'];
$product['subject_name'] = $p['subject_name'];
$product['related'] = array_map($mapProduct, $related_rows);

json_response($product);
