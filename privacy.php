<?php
// ============================================================
//  Grand Azure Hotel — Privacy Policy
// ============================================================

$page_title = 'Privacy Policy';
$meta_desc  = "Grand Azure Hotel's Privacy Policy — how we collect, use, and protect your information.";

require_once 'includes/functions.php';
require_once 'includes/header.php';
?>

<!-- PAGE HERO -->
<section class="page-hero" aria-label="Privacy Policy">
    <div class="container">
        <p class="page-hero-eyebrow">Legal</p>
        <h1 class="page-hero-title">Privacy Policy</h1>
        <p class="page-hero-subtitle">Last updated: <?= date('F Y') ?></p>
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="index.php">Home</a>
            <span class="breadcrumb-sep" aria-hidden="true">›</span>
            <span aria-current="page">Privacy Policy</span>
        </nav>
    </div>
</section>

<section class="section bg-white">
    <div class="container" style="max-width: 820px;">

        <div class="reveal" style="margin-bottom: var(--space-8); color: var(--gray-600); line-height: 1.8;">
            <p>Grand Azure Hotel ("we", "us", "our") respects your privacy. This Privacy Policy explains what information we collect when you use this website, how we use it, and the choices you have.</p>
        </div>

        <div class="reveal" style="margin-bottom: var(--space-8);">
            <h2 style="font-family: var(--font-serif); color: var(--navy); font-size: var(--text-2xl); margin-bottom: var(--space-3);">1. Information We Collect</h2>
            <p style="color: var(--gray-600); line-height: 1.8; margin-bottom: var(--space-3);">When you create an account, make a reservation, or contact us, we may collect:</p>
            <ul style="color: var(--gray-600); line-height: 1.8; padding-left: var(--space-6);">
                <li>Your name, email address, and phone number</li>
                <li>Reservation details — dates, room type, number of guests, and special requests</li>
                <li>Messages you send us through the contact form</li>
                <li>Basic technical information such as browser type, used to keep the site working correctly</li>
            </ul>
        </div>

        <div class="reveal" style="margin-bottom: var(--space-8);">
            <h2 style="font-family: var(--font-serif); color: var(--navy); font-size: var(--text-2xl); margin-bottom: var(--space-3);">2. How We Use Your Information</h2>
            <p style="color: var(--gray-600); line-height: 1.8; margin-bottom: var(--space-3);">We use the information we collect to:</p>
            <ul style="color: var(--gray-600); line-height: 1.8; padding-left: var(--space-6);">
                <li>Process and manage your reservations</li>
                <li>Communicate with you about your booking or inquiry</li>
                <li>Maintain your account and booking history</li>
                <li>Improve the website and the services we offer</li>
            </ul>
        </div>

        <div class="reveal" style="margin-bottom: var(--space-8);">
            <h2 style="font-family: var(--font-serif); color: var(--navy); font-size: var(--text-2xl); margin-bottom: var(--space-3);">3. How We Protect Your Information</h2>
            <p style="color: var(--gray-600); line-height: 1.8;">Your password is stored using industry-standard one-way hashing and is never stored or visible in plain text, including to our own staff. We take reasonable technical measures to protect your account and booking information from unauthorized access.</p>
        </div>

        <div class="reveal" style="margin-bottom: var(--space-8);">
            <h2 style="font-family: var(--font-serif); color: var(--navy); font-size: var(--text-2xl); margin-bottom: var(--space-3);">4. Sharing of Information</h2>
            <p style="color: var(--gray-600); line-height: 1.8;">We do not sell your personal information. Your details are only used internally to manage your stay with us, and are shared with third parties only where necessary to process a payment or where required by law.</p>
        </div>

        <div class="reveal" style="margin-bottom: var(--space-8);">
            <h2 style="font-family: var(--font-serif); color: var(--navy); font-size: var(--text-2xl); margin-bottom: var(--space-3);">5. Your Choices</h2>
            <p style="color: var(--gray-600); line-height: 1.8;">You can review and update your account details at any time from your account dashboard. To request that we delete your account or the personal information we hold about you, please contact us using the details below.</p>
        </div>

        <div class="reveal" style="background: var(--cream); border-radius: var(--radius-lg); padding: var(--space-6);">
            <h2 style="font-family: var(--font-serif); color: var(--navy); font-size: var(--text-xl); margin-bottom: var(--space-3);">Questions?</h2>
            <p style="color: var(--gray-600); line-height: 1.8;">If you have any questions about this Privacy Policy or how your information is handled, please <a href="contact.php" style="color: var(--gold); font-weight: 600;">contact us</a>.</p>
        </div>

    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
