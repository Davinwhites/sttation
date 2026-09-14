<?php
/**
 * api/bootstrap.php
 * Shared bootstrap for every API endpoint: DB connection, CORS headers,
 * JSON helpers and the stateless auth-token helpers used by the Flutter app.
 *
 * This file is included first by every endpoint in this folder.
 */

require_once __DIR__ . '/../includes/config.php';

// Allow only the configured app origin. Native clients may omit Origin entirely.
$allowed_origin = getenv('CORS_ORIGIN') ?: SITE_BASE_URL;
if (!empty($_SERVER['HTTP_ORIGIN']) && $allowed_origin !== '' && hash_equals($allowed_origin, $_SERVER['HTTP_ORIGIN'])) {
    header('Access-Control-Allow-Origin: ' . $allowed_origin);
    header('Vary: Origin');
}
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../includes/db.php';
if (API_SECRET === '') json_error('Service configuration error.', 503);

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
    $payload = base64url_encode(json_encode(['id' => (int)$customer_id, 'exp' => time() + 604800]));
    $sig = hash_hmac('sha256', $payload, API_SECRET);
    return $payload . '.' . $sig;
}
function base64url_encode($value) { return rtrim(strtr(base64_encode($value), '+/', '-_'), '='); }
function base64url_decode($value) {
    $remainder = strlen($value) % 4;
    if ($remainder) $value .= str_repeat('=', 4 - $remainder);
    return base64_decode(strtr($value, '-_', '+/'), true);
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
    $decoded = base64url_decode($payload);
    $claims = json_decode($decoded ?: '', true);
    if (!is_array($claims) || !isset($claims['id'], $claims['exp']) || !is_numeric($claims['id']) || (int)$claims['exp'] < time()) {
        return null;
    }
    return (int)$claims['id'];
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
if (SITE_BASE_URL === '') json_error('Service configuration error.', 503);

function image_url($folder, $filename) {
    if (empty($filename)) return null;
    return SITE_BASE_URL . '/assets/images/' . $folder . '/' . $filename;
}
