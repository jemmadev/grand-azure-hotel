<?php
// ============================================================
//  Grand Azure Hotel — Admin User Management
// ============================================================

$page_title = 'Manage Users';

// Load functions and check admin login WITHOUT printing any HTML yet,
// so that redirect() below still works.
require_once '../includes/functions.php';
require_admin_login();

// Handle user suspension toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_status']) && csrf_verify()) {
    $user_id    = (int)$_POST['user_id'];
    $new_status = sanitize($_POST['status']);
    
    if (in_array($new_status, ['active', 'suspended'])) {
        $upd = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
        $upd->bind_param('si', $new_status, $user_id);
        
        if ($upd->execute()) {
            set_flash('success', 'User account status set to ' . ucfirst($new_status) . '.');
        } else {
            set_flash('error', 'Failed to update user account status.');
        }
    }
    redirect('users.php');
}

// Now it's safe to print the page
require_once 'includes/header.php';

// Fetch all users
$res = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
?>

<div style="margin-bottom: var(--space-8);">
    <h1 style="font-family: var(--font-serif); font-size: var(--text-3xl); color: var(--navy); margin-bottom: var(--space-2);">Manage User Accounts</h1>
    <p style="color: var(--gray-500);">Review registered customers, view booking stats, and manage account statuses.</p>
</div>

<div class="admin-card">
    <?php if (empty($users)): ?>
        <p style="color: var(--gray-400); text-align: center; padding: var(--space-8);">No registered users found.</p>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Registration Date</th>
                        <th>Status</th>
                        <th style="text-align: right;">Control Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td><strong>#<?= $u['id'] ?></strong></td>
                        <td><strong><?= htmlspecialchars($u['name']) ?></strong></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><?= htmlspecialchars($u['phone'] ?? 'No Phone') ?></td>
                        <td><?= format_date($u['created_at']) ?></td>
                        <td>
                            <span class="badge <?= $u['status'] === 'active' ? 'badge-success' : 'badge-danger' ?>">
                                <?= ucfirst($u['status']) ?>
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <form method="POST" style="display: inline;">
                                <?php csrf_field(); ?>
                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                
                                <?php if ($u['status'] === 'active'): ?>
                                    <input type="hidden" name="status" value="suspended">
                                    <button type="submit" name="toggle_status" class="btn btn-outline-gold btn-sm" style="color: var(--danger); border-color: var(--danger);"><i class="fas fa-user-slash"></i> Suspend Account</button>
                                <?php else: ?>
                                    <input type="hidden" name="status" value="active">
                                    <button type="submit" name="toggle_status" class="btn btn-primary btn-sm"><i class="fas fa-user-check"></i> Reactivate Account</button>
                                <?php endif; ?>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
