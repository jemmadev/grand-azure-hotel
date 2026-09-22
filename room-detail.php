<?php
// ============================================================
//  Grand Azure Hotel — Room Detail Page
// ============================================================

$meta_desc = 'Room details at Grand Azure Hotel.';
require_once 'includes/functions.php';

$room_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$room    = $room_id ? get_room_by_id($conn, $room_id) : null;

// Fallback demo room data if DB unavailable
if (!$room) {
    $demo_rooms = [
        1 => ['id'=>1,'name'=>'Comfort Standard Room','category'=>'Standard','description'=>'A cozy room with all essential comforts. Featuring premium bedding, a flat-screen TV, air conditioning, and a well-appointed bathroom. Perfect for solo travelers or couples seeking a relaxing and comfortable stay in the heart of the city.','amenities'=>'Free WiFi,Flat-screen TV,Air Conditioning,Mini-fridge,Safe,Hair Dryer,24h Room Service','capacity'=>2,'price_per_night'=>89.00,'image'=>'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&w=1200&q=80','floor'=>1,'room_number'=>'101','status'=>'available'],
        2 => ['id'=>2,'name'=>'Superior Deluxe Room','category'=>'Deluxe','description'=>'Spacious and elegantly furnished with premium bedding and stunning city views. Features a private balcony, marble bathroom with a soaking tub, and a mini-bar stocked with your favourite beverages.','amenities'=>'Free WiFi,Flat-screen TV,Air Conditioning,Mini-bar,Safe,Hair Dryer,Bathtub,City View,Balcony','capacity'=>2,'price_per_night'=>149.00,'image'=>'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=1200&q=80','floor'=>2,'room_number'=>'201','status'=>'available'],
        3 => ['id'=>3,'name'=>'Executive Business Room','category'=>'Executive','description'=>'Designed for the discerning business traveler. Features a dedicated work desk, ergonomic chair, express checkout, and exclusive access to our Executive Lounge with complimentary refreshments.','amenities'=>'Free WiFi,Smart TV,Air Conditioning,Mini-bar,Work Desk,Safe,Espresso Machine,Bathtub,Shower,Lounge Access','capacity'=>2,'price_per_night'=>229.00,'image'=>'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?auto=format&fit=crop&w=1200&q=80','floor'=>3,'room_number'=>'301','status'=>'available'],
        4 => ['id'=>4,'name'=>'Spacious Family Room','category'=>'Family','description'=>'Thoughtfully designed for families, offering ample space, two bathrooms, and fun amenities for children. Features connecting rooms option and a dedicated kids corner with games and toys.','amenities'=>'Free WiFi,Smart TV,Air Conditioning,Mini-bar,Safe,Two Bathrooms,Children Amenities,Connecting Rooms','capacity'=>5,'price_per_night'=>199.00,'image'=>'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?auto=format&fit=crop&w=1200&q=80','floor'=>2,'room_number'=>'205','status'=>'available'],
        5 => ['id'=>5,'name'=>'Presidential Suite','category'=>'Presidential Suite','description'=>'The pinnacle of luxury. A full two-bedroom suite with a private terrace, dedicated butler service, and panoramic city views. Featuring a gourmet kitchen, private gym, and your own Jacuzzi overlooking the skyline.','amenities'=>'Free WiFi,Smart TV,Air Conditioning,Full Kitchen,Private Terrace,Butler Service,Jacuzzi,Panoramic View,Meeting Room,Private Gym','capacity'=>4,'price_per_night'=>599.00,'image'=>'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1200&q=80','floor'=>10,'room_number'=>'1001','status'=>'available'],
    ];
    $room = $demo_rooms[$room_id] ?? $demo_rooms[1];
}

// Room category images (fallback when a room has no image set)
$cat_images = [
    'Standard'          => 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&w=1200&q=80',
    'Deluxe'            => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=1200&q=80',
    'Executive'         => 'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?auto=format&fit=crop&w=1200&q=80',
    'Family'            => 'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?auto=format&fit=crop&w=1200&q=80',
    'Presidential Suite'=> 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1200&q=80',
];
if (empty($room['image'])) {
    $room['image'] = $cat_images[$room['category']] ?? 'https://via.placeholder.com/1200x800?text=No+Image+Available';
}

$page_title = htmlspecialchars($room['name']);
$amenities  = array_map('trim', explode(',', $room['amenities'] ?? ''));
$room_images = !empty($room['id']) ? get_room_images($conn, (int)$room['id']) : [];

require_once 'includes/header.php';
?>
<style>
.room-gallery-main { width:100%; height:520px; object-fit:cover; border-radius:var(--radius-lg); cursor:pointer; }
.room-thumb { width:100%; height:110px; object-fit:cover; border-radius:var(--radius); cursor:pointer; border:3px solid transparent; transition:.2s; }
.room-thumb.active, .room-thumb:hover { border-color:var(--gold); }
.amenity-row { display:flex; align-items:center; gap:var(--space-3); padding:var(--space-3) 0; border-bottom:1px solid var(--gray-100); }
.amenity-row:last-child { border-bottom:none; }
.amenity-row i { width:28px; color:var(--gold); text-align:center; }
</style>

<!-- PAGE HERO -->
<section class="page-hero">
    <div class="container">
        <p class="page-hero-eyebrow"><?= htmlspecialchars($room['category']) ?></p>
        <h1 class="page-hero-title"><?= htmlspecialchars($room['name']) ?></h1>
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="index.php">Home</a><span class="breadcrumb-sep">›</span>
            <a href="rooms.php">Rooms</a><span class="breadcrumb-sep">›</span>
            <span aria-current="page"><?= htmlspecialchars($room['name']) ?></span>
        </nav>
    </div>
