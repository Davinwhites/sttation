<?php
// admin/categories/delete-category.php
session_start();
require_once '../../includes/db.php';
require_once '../../includes/functions.php';

if (!is_admin_logged_in()) {
    redirect('../login.php');
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id]);
}

redirect('categories.php?msg=deleted');
?>
