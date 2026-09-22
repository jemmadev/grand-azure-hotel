<?php
// ============================================================
//  Grand Azure Hotel — Home Page
// ============================================================

$page_title   = 'Welcome';
$meta_desc    = 'Grand Azure Hotel — Experience unparalleled luxury in the heart of the city. Award-winning service, world-class amenities, and unforgettable stays.';
$meta_keywords= 'luxury hotel, hotel booking, grand azure hotel, city hotel, suites, rooms';

require_once 'includes/functions.php';

// Fetch homepage data
$featured_rooms   = get_all_rooms($conn);
$featured_rooms   = array_slice($featured_rooms, 0, 3); // Show top 3
$services         = get_services($conn, 8);
$gallery_preview  = get_gallery($conn, '', 6);
$testimonials     = get_testimonials($conn, 4);

require_once 'includes/header.php';
?>

<!-- ============================================================
     HERO SECTION
============================================================ -->
<section class="hero" id="hero" aria-label="Welcome to Grand Azure Hotel">
    <div class="hero-bg" style="background-image: url('<?= htmlspecialchars(image_url(setting($conn, 'hero_image', 'assets/images/hero.jpg'))) ?>');" role="img" aria-label="Grand Azure Hotel exterior view"></div>
    <div class="hero-overlay"></div>

    <div class="hero-content animate-fade-up">
        <div class="hero-eyebrow">
            <span>✦</span>
            <span>5-Star Luxury Experience</span>
            <span>✦</span>
        </div>
        <h1 class="hero-title">
            <?php
                $hero_title = $settings['hero_title'] ?? 'Where Luxury Meets Timeless Elegance';
                // Split on the last space so the final word/phrase gets the
                // gold accent styling, matching the original design intent.
                $hero_words = explode(' ', $hero_title);
                $hero_accent = array_pop($hero_words);
                $hero_lead   = implode(' ', $hero_words);
            ?>
            <?= htmlspecialchars($hero_lead) ?><br>
            <span class="gold-text"><?= htmlspecialchars($hero_accent) ?></span>
        </h1>
        <p class="hero-subtitle">
            <?= htmlspecialchars($settings['hero_subtitle'] ?? 'Discover a world of refined sophistication at Grand Azure Hotel. Award-winning service, breathtaking views, and an unparalleled commitment to your comfort.') ?>
        </p>
        <div class="hero-actions">
            <a href="booking.php" class="btn btn-primary btn-lg">
                <i class="fas fa-calendar-check" aria-hidden="true"></i>
                Reserve Your Stay
            </a>
            <a href="rooms.php" class="btn btn-outline btn-lg">
                <i class="fas fa-door-open" aria-hidden="true"></i>
                Explore Rooms
            </a>
        </div>
    </div>

    <!-- Scroll indicator -->
    <div class="hero-scroll" aria-hidden="true">
        <div class="scroll-icon"><div class="scroll-dot"></div></div>
        <span>Scroll</span>
    </div>
</section>

<!-- ============================================================
     QUICK BOOKING BAR
============================================================ -->
<section class="section-sm bg-cream" aria-label="Quick room search">
    <div class="container">
        <div class="booking-bar">
            <form id="quickBookingForm" action="booking.php" method="GET" data-validate>
                <div class="booking-bar-form">
                    <!-- Check-in -->
                    <div class="booking-field">
                        <label for="bar_check_in">Check-In</label>
                        <div class="field-inner">
                            <i class="fas fa-calendar-alt" aria-hidden="true"></i>
                            <input type="date" id="bar_check_in" name="check_in" required>
                        </div>
                    </div>
                    <!-- Check-out -->
                    <div class="booking-field">
                        <label for="bar_check_out">Check-Out</label>
                        <div class="field-inner">
                            <i class="fas fa-calendar-check" aria-hidden="true"></i>
                            <input type="date" id="bar_check_out" name="check_out" required>
                        </div>
                    </div>
                    <!-- Guests -->
                    <div class="booking-field">
                        <label for="bar_guests">Guests</label>
                        <div class="field-inner">
                            <i class="fas fa-users" aria-hidden="true"></i>
                            <select id="bar_guests" name="guests">
                                <?php for ($g = 1; $g <= 6; $g++): ?>
                                    <option value="<?= $g ?>"><?= $g ?> <?= $g === 1 ? 'Guest' : 'Guests' ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    <!-- Room Type -->
                    <div class="booking-field">
                        <label for="bar_room_type">Room Type</label>
                        <div class="field-inner">
                            <i class="fas fa-bed" aria-hidden="true"></i>
                            <select id="bar_room_type" name="room_type">
                                <option value="">Any Room</option>
                                <option value="Standard">Standard Room</option>
                                <option value="Deluxe">Deluxe Room</option>
                                <option value="Executive">Executive Room</option>
                                <option value="Family">Family Room</option>
                                <option value="Presidential Suite">Presidential Suite</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-search" aria-hidden="true"></i>
                        Check Availability
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- ============================================================
     STATS BAR
