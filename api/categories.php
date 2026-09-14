<?php
// api/categories.php - GET all categories
require_once 'bootstrap.php';

$rows = $pdo->query("SELECT * FROM categories ORDER BY category_name ASC")->fetchAll();

$categories = array_map(function ($c) {
    return [
        'id' => (int)$c['id'],
        'name' => $c['category_name'],
        'description' => $c['description'],
        'image' => (!empty($c['image'])) ? image_url('categories', $c['image']) : null,
    ];
}, $rows);

json_response($categories);
