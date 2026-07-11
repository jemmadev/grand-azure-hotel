<?php
// ============================================================
//  Grand Azure Hotel — Rooms Page
// ============================================================

$page_title   = 'Rooms & Suites';
$meta_desc    = 'Browse our luxury room categories — Standard, Deluxe, Executive, Family, and Presidential Suite at Grand Azure Hotel.';

require_once 'includes/functions.php';

// Filter by category from query string
$filter_cat = isset($_GET['cat']) ? sanitize($_GET['cat']) : '';

// Fetch rooms
$sql_rooms = "SELECT * FROM rooms";
$params    = [];
$types     = '';
if ($filter_cat) {
    $sql_rooms .= " WHERE category = ?";
    $types      = 's';
    $params[]   = $filter_cat;
}
$sql_rooms .= " ORDER BY price_per_night ASC";
$stmt_rooms = $conn->prepare($sql_rooms);
if ($types) $stmt_rooms->bind_param($types, ...$params);
$stmt_rooms->execute();
$rooms = $stmt_rooms->get_result()->fetch_all(MYSQLI_ASSOC);

// Room category images (fallback)
$cat_images = [
    'Standard'          => 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&w=800&q=80',
    'Deluxe'            => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=800&q=80',
    'Executive'         => 'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?auto=format&fit=crop&w=800&q=80',
    'Family'            => 'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?auto=format&fit=crop&w=800&q=80',
    'Presidential Suite'=> 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=800&q=80',
];

require_once 'includes/header.php';
?>

<style>
.filter-btn { padding: 8px 20px; border-radius: var(--radius-full); font-size: var(--text-sm); font-weight: 600; border: 2px solid var(--gray-200); color: var(--gray-600); background: var(--white); cursor: pointer; transition: all .2s; }
.filter-btn:hover, .filter-btn.active { background: var(--gold); border-color: var(--gold); color: var(--navy); }
</style>

<!-- PAGE HERO -->
<section class="page-hero">
    <div class="container">
        <p class="page-hero-eyebrow">Accommodations</p>
        <h1 class="page-hero-title">Rooms &amp; Suites</h1>
        <p class="page-hero-subtitle">From elegant standard rooms to the grandeur of the Presidential Suite — each space is crafted for perfection.</p>
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="index.php">Home</a>
            <span class="breadcrumb-sep">›</span>
            <span aria-current="page">Rooms</span>
        </nav>
    </div>
</section>

<!-- FILTER BAR -->
<section class="section-sm bg-white" style="border-bottom:1px solid var(--gray-100);">
    <div class="container">
        <div style="display:flex; gap:var(--space-3); flex-wrap:wrap; align-items:center; justify-content:center;">
            <a href="rooms.php" class="filter-btn <?= !$filter_cat ? 'active' : '' ?>">All Rooms</a>
            <?php foreach (array_keys($cat_images) as $cat): ?>
            <a href="rooms.php?cat=<?= urlencode($cat) ?>" class="filter-btn <?= $filter_cat === $cat ? 'active' : '' ?>">
                <?= htmlspecialchars($cat) ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ROOMS GRID -->
