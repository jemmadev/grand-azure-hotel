<?php
// ============================================================
//  Grand Azure Hotel — User Registration Page
// ============================================================

$page_title = 'Create Account';
$meta_desc  = 'Register for a Grand Azure Hotel account to book rooms, manage reservations, and enjoy exclusive member benefits.';

require_once 'includes/functions.php';

// Already logged in → redirect
if (is_logged_in()) redirect(SITE_URL . '/user/dashboard.php');

$errors  = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = sanitize($_POST['name']             ?? '');
    $email    = sanitize($_POST['email']            ?? '');
    $phone    = sanitize($_POST['phone']            ?? '');
    $password = $_POST['password']                  ?? '';
    $confirm  = $_POST['confirm_password']          ?? '';

    // Validate
    if (empty($name))                                        $errors[] = 'Full name is required.';
    if (empty($email))                                       $errors[] = 'Email address is required.';
    elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';
    if (strlen($password) < 8)                               $errors[] = 'Password must be at least 8 characters.';
    if ($password !== $confirm)                              $errors[] = 'Passwords do not match.';

    if (empty($errors)) {
        // Check if email exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors[] = 'An account with this email already exists.';
        } else {
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            $ins    = $conn->prepare("INSERT INTO users (name, email, password, phone) VALUES (?, ?, ?, ?)");
            $ins->bind_param('ssss', $name, $email, $hashed, $phone);

            if ($ins->execute()) {
                $new_id = $conn->insert_id;
                $_SESSION['user_id']   = $new_id;
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email']= $email;
                set_flash('success', 'Welcome, ' . $name . '! Your account has been created successfully.');
                redirect(SITE_URL . '/user/dashboard.php');
            } else {
                $errors[] = 'Registration failed. Please try again.';
            }
        }
    }
}

require_once 'includes/header.php';
$auth_bg = image_url(setting($conn, 'hero_image', 'assets/images/hero.jpg'));
?>
<style>
.auth-wrapper { min-height:100vh; display:flex; align-items:center; justify-content:center; padding:var(--space-20) var(--space-4);
    background-image: linear-gradient(rgba(10,22,40,.75), rgba(10,22,40,.85)), url('<?= htmlspecialchars($auth_bg) ?>');
    background-size: cover; background-position: center; background-attachment: fixed; }
.auth-card { background:var(--white); border-radius:var(--radius-lg); overflow:hidden; width:100%; max-width:960px; box-shadow:var(--shadow-lg); display:grid; grid-template-columns:1fr 1fr; }
.auth-image { position:relative; overflow:hidden; }
.auth-image img { width:100%; height:100%; object-fit:cover; }
.auth-image-overlay { position:absolute; inset:0; background:linear-gradient(135deg,rgba(10,22,40,.85),rgba(17,34,64,.7)); display:flex; flex-direction:column; align-items:center; justify-content:center; padding:var(--space-10); color:var(--white); text-align:center; }
.auth-form-panel { padding:var(--space-10); }
@media(max-width:768px){ .auth-card { grid-template-columns:1fr; } .auth-image { display:none; } }
</style>

<div class="auth-wrapper" style="padding-top:100px;">
    <div class="auth-card animate-scale-in">
        <!-- Image panel -->
        <div class="auth-image">
            <img src="<?= htmlspecialchars($auth_bg) ?>" alt="Grand Azure Hotel">
            <div class="auth-image-overlay">
                <div class="nav-logo-icon" style="width:60px;height:60px;font-size:1.5rem;margin-bottom:var(--space-5);">GA</div>
                <h2 style="font-family:var(--font-serif);font-size:var(--text-2xl);margin-bottom:var(--space-4);">Join Grand Azure</h2>
                <p style="color:rgba(255,255,255,.75);line-height:var(--lh-relaxed);margin-bottom:var(--space-6);">Create your account for exclusive member rates, priority booking, and personalised service.</p>
                <?php foreach(['Exclusive member discounts','Priority room selection','Booking history & management','24/7 concierge support'] as $b): ?>
                <div style="display:flex;align-items:center;gap:var(--space-3);margin-bottom:var(--space-3);text-align:left;width:100%;color:rgba(255,255,255,.85);font-size:var(--text-sm);">
                    <i class="fas fa-check-circle" style="color:var(--gold);flex-shrink:0;"></i><?= $b ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Form panel -->
        <div class="auth-form-panel">
            <h1 style="font-family:var(--font-serif);font-size:var(--text-3xl);color:var(--navy);margin-bottom:var(--space-2);">Create Account</h1>
            <p style="color:var(--gray-400);font-size:var(--text-sm);margin-bottom:var(--space-6);">Already have an account? <a href="login.php" style="color:var(--gold);font-weight:600;">Sign in</a></p>

            <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <div><strong>Please fix the following:</strong><ul style="margin:.5rem 0 0 1.2rem;"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div>
                <button class="alert-close" onclick="this.parentElement.remove()">&times;</button>
            </div>
            <?php endif; ?>

            <form method="POST" action="register.php" data-validate id="registerForm">
                <div class="form-group">
                    <label class="form-label" for="name">Full Name <span class="required">*</span></label>
                    <input class="form-control" type="text" id="name" name="name" required
                           value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label" for="email">Email Address <span class="required">*</span></label>
                    <input class="form-control" type="email" id="email" name="email" required
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label" for="phone">Phone Number</label>
                    <input class="form-control" type="tel" id="phone" name="phone" 
                           value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label" for="password">Password <span class="required">*</span></label>
                    <div style="position:relative;">
                        <input class="form-control" type="password" id="password" name="password" required minlength="8">
                        <button type="button" onclick="togglePass('password',this)" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--gray-400);cursor:pointer;"><i class="fas fa-eye"></i></button>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label" for="confirm_password">Confirm Password <span class="required">*</span></label>
                    <div style="position:relative;">
                        <input class="form-control" type="password" id="confirm_password" name="confirm_password" required>
                        <button type="button" onclick="togglePass('confirm_password',this)" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--gray-400);cursor:pointer;"><i class="fas fa-eye"></i></button>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-block" style="margin-top:var(--space-4);">
                    <i class="fas fa-user-plus"></i> Create My Account
                </button>
            </form>

            <p style="text-align:center; font-size:var(--text-xs); color:var(--gray-400);">
                By registering, you agree to our <a href="terms.php" style="color:var(--gold);">Terms of Service</a> and <a href="privacy.php" style="color:var(--gold);">Privacy Policy</a>.
            </p>
        </div>
    </div>
</div>

<script>
function togglePass(id, btn) {
    const field = document.getElementById(id);
    field.type  = field.type === 'password' ? 'text' : 'password';
    btn.querySelector('i').classList.toggle('fa-eye');
    btn.querySelector('i').classList.toggle('fa-eye-slash');
}
</script>

<?php require_once 'includes/footer.php'; ?>
