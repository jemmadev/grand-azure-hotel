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
// 'logo', 'hero_image', and 'about_image' are handled separately below because they are file uploads.
$text_fields = [
    'hotel_name'         => 'Hotel Name',
    'phone'              => 'Phone Number',
    'email'              => 'Email Address',
    'address'            => 'Address',
    'footer_text'        => 'Footer Description Text',
    'copyright_text'     => 'Copyright Text (after the year and hotel name)',
    'hero_title'         => 'Homepage Hero Title',
    'hero_subtitle'      => 'Homepage Hero Subtitle',
    'social_facebook'    => 'Facebook URL',
    'social_twitter'     => 'Twitter / X URL',
    'social_instagram'   => 'Instagram URL',
    'social_youtube'     => 'YouTube URL',
    'about_title'        => 'About Title',
    'about_text_1'       => 'About Paragraph 1',
    'about_text_2'       => 'About Paragraph 2',
    'stat_rooms'         => 'Stat Rooms',
    'stat_guests'        => 'Stat Guests',
    'stat_years'         => 'Stat Years',
    'stat_awards'        => 'Stat Awards',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_verify()) {
    $logo_path = $_POST['existing_logo'] ?? '';
    $hero_image_path = $_POST['existing_hero_image'] ?? '';
    $about_image_path = $_POST['existing_about_image'] ?? '';

    $upload_dir = __DIR__ . '/../assets/images/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Logo upload
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $file_tmp  = $_FILES['logo']['tmp_name'];
        $file_name = $_FILES['logo']['name'];
        $ext       = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'svg']) &&
            ($ext === 'svg' || @getimagesize($file_tmp) !== false)) {
            $new_name = 'logo_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
            $dest     = $upload_dir . $new_name;
            if (resize_and_save_upload($file_tmp, $dest, $ext)) {
                $logo_path = 'assets/images/' . $new_name;
            } else {
                $errors[] = 'Failed to move uploaded logo.';
            }
        } else {
            $errors[] = 'Invalid logo file. Only real JPG, PNG, WEBP, or SVG images are allowed.';
        }
    }

    // Hero Background image upload
    if (isset($_FILES['hero_image']) && $_FILES['hero_image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp  = $_FILES['hero_image']['tmp_name'];
        $file_name = $_FILES['hero_image']['name'];
        $ext       = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp']) && @getimagesize($file_tmp) !== false) {
            $new_name = 'hero_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
            $dest     = $upload_dir . $new_name;
            if (resize_and_save_upload($file_tmp, $dest, $ext)) {
                $hero_image_path = 'assets/images/' . $new_name;
            } else {
                $errors[] = 'Failed to move uploaded hero background image.';
            }
        } else {
            $errors[] = 'Invalid hero background image. Only JPG, JPEG, PNG, or WEBP are allowed.';
        }
    }

    // About image upload
    if (isset($_FILES['about_image']) && $_FILES['about_image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp  = $_FILES['about_image']['tmp_name'];
        $file_name = $_FILES['about_image']['name'];
        $ext       = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp']) && @getimagesize($file_tmp) !== false) {
            $new_name = 'about_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
            $dest     = $upload_dir . $new_name;
            if (resize_and_save_upload($file_tmp, $dest, $ext)) {
                $about_image_path = 'assets/images/' . $new_name;
            } else {
                $errors[] = 'Failed to move uploaded about image.';
            }
        } else {
            $errors[] = 'Invalid about image. Only JPG, JPEG, PNG, or WEBP are allowed.';
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

        $heroImageKey = 'hero_image';
        $upsert->bind_param('ss', $heroImageKey, $hero_image_path);
        $upsert->execute();

        $aboutImageKey = 'about_image';
        $upsert->bind_param('ss', $aboutImageKey, $about_image_path);
        $upsert->execute();

        set_flash('success', 'Settings updated successfully.');
        redirect('settings.php');
    }
}

// Load current values
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
    <p style="color: var(--gray-500);">Modify all key brand information, homepage elements, text sections, images, and numbers displayed on the website.</p>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-error">
    <div><strong>Form Errors:</strong><ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div>
