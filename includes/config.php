<?php
// ============================================================
//  Grand Azure Hotel — Site Configuration
// ============================================================

// Database credentials (update to match your XAMPP setup)
define('DB_HOST',     'localhost');
define('DB_USER',     'root');
define('DB_PASS',     '');
define('DB_NAME',     'grand_azure_hotel');

// Site information
define('SITE_NAME',   'Grand Azure Hotel');
// Dynamic Site URL detection
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
$domainName = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
if (strpos($domainName, 'localhost:8000') !== false) {
    define('SITE_URL', $protocol . $domainName);
} else {
    define('SITE_URL', $protocol . $domainName . '/hotel');
}
define('SITE_EMAIL',  'info@grandazure.com');
define('SITE_PHONE',  '+1 (555) 123-4567');
define('SITE_ADDRESS','123 Azure Boulevard, Luxury District, NY 10001, USA');

// Currency
define('CURRENCY',    '$');
define('CURRENCY_CODE', 'USD');

// Session lifetime (seconds)
define('SESSION_LIFETIME', 3600);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
