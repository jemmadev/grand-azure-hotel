<?php
// ============================================================
//  Grand Azure Hotel — Admin Reports & Analytics
// ============================================================

$page_title = 'Reports & Analytics';
require_once 'includes/header.php';

// Revenue by category
$rev_cat_q = "SELECT r.category, SUM(b.total_price) AS total, COUNT(b.id) AS booking_count 
              FROM bookings b
              JOIN rooms r ON b.room_id = r.id
              WHERE b.status IN ('approved', 'completed')
              GROUP BY r.category
              ORDER BY total DESC";
$rev_cat_res = $conn->query($rev_cat_q);
$rev_cat     = $rev_cat_res ? $rev_cat_res->fetch_all(MYSQLI_ASSOC) : [];

// Monthly revenue (last 6 months)
$rev_month_q = "SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, SUM(total_price) AS total, COUNT(id) AS booking_count
                FROM bookings
                WHERE status IN ('approved', 'completed')
                GROUP BY month
                ORDER BY month DESC
                LIMIT 6";
$rev_month_res = $conn->query($rev_month_q);
$rev_month     = $rev_month_res ? $rev_month_res->fetch_all(MYSQLI_ASSOC) : [];

// Booking status breakdown
$status_break_q = "SELECT status, COUNT(*) AS count, SUM(total_price) AS total_val 
                   FROM bookings 
                   GROUP BY status";
$status_break_res = $conn->query($status_break_q);
$status_break     = $status_break_res ? $status_break_res->fetch_all(MYSQLI_ASSOC) : [];

// Total operational stats
$res_total_bk = $conn->query("SELECT COUNT(*) AS total FROM bookings");
$total_bk     = $res_total_bk ? $res_total_bk->fetch_assoc()['total'] : 0;

$res_cancelled = $conn->query("SELECT COUNT(*) AS total FROM bookings WHERE status = 'cancelled'");
$cancelled_bk  = $res_cancelled ? $res_cancelled->fetch_assoc()['total'] : 0;

$cancellation_rate = $total_bk > 0 ? round(($cancelled_bk / $total_bk) * 100, 1) : 0;

// Users report
$res_total_users = $conn->query("SELECT COUNT(*) AS total FROM users");
$total_users      = $res_total_users ? $res_total_users->fetch_assoc()['total'] : 0;

$res_new_users = $conn->query("SELECT COUNT(*) AS total FROM users WHERE created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')");
$new_users_this_month = $res_new_users ? $res_new_users->fetch_assoc()['total'] : 0;

// Room occupancy: current status breakdown + rate
$occupancy_q   = "SELECT status, COUNT(*) AS count FROM rooms GROUP BY status";
$occupancy_res = $conn->query($occupancy_q);
$occupancy     = $occupancy_res ? $occupancy_res->fetch_all(MYSQLI_ASSOC) : [];

$res_total_rooms = $conn->query("SELECT COUNT(*) AS total FROM rooms");
$total_rooms     = $res_total_rooms ? $res_total_rooms->fetch_assoc()['total'] : 0;

$res_booked_rooms = $conn->query("SELECT COUNT(*) AS total FROM rooms WHERE status = 'booked'");
$booked_rooms     = $res_booked_rooms ? $res_booked_rooms->fetch_assoc()['total'] : 0;

$occupancy_rate = $total_rooms > 0 ? round(($booked_rooms / $total_rooms) * 100, 1) : 0;
?>

<div style="margin-bottom: var(--space-8); display: flex; justify-content: space-between; align-items: flex-start;">
    <div>
        <h1 style="font-family: var(--font-serif); font-size: var(--text-3xl); color: var(--navy); margin-bottom: var(--space-2);">Reports &amp; Analytics</h1>
        <p style="color: var(--gray-500);">Financial performance overview, popular room categories, and booking metrics.</p>
    </div>
    <button onclick="window.print()" class="btn btn-outline-dark no-print"><i class="fas fa-print"></i> Print / Export PDF</button>
</div>

<!-- Operational Highlights -->
<div class="grid-3" style="margin-bottom: var(--space-8); gap: var(--space-5);">
    <div class="stat-card" style="background: var(--white); padding: var(--space-6); border-radius: var(--radius-md); box-shadow: var(--shadow-sm); display: flex; align-items: center; gap: var(--space-4);">
        <div class="stat-icon" style="background: rgba(201,168,76,0.15); width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: var(--text-xl); color: var(--gold);"><i class="fas fa-file-invoice-dollar"></i></div>
        <div>
            <div style="font-size: var(--text-2xl); font-weight: 700; color: var(--navy);"><?= $total_bk ?></div>
            <div style="font-size: var(--text-xs); color: var(--gray-500); text-transform: uppercase;">Total Bookings Processed</div>
        </div>
    </div>
    
    <div class="stat-card" style="background: var(--white); padding: var(--space-6); border-radius: var(--radius-md); box-shadow: var(--shadow-sm); display: flex; align-items: center; gap: var(--space-4);">
        <div class="stat-icon" style="background: rgba(231,76,60,0.15); width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: var(--text-xl); color: var(--danger);"><i class="fas fa-ban"></i></div>
        <div>
            <div style="font-size: var(--text-2xl); font-weight: 700; color: var(--navy);"><?= $cancelled_bk ?></div>
            <div style="font-size: var(--text-xs); color: var(--gray-500); text-transform: uppercase;">Cancelled Bookings</div>
        </div>
    </div>

    <div class="stat-card" style="background: var(--white); padding: var(--space-6); border-radius: var(--radius-md); box-shadow: var(--shadow-sm); display: flex; align-items: center; gap: var(--space-4);">
        <div class="stat-icon" style="background: rgba(52,152,219,0.15); width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: var(--text-xl); color: var(--info);"><i class="fas fa-percentage"></i></div>
        <div>
            <div style="font-size: var(--text-2xl); font-weight: 700; color: var(--navy);"><?= $cancellation_rate ?>%</div>
            <div style="font-size: var(--text-xs); color: var(--gray-500); text-transform: uppercase;">Cancellation Rate</div>
        </div>
    </div>