============================================================ -->
<section class="stats-bar" aria-label="Hotel statistics">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item reveal">
                <div class="stat-number" data-count="<?= htmlspecialchars(setting($conn, 'stat_rooms', '250')) ?>" data-suffix="+">0</div>
                <div class="stat-label">Luxury Rooms</div>
            </div>
            <div class="stat-item reveal delay-1">
                <div class="stat-number" data-count="<?= htmlspecialchars(setting($conn, 'stat_guests', '15000')) ?>" data-suffix="+">0</div>
                <div class="stat-label">Happy Guests</div>
            </div>
            <div class="stat-item reveal delay-2">
                <div class="stat-number" data-count="<?= htmlspecialchars(setting($conn, 'stat_years', '25')) ?>" data-suffix="">0</div>
                <div class="stat-label">Years of Excellence</div>
            </div>
            <div class="stat-item reveal delay-3">
                <div class="stat-number" data-count="<?= htmlspecialchars(setting($conn, 'stat_awards', '12')) ?>" data-suffix="">0</div>
                <div class="stat-label">Award Wins</div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     ABOUT INTRO
============================================================ -->
<section class="section bg-white" aria-label="About Grand Azure Hotel">
    <div class="container">
        <div class="grid-2" style="align-items:center; gap:5rem;">
            <!-- Image side -->
            <div class="reveal">
                <div style="position:relative;">
                    <img
                        src="<?= htmlspecialchars(image_url(setting($conn, 'about_image', 'assets/images/about.jpg'))) ?>"
                        alt="Grand Azure Hotel Lobby"
                        style="width:100%; height:500px; object-fit:cover; border-radius:var(--radius-lg);"
                        loading="lazy">
                    <!-- Floating card -->
                    <div style="position:absolute; bottom:-30px; right:-30px; background:var(--gold); color:var(--navy); padding:var(--space-6) var(--space-8); border-radius:var(--radius-md); text-align:center; box-shadow:var(--shadow-gold);">
                        <div style="font-family:var(--font-serif); font-size:var(--text-4xl); font-weight:700; line-height:1;"><?= htmlspecialchars(setting($conn, 'stat_years', '25')) ?></div>
                        <div style="font-size:var(--text-sm); font-weight:600; letter-spacing:.1em; text-transform:uppercase;">Years of<br>Excellence</div>
                    </div>
                </div>
            </div>
            <!-- Text side -->
            <div class="reveal delay-2">
                <span class="section-eyebrow">Our Story</span>
                <h2 class="section-title" style="text-align:left; margin-bottom:var(--space-5);">
                    <?php
                        $about_title = setting($conn, 'about_title', 'A Legacy of Luxury');
                        $about_words = explode(' ', $about_title);
                        $about_accent = array_pop($about_words);
                        $about_lead   = implode(' ', $about_words);
                    ?>
                    <?= htmlspecialchars($about_lead) ?> <span class="accent"><?= htmlspecialchars($about_accent) ?></span>
                </h2>
                <div class="sep sep-left"></div>
                <p style="color:var(--gray-500); line-height:var(--lh-relaxed); margin-bottom:var(--space-5);">
                    <?= htmlspecialchars(setting($conn, 'about_text_1', 'Since 1999, Grand Azure Hotel has stood as a beacon of excellence in luxury hospitality. Nestled in the heart of the city, we have welcomed dignitaries, celebrities, and discerning travelers who seek nothing but the very best.')) ?>
                </p>
                <p style="color:var(--gray-500); line-height:var(--lh-relaxed); margin-bottom:var(--space-8);">
                    <?= htmlspecialchars(setting($conn, 'about_text_2', 'Our commitment to personalized service, culinary artistry, and unparalleled comfort has earned us 12 prestigious industry awards and the unwavering loyalty of guests from across the globe.')) ?>
                </p>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:var(--space-6); margin-bottom:var(--space-8);">
                    <?php
                    $highlights = [
                        ['fas fa-award',   'Award Winning',   htmlspecialchars(setting($conn, 'stat_awards', '12')) . ' prestigious industry awards'],
                        ['fas fa-concierge-bell', '24/7 Service', 'Round-the-clock concierge'],
                        ['fas fa-leaf',    'Sustainable',     'Eco-certified operations'],
                        ['fas fa-wifi',    'Connected',       'High-speed WiFi throughout'],
                    ];
                    foreach ($highlights as $h): ?>
                    <div style="display:flex; gap:var(--space-3); align-items:flex-start;">
                        <div style="width:44px; height:44px; background:linear-gradient(135deg,rgba(201,168,76,.15),rgba(201,168,76,.25)); border-radius:var(--radius); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <i class="<?= $h[0] ?>" style="color:var(--gold);"></i>
                        </div>
                        <div>
                            <div style="font-weight:700; color:var(--navy); margin-bottom:2px;"><?= $h[1] ?></div>
                            <div style="font-size:var(--text-sm); color:var(--gray-500);"><?= $h[2] ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <a href="about.php" class="btn btn-outline-dark">
                    <i class="fas fa-arrow-right" aria-hidden="true"></i>
                    Our Full Story
                </a>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     FEATURED ROOMS
