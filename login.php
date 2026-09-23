<?php
// ============================================================
//  Grand Azure Hotel — User Login Page
// ============================================================

$page_title = 'Sign In';
$meta_desc  = 'Login to your Grand Azure Hotel account to manage bookings and access member benefits.';

require_once 'includes/functions.php';

// Already logged in
if (is_logged_in())       redirect(SITE_URL . '/user/dashboard.php');
if (is_admin_logged_in()) redirect(SITE_URL . '/admin/dashboard.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = sanitize($_POST['email']    ?? '');
    $password = $_POST['password']           ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please enter your email and password.';
    } else {
        // Check users table
        $stmt = $conn->prepare("SELECT id, name, email, password, status FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] === 'suspended') {
                $error = 'Your account has been suspended. Please contact support.';
            } else {
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email']= $user['email'];

                $redirect = $_SESSION['redirect_after_login'] ?? SITE_URL . '/user/dashboard.php';
                unset($_SESSION['redirect_after_login']);
                set_flash('success', 'Welcome back, ' . $user['name'] . '!');
                redirect($redirect);
            }
        } else {
            // Simulate small delay to slow brute force
            usleep(300000);
            $error = 'Invalid email or password. Please try again.';
        }
    }
}

require_once 'includes/header.php';
$auth_bg = image_url(setting($conn, 'hero_image', 'assets/images/hero.jpg'));
?>
<style>
.auth-wrapper{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:var(--space-20) var(--space-4);
    background-image: linear-gradient(rgba(10,22,40,.75), rgba(10,22,40,.85)), url('<?= htmlspecialchars($auth_bg) ?>');
    background-size: cover; background-position: center; background-attachment: fixed;}
.auth-card{background:var(--white);border-radius:var(--radius-lg);overflow:hidden;width:100%;max-width:900px;box-shadow:var(--shadow-lg);display:grid;grid-template-columns:1fr 1fr;}
.auth-image{position:relative;overflow:hidden;}
.auth-image img{width:100%;height:100%;object-fit:cover;}
.auth-image-overlay{position:absolute;inset:0;background:linear-gradient(135deg,rgba(10,22,40,.85),rgba(17,34,64,.7));display:flex;flex-direction:column;align-items:center;justify-content:center;padding:var(--space-10);color:var(--white);text-align:center;}
.auth-form-panel{padding:var(--space-10);}
@media(max-width:768px){.auth-card{grid-template-columns:1fr;}.auth-image{display:none;}}
</style>

<div class="auth-wrapper" style="padding-top:100px;">
    <div class="auth-card animate-scale-in">
        <!-- Image -->
        <div class="auth-image">
            <img src="<?= htmlspecialchars($auth_bg) ?>" alt="Grand Azure Hotel">
            <div class="auth-image-overlay">
                <div class="nav-logo-icon" style="width:60px;height:60px;font-size:1.5rem;margin-bottom:var(--space-5);">GA</div>
                <h2 style="font-family:var(--font-serif);font-size:var(--text-2xl);margin-bottom:var(--space-4);">Welcome Back</h2>
                <p style="color:rgba(255,255,255,.75);line-height:var(--lh-relaxed);">Sign in to manage your bookings, view your history, and enjoy exclusive member benefits at Grand Azure.</p>
                <div style="margin-top:var(--space-8); padding-top:var(--space-8); border-top:1px solid rgba(255,255,255,.15); width:100%;">
                    <p style="color:rgba(255,255,255,.5); font-size:var(--text-xs); text-transform:uppercase; letter-spacing:.1em; margin-bottom:var(--space-3);">Admin Access</p>
                    <a href="admin/login.php" class="btn btn-outline btn-sm" style="width:100%;">
                        <i class="fas fa-user-shield"></i> Administrator Login
                    </a>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="auth-form-panel">
            <h1 style="font-family:var(--font-serif);font-size:var(--text-3xl);color:var(--navy);margin-bottom:var(--space-2);">Sign In</h1>
            <p style="color:var(--gray-400);font-size:var(--text-sm);margin-bottom:var(--space-6);">Don't have an account? <a href="register.php" style="color:var(--gold);font-weight:600;">Create one free</a></p>

            <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($error) ?>
                <button class="alert-close" onclick="this.parentElement.remove()">&times;</button>
            </div>
            <?php endif; ?>

            <?php show_flash(); ?>

            <form method="POST" action="login.php" data-validate>
                <div class="form-group">
                    <label class="form-label" for="email">Email Address <span class="required">*</span></label>
                    <div style="position:relative;">
                        <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--gray-400);"><i class="fas fa-envelope"></i></span>
                        <input class="form-control" type="email" id="email" name="email" required
                               style="padding-left:42px;"
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label" for="password">Password <span class="required">*</span></label>
                    <div style="position:relative;">
                        <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--gray-400);"><i class="fas fa-lock"></i></span>
                        <input class="form-control" type="password" id="password" name="password" required style="padding-left:42px;">
                        <button type="button" onclick="togglePass('password',this)" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--gray-400);cursor:pointer;"><i class="fas fa-eye"></i></button>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-block" style="margin-top:var(--space-6);">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </button>
            </form>

            <div style="margin-top:var(--space-8); padding-top:var(--space-6); border-top:1px solid var(--gray-100); text-align:center;">
                
            </div>
        </div>
    </div>
</div>

<script>
function togglePass(id, btn) {
    const f = document.getElementById(id);
    f.type  = f.type === 'password' ? 'text' : 'password';
    btn.querySelector('i').classList.toggle('fa-eye');
    btn.querySelector('i').classList.toggle('fa-eye-slash');
}
</script>

<?php require_once 'includes/footer.php'; ?>
