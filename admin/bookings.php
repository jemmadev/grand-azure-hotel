<?php
// ============================================================
//  Grand Azure Hotel — Admin Booking Management
// ============================================================

$page_title = 'Manage Bookings';

// Load functions and check admin login WITHOUT printing any HTML yet,
// so that redirect() below (which sends a Location header) still works.
require_once '../includes/functions.php';
require_admin_login();

$filter_status = isset($_GET['status']) ? sanitize($_GET['status']) : '';

// Handle actions (approve / cancel / complete)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status']) && csrf_verify()) {
    $booking_id = (int)$_POST['booking_id'];
    $new_status = sanitize($_POST['status']);
    
    if (in_array($new_status, ['pending', 'approved', 'cancelled', 'completed'])) {
        $upd = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
        $upd->bind_param('si', $new_status, $booking_id);
        
        if ($upd->execute()) {
            set_flash('success', 'Booking status updated to ' . ucfirst($new_status) . '.');
        } else {
            set_flash('error', 'Failed to update booking status.');
        }
    }
    redirect('bookings.php' . ($filter_status ? '?status=' . $filter_status : ''));
}

// Now it's safe to print the page (no more redirects after this point)
require_once 'includes/header.php';

// Fetch bookings with filters
$sql = "SELECT b.*, u.name AS user_name, u.email AS user_email, u.phone AS user_phone, r.name AS room_name, r.room_number 
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN rooms r ON b.room_id = r.id";

$params = [];
$types  = '';
if ($filter_status) {
    $sql .= " WHERE b.status = ?";
    $types = 's';
    $params[] = $filter_status;
}
$sql .= " ORDER BY b.created_at DESC";

$stmt = $conn->prepare($sql);
if ($types) $stmt->bind_param($types, ...$params);
$stmt->execute();
$bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<div style="margin-bottom: var(--space-8); display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 style="font-family: var(--font-serif); font-size: var(--text-3xl); color: var(--navy); margin-bottom: var(--space-2);">Manage Reservations</h1>
        <p style="color: var(--gray-500);">Approve incoming pending reservations, cancel bookings, and view current occupancy.</p>
    </div>
</div>

<!-- Filter controls -->
<div class="admin-card" style="padding: var(--space-4) var(--space-6); display: flex; gap: var(--space-3); flex-wrap: wrap; align-items: center;">
    <span style="font-weight: 600; color: var(--navy); font-size: var(--text-sm);"><i class="fas fa-filter"></i> Filter Status:</span>
    <a href="bookings.php" class="btn btn-sm <?= !$filter_status ? 'btn-primary' : 'btn-outline-dark' ?>">All</a>
    <a href="bookings.php?status=pending" class="btn btn-sm <?= $filter_status === 'pending' ? 'btn-primary' : 'btn-outline-dark' ?>">Pending</a>
    <a href="bookings.php?status=approved" class="btn btn-sm <?= $filter_status === 'approved' ? 'btn-primary' : 'btn-outline-dark' ?>">Approved</a>
    <a href="bookings.php?status=completed" class="btn btn-sm <?= $filter_status === 'completed' ? 'btn-primary' : 'btn-outline-dark' ?>">Completed</a>
    <a href="bookings.php?status=cancelled" class="btn btn-sm <?= $filter_status === 'cancelled' ? 'btn-primary' : 'btn-outline-dark' ?>">Cancelled</a>
</div>

<!-- Bookings Grid/Table -->
<div class="admin-card">
    <?php if (empty($bookings)): ?>
        <p style="color: var(--gray-400); text-align: center; padding: var(--space-8);">No reservations found matching the filters.</p>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Ref #</th>
                        <th>Guest Info</th>
                        <th>Room</th>
                        <th>Check In / Out</th>
                        <th>Guests</th>
                        <th>Total Price</th>
                        <th>Status</th>
                        <th style="text-align: right;">Action Control</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $b): 
                        $nights = nights_between($b['check_in'], $b['check_out']);
                    ?>
                    <tr>
                        <td><strong>#BK-<?= $b['id'] ?></strong></td>
                        <td>
                            <strong><?= htmlspecialchars($b['user_name']) ?></strong>
                            <div style="font-size: 11px; color: var(--gray-400);"><?= htmlspecialchars($b['user_email']) ?></div>
                            <div style="font-size: 11px; color: var(--gray-400);"><?= htmlspecialchars($b['user_phone'] ?? 'No Phone') ?></div>
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($b['room_name']) ?></strong>
                            <div style="font-size: 11px; color: var(--gray-400);">Room #<?= htmlspecialchars($b['room_number']) ?></div>
                        </td>
                        <td>
                            <div><strong>In:</strong> <?= format_date($b['check_in']) ?></div>
                            <div><strong>Out:</strong> <?= format_date($b['check_out']) ?></div>
                            <span style="font-size: 11px; color: var(--gold); font-weight: 600;"><?= $nights ?> night<?= $nights > 1 ? 's' : '' ?></span>
                        </td>
                        <td><?= $b['guests'] ?> Guest<?= $b['guests'] > 1 ? 's' : '' ?></td>
                        <td style="font-weight: 700; color: var(--gold);"><?= format_price($b['total_price']) ?></td>
                        <td><?= status_badge($b['status']) ?></td>
                        <td style="text-align: right;">
                            <form method="POST" style="display: inline-flex; gap: 4px;">
                                <?php csrf_field(); ?>
                                <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                
                                <?php if ($b['status'] === 'pending'): ?>
                                    <button type="submit" name="update_status" value="1" onclick="this.form.status.value='approved'" class="btn btn-primary btn-sm"><i class="fas fa-check"></i> Approve</button>
                                <?php endif; ?>

                                <?php if (in_array($b['status'], ['pending', 'approved'])): ?>
                                    <button type="submit" name="update_status" value="1" onclick="this.form.status.value='cancelled'" class="btn btn-outline-gold btn-sm" style="color: var(--danger); border-color: var(--danger);"><i class="fas fa-times"></i> Cancel</button>
                                <?php endif; ?>

                                <?php if ($b['status'] === 'approved'): ?>
                                    <button type="submit" name="update_status" value="1" onclick="this.form.status.value='completed'" class="btn btn-outline-dark btn-sm"><i class="fas fa-clipboard-check"></i> Check Out</button>
                                <?php endif; ?>

                                <input type="hidden" name="status" value="">
                            </form>
                        </td>
                    </tr>
                    <?php if (!empty($b['special_request'])): ?>
                    <tr style="background: rgba(201,168,76,0.03);">
                        <td colspan="8" style="padding: var(--space-2) var(--space-4); font-size: 12px; color: var(--gray-600); border-bottom: 1px solid var(--gray-100);">
                            <i class="fas fa-comment-dots" style="color: var(--gold); margin-right: 4px;"></i><strong>Special Request:</strong> "<?= htmlspecialchars($b['special_request']) ?>"
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