============================================================ -->
<section class="section bg-cream" id="rooms" aria-label="Featured rooms">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-eyebrow">Accommodations</span>
            <h2 class="section-title">Handcrafted <span class="accent">Rooms</span> & Suites</h2>
            <div class="gold-divider"><i class="fas fa-star"></i></div>
            <p class="section-subtitle">Each of our rooms is a sanctuary of comfort, blending timeless elegance with every modern convenience.</p>
        </div>

        <div class="grid-3" style="margin-bottom:var(--space-12);">
            <?php if (empty($featured_rooms)): ?>
                <div style="grid-column:1/-1; text-align:center; padding:var(--space-12); color:var(--gray-400);">
                    <i class="fas fa-bed" style="font-size:3rem; margin-bottom:1rem;"></i>
                    <p>Rooms will appear here once you populate the database.</p>
                </div>
            <?php else: ?>
                <?php foreach ($featured_rooms as $i => $room): ?>
                <article class="card reveal delay-<?= $i + 1 ?>" aria-label="<?= htmlspecialchars($room['name']) ?>">
                    <div class="card-img-wrapper">
                        <img
                            class="card-img"
                            src="<?= htmlspecialchars($room['image'] ?: 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&w=800&q=80') ?>"
                            alt="<?= htmlspecialchars($room['name']) ?>"
                            loading="lazy">
                        <span class="card-badge"><?= htmlspecialchars($room['category']) ?></span>
                    </div>
                    <div class="card-body">
                        <h3 class="card-title"><?= htmlspecialchars($room['name']) ?></h3>
                        <p class="card-text"><?= htmlspecialchars(truncate($room['description'] ?? '', 110)) ?></p>
                        <!-- Amenities -->
                        <div class="card-amenities">
                            <?php
                            $amenities = array_slice(explode(',', $room['amenities'] ?? ''), 0, 4);
                            foreach ($amenities as $amenity): ?>
                                <span class="amenity-tag">
                                    <i class="fas fa-check" aria-hidden="true"></i>
                                    <?= htmlspecialchars(trim($amenity)) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                        <!-- Capacity + Price -->
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:var(--space-5);">
                            <span style="font-size:var(--text-sm); color:var(--gray-500);">
                                <i class="fas fa-users" style="color:var(--gold); margin-right:4px;"></i>
                                Up to <?= $room['capacity'] ?> guests
                            </span>
                            <div class="card-price" style="margin:0;">
                                <span class="amount"><?= format_price($room['price_per_night']) ?></span>
                                <span class="period">/ night</span>
                            </div>
                        </div>
                        <div style="display:flex; gap:var(--space-3);">
                            <a href="room-detail.php?id=<?= $room['id'] ?>" class="btn btn-outline-dark btn-sm" style="flex:1;">View Details</a>
                            <a href="booking.php?room_id=<?= $room['id'] ?>" class="btn btn-primary btn-sm" style="flex:1;">Book Now</a>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="text-center reveal">
            <a href="rooms.php" class="btn btn-outline-gold btn-lg">
                View All Rooms &amp; Suites
                <i class="fas fa-arrow-right" aria-hidden="true"></i>
            </a>
        </div>
    </div>
</section>

<!-- ============================================================
     SERVICES
