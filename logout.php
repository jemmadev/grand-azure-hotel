<?php
// ============================================================
//  Grand Azure Hotel — Logout
// ============================================================
require_once 'includes/config.php';

// Remember which type of account is logging out, before we wipe the session
$was_admin = isset($_SESSION['admin_id']);

// Destroy all session data
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}
session_destroy();

// Clear remember-me cookie
if (isset($_COOKIE['remember_user'])) {
    setcookie('remember_user', '', time() - 3600, '/');
}

$destination = $was_admin ? '/admin/login.php' : '/login.php?logged_out=1';
header('Location: ' . SITE_URL . $destination);
exit;
