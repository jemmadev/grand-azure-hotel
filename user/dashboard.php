<?php
// ============================================================
//  Grand Azure Hotel — User Dashboard
// ============================================================

$page_title = 'My Dashboard';
require_once 'includes/header.php'; // handles login check + fetches $user, $user_name

// Fetch bookings
$bookings = get_user_bookings($conn, $user_id);
$total_bookings   = count($bookings);
$active_bookings  = count(array_filter($bookings, fn($b) => in_array($b['status'], ['pending','approved'])));
$total_spent      = array_sum(array_column(array_filter($bookings, fn($b) => $b['status'] !== 'cancelled'), 'total_price'));
?>

        <div style="margin-bottom:var(--space-8);">
            <h1 style="font-family:var(--font-serif); font-size:var(--text-3xl); color:var(--navy); margin-bottom:var(--space-2);">
                Welcome back, <?= htmlspecialchars(explode(' ', $user_name)[0]) ?>! 👋
            </h1>
            <p style="color:var(--gray-500);">Manage your reservations and account settings below.</p>
        </div>

        <!-- Stats row -->
        <div class="grid-3" style="margin-bottom:var(--space-8); gap:var(--space-5);">
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg,rgba(201,168,76,.15),rgba(201,168,76,.25));">
                    <i class="fas fa-calendar-check" style="color:var(--gold);"></i>
                </div>
                <div>
                    <div style="font-family:var(--font-serif); font-size:var(--text-3xl); font-weight:700; color:var(--navy);"><?= $total_bookings ?></div>
                    <div style="font-size:var(--text-sm); color:var(--gray-500);">Total Bookings</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg,rgba(46,204,113,.15),rgba(46,204,113,.25));">
                    <i class="fas fa-bed" style="color:var(--success);"></i>
                </div>
                <div>
                    <div style="font-family:var(--font-serif); font-size:var(--text-3xl); font-weight:700; color:var(--navy);"><?= $active_bookings ?></div>
                    <div style="font-size:var(--text-sm); color:var(--gray-500);">Active Bookings</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg,rgba(52,152,219,.15),rgba(52,152,219,.25));">
                    <i class="fas fa-dollar-sign" style="color:var(--info);"></i>
                </div>
                <div>
                    <div style="font-family:var(--font-serif); font-size:var(--text-3xl); font-weight:700; color:var(--navy);"><?= format_price($total_spent) ?></div>
                    <div style="font-size:var(--text-sm); color:var(--gray-500);">Total Spent</div>
                </div>
            </div>
        </div>

        <!-- Recent Bookings -->
        <div class="dash-card">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:var(--space-6);">
                <h2 style="font-family:var(--font-serif); font-size:var(--text-xl); color:var(--navy);">Recent Bookings</h2>
                <a href="history.php" class="btn btn-outline-dark btn-sm">View All</a>
            </div>

            <?php if (empty($bookings)): ?>
            <div style="text-align:center; padding:var(--space-12); color:var(--gray-400);">
                <i class="fas fa-calendar-times" style="font-size:3rem; margin-bottom:var(--space-4); display:block;"></i>
                <p>You haven't made any bookings yet.</p>
                <a href="../booking.php" class="btn btn-primary" style="margin-top:var(--space-4);">
                    <i class="fas fa-calendar-plus"></i> Make Your First Booking
                </a>
            </div>
            <?php else: ?>
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; font-size:var(--text-sm);">
                    <thead>
                        <tr style="border-bottom:2px solid var(--gray-100);">
                            <th style="padding:var(--space-3); text-align:left; color:var(--gray-500); font-weight:600;">Booking #</th>
                            <th style="padding:var(--space-3); text-align:left; color:var(--gray-500); font-weight:600;">Room</th>
                            <th style="padding:var(--space-3); text-align:left; color:var(--gray-500); font-weight:600;">Check-In</th>
                            <th style="padding:var(--space-3); text-align:left; color:var(--gray-500); font-weight:600;">Check-Out</th>
                            <th style="padding:var(--space-3); text-align:left; color:var(--gray-500); font-weight:600;">Total</th>
                            <th style="padding:var(--space-3); text-align:left; color:var(--gray-500); font-weight:600;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($bookings, 0, 5) as $bk): ?>
                        <tr style="border-bottom:1px solid var(--gray-100);">
                            <td style="padding:var(--space-4) var(--space-3); font-weight:700; color:var(--navy);">#BK-<?= $bk['id'] ?></td>
                            <td style="padding:var(--space-4) var(--space-3);"><?= htmlspecialchars($bk['room_name']) ?></td>
                            <td style="padding:var(--space-4) var(--space-3);"><?= format_date($bk['check_in']) ?></td>
                            <td style="padding:var(--space-4) var(--space-3);"><?= format_date($bk['check_out']) ?></td>
                            <td style="padding:var(--space-4) var(--space-3); font-weight:700; color:var(--gold);"><?= format_price($bk['total_price']) ?></td>
                            <td style="padding:var(--space-4) var(--space-3);"><?= status_badge($bk['status']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

        <!-- Quick Actions -->
        <div class="grid-2" style="gap:var(--space-5); margin-top:var(--space-8);">
            <a href="../booking.php" class="dash-card" style="text-decoration:none; display:flex; gap:var(--space-5); align-items:center; border:2px dashed var(--gray-200); transition:all .2s;" onmouseover="this.style.borderColor='var(--gold)';this.style.boxShadow='var(--shadow-gold)'" onmouseout="this.style.borderColor='var(--gray-200)';this.style.boxShadow=''">
                <div class="stat-icon" style="background:linear-gradient(135deg,var(--gold),var(--gold-dark));">
                    <i class="fas fa-plus" style="color:var(--navy);"></i>
                </div>
                <div>
                    <div style="font-weight:700; color:var(--navy); margin-bottom:var(--space-1);">New Booking</div>
                    <div style="font-size:var(--text-sm); color:var(--gray-500);">Reserve a room for your next stay</div>
                </div>
            </a>
            <a href="profile.php" class="dash-card" style="text-decoration:none; display:flex; gap:var(--space-5); align-items:center; border:2px dashed var(--gray-200); transition:all .2s;" onmouseover="this.style.borderColor='var(--gold)';this.style.boxShadow='var(--shadow-gold)'" onmouseout="this.style.borderColor='var(--gray-200)';this.style.boxShadow=''">
                <div class="stat-icon" style="background:linear-gradient(135deg,var(--navy),var(--navy-light));">
                    <i class="fas fa-user-edit" style="color:var(--white);"></i>
                </div>
                <div>
                    <div style="font-weight:700; color:var(--navy); margin-bottom:var(--space-1);">Update Profile</div>
                    <div style="font-size:var(--text-sm); color:var(--gray-500);">Manage your account details</div>
                </div>
            </a>
        </div>
<?php require_once 'includes/footer.php'; ?>
