<?php
require_once __DIR__ . '/config.php';
if (!defined('BASE_URL') || BASE_URL === '') { define('BASE_URL', '/stationery'); }

function csrf_token() {
    app_start_session();
    if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}
function verify_csrf($token) {
    app_start_session();
    return is_string($token) && !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
function require_csrf() {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) { http_response_code(419); exit('Your form expired. Please try again.'); }
}

// includes/functions.php

/**
 * Sanitize user input to prevent XSS and other issues
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Check if the user is logged in as an admin
 */
function is_admin_logged_in() {
    return isset($_SESSION['admin_id']);
}

/**
 * Check if the user is logged in as a customer
 */
function is_customer_logged_in() {
    return isset($_SESSION['customer_id']);
}

/**
 * Redirect user to a specific URL
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
}

/**
 * Format a number to a price (e.g. UGX 1,000)
 */
function format_price($amount) {
    return "UGX " . number_format($amount, 0); // Assuming UGX for example, customize as needed
}

/**
 * Links for the "Get Our App" banner on the homepage. Update these once
 * you have real URLs:
 * - PLAY_STORE_URL: your app's live Play Store listing, once published
 *   (see flutter_app/PLAY_STORE_CHECKLIST.md).
 * - APK_DOWNLOAD_URL: a direct link to a release .apk you've built and
 *   uploaded yourself (e.g. into assets/downloads/), for people to
 *   install straight away while the Play Store listing is still pending
 *   review. Sideloaded APKs show an Android security warning by
 *   default - that's normal, not a bug.
 */
if (!defined('PLAY_STORE_URL')) {
    define('PLAY_STORE_URL', 'https://play.google.com/store/apps/details?id=com.decomatstationers.app');
}
if (!defined('APK_DOWNLOAD_URL')) {
    define('APK_DOWNLOAD_URL', BASE_URL . '/assets/downloads/decomat-stationers.apk');
}
if (!defined('WHATSAPP_ORDER_NUMBER')) {
    define('WHATSAPP_ORDER_NUMBER', '256772616006');
}

/**
 * Builds a wa.me link that opens WhatsApp with a pre-filled message asking
 * to order a specific product. Works on both mobile (opens the WhatsApp
 * app) and desktop (opens WhatsApp Web) since wa.me handles that itself.
 */
function whatsapp_order_link($product_name, $price = null) {
    $message = "Hello Deco&Mat Stationers, I would like to order: " . $product_name;
    if ($price !== null) {
        $message .= " (" . format_price($price) . ")";
    }
    return "https://wa.me/" . WHATSAPP_ORDER_NUMBER . "?text=" . rawurlencode($message);
}

?>
