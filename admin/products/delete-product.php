<?php
// admin/products/delete-product.php
session_start();
require_once '../../includes/db.php';
require_once '../../includes/functions.php';

if (!is_admin_logged_in()) {
    redirect('../login.php');
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // get image to delete it
    $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();
    
    if ($product && $product['image']) {
        if (file_exists('../../assets/images/products/' . $product['image'])) {
            unlink('../../assets/images/products/' . $product['image']);
        }
    }

    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
}

redirect('view-products.php?msg=deleted');
?>
