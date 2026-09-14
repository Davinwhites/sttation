<?php
// Copy this file to a location outside public_html when possible, or protect it with .htaccess.
// Set these values in cPanel's PHP environment or replace the empty defaults on the server.
if (!defined('APP_ENV')) define('APP_ENV', getenv('APP_ENV') ?: 'production');
if (!defined('DB_HOST')) define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
if (!defined('DB_NAME')) define('DB_NAME', getenv('DB_NAME') ?: '');
if (!defined('DB_USER')) define('DB_USER', getenv('DB_USER') ?: '');
if (!defined('DB_PASSWORD')) define('DB_PASSWORD', getenv('DB_PASSWORD') ?: '');
if (!defined('API_SECRET')) define('API_SECRET', getenv('API_SECRET') ?: '');
if (!defined('SITE_BASE_URL')) define('SITE_BASE_URL', rtrim(getenv('SITE_BASE_URL') ?: '', '/'));
if (!defined('BASE_URL')) define('BASE_URL', rtrim(getenv('BASE_URL') ?: '', '/'));

if (DB_NAME === '' || DB_USER === '' || DB_PASSWORD === '' || API_SECRET === '') {
    error_log('Application configuration is incomplete. Set DB_NAME, DB_USER, DB_PASSWORD, API_SECRET, and SITE_BASE_URL.');
}

function app_is_production() { return APP_ENV === 'production'; }
function app_start_session() {
    if (session_status() !== PHP_SESSION_NONE) return;
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_secure', app_is_production() || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? '1' : '0');
    session_set_cookie_params(['httponly' => true, 'secure' => app_is_production() || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'), 'samesite' => 'Lax', 'path' => '/']);
    session_start();
}
?>
