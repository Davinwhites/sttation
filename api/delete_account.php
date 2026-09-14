<?php
// api/delete_account.php - POST (auth required)
// Permanently deletes the logged-in customer's account and personal data.
require_once 'bootstrap.php';

$customer_id = require_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Method not allowed', 405);
}

// NOTE: the database schema (see database/stationery.sql) already has
// ON DELETE CASCADE from cart, orders, order_items and transactions to
// customers, so deleting the customer row removes all of their personal
// data and order history in one step. If you'd rather keep financial
// records for accounting purposes, change those foreign keys to
// ON DELETE SET NULL instead before running this in production.
try {
    $pdo->prepare("DELETE FROM customers WHERE id = ?")->execute([$customer_id]);
} catch (Exception $e) {
    json_error('Failed to delete account. Please try again.', 500);
}

json_response(['deleted' => true]);
