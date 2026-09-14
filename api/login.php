<?php
// api/login.php - POST { email, password }
require_once 'bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Method not allowed', 405);
}

$body = get_body();
$email = trim($body['email'] ?? '');
$password = $body['password'] ?? '';

if ($email === '' || $password === '') {
    json_error('Please enter email and password.');
}

// ---- Brute-force protection: max 5 failed attempts per email+IP per 15 minutes ----
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$identifier = strtolower($email) . '|' . $ip;

try {
    $attempts_stmt = $pdo->prepare("SELECT COUNT(*) as c FROM login_attempts WHERE identifier = ? AND attempted_at > (NOW() - INTERVAL 15 MINUTE)");
    $attempts_stmt->execute([$identifier]);
    $attempts = (int)$attempts_stmt->fetch()['c'];

    if ($attempts >= 5) {
        json_error('Too many failed login attempts. Please try again in 15 minutes.', 429);
    }
} catch (PDOException $e) {
    // login_attempts table missing (run security_additions.sql) - fail open, don't block real users.
}

$stmt = $pdo->prepare("SELECT * FROM customers WHERE email = ?");
$stmt->execute([$email]);
$customer = $stmt->fetch();

if (!$customer || !password_verify($password, $customer['password'])) {
    try {
        $log_stmt = $pdo->prepare("INSERT INTO login_attempts (identifier) VALUES (?)");
        $log_stmt->execute([$identifier]);
    } catch (PDOException $e) { /* table missing, ignore */ }
    json_error('Invalid email or password.', 401);
}

// Successful login: clear any prior failed attempts for this identifier
try {
    $clear_stmt = $pdo->prepare("DELETE FROM login_attempts WHERE identifier = ?");
    $clear_stmt->execute([$identifier]);
} catch (PDOException $e) { /* table missing, ignore */ }

$token = generate_token($customer['id']);

json_response([
    'token' => $token,
    'customer' => [
        'id' => (int)$customer['id'],
        'fullname' => $customer['fullname'],
        'email' => $customer['email'],
        'phone' => $customer['phone'],
        'address' => $customer['address'],
    ],
]);
