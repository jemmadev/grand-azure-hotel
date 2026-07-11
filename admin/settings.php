<?php
// ============================================================
//  Grand Azure Hotel — Admin Settings
//  Edits the key/value rows in the `settings` table that the
//  public header/footer/homepage read from (see includes/functions.php
//  get_all_settings() / setting()).
// ============================================================

$page_title = 'Website Settings';

require_once '../includes/functions.php';
require_admin_login();

$errors = [];

// Every field this page manages. Text inputs are upserted directly;
// 'logo' is handled separately below because it's a file upload.
$text_fields = [
    'hotel_name'    => 'Hotel Name',
    'phone'         => 'Phone Number',
    'email'         => 'Email Address',
    'address'       => 'Address',
    'footer_text'   => 'Footer Description Text',
    'copyright_text'=> 'Copyright Text (after the year and hotel name)',
    'hero_title'    => 'Homepage Hero Title',
    'hero_subtitle' => 'Homepage Hero Subtitle',
    'social_facebook'   => 'Facebook URL',
    'social_instagram'  => 'Instagram URL',
    'social_twitter'    => 'Twitter / X URL',
    'social_tripadvisor'=> 'TripAdvisor URL',
    'social_youtube'    => 'YouTube URL',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_verify()) {
    $logo_path = $_POST['existing_logo'] ?? '';

    // Logo upload — same real-image validation pattern used for
    // room and gallery images (extension + getimagesize() check).
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $file_tmp  = $_FILES['logo']['tmp_name'];
        $file_name = $_FILES['logo']['name'];
        $ext       = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'svg']) &&
            ($ext === 'svg' || @getimagesize($file_tmp) !== false)) {
            $upload_dir = __DIR__ . '/../assets/images/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $new_name = 'logo_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
            $dest     = $upload_dir . $new_name;
            if (move_uploaded_file($file_tmp, $dest)) {
                $logo_path = 'assets/images/' . $new_name;
            } else {
                $errors[] = 'Failed to move uploaded logo.';
            }
        } else {
            $errors[] = 'Invalid logo file. Only real JPG, PNG, WEBP, or SVG images are allowed.';
        }
    }

    if (empty($errors)) {
        $upsert = $conn->prepare(
            "INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)"
        );

        foreach ($text_fields as $key => $label) {
            $value = sanitize($_POST[$key] ?? '');
            $upsert->bind_param('ss', $key, $value);
            $upsert->execute();
        }

        $logoKey = 'logo';
        $upsert->bind_param('ss', $logoKey, $logo_path);
        $upsert->execute();

        set_flash('success', 'Settings updated successfully.');
        redirect('settings.php');
    }
}

// Load current values (fresh, bypassing any per-request cache from
// get_all_settings(), since we may have just written new values above)
$current = [];
$res = $conn->query("SELECT setting_key, setting_value FROM settings");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $current[$row['setting_key']] = $row['setting_value'];
    }
}

require_once 'includes/header.php';
?>

<div style="margin-bottom: var(--space-8);">
    <h1 style="font-family: var(--font-serif); font-size: var(--text-3xl); color: var(--navy); margin-bottom: var(--space-2);">Website Settings</h1>
    <p style="color: var(--gray-500);">Controls the hotel name, logo, contact details, social links, and homepage hero text shown across the public site.</p>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-error">
    <div><strong>Form Errors:</strong><ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div>
