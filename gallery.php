<?php
// ============================================================
//  Grand Azure Hotel — Gallery Page
// ============================================================

$page_title = 'Photo Gallery';
$meta_desc  = 'Browse stunning photos of Grand Azure Hotel — rooms, pool, restaurant, spa, lobby, and more.';

require_once 'includes/functions.php';
$gallery = get_gallery($conn);
require_once 'includes/header.php';

// Static gallery fallback
$static_gallery = [
    ['url'=>'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80', 'caption'=>'Hotel Exterior', 'category'=>'exterior'],
    ['url'=>'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?auto=format&fit=crop&w=800&q=80', 'caption'=>'Presidential Suite', 'category'=>'rooms'],
    ['url'=>'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=800&q=80', 'caption'=>'Deluxe Room', 'category'=>'rooms'],
    ['url'=>'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&w=800&q=80', 'caption'=>'Standard Room', 'category'=>'rooms'],
    ['url'=>'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?auto=format&fit=crop&w=800&q=80', 'caption'=>'Fine Dining Restaurant', 'category'=>'restaurant'],
    ['url'=>'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?auto=format&fit=crop&w=800&q=80', 'caption'=>'Infinity Pool', 'category'=>'pool'],
    ['url'=>'https://images.unsplash.com/photo-1544161515-4ab6ce6db874?auto=format&fit=crop&w=800&q=80', 'caption'=>'Azure Spa', 'category'=>'spa'],
    ['url'=>'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=800&q=80', 'caption'=>'Spa Treatment', 'category'=>'spa'],
    ['url'=>'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&w=800&q=80', 'caption'=>'Fitness Center', 'category'=>'events'],
    ['url'=>'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?auto=format&fit=crop&w=800&q=80', 'caption'=>'Grand Lobby', 'category'=>'lobby'],
    ['url'=>'https://images.unsplash.com/photo-1587827073598-f8d7fc16c8c4?auto=format&fit=crop&w=800&q=80', 'caption'=>'Sky Bar', 'category'=>'lobby'],
    ['url'=>'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=800&q=80', 'caption'=>'Hotel Facade', 'category'=>'exterior'],
];

$display_gallery = !empty($gallery)
    ? array_map(fn($g) => ['url' => image_url($g['image']), 'caption' => $g['caption'], 'category' => $g['category']], $gallery)
    : $static_gallery;

$categories = array_unique(array_column($display_gallery, 'category'));
?>

<!-- PAGE HERO -->
<section class="page-hero">
    <div class="container">
        <p class="page-hero-eyebrow">Visual Tour</p>
        <h1 class="page-hero-title">Photo Gallery</h1>
        <p class="page-hero-subtitle">Experience Grand Azure through our lens — beautiful spaces, unforgettable moments.</p>
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="index.php">Home</a><span class="breadcrumb-sep">›</span>
            <span aria-current="page">Gallery</span>
        </nav>
    </div>
</section>

<!-- FILTER -->
<section class="section-sm bg-white" style="border-bottom:1px solid var(--gray-100);">
    <div class="container">
        <div style="display:flex; gap:var(--space-3); flex-wrap:wrap; justify-content:center;">
            <button class="filter-btn active" data-filter="all" onclick="filterGallery('all', this)">All Photos</button>
            <?php foreach ($categories as $cat): ?>
            <button class="filter-btn" data-filter="<?= htmlspecialchars($cat) ?>" onclick="filterGallery('<?= htmlspecialchars($cat) ?>', this)">
                <?= ucfirst(htmlspecialchars($cat)) ?>
            </button>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- GALLERY MASONRY -->
<section class="section bg-cream">
    <div class="container">
        <div id="galleryGrid" style="columns:3; column-gap:var(--space-4); orphans:1; widows:1;">
            <?php foreach ($display_gallery as $i => $img): ?>
            <div class="gallery-item reveal" data-category="<?= htmlspecialchars($img['category']) ?>"
                 style="display:inline-block; width:100%; margin-bottom:var(--space-4); break-inside:avoid; border-radius:var(--radius-md); overflow:hidden;">
                <img src="<?= htmlspecialchars($img['url']) ?>"
                     alt="<?= htmlspecialchars($img['caption']) ?>"
                     loading="lazy"
                     style="width:100%; display:block; transition:transform .5s;">
                <div class="gallery-overlay" style="border-radius:0;">
                    <span class="gallery-caption"><?= htmlspecialchars($img['caption']) ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<style>
.filter-btn { padding:8px 20px; border-radius:var(--radius-full); font-size:var(--text-sm); font-weight:600; border:2px solid var(--gray-200); color:var(--gray-600); background:var(--white); cursor:pointer; transition:all .2s; }
.filter-btn:hover,.filter-btn.active { background:var(--gold); border-color:var(--gold); color:var(--navy); }
.gallery-item.hidden { display:none !important; }
@media(max-width:768px){ #galleryGrid { columns:2; } }
@media(max-width:480px){ #galleryGrid { columns:1; } }
</style>

<script>
function filterGallery(cat, btn) {
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('#galleryGrid .gallery-item').forEach(item => {
        item.classList.toggle('hidden', cat !== 'all' && item.dataset.category !== cat);
    });
}
</script>

<?php require_once 'includes/footer.php'; ?>