</div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <?php csrf_field(); ?>
    <input type="hidden" name="existing_logo" value="<?= htmlspecialchars($current['logo'] ?? '') ?>">
    <input type="hidden" name="existing_hero_image" value="<?= htmlspecialchars($current['hero_image'] ?? '') ?>">
    <input type="hidden" name="existing_about_image" value="<?= htmlspecialchars($current['about_image'] ?? '') ?>">

    <div class="admin-card">
        <h2 style="font-family: var(--font-serif); font-size: var(--text-xl); color: var(--navy); margin-bottom: var(--space-6);">Brand &amp; Logo</h2>
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
                <label class="form-label"><i class="fab fa-x-twitter"></i> Twitter / X URL</label>
                <input class="form-control" type="text" name="social_twitter" value="<?= htmlspecialchars($current['social_twitter'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label"><i class="fab fa-instagram"></i> Instagram URL</label>
                <input class="form-control" type="text" name="social_instagram" value="<?= htmlspecialchars($current['social_instagram'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label"><i class="fab fa-youtube"></i> YouTube URL</label>
                <input class="form-control" type="text" name="social_youtube" value="<?= htmlspecialchars($current['social_youtube'] ?? '') ?>">
            </div>
        </div>
    </div>

    <div class="admin-card">
        <h2 style="font-family: var(--font-serif); font-size: var(--text-xl); color: var(--navy); margin-bottom: var(--space-6);">Homepage Hero Section</h2>
        <div class="form-group">
            <label class="form-label">Hero Title</label>
            <input class="form-control" type="text" name="hero_title" value="<?= htmlspecialchars($current['hero_title'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label class="form-label">Hero Subtitle</label>
            <textarea class="form-control" name="hero_subtitle" rows="3"><?= htmlspecialchars($current['hero_subtitle'] ?? '') ?></textarea>
        </div>
        <div class="form-group">
            <label class="form-label">Hero Background Image</label>
            <input class="form-control" type="file" name="hero_image" accept="image/*">
            <?php if (!empty($current['hero_image'])): ?>
                <div style="margin-top: var(--space-3);">
                    <img src="<?= htmlspecialchars(image_url($current['hero_image'])) ?>" alt="Hero image" style="max-height: 120px; border-radius: var(--radius-sm);">
                    <span style="font-size: var(--text-xs); color: var(--gray-400); display: block;">Leave blank to keep the current background image.</span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="admin-card">
        <h2 style="font-family: var(--font-serif); font-size: var(--text-xl); color: var(--navy); margin-bottom: var(--space-6);">Homepage About Section</h2>
        <div class="form-group">
            <label class="form-label">About Section Title</label>
            <input class="form-control" type="text" name="about_title" value="<?= htmlspecialchars($current['about_title'] ?? 'A Legacy of Luxury') ?>">
        </div>
        <div class="form-group">
            <label class="form-label">About Paragraph 1</label>
            <textarea class="form-control" name="about_text_1" rows="3"><?= htmlspecialchars($current['about_text_1'] ?? 'Since 1999, Grand Azure Hotel has stood as a beacon of excellence in luxury hospitality. Nestled in the heart of the city, we have welcomed dignitaries, celebrities, and discerning travelers who seek nothing but the very best.') ?></textarea>
        </div>
        <div class="form-group">
            <label class="form-label">About Paragraph 2</label>
            <textarea class="form-control" name="about_text_2" rows="3"><?= htmlspecialchars($current['about_text_2'] ?? 'Our commitment to personalized service, culinary artistry, and unparalleled comfort has earned us 12 prestigious industry awards and the unwavering loyalty of guests from across the globe.') ?></textarea>
        </div>
        <div class="form-group">
            <label class="form-label">About Section Image</label>
            <input class="form-control" type="file" name="about_image" accept="image/*">
            <?php if (!empty($current['about_image'])): ?>
                <div style="margin-top: var(--space-3);">
                    <img src="<?= htmlspecialchars(image_url($current['about_image'])) ?>" alt="About image" style="max-height: 120px; border-radius: var(--radius-sm);">
                    <span style="font-size: var(--text-xs); color: var(--gray-400); display: block;">Leave blank to keep the current section image.</span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="admin-card">
        <h2 style="font-family: var(--font-serif); font-size: var(--text-xl); color: var(--navy); margin-bottom: var(--space-6);">Homepage Counters &amp; Statistics</h2>
        <div class="grid-4" style="gap: var(--space-4);">
            <div class="form-group">
                <label class="form-label">Luxury Rooms Count</label>
                <input class="form-control" type="text" name="stat_rooms" value="<?= htmlspecialchars($current['stat_rooms'] ?? '250') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Happy Guests Count</label>
                <input class="form-control" type="text" name="stat_guests" value="<?= htmlspecialchars($current['stat_guests'] ?? '15000') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Years of Excellence</label>
                <input class="form-control" type="text" name="stat_years" value="<?= htmlspecialchars($current['stat_years'] ?? '25') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Award Wins</label>
                <input class="form-control" type="text" name="stat_awards" value="<?= htmlspecialchars($current['stat_awards'] ?? '12') ?>">
            </div>
        </div>
    </div>

    <div class="admin-card">
        <h2 style="font-family: var(--font-serif); font-size: var(--text-xl); color: var(--navy); margin-bottom: var(--space-6);">Footer &amp; Copyright</h2>
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
