<?php
// ============================================================
//  Grand Azure Hotel — Restaurant Page
//  Converted from restaurant.html to a dynamic PHP page so the
//  Fine Dining and Sky Bar images can be managed from
//  Admin > Site Content instead of being hardcoded.
// ============================================================

$page_title = 'Restaurant';
$meta_desc  = "Dine at Grand Azure Hotel's award-winning restaurant. International cuisine, daily buffet breakfast, and a sky bar with live music.";

require_once 'includes/functions.php';
require_once 'includes/header.php';
?>

<!-- PAGE HERO -->
<section class="page-hero" aria-label="Restaurant">
    <div class="container">
        <p class="page-hero-eyebrow">Culinary Excellence</p>
        <h1 class="page-hero-title">Azure Restaurant</h1>
        <p class="page-hero-subtitle">An extraordinary culinary journey through international flavours, crafted by our award-winning executive chef.</p>
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="index.php">Home</a><span class="breadcrumb-sep">›</span>
            <span aria-current="page">Restaurant</span>
        </nav>
    </div>
</section>

<!-- INTRO -->
<section class="section bg-white">
    <div class="container">
        <div class="grid-2" style="align-items:center; gap:5rem;">
            <div class="reveal">
                <img src="<?= htmlspecialchars(image_url(setting($conn, 'dining_image'))) ?>"
                     alt="Azure Fine Dining Restaurant" style="width:100%;height:500px;object-fit:cover;border-radius:var(--radius-lg);" loading="lazy">
            </div>
            <div class="reveal delay-2">
                <span class="section-eyebrow">Fine Dining</span>
                <h2 class="section-title" style="text-align:left;">A Feast for Every <span class="accent">Sense</span></h2>
                <div class="sep sep-left"></div>
                <p style="color:var(--gray-500); line-height:var(--lh-relaxed); margin-bottom:var(--space-5);">
                    The Azure Restaurant is more than a meal — it is a sensory journey. Under the direction of Executive Chef Marcus DeLeon, our kitchen produces cuisine that honours the finest seasonal ingredients while embracing culinary creativity.
                </p>
                <p style="color:var(--gray-500); line-height:var(--lh-relaxed); margin-bottom:var(--space-8);">
                    With a menu that changes with the seasons, our restaurant has earned recognition from international food critics and holds a coveted position among the city's top dining destinations.
                </p>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:var(--space-5); margin-bottom:var(--space-8);">
                    <div style="display:flex; gap:var(--space-3); align-items:center;">
                        <i class="fas fa-clock" style="color:var(--gold); font-size:var(--text-xl); flex-shrink:0;"></i>
                        <div>
                            <div style="font-weight:700; color:var(--navy); font-size:var(--text-sm);">Breakfast</div>
                            <div style="font-size:var(--text-xs); color:var(--gray-500);">7:00 AM – 10:30 AM</div>
                        </div>
                    </div>
                    <div style="display:flex; gap:var(--space-3); align-items:center;">
                        <i class="fas fa-clock" style="color:var(--gold); font-size:var(--text-xl); flex-shrink:0;"></i>
                        <div>
                            <div style="font-weight:700; color:var(--navy); font-size:var(--text-sm);">Lunch</div>
                            <div style="font-size:var(--text-xs); color:var(--gray-500);">12:00 PM – 3:00 PM</div>
                        </div>
                    </div>
                    <div style="display:flex; gap:var(--space-3); align-items:center;">
                        <i class="fas fa-clock" style="color:var(--gold); font-size:var(--text-xl); flex-shrink:0;"></i>
                        <div>
                            <div style="font-weight:700; color:var(--navy); font-size:var(--text-sm);">Dinner</div>
                            <div style="font-size:var(--text-xs); color:var(--gray-500);">6:30 PM – 11:00 PM</div>
                        </div>
                    </div>
                    <div style="display:flex; gap:var(--space-3); align-items:center;">
                        <i class="fas fa-utensils" style="color:var(--gold); font-size:var(--text-xl); flex-shrink:0;"></i>
                        <div>
                            <div style="font-weight:700; color:var(--navy); font-size:var(--text-sm);">Cuisine</div>
                            <div style="font-size:var(--text-xs); color:var(--gray-500);">International</div>
                        </div>
                    </div>
                </div>
                <a href="contact.php?subject=Table+Reservation" class="btn btn-primary">
                    <i class="fas fa-concierge-bell"></i> Reserve a Table
                </a>
            </div>
        </div>
    </div>
</section>

