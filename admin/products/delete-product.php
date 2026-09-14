<?php
// admin/products/delete-product.php
require_once '../../includes/config.php';
app_start_session();
require_once '../../includes/db.php';
require_once '../../includes/functions.php';

if (!is_admin_logged_in()) {
    redirect('../login.php');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('view-products.php');
}
require_csrf();
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if ($id) {
    
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
