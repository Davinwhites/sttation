<?php
// api/profile.php
// GET  -> returns the logged-in customer's profile
// POST { fullname, phone, address } -> updates profile
require_once 'bootstrap.php';

$customer_id = require_auth();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->prepare("SELECT id, fullname, email, phone, address FROM customers WHERE id = ?");
    $stmt->execute([$customer_id]);
    $customer = $stmt->fetch();
    if (!$customer) json_error('Customer not found.', 404);
    json_response([
        'id' => (int)$customer['id'],
        'fullname' => $customer['fullname'],
        'email' => $customer['email'],
        'phone' => $customer['phone'],
        'address' => $customer['address'],
    ]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = get_body();
    $fullname = trim($body['fullname'] ?? '');
    $phone = trim($body['phone'] ?? '');
    $address = trim($body['address'] ?? '');

    if ($fullname === '' || $phone === '') {
        json_error('Full name and phone are required.');
    }

    $update = $pdo->prepare("UPDATE customers SET fullname = ?, phone = ?, address = ? WHERE id = ?");
    $ok = $update->execute([$fullname, $phone, $address, $customer_id]);

    if (!$ok) json_error('Failed to update profile.', 500);

    json_response(['fullname' => $fullname, 'phone' => $phone, 'address' => $address]);
}

json_error('Method not allowed', 405);
