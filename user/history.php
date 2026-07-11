<?php
// ============================================================
//  Grand Azure Hotel — User Booking History
// ============================================================

$page_title = 'My Bookings';

// Load functions and check login WITHOUT printing any HTML yet,
// so redirect() below still works.
require_once '../includes/functions.php';
require_user_login();

$user_id = $_SESSION['user_id'];

// Handle cancellation request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_booking']) && csrf_verify()) {
    $booking_id = (int)$_POST['booking_id'];
    
    // Check if the booking belongs to this user and is still in a cancelable state (pending or approved)
    $check_stmt = $conn->prepare("SELECT status FROM bookings WHERE id = ? AND user_id = ?");
    $check_stmt->bind_param('ii', $booking_id, $user_id);
    $check_stmt->execute();
    $res = $check_stmt->get_result()->fetch_assoc();
    
    if ($res) {
        if (in_array($res['status'], ['pending', 'approved'])) {
            $upd_stmt = $conn->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?");
            $upd_stmt->bind_param('i', $booking_id);
            if ($upd_stmt->execute()) {
                set_flash('success', 'Booking #' . $booking_id . ' has been successfully cancelled.');
            } else {
                set_flash('error', 'Failed to cancel the booking. Please try again.');
            }
        } else {
            set_flash('error', 'This booking cannot be cancelled because it is already ' . $res['status'] . '.');
        }
    } else {
        set_flash('error', 'Invalid booking request.');
    }
    redirect('history.php');
}

// Now it's safe to print the page (no more redirects after this point)
require_once 'includes/header.php';

// Fetch bookings
$bookings = get_user_bookings($conn, $user_id);
?>

        <div style="margin-bottom:var(--space-8); display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h1 style="font-family:var(--font-serif); font-size:var(--text-3xl); color:var(--navy); margin-bottom:var(--space-2);">My Bookings</h1>
                <p style="color:var(--gray-500);">Review and manage your current and past hotel reservations.</p>
            </div>
            <a href="../booking.php" class="btn btn-primary"><i class="fas fa-plus"></i> Book Another Stay</a>
        </div>

        <div class="dash-card">
            <?php if (empty($bookings)): ?>
            <div style="text-align:center; padding:var(--space-12); color:var(--gray-400);">
                <i class="fas fa-calendar-times" style="font-size:3rem; margin-bottom:var(--space-4); display:block;"></i>
                <p>No bookings found in your history.</p>
            </div>
            <?php else: ?>
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; font-size:var(--text-sm);">
                    <thead>
                        <tr style="border-bottom:2px solid var(--gray-100); text-align:left;">
                            <th style="padding:var(--space-3); color:var(--gray-500); font-weight:600;">Booking Reference</th>
                            <th style="padding:var(--space-3); color:var(--gray-500); font-weight:600;">Room Type</th>
                            <th style="padding:var(--space-3); color:var(--gray-500); font-weight:600;">Dates</th>
                            <th style="padding:var(--space-3); color:var(--gray-500); font-weight:600;">Nights</th>
                            <th style="padding:var(--space-3); color:var(--gray-500); font-weight:600;">Guests</th>
                            <th style="padding:var(--space-3); color:var(--gray-500); font-weight:600;">Total Price</th>
                            <th style="padding:var(--space-3); color:var(--gray-500); font-weight:600;">Status</th>
                            <th style="padding:var(--space-3); color:var(--gray-500); font-weight:600; text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $bk): 
                            $nights = nights_between($bk['check_in'], $bk['check_out']);
                        ?>
                        <tr style="border-bottom:1px solid var(--gray-100);">
                            <td style="padding:var(--space-4) var(--space-3); font-weight:700; color:var(--navy);">#BK-<?= $bk['id'] ?></td>
                            <td style="padding:var(--space-4) var(--space-3);">
                                <div style="display:flex; align-items:center; gap:var(--space-2);">
                                    <img src="<?= htmlspecialchars(image_url($bk['room_image'], 'assets/images/rooms/standard.jpg')) ?>" 
                                         alt="<?= htmlspecialchars($bk['room_name']) ?>" 
                                         style="width:50px; height:35px; object-fit:cover; border-radius:var(--radius-sm);">
                                    <div>
                                        <div style="font-weight:600; color:var(--navy);"><?= htmlspecialchars($bk['room_name']) ?></div>
                                        <span class="badge badge-secondary" style="font-size:10px;"><?= htmlspecialchars($bk['category']) ?></span>
                                    </div>
                                </div>
                            </td>
                            <td style="padding:var(--space-4) var(--space-3);">
                                <div><strong>In:</strong> <?= format_date($bk['check_in']) ?></div>
                                <div style="font-size:11px; color:var(--gray-400);"><strong>Out:</strong> <?= format_date($bk['check_out']) ?></div>
                            </td>
                            <td style="padding:var(--space-4) var(--space-3);"><?= $nights ?> night<?= $nights > 1 ? 's' : '' ?></td>
                            <td style="padding:var(--space-4) var(--space-3);"><?= $bk['guests'] ?></td>
                            <td style="padding:var(--space-4) var(--space-3); font-weight:700; color:var(--gold);"><?= format_price($bk['total_price']) ?></td>
                            <td style="padding:var(--space-4) var(--space-3);"><?= status_badge($bk['status']) ?></td>
                            <td style="padding:var(--space-4) var(--space-3); text-align:right;">
                                <?php if (in_array($bk['status'], ['pending', 'approved'])): ?>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                    <?php csrf_field(); ?>
                                    <input type="hidden" name="booking_id" value="<?= $bk['id'] ?>">
                                    <button type="submit" name="cancel_booking" class="btn btn-outline-gold btn-sm" style="color:var(--danger); border-color:var(--danger); background:transparent;">
                                        Cancel Booking
                                    </button>
                                </form>
                                <?php else: ?>
                                <span style="font-size:var(--text-xs); color:var(--gray-400);">None</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
<?php require_once 'includes/footer.php'; ?>
