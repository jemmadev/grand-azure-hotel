<?php
// ============================================================
//  Grand Azure Hotel — Services Page
// ============================================================

$page_title = 'Hotel Services';
$meta_desc  = 'Discover all the world-class services and amenities offered at Grand Azure Hotel — spa, pool, restaurant, gym, and more.';

require_once 'includes/functions.php';
$services = get_services($conn);
require_once 'includes/header.php';

$all_services = [
    ['fas fa-wifi',             'Free WiFi',         'Enjoy complimentary high-speed wireless internet access in all rooms, public areas, and conference facilities. Perfect for both leisure and business travelers.',  '#connectivity'],
    ['fas fa-swimming-pool',    'Swimming Pool',      'Our temperature-controlled outdoor infinity pool offers stunning city views. Open daily 6:00 AM – 10:00 PM. Poolside bar and sun loungers available.',          '#pool'],
    ['fas fa-utensils',         'Fine Dining',        'Our award-winning Azure Restaurant serves exquisite international cuisine crafted by our executive chef using the finest locally sourced ingredients.',          '#dining'],
    ['fas fa-coffee',           'Daily Breakfast',    'Start your morning right with our lavish breakfast buffet featuring over 50 fresh items — from pastries to made-to-order omelettes, smoothies, and more.',     '#breakfast'],
    ['fas fa-car',              'Airport Transfers',  'Arrive in style with our premium airport transfer service. Luxury vehicles available 24/7. Complimentary for Presidential Suite guests, affordable for all.',   '#transport'],
    ['fas fa-chalkboard',       'Conference Hall',    'State-of-the-art conference facilities for events of 10 to 500 guests. Equipped with AV technology, high-speed internet, and dedicated event coordinators.',  '#events'],
    ['fas fa-tshirt',           'Laundry & Dry Clean','Same-day laundry and expert dry-cleaning services available 7 days a week. Express 4-hour service also available. Delivered fresh to your room.',             '#laundry'],
    ['fas fa-spa',              'Spa & Wellness',     'Rejuvenate body and mind at our luxury spa. Choose from a full menu of massages, facials, body wraps, and holistic wellness treatments. By appointment.',      '#spa'],
    ['fas fa-dumbbell',         'Fitness Center',     'Our fully-equipped 24-hour gym features the latest cardio and strength training equipment. Personal trainers available by appointment at reception.',           '#gym'],
    ['fas fa-parking',          'Secure Parking',     'Complimentary underground parking for all guests with 24-hour CCTV surveillance and direct elevator access to all hotel floors.',                              '#parking'],
    ['fas fa-concierge-bell',   'Room Service',       '24-hour in-room dining with an extensive menu. From light snacks to full gourmet meals — delivered fresh to your door within 30 minutes.',                   '#roomservice'],
    ['fas fa-glass-martini-alt','Bar & Lounge',       'Unwind at our signature sky bar with handcrafted cocktails, premium spirits, and live music every Friday and Saturday evening. Open until 2:00 AM.',         '#bar'],
];
?>

<!-- PAGE HERO -->
<section class="page-hero">
    <div class="container">
        <p class="page-hero-eyebrow">Amenities</p>
        <h1 class="page-hero-title">Hotel Services</h1>
        <p class="page-hero-subtitle">Exceptional amenities designed to make every moment of your stay extraordinary.</p>
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="index.php">Home</a><span class="breadcrumb-sep">›</span>
            <span aria-current="page">Services</span>
        </nav>
    </div>
</section>

<!-- SERVICES GRID -->
<section class="section bg-cream">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-eyebrow">What We Offer</span>
            <h2 class="section-title">Everything You <span class="accent">Need</span></h2>
            <div class="gold-divider"><i class="fas fa-star"></i></div>
            <p class="section-subtitle">From arrival to departure, every detail is thoughtfully arranged for your comfort and pleasure.</p>
        </div>
        <div class="grid-3">
            <?php
            $display = !empty($services) ? array_map(fn($s) => [$s['icon'], $s['name'], $s['description'], '#'], $services) : $all_services;
            foreach ($display as $i => $svc): ?>
            <div class="service-card reveal delay-<?= ($i % 3) + 1 ?>" id="service-<?= $i ?>">
                <div class="service-icon"><i class="<?= htmlspecialchars($svc[0]) ?>"></i></div>
                <h3><?= htmlspecialchars($svc[1]) ?></h3>
                <p><?= htmlspecialchars($svc[2]) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- DETAILED SERVICE SECTIONS -->
