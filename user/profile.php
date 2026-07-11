<?php
// ============================================================
//  Grand Azure Hotel — User Profile
// ============================================================

$page_title = 'Edit Profile';
require_once '../includes/functions.php';
require_user_login();

$user_id   = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

$errors  = [];
$success = '';

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Handle info update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile']) && csrf_verify()) {
    $name    = sanitize($_POST['name']    ?? '');
    $phone   = sanitize($_POST['phone']   ?? '');
    $address = sanitize($_POST['address'] ?? '');

    if (empty($name)) {
        $errors[] = 'Full name is required.';
    } else {
        $upd = $conn->prepare("UPDATE users SET name = ?, phone = ?, address = ? WHERE id = ?");
        $upd->bind_param('sssi', $name, $phone, $address, $user_id);
        
        if ($upd->execute()) {
            $_SESSION['user_name'] = $name;
            $user_name = $name;
            $success = 'Profile updated successfully.';
            
            // Reload user info
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
        } else {
            $errors[] = 'Failed to update profile. Please try again.';
        }
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password']) && csrf_verify()) {
    $current_pass = $_POST['current_password'] ?? '';
    $new_pass     = $_POST['new_password']     ?? '';
    $confirm_pass = $_POST['confirm_password'] ?? '';

    if (empty($current_pass) || empty($new_pass) || empty($confirm_pass)) {
        $errors[] = 'All password fields are required.';
    } elseif ($new_pass !== $confirm_pass) {
        $errors[] = 'New password and confirmation do not match.';
    } elseif (strlen($new_pass) < 8) {
        $errors[] = 'New password must be at least 8 characters long.';
    } else {
        // Verify current password
        if (password_verify($current_pass, $user['password'])) {
            $hashed = password_hash($new_pass, PASSWORD_BCRYPT);
            $upd_pass = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $upd_pass->bind_param('si', $hashed, $user_id);
            if ($upd_pass->execute()) {
                $success = 'Password changed successfully.';
            } else {
                $errors[] = 'Failed to change password. Please try again.';
            }
        } else {
            $errors[] = 'Incorrect current password.';
        }
    }
}

require_once 'includes/header.php'; // prints HTML shell + refetches fresh $user
?>

        <div style="margin-bottom:var(--space-8);">
            <h1 style="font-family:var(--font-serif); font-size:var(--text-3xl); color:var(--navy); margin-bottom:var(--space-2);">Edit Profile</h1>
            <p style="color:var(--gray-500);">Keep your details up to date for a smoother experience.</p>
        </div>

        <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <div><strong>Please correct the following errors:</strong><ul style="margin-top:.25rem;"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div>
            <button class="alert-close" onclick="this.parentElement.remove()">&times;</button>
        </div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
            <button class="alert-close" onclick="this.parentElement.remove()">&times;</button>
        </div>
        <?php endif; ?>

        <div class="grid-2" style="gap:var(--space-8); align-items:start;">
            <!-- Profile Info Card -->
            <div class="dash-card">
                <h2 style="font-family:var(--font-serif); font-size:var(--text-xl); color:var(--navy); margin-bottom:var(--space-6); border-bottom:1px solid var(--gray-100); padding-bottom:var(--space-3);">
                    Personal Information
                </h2>
                <form method="POST" action="profile.php">
                    <?php csrf_field(); ?>
                    <div class="form-group">
                        <label class="form-label" for="email">Email Address</label>
                        <input class="form-control" type="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" disabled style="background:var(--gray-100); color:var(--gray-500); cursor:not-allowed;">
                        <span style="font-size:var(--text-xs); color:var(--gray-400); display:block; margin-top:.25rem;">Email addresses cannot be changed.</span>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="name">Full Name <span class="required">*</span></label>
                        <input class="form-control" type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="phone">Phone Number</label>
                        <input class="form-control" type="tel" id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="address">Postal/Billing Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" name="update_profile" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </form>
            </div>

            <!-- Password Change Card -->
            <div class="dash-card">
                <h2 style="font-family:var(--font-serif); font-size:var(--text-xl); color:var(--navy); margin-bottom:var(--space-6); border-bottom:1px solid var(--gray-100); padding-bottom:var(--space-3);">
                    Change Password
                </h2>
                <form method="POST" action="profile.php" data-validate>
                    <?php csrf_field(); ?>
                    <div class="form-group">
                        <label class="form-label" for="current_password">Current Password <span class="required">*</span></label>
                        <input class="form-control" type="password" id="current_password" name="current_password" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="new_password">New Password <span class="required">*</span></label>
                        <input class="form-control" type="password" id="new_password" name="new_password" required minlength="8" placeholder="Min. 8 characters">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="confirm_password">Confirm New Password <span class="required">*</span></label>
                        <input class="form-control" type="password" id="confirm_password" name="confirm_password" required>
                    </div>

                    <button type="submit" name="change_password" class="btn btn-outline-dark">
                        <i class="fas fa-key"></i> Update Password
                    </button>
                </form>
            </div>
        </div>
<?php require_once 'includes/footer.php'; ?>
