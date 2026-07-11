<?php
// ============================================================
//  Grand Azure Hotel — Admin Dashboard
// ============================================================

$page_title = 'Dashboard';
require_once 'includes/header.php';

// Stats: bookings count
$res_pending   = $conn->query("SELECT COUNT(*) AS total FROM bookings WHERE status = 'pending'");
$pending_count = $res_pending ? $res_pending->fetch_assoc()['total'] : 0;

$res_approved  = $conn->query("SELECT COUNT(*) AS total FROM bookings WHERE status = 'approved'");
$approved_count= $res_approved ? $res_approved->fetch_assoc()['total'] : 0;

// Stats: total users
$res_users     = $conn->query("SELECT COUNT(*) AS total FROM users");
$user_count    = $res_users ? $res_users->fetch_assoc()['total'] : 0;

// Stats: revenue
$res_rev       = $conn->query("SELECT SUM(total_price) AS total FROM bookings WHERE status IN ('approved', 'completed')");
$revenue       = $res_rev ? floatval($res_rev->fetch_assoc()['total']) : 0.00;

// Stats: rooms
$res_rooms     = $conn->query("SELECT COUNT(*) AS total FROM rooms");
$room_count    = $res_rooms ? $res_rooms->fetch_assoc()['total'] : 0;

// Recent 5 bookings
$bookings_q = "SELECT b.*, u.name AS user_name, r.name AS room_name 
               FROM bookings b 
               JOIN users u ON b.user_id = u.id 
               JOIN rooms r ON b.room_id = r.id 
               ORDER BY b.created_at DESC LIMIT 5";
$recent_bookings = $conn->query($bookings_q);
$recent_bookings = $recent_bookings ? $recent_bookings->fetch_all(MYSQLI_ASSOC) : [];
?>

<div style="margin-bottom: var(--space-8);">
    <h1 style="font-family: var(--font-serif); font-size: var(--text-3xl); color: var(--navy); margin-bottom: var(--space-2);">Control Dashboard</h1>
    <p style="color: var(--gray-500);">Real-time monitoring of hotel bookings, user registrations, and operational metrics.</p>
</div>

<!-- Stats widgets -->
<div class="grid-4" style="margin-bottom: var(--space-8); gap: var(--space-5);">
    <div class="stat-card" style="background: var(--white); padding: var(--space-6); border-radius: var(--radius-md); box-shadow: var(--shadow-sm); display: flex; align-items: center; gap: var(--space-4);">
        <div class="stat-icon" style="background: rgba(243,156,18,0.15); width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: var(--text-xl); color: var(--warning);"><i class="fas fa-clock"></i></div>
        <div>
            <div style="font-size: var(--text-2xl); font-weight: 700; color: var(--navy);"><?= $pending_count ?></div>
            <div style="font-size: var(--text-xs); color: var(--gray-500); text-transform: uppercase;">Pending Bookings</div>
        </div>
    </div>
    
    <div class="stat-card" style="background: var(--white); padding: var(--space-6); border-radius: var(--radius-md); box-shadow: var(--shadow-sm); display: flex; align-items: center; gap: var(--space-4);">
        <div class="stat-icon" style="background: rgba(46,204,113,0.15); width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: var(--text-xl); color: var(--success);"><i class="fas fa-check-circle"></i></div>
        <div>
            <div style="font-size: var(--text-2xl); font-weight: 700; color: var(--navy);"><?= $approved_count ?></div>
            <div style="font-size: var(--text-xs); color: var(--gray-500); text-transform: uppercase;">Approved Bookings</div>
        </div>
    </div>

    <div class="stat-card" style="background: var(--white); padding: var(--space-6); border-radius: var(--radius-md); box-shadow: var(--shadow-sm); display: flex; align-items: center; gap: var(--space-4);">
        <div class="stat-icon" style="background: rgba(201,168,76,0.15); width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: var(--text-xl); color: var(--gold);"><i class="fas fa-dollar-sign"></i></div>
        <div>
            <div style="font-size: var(--text-2xl); font-weight: 700; color: var(--navy);"><?= format_price($revenue) ?></div>
            <div style="font-size: var(--text-xs); color: var(--gray-500); text-transform: uppercase;">Total Revenue</div>
        </div>
    </div>

    <div class="stat-card" style="background: var(--white); padding: var(--space-6); border-radius: var(--radius-md); box-shadow: var(--shadow-sm); display: flex; align-items: center; gap: var(--space-4);">
        <div class="stat-icon" style="background: rgba(52,152,219,0.15); width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: var(--text-xl); color: var(--info);"><i class="fas fa-users"></i></div>
        <div>
            <div style="font-size: var(--text-2xl); font-weight: 700; color: var(--navy);"><?= $user_count ?></div>
            <div style="font-size: var(--text-xs); color: var(--gray-500); text-transform: uppercase;">Registered Users</div>
        </div>
    </div>
</div>

<div class="grid-2" style="gap: var(--space-8); align-items: start;">
    <!-- Recent Bookings Table -->
    <div class="admin-card" style="margin-bottom: 0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-6);">
            <h2 style="font-family: var(--font-serif); font-size: var(--text-xl); color: var(--navy);">Recent Booking Activity</h2>
            <a href="bookings.php" class="btn btn-outline-dark btn-sm">View All Bookings</a>
        </div>
        
        <?php if (empty($recent_bookings)): ?>
            <p style="color: var(--gray-400); text-align: center; padding: var(--space-8);">No bookings registered yet.</p>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Guest</th>
                            <th>Room</th>
                            <th>Dates</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_bookings as $b): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($b['user_name']) ?></strong></td>
                            <td><?= htmlspecialchars($b['room_name']) ?></td>
                            <td><?= date('M j', strtotime($b['check_in'])) ?> - <?= date('M j', strtotime($b['check_out'])) ?></td>
                            <td style="font-weight: 600; color: var(--gold);"><?= format_price($b['total_price']) ?></td>
                            <td><?= status_badge($b['status']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Quick Operations -->
    <div class="admin-card" style="margin-bottom: 0;">
        <h2 style="font-family: var(--font-serif); font-size: var(--text-xl); color: var(--navy); margin-bottom: var(--space-6);">Administrative Quick Tasks</h2>
        
        <div style="display: grid; gap: var(--space-4);">
            <a href="rooms.php?action=add" class="btn btn-primary btn-block" style="text-align: left; justify-content: flex-start; padding: var(--space-4);">
                <i class="fas fa-plus-circle"></i> Add a New Guest Room
            </a>
            
            <a href="bookings.php?status=pending" class="btn btn-outline-dark btn-block" style="text-align: left; justify-content: flex-start; padding: var(--space-4);">
                <i class="fas fa-clock"></i> Review Pending Reservations
            </a>
            
            <a href="reports.php" class="btn btn-outline-dark btn-block" style="text-align: left; justify-content: flex-start; padding: var(--space-4);">
                <i class="fas fa-file-invoice-dollar"></i> Generate Financial Reports
            </a>
        </div>
        
        <div style="margin-top: var(--space-8); padding: var(--space-4); background: var(--cream); border-radius: var(--radius); font-size: var(--text-xs); color: var(--gray-600);">
            <strong>System Info:</strong> PHP version <?= phpversion() ?> | MySQL Server version <?= $conn->server_info ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