<!-- SPA -->
<section class="section bg-white" id="spa">
    <div class="container">
        <div class="grid-2" style="align-items:center; gap:5rem;">
            <div class="reveal">
                <img src="https://images.unsplash.com/photo-1544161515-4ab6ce6db874?auto=format&fit=crop&w=800&q=80"
                     alt="Grand Azure Spa" style="width:100%; height:440px; object-fit:cover; border-radius:var(--radius-lg);" loading="lazy">
            </div>
            <div class="reveal delay-2">
                <span class="section-eyebrow">Wellness</span>
                <h2 class="section-title" style="text-align:left;">Azure <span class="accent">Spa</span></h2>
                <div class="sep sep-left"></div>
                <p style="color:var(--gray-500); line-height:var(--lh-relaxed); margin-bottom:var(--space-5);">
                    Step into a world of serenity at our award-winning Azure Spa. Our expert therapists blend ancient healing traditions with modern techniques to create deeply restorative experiences.
                </p>
                <?php foreach(['Swedish Massage — 60 / 90 min', 'Deep Tissue Massage — 90 min', 'Hot Stone Therapy — 75 min', 'Facial Treatments — 45 / 75 min', 'Aromatherapy Wrap — 60 min', 'Couples Retreat Package — 2 hrs'] as $treatment): ?>
                <div style="display:flex; align-items:center; gap:var(--space-3); padding:var(--space-3) 0; border-bottom:1px solid var(--gray-100);">
                    <i class="fas fa-leaf" style="color:var(--gold);"></i>
                    <span><?= $treatment ?></span>
                </div>
                <?php endforeach; ?>
                <a href="contact.php" class="btn btn-primary" style="margin-top:var(--space-6);">Book a Treatment</a>
            </div>
        </div>
    </div>
</section>

<!-- POOL -->
<section class="section bg-navy" id="pool">
    <div class="container">
        <div class="grid-2" style="align-items:center; gap:5rem;">
            <div class="reveal delay-2">
                <span class="section-eyebrow" style="color:var(--gold);">Aquatic</span>
                <h2 class="section-title" style="text-align:left; color:var(--white);">Infinity <span class="accent">Pool</span></h2>
                <div class="sep sep-left"></div>
                <p style="color:rgba(255,255,255,.7); line-height:var(--lh-relaxed); margin-bottom:var(--space-5);">
                    Our breathtaking infinity pool extends to the horizon, offering panoramic city views as you swim. Temperature-controlled year-round, the pool is the ultimate place to relax and rejuvenate.
                </p>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:var(--space-4); margin-bottom:var(--space-6);">
                    <?php foreach([['fas fa-clock','6:00 AM – 10:00 PM','Daily Hours'],['fas fa-thermometer-half','28°C / 82°F','Water Temp'],['fas fa-cocktail','Poolside Bar','Refreshments'],['fas fa-child','Shallow End','Kids Area']] as $f): ?>
                    <div style="background:rgba(255,255,255,.05); border-radius:var(--radius); padding:var(--space-4); display:flex; gap:var(--space-3); align-items:center;">
                        <i class="<?= $f[0] ?>" style="color:var(--gold); font-size:var(--text-xl);"></i>
                        <div>
                            <div style="color:var(--white); font-weight:600; font-size:var(--text-sm);"><?= $f[1] ?></div>
                            <div style="color:rgba(255,255,255,.5); font-size:var(--text-xs);"><?= $f[2] ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="reveal">
                <img src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?auto=format&fit=crop&w=800&q=80"
                     alt="Infinity Pool" style="width:100%; height:440px; object-fit:cover; border-radius:var(--radius-lg);" loading="lazy">
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="section bg-cream text-center">
    <div class="container">
        <div class="reveal" style="max-width:600px; margin:0 auto;">
            <h2 class="section-title">Ready to <span class="accent">Experience</span> It All?</h2>
            <p style="color:var(--gray-500); margin-bottom:var(--space-8);">All services are available to hotel guests. Book your stay today and enjoy complimentary access to our world-class facilities.</p>
            <a href="booking.php" class="btn btn-primary btn-lg"><i class="fas fa-calendar-check"></i> Book Your Stay</a>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
