<?php
// admin/categories/delete-category.php
require_once '../../includes/config.php';
app_start_session();
require_once '../../includes/db.php';
require_once '../../includes/functions.php';

if (!is_admin_logged_in()) {
    redirect('../login.php');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('categories.php');
}
require_csrf();
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if ($id) {
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id]);
}

redirect('categories.php?msg=deleted');
?>
