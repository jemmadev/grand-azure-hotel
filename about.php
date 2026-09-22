<?php
// ============================================================
//  Grand Azure Hotel — About Page
//  Converted from about.html to a dynamic PHP page so the
//  founder image and leadership team can be managed from
//  Admin > Site Content instead of being hardcoded.
// ============================================================

$page_title = 'About Us';
$meta_desc  = "Learn about Grand Azure Hotel's history, mission, vision, and what makes us a world-class luxury destination.";

require_once 'includes/functions.php';
$team_members = get_team_members($conn);
require_once 'includes/header.php';
?>

<!-- PAGE HERO -->
<section class="page-hero" aria-label="About Us">
    <div class="container">
        <p class="page-hero-eyebrow">Our Story</p>
        <h1 class="page-hero-title">About Grand Azure Hotel</h1>
        <p class="page-hero-subtitle">A legacy of luxury, service, and unforgettable experiences since 1999.</p>
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="index.php">Home</a>
            <span class="breadcrumb-sep" aria-hidden="true">›</span>
            <span aria-current="page">About Us</span>
        </nav>
    </div>
</section>

<!-- HOTEL STORY -->
<section class="section bg-white">
    <div class="container">
        <div class="grid-2" style="align-items:center; gap:5rem;">
            <div class="reveal">
                <img src="<?= htmlspecialchars(image_url(setting($conn, 'founder_image'))) ?>"
                     alt="Hotel Founder" style="width:100%; height:520px; object-fit:cover; border-radius:var(--radius-lg);" loading="lazy">
            </div>
            <div class="reveal delay-2">
                <span class="section-eyebrow">Our History</span>
                <h2 class="section-title" style="text-align:left;">A Story of <span class="accent">Passion</span></h2>
                <div class="sep sep-left"></div>
                <p style="color:var(--gray-500); line-height:var(--lh-relaxed); margin-bottom:var(--space-5);">
                    Grand Azure Hotel was born from a simple but powerful dream — to create a place where every guest feels genuinely valued, where service transcends expectation, and where luxury is not just a word but a way of life.
                </p>
                <p style="color:var(--gray-500); line-height:var(--lh-relaxed); margin-bottom:var(--space-5);">
                    Founded in 1999 by the Azure Hospitality Group, we started with 50 rooms and a small restaurant. Over 25 years of relentless dedication to excellence, we have grown to 250 rooms and suites, multiple dining venues, a world-class spa, and state-of-the-art conference facilities.
                </p>
                <p style="color:var(--gray-500); line-height:var(--lh-relaxed); margin-bottom:var(--space-8);">
                    Today, Grand Azure Hotel stands proudly as a landmark of luxury, having hosted heads of state, international celebrities, and tens of thousands of families whose memories we have had the honour of shaping.
                </p>
                <a href="booking.php" class="btn btn-primary">
                    <i class="fas fa-calendar-check"></i> Book a Stay
                </a>
            </div>
        </div>
    </div>
</section>

<!-- MISSION & VISION -->
<section class="section bg-cream">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-eyebrow">Our Purpose</span>
            <h2 class="section-title">Mission &amp; <span class="accent">Vision</span></h2>
            <div class="gold-divider"><i class="fas fa-star"></i></div>
        </div>
        <div class="grid-2" style="gap:var(--space-8);">
            <div class="reveal" style="background:var(--navy); color:var(--white); border-radius:var(--radius-lg); padding:var(--space-10); position:relative; overflow:hidden;">
                <div style="position:absolute; top:-20px; right:-20px; width:150px; height:150px; background:rgba(201,168,76,.08); border-radius:50%;"></div>
                <div style="position:absolute; bottom:-30px; left:-30px; width:200px; height:200px; background:rgba(201,168,76,.05); border-radius:50%;"></div>
                <div style="width:64px; height:64px; background:linear-gradient(135deg, var(--gold), var(--gold-dark)); border-radius:var(--radius); display:flex; align-items:center; justify-content:center; margin-bottom:var(--space-6);">
                    <i class="fas fa-bullseye" style="color:var(--navy); font-size:var(--text-2xl);"></i>
                </div>
                <h3 style="font-family:var(--font-serif); font-size:var(--text-2xl); color:var(--gold); margin-bottom:var(--space-4);">Our Mission</h3>
                <p style="color:rgba(255,255,255,.75); line-height:var(--lh-relaxed);">
                    To deliver extraordinary hospitality experiences that inspire, delight, and create lasting memories for every guest, while fostering a culture of excellence, respect, and continuous innovation in everything we do.
                </p>
            </div>
            <div class="reveal delay-2" style="background:var(--gold); color:var(--navy); border-radius:var(--radius-lg); padding:var(--space-10); position:relative; overflow:hidden;">
                <div style="position:absolute; top:-20px; right:-20px; width:150px; height:150px; background:rgba(10,22,40,.05); border-radius:50%;"></div>
                <div style="width:64px; height:64px; background:rgba(10,22,40,.15); border-radius:var(--radius); display:flex; align-items:center; justify-content:center; margin-bottom:var(--space-6);">
                    <i class="fas fa-eye" style="color:var(--navy); font-size:var(--text-2xl);"></i>
                </div>
                <h3 style="font-family:var(--font-serif); font-size:var(--text-2xl); margin-bottom:var(--space-4);">Our Vision</h3>
                <p style="color:rgba(10,22,40,.8); line-height:var(--lh-relaxed);">
                    To be globally recognized as the premier luxury hotel destination, setting the benchmark for excellence, sustainability, and heartfelt service, while continuously redefining what world-class hospitality means.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- WHY CHOOSE US -->
