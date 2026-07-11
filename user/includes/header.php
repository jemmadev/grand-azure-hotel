<?php
// ============================================================
//  Grand Azure Hotel — User Dashboard Header
//  Include at the top of every page inside /user
// ============================================================
require_once __DIR__ . '/../../includes/functions.php';
require_user_login();

$user_id   = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Fetch fresh user data (email, etc.) for the sidebar card
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Highlight the active sidebar link
$user_current = basename($_SERVER['PHP_SELF']);
function user_nav_class(string $page, string $current): string {
    return $page === $current ? 'sidebar-link active' : 'sidebar-link';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'My Account') ?> | Grand Azure Hotel</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='6' fill='%230a1628'/%3E%3Ctext y='22' x='5' font-size='18' font-family='Georgia' fill='%23c9a84c'%3EGA%3C/text%3E%3C/svg%3E">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
    <style>
        .dashboard-wrapper { display:grid; grid-template-columns:260px 1fr; min-height:100vh; background:var(--cream); }
        .sidebar { background:var(--navy); color:var(--white); padding:var(--space-8) 0; position:sticky; top:0; height:100vh; overflow-y:auto; }
        .sidebar-user { padding:0 var(--space-6) var(--space-8); border-bottom:1px solid rgba(255,255,255,.1); margin-bottom:var(--space-6); }
        .sidebar-avatar { width:72px; height:72px; border-radius:50%; background:linear-gradient(135deg,var(--gold),var(--gold-dark)); display:flex; align-items:center; justify-content:center; font-family:var(--font-serif); font-size:var(--text-2xl); font-weight:700; color:var(--navy); margin-bottom:var(--space-4); }
        .sidebar-link { display:flex; align-items:center; gap:var(--space-3); padding:var(--space-3) var(--space-6); font-size:var(--text-sm); color:rgba(255,255,255,.7); transition:all .2s; border-left:3px solid transparent; text-decoration:none; }
        .sidebar-link:hover,.sidebar-link.active { background:rgba(255,255,255,.05); color:var(--white); border-left-color:var(--gold); }
        .sidebar-link i { width:20px; text-align:center; }
        .main-content { padding:var(--space-10); padding-top:100px; }
        .dash-card { background:var(--white); border-radius:var(--radius-md); padding:var(--space-6); box-shadow:var(--shadow-sm); }
        .stat-card { background:var(--white); border-radius:var(--radius-md); padding:var(--space-6); display:flex; align-items:center; gap:var(--space-5); box-shadow:var(--shadow-sm); }
        .stat-icon { width:56px; height:56px; border-radius:var(--radius); display:flex; align-items:center; justify-content:center; font-size:var(--text-2xl); flex-shrink:0; }
        @media(max-width:768px){ .dashboard-wrapper{grid-template-columns:1fr;} .sidebar{position:relative;height:auto;} }
    </style>
</head>
<body>

<div class="dashboard-wrapper">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-user">
            <div class="sidebar-avatar"><?= strtoupper(substr($user_name, 0, 1)) ?></div>
            <div style="font-weight:700; color:var(--white);"><?= htmlspecialchars($user_name) ?></div>
            <div style="font-size:var(--text-xs); color:rgba(255,255,255,.5);"><?= htmlspecialchars($user['email']) ?></div>
        </div>
        <nav>
            <a href="dashboard.php" class="<?= user_nav_class('dashboard.php', $user_current) ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="history.php"   class="<?= user_nav_class('history.php', $user_current) ?>"><i class="fas fa-history"></i> My Bookings</a>
            <a href="profile.php"   class="<?= user_nav_class('profile.php', $user_current) ?>"><i class="fas fa-user-edit"></i> Edit Profile</a>
            <a href="../booking.php" class="sidebar-link"><i class="fas fa-calendar-plus"></i> New Booking</a>
            <a href="../index.php"  class="sidebar-link"><i class="fas fa-home"></i> Back to Site</a>
            <a href="../logout.php" class="sidebar-link" style="margin-top:var(--space-8); color:rgba(255,80,80,.8);"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <?php show_flash(); ?>