<!-- SAMPLE MENU -->
<section class="section bg-cream">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-eyebrow">Our Menu</span>
            <h2 class="section-title">Signature <span class="accent">Dishes</span></h2>
            <div class="gold-divider"><i class="fas fa-star"></i></div>
        </div>
        <div class="grid-3" style="margin-bottom:var(--space-10);">
            <div class="reveal delay-1" style="background:var(--white); border-radius:var(--radius-lg); padding:var(--space-8); box-shadow:var(--shadow-sm);">
                <h3 style="font-family:var(--font-serif); font-size:var(--text-2xl); color:var(--navy); margin-bottom:var(--space-6); padding-bottom:var(--space-3); border-bottom:2px solid var(--gold);">🥗 Starters</h3>
                <div style="padding:var(--space-4) 0; border-bottom:1px solid var(--gray-100);">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:var(--space-1);">
                        <span style="font-weight:600; color:var(--navy);">Seared Scallops</span>
                        <span style="font-family:var(--font-serif); color:var(--gold); font-weight:700; white-space:nowrap; margin-left:var(--space-4);">$28</span>
                    </div>
                    <span style="font-size:var(--text-sm); color:var(--gray-500);">Pan-seared king scallops, cauliflower purée, pancetta crisp</span>
                </div>
                <div style="padding:var(--space-4) 0; border-bottom:1px solid var(--gray-100);">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:var(--space-1);">
                        <span style="font-weight:600; color:var(--navy);">Foie Gras Torchon</span>
                        <span style="font-family:var(--font-serif); color:var(--gold); font-weight:700; white-space:nowrap; margin-left:var(--space-4);">$36</span>
                    </div>
                    <span style="font-size:var(--text-sm); color:var(--gray-500);">Pressed foie gras, brioche toast, fig compote</span>
                </div>
                <div style="padding:var(--space-4) 0; border-bottom:1px solid var(--gray-100);">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:var(--space-1);">
                        <span style="font-weight:600; color:var(--navy);">Heirloom Tomato Salad</span>
                        <span style="font-family:var(--font-serif); color:var(--gold); font-weight:700; white-space:nowrap; margin-left:var(--space-4);">$22</span>
                    </div>
                    <span style="font-size:var(--text-sm); color:var(--gray-500);">Buffalo mozzarella, aged balsamic, fresh basil oil</span>
                </div>
            </div>
            <div class="reveal delay-2" style="background:var(--white); border-radius:var(--radius-lg); padding:var(--space-8); box-shadow:var(--shadow-sm);">
                <h3 style="font-family:var(--font-serif); font-size:var(--text-2xl); color:var(--navy); margin-bottom:var(--space-6); padding-bottom:var(--space-3); border-bottom:2px solid var(--gold);">🍽 Main Courses</h3>
                <div style="padding:var(--space-4) 0; border-bottom:1px solid var(--gray-100);">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:var(--space-1);">
                        <span style="font-weight:600; color:var(--navy);">Wagyu Tenderloin</span>
                        <span style="font-family:var(--font-serif); color:var(--gold); font-weight:700; white-space:nowrap; margin-left:var(--space-4);">$95</span>
                    </div>
                    <span style="font-size:var(--text-sm); color:var(--gray-500);">200g A5 wagyu, truffle potato, red wine jus</span>
                </div>
                <div style="padding:var(--space-4) 0; border-bottom:1px solid var(--gray-100);">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:var(--space-1);">
                        <span style="font-weight:600; color:var(--navy);">Herb-Crusted Lamb Rack</span>
                        <span style="font-family:var(--font-serif); color:var(--gold); font-weight:700; white-space:nowrap; margin-left:var(--space-4);">$72</span>
                    </div>
                    <span style="font-size:var(--text-sm); color:var(--gray-500);">NZ rack of lamb, rosemary jus, dauphinoise potatoes</span>
                </div>
                <div style="padding:var(--space-4) 0; border-bottom:1px solid var(--gray-100);">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:var(--space-1);">
                        <span style="font-weight:600; color:var(--navy);">Pan-Roasted Sea Bass</span>
                        <span style="font-family:var(--font-serif); color:var(--gold); font-weight:700; white-space:nowrap; margin-left:var(--space-4);">$68</span>
                    </div>
                    <span style="font-size:var(--text-sm); color:var(--gray-500);">Wild sea bass, saffron risotto, beurre blanc</span>
                </div>
            </div>
            <div class="reveal delay-3" style="background:var(--white); border-radius:var(--radius-lg); padding:var(--space-8); box-shadow:var(--shadow-sm);">
                <h3 style="font-family:var(--font-serif); font-size:var(--text-2xl); color:var(--navy); margin-bottom:var(--space-6); padding-bottom:var(--space-3); border-bottom:2px solid var(--gold);">🍰 Desserts</h3>
                <div style="padding:var(--space-4) 0; border-bottom:1px solid var(--gray-100);">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:var(--space-1);">
                        <span style="font-weight:600; color:var(--navy);">Valrhona Chocolate Fondant</span>
                        <span style="font-family:var(--font-serif); color:var(--gold); font-weight:700; white-space:nowrap; margin-left:var(--space-4);">$24</span>
                    </div>
                    <span style="font-size:var(--text-sm); color:var(--gray-500);">Warm chocolate lava cake, vanilla bean ice cream</span>
                </div>
                <div style="padding:var(--space-4) 0; border-bottom:1px solid var(--gray-100);">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:var(--space-1);">
                        <span style="font-weight:600; color:var(--navy);">Crème Brûlée</span>
                        <span style="font-family:var(--font-serif); color:var(--gold); font-weight:700; white-space:nowrap; margin-left:var(--space-4);">$18</span>
                    </div>
                    <span style="font-size:var(--text-sm); color:var(--gray-500);">Classic vanilla custard, caramelised sugar crust</span>
                </div>
                <div style="padding:var(--space-4) 0; border-bottom:1px solid var(--gray-100);">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:var(--space-1);">
                        <span style="font-weight:600; color:var(--navy);">Cheese Selection</span>
                        <span style="font-family:var(--font-serif); color:var(--gold); font-weight:700; white-space:nowrap; margin-left:var(--space-4);">$32</span>
                    </div>
                    <span style="font-size:var(--text-sm); color:var(--gray-500);">Artisan cheeses, honeycomb, candied walnuts, crackers</span>
                </div>
            </div>
        </div>
        <div class="text-center reveal">
            <p style="color:var(--gray-400); font-size:var(--text-sm); margin-bottom:var(--space-4);">Prices are exclusive of tax and service charge. Menu subject to seasonal changes.</p>
        </div>
    </div>
