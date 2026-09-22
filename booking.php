<?php
// ============================================================
//  Grand Azure Hotel — Book Now Page
// ============================================================

$page_title = 'Book a Room';
$meta_desc  = 'Reserve your room at Grand Azure Hotel. Check availability, choose your dates, and confirm your booking online.';

require_once 'includes/functions.php';

// Must be logged in to complete a booking
if (!is_logged_in()) {
    $_SESSION['redirect_after_login'] = SITE_URL . '/booking.php?' . http_build_query($_GET);
    set_flash('info', 'Please login or create an account to complete your booking.');
    redirect(SITE_URL . '/login.php');
}

// Pre-fill from query params (from rooms page or quick booking bar)
$pre_room_id  = isset($_GET['room_id'])   ? (int)$_GET['room_id']   : 0;
$pre_room_type= isset($_GET['room_type']) ? sanitize($_GET['room_type']) : '';
$pre_check_in = sanitize($_GET['check_in']  ?? '');
$pre_check_out= sanitize($_GET['check_out'] ?? '');
$pre_guests   = isset($_GET['guests']) ? (int)$_GET['guests'] : 1;

// Fetch all available rooms
$all_rooms = get_all_rooms($conn);

// Fetch the pre-selected room if any
$selected_room = $pre_room_id ? get_room_by_id($conn, $pre_room_id) : null;

// If no specific room was chosen but a room type/category was (e.g. from
// the homepage quick-search bar's "Room Type" dropdown), pick the first
// available room in that category instead of ignoring the selection.
if (!$selected_room && $pre_room_type) {
    foreach ($all_rooms as $r) {
        if ($r['category'] === $pre_room_type && $r['status'] === 'available') {
            $selected_room = $r;
            $pre_room_id   = (int)$r['id'];
            break;
        }
    }
}

$error   = '';
$success = '';
$booking_id = 0;

// Handle booking submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_booking']) && csrf_verify()) {
    $room_id   = (int)($_POST['room_id']     ?? 0);
    $check_in  = sanitize($_POST['check_in'] ?? '');
    $check_out = sanitize($_POST['check_out']?? '');
    $guests    = (int)($_POST['guests']      ?? 1);
    $special   = sanitize($_POST['special_request'] ?? '');
    $user_id   = $_SESSION['user_id'];

    // Validate
    if (!$room_id || !$check_in || !$check_out) {
        $error = 'Please fill in all required fields.';
    } elseif (strtotime($check_out) <= strtotime($check_in)) {
        $error = 'Check-out date must be after check-in date.';
    } elseif (strtotime($check_in) < strtotime('today')) {
        $error = 'Check-in date cannot be in the past.';
    } else {
        // Check room availability
        $avail_stmt = $conn->prepare(
            "SELECT id FROM bookings
             WHERE room_id = ?
               AND status NOT IN ('cancelled')
               AND check_in < ? AND check_out > ?"
        );
        $avail_stmt->bind_param('iss', $room_id, $check_out, $check_in);
        $avail_stmt->execute();
        $avail_stmt->store_result();

        if ($avail_stmt->num_rows > 0) {
            $error = 'Sorry, this room is not available for the selected dates. Please choose different dates or another room.';
        } else {
            // Calculate price
            $room        = get_room_by_id($conn, $room_id);
            $nights      = nights_between($check_in, $check_out);
            $total_price = $room['price_per_night'] * $nights;

            // Insert booking
            $ins = $conn->prepare(
                "INSERT INTO bookings (user_id, room_id, check_in, check_out, guests, special_request, total_price)
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            $ins->bind_param('iisssis', $user_id, $room_id, $check_in, $check_out, $guests, $special, $total_price);

            if ($ins->execute()) {
                $booking_id = $conn->insert_id;
                $success    = "Booking confirmed! Your reference number is <strong>#BK-{$booking_id}</strong>. We'll contact you to confirm the details.";
            } else {
                $error = 'Booking failed. Please try again.';
            }
        }
    }
}

// Recalculate for display
$nights        = ($pre_check_in && $pre_check_out) ? nights_between($pre_check_in, $pre_check_out) : 1;
$estimated     = $selected_room ? $selected_room['price_per_night'] * max(1, $nights) : 0;

require_once 'includes/header.php';
?>