============================================================ -->
<section class="section bg-white" id="services" aria-label="Hotel services">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-eyebrow">Amenities</span>
            <h2 class="section-title">World-Class <span class="accent">Services</span></h2>
            <div class="gold-divider"><i class="fas fa-star"></i></div>
            <p class="section-subtitle">From morning wellness to evening entertainment, every moment at Grand Azure is designed to delight.</p>
        </div>

        <div class="grid-4">
            <?php if (empty($services)): ?>
                <?php
                // Fallback static services
                $static_services = [
                    ['fas fa-wifi',            'Free WiFi',       'High-speed wireless throughout the property.'],
                    ['fas fa-swimming-pool',   'Swimming Pool',   'Outdoor infinity pool with panoramic views.'],
                    ['fas fa-utensils',        'Fine Dining',     'Award-winning international restaurant.'],
                    ['fas fa-spa',             'Spa & Wellness',  'Rejuvenating treatments and massages.'],
                    ['fas fa-dumbbell',        'Fitness Center',  'State-of-the-art gym, open 24/7.'],
                    ['fas fa-car',             'Airport Pickup',  'Complimentary luxury transfers available.'],
                    ['fas fa-concierge-bell',  'Room Service',    '24-hour in-room dining service.'],
                    ['fas fa-glass-martini-alt','Sky Bar',        'Signature cocktails with skyline views.'],
                ];
                foreach ($static_services as $i => $svc): ?>
                <div class="service-card reveal delay-<?= ($i % 4) + 1 ?>">
                    <div class="service-icon" aria-hidden="true"><i class="<?= $svc[0] ?>"></i></div>
                    <h3><?= $svc[1] ?></h3>
                    <p><?= $svc[2] ?></p>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <?php foreach ($services as $i => $svc): ?>
                <div class="service-card reveal delay-<?= ($i % 4) + 1 ?>">
                    <div class="service-icon" aria-hidden="true">
                        <i class="<?= htmlspecialchars($svc['icon']) ?>"></i>
                    </div>
                    <h3><?= htmlspecialchars($svc['name']) ?></h3>
                    <p><?= htmlspecialchars(truncate($svc['description'], 90)) ?></p>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ============================================================
     GALLERY PREVIEW
