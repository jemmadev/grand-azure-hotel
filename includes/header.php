<?php
// ============================================================
//  Grand Azure Hotel — Shared Header Component
//  Include at top of every public-facing page
// ============================================================
require_once __DIR__ . '/functions.php';

// Site settings editable from Admin > Settings (hotel name, logo, etc.)
// Falls back to the SITE_NAME constant if a key isn't set.
$settings = get_all_settings($conn);
$hotel_name = $settings['hotel_name'] ?? SITE_NAME;

// Determine active page for nav highlighting
$current_page = basename($_SERVER['PHP_SELF']);
function nav_class(string $page, string $current): string {
    return $page === $current ? 'nav-link active' : 'nav-link';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- SEO Meta -->
    <meta name="description"  content="<?= htmlspecialchars($meta_desc  ?? $hotel_name . ' — Luxury stays in the heart of the city. Book your dream room today.') ?>">
    <meta name="keywords"     content="<?= htmlspecialchars($meta_keywords ?? 'luxury hotel, hotel booking, ' . strtolower($hotel_name) . ', rooms, suites') ?>">
    <meta name="author"       content="<?= htmlspecialchars($hotel_name) ?>">

    <!-- Open Graph -->
    <meta property="og:type"        content="website">
    <meta property="og:title"       content="<?= htmlspecialchars(($page_title ?? 'Welcome') . ' | ' . $hotel_name) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($meta_desc ?? 'Experience unparalleled luxury at ' . $hotel_name . '.') ?>">
    <meta property="og:url"         content="<?= SITE_URL ?>">

    <title><?= htmlspecialchars(($page_title ?? 'Welcome') . ' | ' . $hotel_name) ?></title>

    <!-- Favicon (inline SVG data URI) -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='6' fill='%230a1628'/%3E%3Ctext y='22' x='5' font-size='18' font-family='Georgia' fill='%23c9a84c'%3EGA%3C/text%3E%3C/svg%3E">

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Global Stylesheet -->
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">

    <!-- Page-specific stylesheet (optional) -->
    <?php if (!empty($extra_css)): ?>
        <link rel="stylesheet" href="<?= SITE_URL . '/assets/css/' . $extra_css ?>">
    <?php endif; ?>
</head>
<body>

<!-- ===================== NAVIGATION ======================= -->
<nav class="navbar transparent" id="navbar" role="navigation" aria-label="Main navigation">
    <div class="container">
        <div class="nav-inner">

            <!-- Logo -->
            <a href="<?= SITE_URL ?>/index.php" class="nav-logo" aria-label="<?= htmlspecialchars($hotel_name) ?> Home">
                <?php if (!empty($settings['logo'])): ?>
                    <img src="<?= htmlspecialchars(image_url($settings['logo'])) ?>" alt="<?= htmlspecialchars($hotel_name) ?> logo" style="height: 36px; width: auto;">
                <?php else: ?>
                    <div class="nav-logo-icon" aria-hidden="true"><?= htmlspecialchars(strtoupper(substr($hotel_name, 0, 1) . (strpos($hotel_name, ' ') !== false ? substr($hotel_name, strpos($hotel_name, ' ') + 1, 1) : ''))) ?></div>
                <?php endif; ?>
                <div class="nav-logo-text">
                    <span class="nav-logo-name"><?= htmlspecialchars($hotel_name) ?></span>
                    <span class="nav-logo-tagline">Luxury Hotel</span>
                </div>
            </a>

            <!-- Main Menu -->
            <ul class="nav-menu" id="navMenu" role="menubar">
                <li role="none"><a href="<?= SITE_URL ?>/index.php"      class="<?= nav_class('index.php', $current_page) ?>"      role="menuitem">Home</a></li>
                <li role="none"><a href="<?= SITE_URL ?>/about.php"      class="<?= nav_class('about.php', $current_page) ?>"      role="menuitem">About</a></li>
                <li role="none"><a href="<?= SITE_URL ?>/rooms.php"      class="<?= nav_class('rooms.php', $current_page) ?>"      role="menuitem">Rooms</a></li>
                <li role="none"><a href="<?= SITE_URL ?>/services.php"   class="<?= nav_class('services.php', $current_page) ?>"   role="menuitem">Services</a></li>
                <li role="none"><a href="<?= SITE_URL ?>/restaurant.php" class="<?= nav_class('restaurant.php', $current_page) ?>" role="menuitem">Restaurant</a></li>
                <li role="none"><a href="<?= SITE_URL ?>/gallery.php"    class="<?= nav_class('gallery.php', $current_page) ?>"    role="menuitem">Gallery</a></li>
                <li role="none"><a href="<?= SITE_URL ?>/contact.php"    class="<?= nav_class('contact.php', $current_page) ?>"    role="menuitem">Contact</a></li>
            </ul>

            <!-- CTA Buttons -->
            <div class="nav-actions" id="navActions">
                <?php if (is_logged_in()): ?>
                    <a href="<?= SITE_URL ?>/user/dashboard.php" class="btn btn-outline btn-sm">
                        <i class="fas fa-user" aria-hidden="true"></i>
                        <?= htmlspecialchars($_SESSION['user_name'] ?? 'My Account') ?>
                    </a>
                    <a href="<?= SITE_URL ?>/logout.php" class="btn btn-primary btn-sm">Logout</a>
                <?php else: ?>
                    <a href="<?= SITE_URL ?>/login.php"  class="btn btn-outline btn-sm">Login</a>
                    <a href="<?= SITE_URL ?>/booking.php" class="btn btn-primary btn-sm">
                        <i class="fas fa-calendar-check" aria-hidden="true"></i> Book Now
                    </a>
                <?php endif; ?>
            </div>

            <!-- Hamburger Toggle (mobile) -->
            <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation" aria-expanded="false" aria-controls="navMenu">
                <span></span>
                <span></span>
                <span></span>
            </button>

        </div>
    </div>
</nav>
<!-- =================== END NAVIGATION ===================== -->