</section>

<!-- SKY BAR -->
<section class="section bg-navy">
    <div class="container">
        <div class="grid-2" style="align-items:center; gap:5rem;">
            <div class="reveal delay-2">
                <span class="section-eyebrow" style="color:var(--gold);">Bar &amp; Lounge</span>
                <h2 class="section-title" style="text-align:left; color:var(--white);">Azure <span class="accent">Sky Bar</span></h2>
                <div class="sep sep-left"></div>
                <p style="color:rgba(255,255,255,.7); line-height:var(--lh-relaxed); margin-bottom:var(--space-6);">
                    Perched on the 10th floor, our Sky Bar offers spectacular 360° city views, handcrafted cocktails by our award-winning mixologists, and live music every Friday and Saturday evening from 8:00 PM.
                </p>
                <div style="margin-bottom:var(--space-6);">
                    <div style="display:flex; align-items:center; gap:var(--space-3); padding:var(--space-3) 0; border-bottom:1px solid rgba(255,255,255,.1); color:rgba(255,255,255,.75); font-size:var(--text-sm);">
                        <i class="fas fa-check-circle" style="color:var(--gold);"></i> Sun – Thu: 4:00 PM – 1:00 AM
                    </div>
                    <div style="display:flex; align-items:center; gap:var(--space-3); padding:var(--space-3) 0; border-bottom:1px solid rgba(255,255,255,.1); color:rgba(255,255,255,.75); font-size:var(--text-sm);">
                        <i class="fas fa-check-circle" style="color:var(--gold);"></i> Fri &amp; Sat: 4:00 PM – 2:00 AM
                    </div>
                    <div style="display:flex; align-items:center; gap:var(--space-3); padding:var(--space-3) 0; border-bottom:1px solid rgba(255,255,255,.1); color:rgba(255,255,255,.75); font-size:var(--text-sm);">
                        <i class="fas fa-check-circle" style="color:var(--gold);"></i> Happy Hour: 4:00 PM – 7:00 PM daily
                    </div>
                    <div style="display:flex; align-items:center; gap:var(--space-3); padding:var(--space-3) 0; border-bottom:1px solid rgba(255,255,255,.1); color:rgba(255,255,255,.75); font-size:var(--text-sm);">
                        <i class="fas fa-check-circle" style="color:var(--gold);"></i> Live Music: Fri &amp; Sat from 8:00 PM
                    </div>
                </div>
                <a href="contact.php" class="btn btn-primary"><i class="fas fa-cocktail"></i> Reserve a Table</a>
            </div>
            <div class="reveal">
                <img src="<?= htmlspecialchars(image_url(setting($conn, 'skybar_image'))) ?>"
                     alt="Azure Sky Bar" style="width:100%;height:440px;object-fit:cover;border-radius:var(--radius-lg);" loading="lazy">
            </div>
        </div>
    </div>
</section>

<!-- BREAKFAST -->
<section class="section bg-cream">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-eyebrow">Morning Ritual</span>
            <h2 class="section-title">Lavish <span class="accent">Breakfast</span> Buffet</h2>
            <p class="section-subtitle">Begin every day with our spectacular breakfast buffet — over 50 fresh items served daily from 7:00 AM to 10:30 AM.</p>
        </div>
        <div class="grid-4">
            <div class="service-card reveal delay-1">
                <div class="service-icon"><i class="fas fa-bread-slice"></i></div>
                <h3>Bakery Corner</h3>
                <p>Freshly baked croissants, sourdough, pastries</p>
            </div>
            <div class="service-card reveal delay-2">
                <div class="service-icon"><i class="fas fa-egg"></i></div>
                <h3>Live Station</h3>
                <p>Made-to-order omelettes, eggs Benedict</p>
            </div>
            <div class="service-card reveal delay-3">
                <div class="service-icon"><i class="fas fa-apple-alt"></i></div>
                <h3>Fresh Fruits</h3>
                <p>Seasonal tropical fruits and juices</p>
            </div>
            <div class="service-card reveal delay-4">
                <div class="service-icon"><i class="fas fa-cheese"></i></div>
                <h3>Continental</h3>
                <p>Cold cuts, artisan cheeses, smoked salmon</p>
            </div>
        </div>
    </div>
</section>


<?php require_once 'includes/footer.php'; ?>