<!-- PAGE HERO -->
<section class="page-hero">
    <div class="container">
        <p class="page-hero-eyebrow">Reservations</p>
        <h1 class="page-hero-title">Book Your Stay</h1>
        <p class="page-hero-subtitle">Complete your reservation in just a few steps. Best rate guaranteed when you book direct.</p>
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="index.php">Home</a><span class="breadcrumb-sep">›</span>
            <span aria-current="page">Book Now</span>
        </nav>
    </div>
</section>

<section class="section bg-cream">
    <div class="container">

        <?php if ($success): ?>
        <!-- SUCCESS STATE -->
        <div style="max-width:640px; margin:0 auto; text-align:center; padding:var(--space-16) var(--space-8); background:var(--white); border-radius:var(--radius-lg); box-shadow:var(--shadow-md);">
            <div style="width:96px;height:96px;background:linear-gradient(135deg,var(--success),#27ae60);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto var(--space-8);">
                <i class="fas fa-check" style="color:white;font-size:2.5rem;"></i>
            </div>
            <h2 style="font-family:var(--font-serif);font-size:var(--text-3xl);color:var(--navy);margin-bottom:var(--space-4);">Booking Confirmed!</h2>
            <p style="color:var(--gray-500);line-height:var(--lh-relaxed);margin-bottom:var(--space-8);"><?= $success ?></p>
            <div style="display:flex;gap:var(--space-4);justify-content:center;flex-wrap:wrap;">
                <a href="user/dashboard.php" class="btn btn-primary"><i class="fas fa-list"></i> View My Bookings</a>
                <a href="index.php" class="btn btn-outline-dark"><i class="fas fa-home"></i> Back to Home</a>
            </div>
        </div>

        <?php else: ?>
        <!-- BOOKING FORM -->
        <div class="grid-2" style="gap:4rem; align-items:flex-start;">

            <!-- Step 1: Form -->
            <div class="reveal">
                <h2 style="font-family:var(--font-serif);font-size:var(--text-2xl);color:var(--navy);margin-bottom:var(--space-8);">
                    <i class="fas fa-calendar-check" style="color:var(--gold);margin-right:var(--space-3);"></i>
                    Reservation Details
                </h2>

                <?php if ($error): ?>
                <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i><?= $error ?><button class="alert-close" onclick="this.parentElement.remove()">&times;</button></div>
                <?php endif; ?>

                <?php show_flash(); ?>

                <form method="POST" action="booking.php" data-validate id="bookingForm">
                    <?php csrf_field(); ?>
                    <!-- Room Selection -->
                    <div class="form-group">
                        <label class="form-label" for="room_id">Select Room <span class="required">*</span></label>
                        <select class="form-control" id="room_id" name="room_id" required onchange="updatePrice(this)">
                            <option value="">-- Choose a Room --</option>
                            <?php foreach ($all_rooms as $r): ?>
                            <option value="<?= $r['id'] ?>"
                                    data-price="<?= $r['price_per_night'] ?>"
                                    data-capacity="<?= $r['capacity'] ?>"
                                    <?= $r['id'] == $pre_room_id ? 'selected' : '' ?>
                                    <?= $r['status'] !== 'available' ? 'disabled' : '' ?>>
                                <?= htmlspecialchars($r['name']) ?> — <?= format_price($r['price_per_night']) ?>/night
                                <?= $r['status'] !== 'available' ? '(' . ucfirst($r['status']) . ')' : '' ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Dates -->
                    <div class="grid-2" style="gap:var(--space-4);">
                        <div class="form-group">
                            <label class="form-label" for="check_in">Check-In Date <span class="required">*</span></label>
                            <input class="form-control" type="date" id="check_in" name="check_in"
                                   value="<?= htmlspecialchars($pre_check_in ?: ($_POST['check_in'] ?? '')) ?>" required onchange="calculateTotal()">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="check_out">Check-Out Date <span class="required">*</span></label>
                            <input class="form-control" type="date" id="check_out" name="check_out"
                                   value="<?= htmlspecialchars($pre_check_out ?: ($_POST['check_out'] ?? '')) ?>" required onchange="calculateTotal()">
                        </div>
                    </div>

                    <!-- Guests -->
                    <div class="form-group">
                        <label class="form-label" for="guests">Number of Guests <span class="required">*</span></label>
                        <select class="form-control" id="guests" name="guests">
                            <?php for ($g = 1; $g <= 6; $g++): ?>
                            <option value="<?= $g ?>" <?= $pre_guests == $g ? 'selected' : '' ?>><?= $g ?> Guest<?= $g > 1 ? 's' : '' ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <!-- Special Requests -->
                    <div class="form-group">
                        <label class="form-label" for="special_request">Special Requests</label>
                        <textarea class="form-control" id="special_request" name="special_request" rows="3"
                                  placeholder="e.g., high floor, twin beds, early check-in, anniversary decoration..."><?= htmlspecialchars($_POST['special_request'] ?? '') ?></textarea>
                    </div>

                    <!-- Guest Details (from session) -->
                    <div style="background:var(--gray-100);border-radius:var(--radius);padding:var(--space-4) var(--space-5); margin-bottom:var(--space-5);">
                        <div style="font-size:var(--text-sm); font-weight:600; color:var(--gray-600); margin-bottom:var(--space-2);">Booking for:</div>
                        <div style="display:flex;align-items:center;gap:var(--space-3);">
                            <div style="width:36px;height:36px;background:var(--gold);border-radius:50%;display:flex;align-items:center;justify-content:center;color:var(--navy);font-weight:700;">
                                <?= strtoupper(substr($_SESSION['user_name'], 0, 1)) ?>
                            </div>
                            <div>
                                <div style="font-weight:600;color:var(--navy);"><?= htmlspecialchars($_SESSION['user_name']) ?></div>
                                <div style="font-size:var(--text-xs);color:var(--gray-500);"><?= htmlspecialchars($_SESSION['user_email']) ?></div>
                            </div>
                            <a href="user/profile.php" style="margin-left:auto;font-size:var(--text-xs);color:var(--gold);">Edit profile</a>
                        </div>
                    </div>

                    <button type="submit" name="confirm_booking" class="btn btn-primary btn-block btn-lg">
                        <i class="fas fa-lock"></i> Confirm Reservation
                    </button>
                    <p style="text-align:center;font-size:var(--text-xs);color:var(--gray-400);margin-top:var(--space-3);">
                        <i class="fas fa-shield-alt"></i> Secure booking. Free cancellation 48h before check-in.
                    </p>
                </form>
            </div>

            <!-- Step 2: Summary -->
            <div class="reveal delay-2">
                <div style="background:var(--white);border-radius:var(--radius-lg);padding:var(--space-8);box-shadow:var(--shadow-md);position:sticky;top:120px;">
                    <h3 style="font-family:var(--font-serif);font-size:var(--text-xl);color:var(--navy);margin-bottom:var(--space-6);padding-bottom:var(--space-4);border-bottom:2px solid var(--gold);">
                        Booking Summary
                    </h3>

                    <!-- Room preview -->
                    <?php if ($selected_room): ?>
                    <img src="<?= htmlspecialchars($selected_room['image'] ?: 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&w=600&q=80') ?>"
                         alt="<?= htmlspecialchars($selected_room['name']) ?>"
                         style="width:100%;height:160px;object-fit:cover;border-radius:var(--radius-md);margin-bottom:var(--space-5);">
                    <h4 style="font-family:var(--font-serif);font-size:var(--text-lg);color:var(--navy);margin-bottom:var(--space-1);" id="summary_room_name">
                        <?= htmlspecialchars($selected_room['name']) ?>
                    </h4>
                    <span class="badge badge-gold" style="margin-bottom:var(--space-5);"><?= htmlspecialchars($selected_room['category']) ?></span>
                    <?php else: ?>
                    <div style="background:var(--gray-100);border-radius:var(--radius-md);height:160px;display:flex;align-items:center;justify-content:center;margin-bottom:var(--space-5);color:var(--gray-400);">
                        <div style="text-align:center;"><i class="fas fa-bed" style="font-size:2rem;margin-bottom:.5rem;display:block;"></i>Select a room</div>
                    </div>
                    <h4 style="color:var(--gray-400);margin-bottom:var(--space-5);" id="summary_room_name">No room selected</h4>
                    <?php endif; ?>

                    <!-- Price breakdown -->
                    <div style="margin-bottom:var(--space-6);">
                        <div style="display:flex;justify-content:space-between;padding:var(--space-3) 0;border-bottom:1px solid var(--gray-100);font-size:var(--text-sm);">
                            <span style="color:var(--gray-500);">Rate per night</span>
                            <span id="summary_rate" style="font-weight:600;color:var(--navy);">
                                <?= $selected_room ? format_price($selected_room['price_per_night']) : '—' ?>
                            </span>
                        </div>
                        <div style="display:flex;justify-content:space-between;padding:var(--space-3) 0;border-bottom:1px solid var(--gray-100);font-size:var(--text-sm);">
                            <span style="color:var(--gray-500);">Nights</span>
                            <span id="summary_nights" style="font-weight:600;color:var(--navy);"><?= max(1, $nights) ?></span>
                        </div>
                        <div style="display:flex;justify-content:space-between;padding:var(--space-3) 0;border-bottom:1px solid var(--gray-100);font-size:var(--text-sm);">
                            <span style="color:var(--gray-500);">Taxes & fees (10%)</span>
                            <span id="summary_tax" style="font-weight:600;color:var(--navy);">
                                <?= $estimated ? format_price($estimated * 0.1) : '—' ?>
                            </span>
                        </div>
                        <div style="display:flex;justify-content:space-between;padding:var(--space-4) 0;font-size:var(--text-lg);">
                            <span style="font-weight:700;color:var(--navy);">Total</span>
                            <span id="summary_total" style="font-family:var(--font-serif);font-size:var(--text-2xl);font-weight:700;color:var(--gold);">
                                <?= $estimated ? format_price($estimated * 1.1) : '—' ?>
                            </span>
                        </div>
                    </div>

                    <!-- Policies -->
                    <div style="background:var(--cream);border-radius:var(--radius);padding:var(--space-4);">
                        <?php foreach([
                            ['fas fa-check-circle','Free cancellation 48h before check-in','var(--success)'],
                            ['fas fa-check-circle','Best rate guaranteed','var(--success)'],
                            ['fas fa-check-circle','Instant confirmation','var(--success)'],
                            ['fas fa-clock','Payment at hotel check-in','var(--gold)'],
                        ] as $p): ?>
                        <div style="display:flex;align-items:center;gap:var(--space-3);padding:var(--space-2) 0;font-size:var(--text-xs);color:var(--gray-600);">
                            <i class="<?= $p[0] ?>" style="color:<?= $p[2] ?>;"></i> <?= $p[1] ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>