<section class="section bg-cream">
    <div class="container">
        <?php if (empty($rooms)): ?>
        <!-- Fallback: show static room cards -->
        <?php
        $static_rooms = [
            ['Standard', 'Comfort Standard Room', 2, 89, 'A cozy room with all essential comforts. Perfect for solo travelers or couples seeking a relaxing stay.', 'Free WiFi,Flat-screen TV,Air Conditioning,Mini-fridge,Safe,Hair Dryer'],
            ['Deluxe', 'Superior Deluxe Room', 2, 149, 'Spacious and elegantly furnished with premium bedding, stunning city views, and a private balcony.', 'Free WiFi,Flat-screen TV,Air Conditioning,Mini-bar,Safe,Bathtub,Balcony'],
            ['Executive', 'Executive Business Room', 2, 229, 'Designed for the discerning business traveler with a dedicated work desk and express checkout.', 'Free WiFi,Smart TV,Air Conditioning,Mini-bar,Work Desk,Espresso Machine,Bathtub'],
            ['Family', 'Spacious Family Room', 5, 199, 'Thoughtfully designed for families, offering ample space, two bathrooms, and fun amenities for children.', 'Free WiFi,Smart TV,Air Conditioning,Mini-bar,Two Bathrooms,Children Amenities'],
            ['Presidential Suite', 'Presidential Suite', 4, 599, 'The pinnacle of luxury — a full two-bedroom suite with private terrace, butler service, and panoramic views.', 'Free WiFi,Smart TV,Full Kitchen,Private Terrace,Butler Service,Jacuzzi,Panoramic View'],
        ];
        ?>
        <div style="text-align:center; background:var(--warning); color:var(--navy); padding:var(--space-3) var(--space-6); border-radius:var(--radius); margin-bottom:var(--space-8); font-size:var(--text-sm);">
            <i class="fas fa-info-circle"></i> Database not connected — showing demo rooms below.
        </div>
        <div class="grid-3">
            <?php foreach ($static_rooms as $i => $room): ?>
            <article class="card reveal delay-<?= ($i % 3) + 1 ?>">
                <div class="card-img-wrapper">
                    <img class="card-img" src="<?= $cat_images[$room[0]] ?>" alt="<?= htmlspecialchars($room[1]) ?>" loading="lazy">
                    <span class="card-badge"><?= htmlspecialchars($room[0]) ?></span>
                </div>
                <div class="card-body">
                    <h2 class="card-title"><?= htmlspecialchars($room[1]) ?></h2>
                    <p class="card-text"><?= htmlspecialchars($room[4]) ?></p>
                    <div class="card-amenities">
                        <?php foreach (array_slice(explode(',', $room[5]), 0, 4) as $am): ?>
                        <span class="amenity-tag"><i class="fas fa-check"></i><?= htmlspecialchars(trim($am)) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:var(--space-5);">
                        <span style="font-size:var(--text-sm); color:var(--gray-500);"><i class="fas fa-users" style="color:var(--gold); margin-right:4px;"></i>Up to <?= $room[2] ?> guests</span>
                        <div class="card-price" style="margin:0;"><span class="amount"><?= CURRENCY . number_format($room[3], 2) ?></span><span class="period">/ night</span></div>
                    </div>
                    <div style="display:flex; gap:var(--space-3);">
                        <a href="room-detail.php?id=<?= $i + 1 ?>" class="btn btn-outline-dark btn-sm" style="flex:1;">Details</a>
                        <a href="booking.php?room_id=<?= $i + 1 ?>" class="btn btn-primary btn-sm" style="flex:1;">Book Now</a>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <?php else: ?>
        <div class="grid-3">
            <?php foreach ($rooms as $i => $room): ?>
            <article class="card reveal delay-<?= ($i % 3) + 1 ?>">
                <div class="card-img-wrapper">
                    <img class="card-img"
                         src="<?= htmlspecialchars($room['image'] ?: ($cat_images[$room['category']] ?? '')) ?>"
                         alt="<?= htmlspecialchars($room['name']) ?>"
                         loading="lazy">
                    <span class="card-badge"><?= htmlspecialchars($room['category']) ?></span>
                    <?php if ($room['status'] !== 'available'): ?>
                    <div style="position:absolute; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,.5); display:flex; align-items:center; justify-content:center;">
                        <span class="badge badge-danger" style="font-size:var(--text-base);">
                            <?= ucfirst($room['status']) ?>
                        </span>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <h2 class="card-title"><?= htmlspecialchars($room['name']) ?></h2>
                    <p class="card-text"><?= htmlspecialchars(truncate($room['description'] ?? '', 110)) ?></p>
                    <div class="card-amenities">
                        <?php foreach (array_slice(explode(',', $room['amenities'] ?? ''), 0, 4) as $am): ?>
                        <span class="amenity-tag"><i class="fas fa-check"></i><?= htmlspecialchars(trim($am)) ?></span>
                        <?php endforeach; ?>
                    </div>
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
                        <a href="room-detail.php?id=<?= $room['id'] ?>" class="btn btn-outline-dark btn-sm" style="flex:1;">Details</a>
                        <?php if ($room['status'] === 'available'): ?>
                        <a href="booking.php?room_id=<?= $room['id'] ?>" class="btn btn-primary btn-sm" style="flex:1;">Book Now</a>
                        <?php else: ?>
                        <button class="btn btn-sm" style="flex:1; background:var(--gray-200); color:var(--gray-500); cursor:not-allowed;">Unavailable</button>
                        <?php endif; ?>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- COMPARE SECTION -->
<section class="section bg-white">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-eyebrow">Compare</span>
            <h2 class="section-title">Room <span class="accent">Comparison</span></h2>
        </div>
        <div class="reveal" style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; font-size:var(--text-sm);">
                <thead>
                    <tr style="background:var(--navy); color:var(--white);">
                        <th style="padding:var(--space-4) var(--space-5); text-align:left;">Feature</th>
                        <th style="padding:var(--space-4) var(--space-5); text-align:center;">Standard</th>
                        <th style="padding:var(--space-4) var(--space-5); text-align:center;">Deluxe</th>
                        <th style="padding:var(--space-4) var(--space-5); text-align:center;">Executive</th>
                        <th style="padding:var(--space-4) var(--space-5); text-align:center;">Family</th>
                        <th style="padding:var(--space-4) var(--space-5); text-align:center; background:var(--gold); color:var(--navy);">Presidential</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $compare = [
                        ['Price / Night',     '$89', '$149', '$229', '$199', '$599'],
                        ['Max Guests',        '2',   '2',    '2',    '5',    '4'],
                        ['Free WiFi',         '✓',   '✓',    '✓',    '✓',    '✓'],
                        ['Flat-screen TV',    '✓',   '✓',    '✓',    '✓',    '✓'],
                        ['Mini-bar',          '—',   '✓',    '✓',    '✓',    '✓'],
                        ['Balcony/Terrace',   '—',   '✓',    '—',    '—',    '✓'],
                        ['Bathtub',           '—',   '✓',    '✓',    '—',    '✓'],
                        ['Work Desk',         '—',   '—',    '✓',    '—',    '✓'],
                        ['Butler Service',    '—',   '—',    '—',    '—',    '✓'],
                        ['Club Lounge Access','—',   '—',    '✓',    '—',    '✓'],
                        ['Breakfast',        'Paid', 'Incl.','Incl.','Paid','Incl.'],
                    ];
                    foreach ($compare as $j => $row): ?>
                    <tr style="background: <?= $j % 2 ? 'var(--gray-100)' : 'var(--white)' ?>;">
                        <td style="padding:var(--space-4) var(--space-5); font-weight:600; color:var(--navy);"><?= htmlspecialchars($row[0]) ?></td>
                        <?php for ($k = 1; $k <= 5; $k++):
                            $v     = $row[$k];
                            $color = $v === '✓' ? 'var(--success)' : ($v === '—' ? 'var(--gray-300)' : 'var(--navy)');
                            $bg    = $k === 5 ? 'rgba(201,168,76,.05)' : 'transparent';
                        ?>
                        <td style="padding:var(--space-4) var(--space-5); text-align:center; color:<?= $color ?>; background:<?= $bg ?>; font-weight:<?= in_array($v, ['✓','—']) ? '700' : '400' ?>;">
                            <?= $v ?>
                        </td>
                        <?php endfor; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