============================================================ -->
<section class="section bg-navy" id="gallery-preview" aria-label="Gallery preview">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-eyebrow" style="color:var(--gold);">Gallery</span>
            <h2 class="section-title" style="color:var(--white);">See <span class="accent">Grand Azure</span> in Photos</h2>
            <div class="gold-divider"><i class="fas fa-star"></i></div>
            <p class="section-subtitle">A visual journey through our beautiful spaces and world-class facilities.</p>
        </div>

        <div class="gallery-grid reveal">
            <?php
            if (empty($gallery_preview)) {
                $gallery_preview = [
                    ['image' => 'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?auto=format&fit=crop&w=800&q=80', 'caption' => 'Presidential Suite', 'class' => 'tall'],
                    ['image' => SITE_URL . '/assets/images/gallery/infinity_pool.jpg', 'caption' => 'Infinity Pool',       'class' => ''],
                    ['image' => 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?auto=format&fit=crop&w=800&q=80', 'caption' => 'Fine Dining',        'class' => ''],
                    ['image' => 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=800&q=80', 'caption' => 'Luxury Spa',         'class' => ''],
                    ['image' => 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=800&q=80', 'caption' => 'Grand Lobby',         'class' => 'wide'],
                ];
            } else {
                $classes = ['tall', '', '', '', 'wide', ''];
                foreach ($gallery_preview as $idx => &$item) {
                    $item['class'] = $classes[$idx % 6] ?? '';
                }
            }
            foreach ($gallery_preview as $img):
                $img_src = isset($img['image']) ? image_url($img['image']) : '';
                $img_cap = isset($img['caption']) ? $img['caption'] : '';
                $img_cls = isset($img['class']) ? $img['class'] : '';
            ?>
            <div class="gallery-item <?= htmlspecialchars($img_cls) ?>" style="height:<?= $img_cls === 'tall' ? '100%' : '220px' ?>; min-height:220px;">
                <img src="<?= htmlspecialchars($img_src) ?>" alt="<?= htmlspecialchars($img_cap) ?>" loading="lazy" style="width:100%; height:100%; object-fit:cover;">
                <div class="gallery-overlay">
                    <span class="gallery-caption"><?= htmlspecialchars($img_cap) ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center" style="margin-top:var(--space-10);">
            <a href="gallery.php" class="btn btn-outline btn-lg reveal">
                <i class="fas fa-images"></i>
                View Full Gallery
            </a>
        </div>
    </div>
</section>

<!-- ============================================================
     TESTIMONIALS
============================================================ -->
<section class="section bg-cream" id="testimonials" aria-label="Guest testimonials">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-eyebrow">Reviews</span>
            <h2 class="section-title">What Our <span class="accent">Guests</span> Say</h2>
            <div class="gold-divider"><i class="fas fa-star"></i></div>
            <p class="section-subtitle">Real experiences from our valued guests who have made Grand Azure their home away from home.</p>
        </div>

        <div class="grid-4">
            <?php if (empty($testimonials)): ?>
                <?php
                $static_testimonials = [
                    ['Emily Richardson', 'Business Traveler', 5, 'Absolutely stunning hotel! The Executive Room exceeded all expectations. The service was impeccable and the staff went above and beyond.'],
                    ['James Whitmore',   'Honeymoon Couple',  5, 'We spent our honeymoon here and it was magical. The Presidential Suite was breathtaking. The staff made every moment special.'],
                    ['Sarah Chen',       'Family Vacation',   5, 'Perfect hotel for families. The kids loved the pool and the family room had everything we needed. Breakfast was exceptional.'],
                    ['Michael Torres',   'Conference Guest',  4, 'The conference facilities are world-class. Our corporate event went flawlessly. The team was professional throughout.'],
                ];
                foreach ($static_testimonials as $i => $t): ?>
                <div class="testimonial-card reveal delay-<?= $i + 1 ?>">
                    <div class="testimonial-stars" aria-label="<?= $t[2] ?> out of 5 stars">
                        <?= star_rating($t[2]) ?>
                    </div>
                    <p class="testimonial-text"><?= htmlspecialchars($t[3]) ?></p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar-initials" aria-hidden="true">
                            <?= strtoupper(substr($t[0], 0, 1)) ?>
                        </div>
                        <div class="testimonial-author-info">
                            <span class="testimonial-author-name"><?= htmlspecialchars($t[0]) ?></span>
                            <span class="testimonial-author-role"><?= htmlspecialchars($t[1]) ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <?php foreach ($testimonials as $i => $t): ?>
                <div class="testimonial-card reveal delay-<?= ($i % 4) + 1 ?>">
                    <div class="testimonial-stars" aria-label="<?= $t['rating'] ?> out of 5 stars">
                        <?= star_rating($t['rating']) ?>
                    </div>
                    <p class="testimonial-text"><?= htmlspecialchars($t['content']) ?></p>
                    <div class="testimonial-author">
                        <?php if (!empty($t['avatar'])): ?>
                            <img src="<?= htmlspecialchars(image_url($t['avatar'])) ?>" alt="<?= htmlspecialchars($t['author_name']) ?>" style="width: 44px; height: 44px; border-radius: 50%; object-fit: cover; margin-right: var(--space-3);">
                        <?php else: ?>
                            <div class="testimonial-avatar-initials" aria-hidden="true">
                                <?= strtoupper(substr($t['author_name'], 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                        <div class="testimonial-author-info">
                            <span class="testimonial-author-name"><?= htmlspecialchars($t['author_name']) ?></span>
                            <span class="testimonial-author-role"><?= htmlspecialchars($t['author_role']) ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ============================================================
     CALL TO ACTION
============================================================ -->
<section class="section bg-gradient" id="cta" aria-label="Book your stay">
    <div class="container text-center">
        <div class="reveal" style="max-width:700px; margin-inline:auto;">
            <span class="section-eyebrow">Limited Availability</span>
            <h2 class="section-title" style="color:var(--white); margin-bottom:var(--space-6);">
                Begin Your <span style="color:var(--gold);">Grand Azure</span> Experience
            </h2>
            <p style="color:rgba(255,255,255,.75); font-size:var(--text-lg); line-height:var(--lh-relaxed); margin-bottom:var(--space-10);">
                Reserve your room today and unlock a world of luxury, comfort, and memories that last a lifetime. Best rate guaranteed when you book direct.
            </p>
            <div class="hero-actions">
                <a href="booking.php" class="btn btn-primary btn-xl">
                    <i class="fas fa-calendar-check"></i>
                    Book Your Stay
                </a>
                <a href="contact.php" class="btn btn-outline btn-xl">
                    <i class="fas fa-phone"></i>
                    Contact Us
                </a>
            </div>
            <p style="margin-top:var(--space-8); color:rgba(255,255,255,.45); font-size:var(--text-sm);">
                <i class="fas fa-lock" style="margin-right:6px;"></i>
                Secure booking &nbsp;·&nbsp; Free cancellation 48h before check-in &nbsp;·&nbsp; Best rate guaranteed
            </p>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
