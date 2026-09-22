<?php
// ============================================================
//  Grand Azure Hotel — Terms of Service
// ============================================================

$page_title = 'Terms of Service';
$meta_desc  = "Grand Azure Hotel's Terms of Service — booking, cancellation, and stay policies.";

require_once 'includes/functions.php';
require_once 'includes/header.php';
?>

<!-- PAGE HERO -->
<section class="page-hero" aria-label="Terms of Service">
    <div class="container">
        <p class="page-hero-eyebrow">Legal</p>
        <h1 class="page-hero-title">Terms of Service</h1>
        <p class="page-hero-subtitle">Last updated: <?= date('F Y') ?></p>
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="index.php">Home</a>
            <span class="breadcrumb-sep" aria-hidden="true">›</span>
            <span aria-current="page">Terms of Service</span>
        </nav>
    </div>
</section>

<section class="section bg-white">
    <div class="container" style="max-width: 820px;">

        <div class="reveal" style="margin-bottom: var(--space-8); color: var(--gray-600); line-height: 1.8;">
            <p>These Terms of Service ("Terms") govern your use of the Grand Azure Hotel website and your booking of accommodations, dining, and services with us. By creating an account, making a reservation, or otherwise using this website, you agree to these Terms.</p>
        </div>

        <div class="reveal" style="margin-bottom: var(--space-8);">
            <h2 style="font-family: var(--font-serif); color: var(--navy); font-size: var(--text-2xl); margin-bottom: var(--space-3);">1. Reservations &amp; Booking</h2>
            <p style="color: var(--gray-600); line-height: 1.8;">All bookings made through this website are subject to room availability at the time of confirmation. A valid account with an accurate name, email address, and phone number is required to complete a reservation. You are responsible for reviewing your check-in and check-out dates, room type, and guest count before confirming a booking.</p>
        </div>

        <div class="reveal" style="margin-bottom: var(--space-8);">
            <h2 style="font-family: var(--font-serif); color: var(--navy); font-size: var(--text-2xl); margin-bottom: var(--space-3);">2. Cancellations &amp; Refunds</h2>
            <p style="color: var(--gray-600); line-height: 1.8;">Cancellations made 48 hours or more before the scheduled check-in time are eligible for a full refund. Cancellations made within 48 hours of check-in will be charged the equivalent of one night's room rate. No-shows will be charged the full reservation amount. Refunds, where applicable, are processed back to the original payment method within a reasonable timeframe.</p>
        </div>

        <div class="reveal" style="margin-bottom: var(--space-8);">
            <h2 style="font-family: var(--font-serif); color: var(--navy); font-size: var(--text-2xl); margin-bottom: var(--space-3);">3. Check-In &amp; Check-Out</h2>
            <p style="color: var(--gray-600); line-height: 1.8;">Standard check-in time is 3:00 PM and check-out is 12:00 PM (noon). Early check-in and late check-out may be requested and are granted subject to availability, and may incur an additional charge. Guests must present a valid government-issued ID matching the name on the reservation at check-in.</p>
        </div>

        <div class="reveal" style="margin-bottom: var(--space-8);">
            <h2 style="font-family: var(--font-serif); color: var(--navy); font-size: var(--text-2xl); margin-bottom: var(--space-3);">4. Guest Conduct</h2>
            <p style="color: var(--gray-600); line-height: 1.8;">Guests are expected to treat hotel staff, other guests, and hotel property with respect. Grand Azure Hotel reserves the right to refuse service or remove a guest from the premises, without refund, in cases of disruptive behavior, damage to property, or violation of local law.</p>
        </div>

        <div class="reveal" style="margin-bottom: var(--space-8);">
            <h2 style="font-family: var(--font-serif); color: var(--navy); font-size: var(--text-2xl); margin-bottom: var(--space-3);">5. Account Responsibility</h2>
            <p style="color: var(--gray-600); line-height: 1.8;">You are responsible for maintaining the confidentiality of your account credentials and for all activity that occurs under your account. Notify us immediately if you suspect unauthorized use of your account.</p>
        </div>

        <div class="reveal" style="margin-bottom: var(--space-8);">
            <h2 style="font-family: var(--font-serif); color: var(--navy); font-size: var(--text-2xl); margin-bottom: var(--space-3);">6. Changes to These Terms</h2>
            <p style="color: var(--gray-600); line-height: 1.8;">We may update these Terms from time to time to reflect changes in our policies or services. Continued use of the website after changes are posted constitutes acceptance of the revised Terms.</p>
        </div>

        <div class="reveal" style="background: var(--cream); border-radius: var(--radius-lg); padding: var(--space-6);">
            <h2 style="font-family: var(--font-serif); color: var(--navy); font-size: var(--text-xl); margin-bottom: var(--space-3);">Questions?</h2>
            <p style="color: var(--gray-600); line-height: 1.8;">If you have any questions about these Terms, please <a href="contact.php" style="color: var(--gold); font-weight: 600;">contact us</a>.</p>
        </div>

    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