</section>

<section class="section bg-cream">
    <div class="container">
        <div class="grid-2" style="gap:4rem; align-items:start;">
            <!-- Left: Gallery -->
            <div class="reveal">
                <img id="mainRoomImg" class="room-gallery-main" src="<?= htmlspecialchars($room['image']) ?>" alt="<?= htmlspecialchars($room['name']) ?>">
                <?php if (!empty($room_images)): ?>
                <div class="grid-4" style="margin-top:var(--space-3); gap:var(--space-3);">
                    <img class="room-thumb active"
                         src="<?= htmlspecialchars($room['image']) ?>"
                         alt="<?= htmlspecialchars($room['name']) ?> — main view"
                         loading="lazy"
                         onclick="switchRoomImg(this)">
                    <?php foreach ($room_images as $ri): ?>
                    <img class="room-thumb"
                         src="<?= htmlspecialchars(image_url($ri['image'])) ?>"
                         alt="<?= htmlspecialchars($ri['caption'] ?: ($room['name'] . ' — additional view')) ?>"
                         loading="lazy"
                         onclick="switchRoomImg(this)">
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Right: Details -->
            <div class="reveal delay-0">
                <div style="display:flex; gap:var(--space-3); flex-wrap:wrap; margin-bottom:var(--space-5);">
                    <span class="badge badge-gold"><?= htmlspecialchars($room['category']) ?></span>
                    <span class="badge <?= $room['status']==='available' ? 'badge-success' : 'badge-danger' ?>">
                        <?= $room['status']==='available' ? 'Available' : ucfirst($room['status']) ?>
                    </span>
                    <span class="badge badge-secondary">Floor <?= $room['floor'] ?> · Room <?= htmlspecialchars($room['room_number']) ?></span>
                </div>

                <h2 style="font-family:var(--font-serif); font-size:var(--text-3xl); color:var(--navy); margin-bottom:var(--space-5);">
                    <?= htmlspecialchars($room['name']) ?>
                </h2>

                <!-- Capacity / Price row -->
                <div style="display:flex; align-items:center; gap:var(--space-8); padding:var(--space-5); background:var(--white); border-radius:var(--radius-md); margin-bottom:var(--space-6);">
                    <div style="text-align:center;">
                        <div style="font-family:var(--font-serif); font-size:var(--text-3xl); color:var(--gold); font-weight:700;"><?= format_price($room['price_per_night']) ?></div>
                        <div style="font-size:var(--text-xs); color:var(--gray-400); text-transform:uppercase; letter-spacing:.08em;">per night</div>
                    </div>
                    <div style="width:1px; height:50px; background:var(--gray-200);"></div>
                    <div style="text-align:center;">
                        <div style="font-size:var(--text-2xl); color:var(--navy); font-weight:700;"><?= $room['capacity'] ?></div>
                        <div style="font-size:var(--text-xs); color:var(--gray-400); text-transform:uppercase; letter-spacing:.08em;">guests max</div>
                    </div>
                </div>

                <p style="color:var(--gray-500); line-height:var(--lh-relaxed); margin-bottom:var(--space-6);">
                    <?= htmlspecialchars($room['description']) ?>
                </p>

                <!-- Amenities -->
                <h3 style="font-family:var(--font-serif); font-size:var(--text-xl); color:var(--navy); margin-bottom:var(--space-4);">Included Amenities</h3>
                <div style="margin-bottom:var(--space-8);">
                    <?php foreach ($amenities as $am): ?>
                    <div class="amenity-row">
                        <i class="fas fa-check-circle"></i>
                        <span><?= htmlspecialchars($am) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Booking form -->
                <?php if ($room['status'] === 'available'): ?>
                <div style="background:var(--white); border-radius:var(--radius-md); padding:var(--space-6); border:2px solid var(--gold);">
                    <h3 style="font-family:var(--font-serif); font-size:var(--text-xl); color:var(--navy); margin-bottom:var(--space-5);">
                        <i class="fas fa-calendar-check" style="color:var(--gold); margin-right:var(--space-2);"></i>
                        Quick Reserve
                    </h3>
                    <form action="booking.php" method="GET" data-validate>
                        <input type="hidden" name="room_id" value="<?= $room['id'] ?>">
                        <div class="grid-2" style="gap:var(--space-4); margin-bottom:var(--space-4);">
                            <div class="form-group" style="margin:0;">
                                <label class="form-label" for="rd_check_in">Check-In</label>
                                <input class="form-control" type="date" id="rd_check_in" name="check_in" required>
                            </div>
                            <div class="form-group" style="margin:0;">
                                <label class="form-label" for="rd_check_out">Check-Out</label>
                                <input class="form-control" type="date" id="rd_check_out" name="check_out" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="rd_guests">Guests</label>
                            <select class="form-control" id="rd_guests" name="guests">
                                <?php for ($g = 1; $g <= $room['capacity']; $g++): ?>
                                <option value="<?= $g ?>"><?= $g ?> Guest<?= $g > 1 ? 's' : '' ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-calendar-check"></i>
                            Book This Room
                        </button>
                    </form>
                </div>
                <?php else: ?>
                <div class="alert alert-error">This room is currently not available for booking.</div>
                <a href="rooms.php" class="btn btn-outline-dark btn-block">Browse Other Rooms</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($room_images)): ?>
<script>
function switchRoomImg(thumb) {
    document.getElementById('mainRoomImg').src = thumb.src;
    document.querySelectorAll('.room-thumb').forEach(t => t.classList.remove('active'));
    thumb.classList.add('active');
}
</script>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