</div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <?php csrf_field(); ?>
    <input type="hidden" name="existing_logo" value="<?= htmlspecialchars($current['logo'] ?? '') ?>">

    <div class="admin-card">
        <h2 style="font-family: var(--font-serif); font-size: var(--text-xl); color: var(--navy); margin-bottom: var(--space-6);">Brand</h2>
        <div class="grid-2" style="gap: var(--space-5);">
            <div class="form-group">
                <label class="form-label">Hotel Name</label>
                <input class="form-control" type="text" name="hotel_name" value="<?= htmlspecialchars($current['hotel_name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Logo</label>
                <input class="form-control" type="file" name="logo" accept="image/*,.svg">
                <?php if (!empty($current['logo'])): ?>
                    <div style="margin-top: var(--space-3);">
                        <img src="<?= htmlspecialchars(image_url($current['logo'])) ?>" alt="Current logo" style="height: 44px;">
                        <span style="font-size: var(--text-xs); color: var(--gray-400); display: block;">Leave blank to keep the current logo. If no logo is uploaded, the site falls back to the "GA" monogram.</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <h2 style="font-family: var(--font-serif); font-size: var(--text-xl); color: var(--navy); margin-bottom: var(--space-6);">Contact Info</h2>
        <div class="grid-2" style="gap: var(--space-5);">
            <div class="form-group">
                <label class="form-label">Phone Number</label>
                <input class="form-control" type="text" name="phone" value="<?= htmlspecialchars($current['phone'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input class="form-control" type="email" name="email" value="<?= htmlspecialchars($current['email'] ?? '') ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Address</label>
            <input class="form-control" type="text" name="address" value="<?= htmlspecialchars($current['address'] ?? '') ?>">
        </div>
    </div>

    <div class="admin-card">
        <h2 style="font-family: var(--font-serif); font-size: var(--text-xl); color: var(--navy); margin-bottom: var(--space-6);">Social Media Links</h2>
        <div class="grid-2" style="gap: var(--space-5);">
            <div class="form-group">
                <label class="form-label"><i class="fab fa-facebook-f"></i> Facebook URL</label>
                <input class="form-control" type="text" name="social_facebook" value="<?= htmlspecialchars($current['social_facebook'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label"><i class="fab fa-instagram"></i> Instagram URL</label>
                <input class="form-control" type="text" name="social_instagram" value="<?= htmlspecialchars($current['social_instagram'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label"><i class="fab fa-x-twitter"></i> Twitter / X URL</label>
                <input class="form-control" type="text" name="social_twitter" value="<?= htmlspecialchars($current['social_twitter'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label"><i class="fab fa-tripadvisor"></i> TripAdvisor URL</label>
                <input class="form-control" type="text" name="social_tripadvisor" value="<?= htmlspecialchars($current['social_tripadvisor'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label"><i class="fab fa-youtube"></i> YouTube URL</label>
                <input class="form-control" type="text" name="social_youtube" value="<?= htmlspecialchars($current['social_youtube'] ?? '') ?>">
            </div>
        </div>
    </div>

    <div class="admin-card">
        <h2 style="font-family: var(--font-serif); font-size: var(--text-xl); color: var(--navy); margin-bottom: var(--space-6);">Homepage Hero</h2>
        <div class="form-group">
            <label class="form-label">Hero Title</label>
            <input class="form-control" type="text" name="hero_title" value="<?= htmlspecialchars($current['hero_title'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label class="form-label">Hero Subtitle</label>
            <textarea class="form-control" name="hero_subtitle" rows="3"><?= htmlspecialchars($current['hero_subtitle'] ?? '') ?></textarea>
        </div>
    </div>

    <div class="admin-card">
        <h2 style="font-family: var(--font-serif); font-size: var(--text-xl); color: var(--navy); margin-bottom: var(--space-6);">Footer</h2>
        <div class="form-group">
            <label class="form-label">Footer Description Text</label>
            <textarea class="form-control" name="footer_text" rows="3"><?= htmlspecialchars($current['footer_text'] ?? '') ?></textarea>
        </div>
        <div class="form-group">
            <label class="form-label">Copyright Text</label>
            <input class="form-control" type="text" name="copyright_text" value="<?= htmlspecialchars($current['copyright_text'] ?? '') ?>">
            <span style="font-size: var(--text-xs); color: var(--gray-400);">Shown as: &copy; <?= date('Y') ?> [Hotel Name]. [this text]</span>
        </div>
    </div>

    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Settings</button>
</form>

<?php require_once 'includes/footer.php'; ?>
