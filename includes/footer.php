<?php
// ============================================================
//  Grand Azure Hotel — Shared Footer Component
//  Include at bottom of every public-facing page
// ============================================================
?>

<!-- ===================== FOOTER =========================== -->
<footer class="footer" role="contentinfo">
    <div class="container">
        <div class="footer-grid">

            <!-- Brand column -->
            <div class="footer-brand">
                <a href="<?= SITE_URL ?>/index.php" class="nav-logo" style="margin-bottom:1.25rem; display:inline-flex;">
                    <?php if (!empty($settings['logo'])): ?>
                        <img src="<?= htmlspecialchars(image_url($settings['logo'])) ?>" alt="<?= htmlspecialchars($hotel_name) ?> logo" style="height: 36px; width: auto;">
                    <?php else: ?>
                        <div class="nav-logo-icon"><?= htmlspecialchars(strtoupper(substr($hotel_name, 0, 1) . (strpos($hotel_name, ' ') !== false ? substr($hotel_name, strpos($hotel_name, ' ') + 1, 1) : ''))) ?></div>
                    <?php endif; ?>
                    <div class="nav-logo-text">
                        <span class="nav-logo-name"><?= htmlspecialchars($hotel_name) ?></span>
                        <span class="nav-logo-tagline">Luxury Hotel</span>
                    </div>
                </a>
                <p class="footer-desc">
                    <?= htmlspecialchars($settings['footer_text'] ?? '') ?>
                </p>
                <div class="footer-social" role="list" aria-label="Social media links">
                    <a href="<?= htmlspecialchars($settings['social_facebook'] ?? '#') ?>" class="social-link" role="listitem" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="<?= htmlspecialchars($settings['social_instagram'] ?? '#') ?>" class="social-link" role="listitem" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="<?= htmlspecialchars($settings['social_twitter'] ?? '#') ?>" class="social-link" role="listitem" aria-label="Twitter / X"><i class="fab fa-x-twitter"></i></a>
                    <a href="<?= htmlspecialchars($settings['social_tripadvisor'] ?? '#') ?>" class="social-link" role="listitem" aria-label="TripAdvisor"><i class="fab fa-tripadvisor"></i></a>
                    <a href="<?= htmlspecialchars($settings['social_youtube'] ?? '#') ?>" class="social-link" role="listitem" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                </div>
            </div>

            <!-- Quick Links -->
            <nav aria-label="Footer quick links">
                <h3 class="footer-heading">Quick Links</h3>
                <ul class="footer-links">
                    <li><a href="<?= SITE_URL ?>/index.php"      class="footer-link"><i class="fas fa-chevron-right"></i> Home</a></li>
                    <li><a href="<?= SITE_URL ?>/about.html"      class="footer-link"><i class="fas fa-chevron-right"></i> About Us</a></li>
                    <li><a href="<?= SITE_URL ?>/rooms.php"       class="footer-link"><i class="fas fa-chevron-right"></i> Rooms &amp; Suites</a></li>
                    <li><a href="<?= SITE_URL ?>/services.php"   class="footer-link"><i class="fas fa-chevron-right"></i> Services</a></li>
                    <li><a href="<?= SITE_URL ?>/restaurant.html" class="footer-link"><i class="fas fa-chevron-right"></i> Restaurant</a></li>
                    <li><a href="<?= SITE_URL ?>/gallery.php"    class="footer-link"><i class="fas fa-chevron-right"></i> Gallery</a></li>
                    <li><a href="<?= SITE_URL ?>/faq.php"        class="footer-link"><i class="fas fa-chevron-right"></i> FAQ</a></li>
                    <li><a href="<?= SITE_URL ?>/contact.php"    class="footer-link"><i class="fas fa-chevron-right"></i> Contact Us</a></li>
                </ul>
            </nav>

            <!-- Room Types -->
            <nav aria-label="Room types">
                <h3 class="footer-heading">Room Types</h3>
                <ul class="footer-links">
                    <li><a href="<?= SITE_URL ?>/rooms.php?cat=Standard"          class="footer-link"><i class="fas fa-chevron-right"></i> Standard Room</a></li>
                    <li><a href="<?= SITE_URL ?>/rooms.php?cat=Deluxe"            class="footer-link"><i class="fas fa-chevron-right"></i> Deluxe Room</a></li>
                    <li><a href="<?= SITE_URL ?>/rooms.php?cat=Executive"         class="footer-link"><i class="fas fa-chevron-right"></i> Executive Room</a></li>
                    <li><a href="<?= SITE_URL ?>/rooms.php?cat=Family"            class="footer-link"><i class="fas fa-chevron-right"></i> Family Room</a></li>
                    <li><a href="<?= SITE_URL ?>/rooms.php?cat=Presidential+Suite" class="footer-link"><i class="fas fa-chevron-right"></i> Presidential Suite</a></li>
                    <li><a href="<?= SITE_URL ?>/booking.php"                     class="footer-link"><i class="fas fa-chevron-right"></i> Book Now</a></li>
                </ul>
            </nav>

            <!-- Contact Info -->
            <address>
                <h3 class="footer-heading">Contact Us</h3>
                <div class="footer-contact-item">
                    <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                    <span><?= htmlspecialchars($settings['address'] ?? SITE_ADDRESS) ?></span>
                </div>
                <div class="footer-contact-item">
                    <i class="fas fa-phone" aria-hidden="true"></i>
                    <a href="tel:<?= str_replace([' ', '(', ')', '-'], '', $settings['phone'] ?? SITE_PHONE) ?>" style="color:inherit"><?= htmlspecialchars($settings['phone'] ?? SITE_PHONE) ?></a>
                </div>
                <div class="footer-contact-item">
                    <i class="fas fa-envelope" aria-hidden="true"></i>
                    <a href="mailto:<?= htmlspecialchars($settings['email'] ?? SITE_EMAIL) ?>" style="color:inherit"><?= htmlspecialchars($settings['email'] ?? SITE_EMAIL) ?></a>
                </div>
                <div class="footer-contact-item">
                    <i class="fas fa-clock" aria-hidden="true"></i>
                    <span>Check-in: 3:00 PM &nbsp;|&nbsp; Check-out: 12:00 PM</span>
                </div>
                <div style="margin-top:1.5rem;">
                    <a href="<?= SITE_URL ?>/booking.php" class="btn btn-primary btn-sm">
                        <i class="fas fa-calendar-check"></i> Reserve a Room
                    </a>
                </div>
            </address>

        </div>

        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> <strong style="color:rgba(255,255,255,.7)"><?= htmlspecialchars($hotel_name ?? SITE_NAME) ?></strong>. <?= htmlspecialchars($settings['copyright_text'] ?? 'All rights reserved.') ?></p>
            <p>Designed with <i class="fas fa-heart" style="color:var(--gold);margin:0 2px"></i> for luxury hospitality</p>
        </div>
    </div>
</footer>
<!-- =================== END FOOTER ========================= -->

<!-- Scroll-to-top button -->
<button class="scroll-top" id="scrollTop" aria-label="Scroll to top">
    <i class="fas fa-arrow-up" aria-hidden="true"></i>
</button>

<!-- ===================== SCRIPTS ========================== -->
<script src="<?= SITE_URL ?>/assets/js/main.js"></script>
<?php if (!empty($extra_js)): ?>
    <script src="<?= SITE_URL ?>/assets/js/<?= $extra_js ?>"></script>
<?php endif; ?>
</body>
</html>
