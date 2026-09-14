<?php
// client/logout.php
session_start();
if(isset($_SESSION['customer_id'])) {
    unset($_SESSION['customer_id']);
    unset($_SESSION['customer_name']);
}
header("Location: home.php");
exit();
?>