</div>

<!-- Users & Room Occupancy -->
<div class="grid-2" style="gap: var(--space-8); align-items: start; margin-bottom: var(--space-8);">

    <!-- Users -->
    <div class="admin-card" style="margin-bottom: 0;">
        <h2 style="font-family: var(--font-serif); font-size: var(--text-xl); color: var(--navy); margin-bottom: var(--space-4);">Users</h2>
        <p style="color: var(--gray-400); font-size: var(--text-xs); margin-bottom: var(--space-5);">Registered guest accounts.</p>
        <table class="admin-table">
            <tbody>
                <tr><td>Total Registered Users</td><td style="text-align: right; font-weight: 600; color: var(--navy);"><?= $total_users ?></td></tr>
                <tr><td>New Users This Month</td><td style="text-align: right; font-weight: 600; color: var(--gold);"><?= $new_users_this_month ?></td></tr>
            </tbody>
        </table>
    </div>

    <!-- Room Occupancy -->
    <div class="admin-card" style="margin-bottom: 0;">
        <h2 style="font-family: var(--font-serif); font-size: var(--text-xl); color: var(--navy); margin-bottom: var(--space-4);">Room Occupancy</h2>
        <p style="color: var(--gray-400); font-size: var(--text-xs); margin-bottom: var(--space-5);">Current status of all rooms in the property.</p>
        <table class="admin-table">
            <thead>
                <tr><th>Status</th><th style="text-align: right;">Rooms</th></tr>
            </thead>
            <tbody>
                <?php foreach ($occupancy as $o): ?>
                <tr><td><?= ucfirst($o['status']) ?></td><td style="text-align: right; font-weight: 600;"><?= $o['count'] ?></td></tr>
                <?php endforeach; ?>
                <tr><td><strong>Occupancy Rate (booked / total)</strong></td><td style="text-align: right; font-weight: 700; color: var(--gold);"><?= $occupancy_rate ?>%</td></tr>
            </tbody>
        </table>
    </div>
</div>

<div class="grid-2" style="gap: var(--space-8); align-items: start; margin-bottom: var(--space-8);">
    
    <!-- Revenue by Room Category -->
    <div class="admin-card" style="margin-bottom: 0;">
        <h2 style="font-family: var(--font-serif); font-size: var(--text-xl); color: var(--navy); margin-bottom: var(--space-4);">Popularity by Category</h2>
        <p style="color: var(--gray-400); font-size: var(--text-xs); margin-bottom: var(--space-5);">Revenue earned grouped by type of accommodation.</p>
        
        <?php if (empty($rev_cat)): ?>
            <p style="color: var(--gray-400); text-align: center; padding: var(--space-8);">No sales data available yet.</p>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Bookings count</th>
                        <th>Revenue Generated</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rev_cat as $rc): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($rc['category']) ?></strong></td>
                        <td><?= $rc['booking_count'] ?> reservations</td>
                        <td style="font-weight: 600; color: var(--gold);"><?= format_price($rc['total']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Monthly Revenue -->
    <div class="admin-card" style="margin-bottom: 0;">
        <h2 style="font-family: var(--font-serif); font-size: var(--text-xl); color: var(--navy); margin-bottom: var(--space-4);">Monthly Performance</h2>
        <p style="color: var(--gray-400); font-size: var(--text-xs); margin-bottom: var(--space-5);">Sales performance over the last 6 months.</p>
        
        <?php if (empty($rev_month)): ?>
            <p style="color: var(--gray-400); text-align: center; padding: var(--space-8);">No monthly transaction logs yet.</p>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Bookings count</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rev_month as $rm): ?>
                    <tr>
                        <td><strong><?= date('F Y', strtotime($rm['month'] . '-01')) ?></strong></td>
                        <td><?= $rm['booking_count'] ?> sales</td>
                        <td style="font-weight: 600; color: var(--gold);"><?= format_price($rm['total']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

</div>

<!-- Booking Status distribution -->
<div class="admin-card">
    <h2 style="font-family: var(--font-serif); font-size: var(--text-xl); color: var(--navy); margin-bottom: var(--space-4);">Booking Status Breakdown</h2>
    <p style="color: var(--gray-400); font-size: var(--text-xs); margin-bottom: var(--space-6);">Summary of bookings grouped by current status.</p>
    
    <?php if (empty($status_break)): ?>
        <p style="color: var(--gray-400); text-align: center; padding: var(--space-8);">No records found.</p>
    <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Count</th>
                    <th>Subtotal Price Value</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($status_break as $sb): ?>
                <tr>
                    <td><?= status_badge($sb['status']) ?></td>
                    <td><strong><?= $sb['count'] ?></strong> bookings</td>
                    <td style="font-weight: 600; color: var(--gold);"><?= format_price($sb['total_val'] ?? 0.00) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
