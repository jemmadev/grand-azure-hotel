<?php
// ============================================================
//  Grand Azure Hotel — Admin Header
// ============================================================
require_once __DIR__ . '/../../includes/functions.php';
require_admin_login();

$admin_name = $_SESSION['admin_name'];
$admin_email= $_SESSION['admin_email'];

// Highlight helper
$admin_current = basename($_SERVER['PHP_SELF']);
function admin_nav_class(string $page, string $current): string {
    return $page === $current ? 'sidebar-link active' : 'sidebar-link';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'Admin Panel') ?> | Grand Azure Hotel</title>
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='6' fill='%230a1628'/%3E%3Ctext y='22' x='5' font-size='18' font-family='Georgia' fill='%23c9a84c'%3EGA%3C/text%3E%3C/svg%3E">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
    <style>
        .admin-wrapper { display: grid; grid-template-columns: 260px 1fr; min-height: 100vh; background: var(--cream); }
        .sidebar { background: var(--navy); color: var(--white); padding: var(--space-8) 0; position: sticky; top: 0; height: 100vh; overflow-y: auto; z-index: 100; }
        .sidebar-brand { padding: 0 var(--space-6) var(--space-6); border-bottom: 1px solid rgba(255,255,255,.1); margin-bottom: var(--space-6); }
        .sidebar-link { display: flex; align-items: center; gap: var(--space-3); padding: var(--space-3) var(--space-6); font-size: var(--text-sm); color: rgba(255,255,255,.75); transition: all .2s; border-left: 3px solid transparent; text-decoration: none; }
        .sidebar-link:hover, .sidebar-link.active { background: rgba(255,255,255,.05); color: var(--white); border-left-color: var(--gold); }
        .sidebar-link i { width: 20px; text-align: center; }
        .main-content { padding: var(--space-10); }
        .admin-card { background: var(--white); border-radius: var(--radius-md); padding: var(--space-8); box-shadow: var(--shadow-sm); margin-bottom: var(--space-6); }
        .admin-table { width: 100%; border-collapse: collapse; font-size: var(--text-sm); }
        .admin-table th { padding: var(--space-3); text-align: left; color: var(--gray-500); font-weight: 600; border-bottom: 2px solid var(--gray-100); }
        .admin-table td { padding: var(--space-4) var(--space-3); border-bottom: 1px solid var(--gray-100); color: var(--gray-700); }
        .admin-table tr:hover { background: var(--gray-100); }
        @media(max-width:768px){ .admin-wrapper { grid-template-columns: 1fr; } .sidebar { position: relative; height: auto; } }

        /* Print-friendly view: used by Reports "Print / Export PDF" button.
           Hides the sidebar, buttons, and flash messages so the printed
           page (or browser's "Save as PDF") only shows the report itself. */
        @media print {
            .sidebar, .no-print, .btn, form button { display: none !important; }
            .admin-wrapper { display: block; }
            .main-content { padding: 0; }
            .admin-card { box-shadow: none; border: 1px solid #ddd; break-inside: avoid; }
        }
    </style>
</head>
<body>

<div class="admin-wrapper">
    <!-- Admin Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="nav-logo" style="margin-bottom: var(--space-4);">
                <div class="nav-logo-icon">GA</div>
                <div class="nav-logo-text">
                    <span class="nav-logo-name" style="color:var(--white);">Grand Azure</span>
                    <span class="nav-logo-tagline">Admin Control</span>
                </div>
            </div>
            <div style="font-size: var(--text-xs); color: var(--gold); font-weight: 600; text-transform: uppercase;">
                <i class="fas fa-user-shield"></i> <?= htmlspecialchars($admin_name) ?>
            </div>
        </div>
        <nav>
            <a href="dashboard.php" class="<?= admin_nav_class('dashboard.php', $admin_current) ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="bookings.php"  class="<?= admin_nav_class('bookings.php', $admin_current) ?>"><i class="fas fa-calendar-check"></i> Bookings</a>
            <a href="rooms.php"     class="<?= admin_nav_class('rooms.php', $admin_current) ?>"><i class="fas fa-door-open"></i> Rooms</a>
            <a href="gallery.php"   class="<?= admin_nav_class('gallery.php', $admin_current) ?>"><i class="fas fa-images"></i> Gallery</a>
            <a href="users.php"     class="<?= admin_nav_class('users.php', $admin_current) ?>"><i class="fas fa-users"></i> Users</a>
            <a href="reports.php"   class="<?= admin_nav_class('reports.php', $admin_current) ?>"><i class="fas fa-chart-line"></i> Reports</a>
            <a href="settings.php"  class="<?= admin_nav_class('settings.php', $admin_current) ?>"><i class="fas fa-cog"></i> Settings</a>
            <a href="../index.php"  class="sidebar-link" target="_blank"><i class="fas fa-external-link-alt"></i> View Website</a>
            <a href="../logout.php" class="sidebar-link" style="margin-top: var(--space-8); color: rgba(255,80,80,.8);"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <?php show_flash(); ?>
