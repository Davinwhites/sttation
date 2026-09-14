<?php
// api/register.php - POST { fullname, email, phone, password, address }
require_once 'bootstrap.php';
require_once __DIR__ . '/../includes/mailer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Method not allowed', 405);
}

$body = get_body();
$fullname = trim($body['fullname'] ?? '');
$email = trim($body['email'] ?? '');
$phone = trim($body['phone'] ?? '');
$password = $body['password'] ?? '';
$address = trim($body['address'] ?? '');

if ($fullname === '' || $email === '' || $phone === '' || $password === '') {
    json_error('All required fields must be filled.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_error('Please enter a valid email address.');
}

if (strlen($password) < 6) {
    json_error('Password must be at least 6 characters.');
}

$stmt = $pdo->prepare("SELECT id FROM customers WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    json_error('Email already registered.');
}

$hashed = password_hash($password, PASSWORD_DEFAULT);
$insert = $pdo->prepare("INSERT INTO customers (fullname, phone, email, password, address) VALUES (?, ?, ?, ?, ?)");
$ok = $insert->execute([$fullname, $phone, $email, $hashed, $address]);

if (!$ok) {
    json_error('Registration failed. Please try again.', 500);
}

$customer_id = $pdo->lastInsertId();
$token = generate_token($customer_id);
send_welcome_email($email, $fullname);

json_response([
    'token' => $token,
    'customer' => [
        'id' => (int)$customer_id,
        'fullname' => $fullname,
        'email' => $email,
        'phone' => $phone,
        'address' => $address,
    ],
], 201);