</section>

<script>
const roomData = <?php
    $data = [];
    foreach ($all_rooms as $r) {
        $data[$r['id']] = ['price' => floatval($r['price_per_night']), 'name' => $r['name'], 'capacity' => $r['capacity']];
    }
    echo json_encode($data);
?>;

function updatePrice(sel) {
    const room = roomData[sel.value];
    if (!room) return;
    document.getElementById('summary_room_name').textContent = room.name;
    calculateTotal();
    // Update guests max
    const guestsSel = document.getElementById('guests');
    const curGuests  = parseInt(guestsSel.value);
    guestsSel.innerHTML = '';
    for (let i = 1; i <= room.capacity; i++) {
        guestsSel.innerHTML += `<option value="${i}" ${i===Math.min(curGuests,room.capacity)?'selected':''}>${i} Guest${i>1?'s':''}</option>`;
    }
}

function calculateTotal() {
    const roomSel   = document.getElementById('room_id');
    const checkIn   = document.getElementById('check_in').value;
    const checkOut  = document.getElementById('check_out').value;
    if (!roomSel.value || !checkIn || !checkOut) return;

    const room   = roomData[roomSel.value];
    if (!room) return;

    const d1 = new Date(checkIn);
    const d2 = new Date(checkOut);
    const nights = Math.max(1, (d2 - d1) / 86400000);
    const subtotal = room.price * nights;
    const tax      = subtotal * 0.1;
    const total    = subtotal + tax;

    document.getElementById('summary_rate').textContent   = '$' + room.price.toFixed(2);
    document.getElementById('summary_nights').textContent  = nights;
    document.getElementById('summary_tax').textContent    = '$' + tax.toFixed(2);
    document.getElementById('summary_total').textContent  = '$' + total.toFixed(2);
}
</script>

<?php require_once 'includes/footer.php'; ?>
