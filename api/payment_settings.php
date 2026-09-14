<?php
// api/payment_settings.php - GET the store's online-payment instructions
require_once 'bootstrap.php';

$row = $pdo->query("SELECT * FROM payment_settings LIMIT 1")->fetch();

if (!$row) {
    json_response(null);
}

json_response([
    'phone_number' => $row['phone_number'],
    'account_name' => $row['account_name'],
    'payment_type' => $row['payment_type'],
    'instructions' => $row['instructions'],
]);
