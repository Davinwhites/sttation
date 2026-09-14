<?php
/**
 * api/bootstrap.php
 * Shared bootstrap for every API endpoint: DB connection, CORS headers,
 * JSON helpers and the stateless auth-token helpers used by the Flutter app.
 *
 * This file is included first by every endpoint in this folder.
 */

// ---- CORS (needed because the Flutter app calls this API from a different origin) ----
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ---- DB connection (same credentials/schema as the original website) ----
$host = 'localhost';
$dbname = 'stationery';
$username = 'root';
$password = '1234';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    json_error('Database connection failed', 500);
}

// ---- Secret used to sign stateless auth tokens (change this in production!) ----
define('API_SECRET', 'decomat-stationers-2026-change-me');

/**
 * Send a JSON success response and stop execution.
 */
function json_response($data, $code = 200) {
    http_response_code($code);
    echo json_encode(['success' => true, 'data' => $data]);
    exit();
}

/**
 * Send a JSON error response and stop execution.
 */
function json_error($message, $code = 400) {
    http_response_code($code);
    echo json_encode(['success' => false, 'message' => $message]);
    exit();
}

/**
 * Read and decode a JSON request body (falls back to $_POST for form submits).
 */
function get_body() {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    if (is_array($data)) {
        return $data;
    }
    return $_POST;
}

/**
 * Build a stateless bearer token for a customer id: base64(id).hmac
 * No DB storage needed, verified again on every request.
 */
function generate_token($customer_id) {
    $payload = base64_encode((string)$customer_id);
    $sig = hash_hmac('sha256', $payload, API_SECRET);
    return $payload . '.' . $sig;
}

/**
 * Returns the authenticated customer_id, or null if missing/invalid.
 */
function get_auth_customer_id() {
    $headers = function_exists('getallheaders') ? getallheaders() : [];
    $authHeader = null;
    foreach ($headers as $key => $value) {
        if (strtolower($key) === 'authorization') {
            $authHeader = $value;
            break;
        }
    }
    if (!$authHeader && isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
    }
    if (!$authHeader || stripos($authHeader, 'Bearer ') !== 0) {
        return null;
    }
    $token = trim(substr($authHeader, 7));
    $parts = explode('.', $token);
    if (count($parts) !== 2) {
        return null;
    }
    [$payload, $sig] = $parts;
    $expected = hash_hmac('sha256', $payload, API_SECRET);
    if (!hash_equals($expected, $sig)) {
        return null;
    }
    $id = base64_decode($payload);
    if (!ctype_digit($id)) {
        return null;
    }
    return (int)$id;
}

/**
 * Require authentication or exit with a 401 error.
 */
function require_auth() {
    $id = get_auth_customer_id();
    if (!$id) {
        json_error('Unauthorized. Please log in again.', 401);
    }
    return $id;
}

/**
 * The public base URL of the website's assets folder (images), used to
 * build absolute image URLs for the mobile app. This is the local-dev
 * default for the Android emulator. Before release, change this to your
 * real production domain served over HTTPS, e.g.
 * 'https://decomatstationers.com'.
 */
define('SITE_BASE_URL', 'http://10.0.2.2/stationery');

function image_url($folder, $filename) {
    if (empty($filename)) return null;
    return SITE_BASE_URL . '/assets/images/' . $folder . '/' . $filename;
}