<section class="section bg-white">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-eyebrow">Our Difference</span>
            <h2 class="section-title">Why <span class="accent">Choose</span> Grand Azure?</h2>
            <div class="gold-divider"><i class="fas fa-star"></i></div>
            <p class="section-subtitle">Discover the elements that make Grand Azure a destination unlike any other.</p>
        </div>

        <div class="grid-3">
            <div class="service-card reveal delay-1">
                <div class="service-icon"><i class="fas fa-award"></i></div>
                <h3>Award-Winning Excellence</h3>
                <p>Recipient of 12 prestigious international hospitality awards, our commitment to quality is recognised worldwide.</p>
            </div>
            <div class="service-card reveal delay-2">
                <div class="service-icon"><i class="fas fa-heart"></i></div>
                <h3>Personalised Service</h3>
                <p>Every guest receives a bespoke experience crafted by our dedicated concierge team — because you are unique.</p>
            </div>
            <div class="service-card reveal delay-3">
                <div class="service-icon"><i class="fas fa-map-marker-alt"></i></div>
                <h3>Prime City Location</h3>
                <p>Perfectly situated in the heart of the city, within walking distance of premier shopping, dining, and cultural landmarks.</p>
            </div>
            <div class="service-card reveal delay-1">
                <div class="service-icon"><i class="fas fa-utensils"></i></div>
                <h3>Culinary Artistry</h3>
                <p>Our executive chefs curate menus that celebrate local ingredients with international techniques, delivering unforgettable meals.</p>
            </div>
            <div class="service-card reveal delay-2">
                <div class="service-icon"><i class="fas fa-shield-alt"></i></div>
                <h3>Safety &amp; Privacy</h3>
                <p>State-of-the-art security, strict privacy protocols, and discreet service ensure your absolute peace of mind.</p>
            </div>
            <div class="service-card reveal delay-3">
                <div class="service-icon"><i class="fas fa-leaf"></i></div>
                <h3>Eco Responsibility</h3>
                <p>Certified green operations, zero-waste kitchens, and sustainable sourcing — luxury and responsibility, hand in hand.</p>
            </div>
        </div>
    </div>
</section>

<!-- TEAM -->
<section class="section bg-cream">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-eyebrow">Our People</span>
            <h2 class="section-title">Meet Our <span class="accent">Leadership</span></h2>
            <div class="gold-divider"><i class="fas fa-star"></i></div>
        </div>
        <div class="grid-4">
            <?php foreach ($team_members as $member): ?>
            <div class="card reveal">
                <div class="card-img-wrapper">
                    <img class="card-img" src="<?= htmlspecialchars(image_url($member['image'])) ?>" alt="<?= htmlspecialchars($member['name']) ?>" style="height:280px;" loading="lazy">
                </div>
                <div class="card-body" style="text-align:center;">
                    <h3 class="card-title" style="font-size:var(--text-xl);"><?= htmlspecialchars($member['name']) ?></h3>
                    <p style="color:var(--gold); font-size:var(--text-sm); font-weight:600; text-transform:uppercase; letter-spacing:.08em;"><?= htmlspecialchars($member['role']) ?></p>
                    <div class="footer-social" style="justify-content:center; margin-top:var(--space-4);">
                        <a href="<?= htmlspecialchars($member['linkedin_url'] ?: '#') ?>" class="social-link" style="background:var(--gray-100); color:var(--gray-600);"><i class="fab fa-linkedin-in"></i></a>
                        <a href="<?= htmlspecialchars($member['twitter_url'] ?: '#') ?>" class="social-link" style="background:var(--gray-100); color:var(--gray-600);"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($team_members)): ?>
            <p style="color:var(--gray-400); grid-column:1/-1; text-align:center;">Leadership team coming soon.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="section bg-gradient text-center">
    <div class="container">
        <div class="reveal" style="max-width:640px; margin:0 auto;">
            <h2 class="section-title" style="color:var(--white);">Experience <span style="color:var(--gold);">Grand Azure</span></h2>
            <p style="color:rgba(255,255,255,.7); margin-bottom:var(--space-8);">Book a room today and join thousands of guests who have made Grand Azure their preferred luxury destination.</p>
            <a href="booking.php" class="btn btn-primary btn-lg"><i class="fas fa-calendar-check"></i> Book Your Stay</a>
        </div>
    </div>
</section>


<?php require_once 'includes/footer.php'; ?>
