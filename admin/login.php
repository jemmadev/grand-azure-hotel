<?php
// ============================================================
//  Grand Azure Hotel — Admin Login Page
// ============================================================

require_once '../includes/functions.php';

// Already logged in as admin
if (is_admin_logged_in()) {
    redirect(SITE_URL . '/admin/dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = sanitize($_POST['email']    ?? '');
    $password = $_POST['password']           ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please enter your administrator credentials.';
    } else {
        // Query admin table
        $stmt = $conn->prepare("SELECT id, name, email, password FROM admins WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $admin = $stmt->get_result()->fetch_assoc();

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id']    = $admin['id'];
            $_SESSION['admin_name']  = $admin['name'];
            $_SESSION['admin_email'] = $admin['email'];
            
            set_flash('success', 'Logged in successfully as Administrator.');
            redirect(SITE_URL . '/admin/dashboard.php');
        } else {
            usleep(400000); // Prevent brute-force attempts
            $error = 'Invalid admin email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator Login | Grand Azure Hotel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
    <style>
        body { background: var(--navy); display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .admin-login-card { background: var(--white); border-radius: var(--radius-lg); padding: var(--space-10); width: 100%; max-width: 440px; box-shadow: var(--shadow-lg); text-align: center; }
    </style>
</head>
<body>

<div class="admin-login-card animate-scale-in">
    <div class="nav-logo-icon" style="width: 60px; height: 60px; font-size: 1.5rem; margin: 0 auto var(--space-4);">GA</div>
    <h1 style="font-family: var(--font-serif); font-size: var(--text-2xl); color: var(--navy); margin-bottom: var(--space-2);">Admin Control Panel</h1>
    <p style="color: var(--gray-400); font-size: var(--text-sm); margin-bottom: var(--space-6);">Authorized personnel only</p>

    <?php if ($error): ?>
    <div class="alert alert-error" style="text-align: left;">
        <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="login.php" data-validate>
        <div class="form-group" style="text-align: left;">
            <label class="form-label" for="email">Admin Email</label>
            <div style="position: relative;">
                <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--gray-400);"><i class="fas fa-envelope"></i></span>
                <input class="form-control" type="email" id="email" name="email" required style="padding-left: 42px;" placeholder="Enter Admin Email">
            </div>
        </div>
        
        <div class="form-group" style="text-align: left;">
            <label class="form-label" for="password">Password</label>
            <div style="position: relative;">
                <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--gray-400);"><i class="fas fa-lock"></i></span>
                <input class="form-control" type="password" id="password" name="password" required style="padding-left: 42px;" placeholder="••••••••">
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top: var(--space-4);">
            <i class="fas fa-sign-in-alt"></i> Login to Dashboard
        </button>
    </form>
    
    <div style="margin-top: var(--space-6); text-align: center;">
        <a href="<?= SITE_URL ?>/index.php" style="font-size: var(--text-sm); color: var(--gold);"><i class="fas fa-arrow-left"></i> Return to Main Website</a>
    </div>
</div>

</body>
</html>
