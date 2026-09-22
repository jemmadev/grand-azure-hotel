<?php
// ============================================================
//  Grand Azure Hotel — Admin Site Content Management
//  Manages the content that used to be hardcoded into the
//  static about.html / restaurant.html / services.php pages:
//    - Founder photo (About page)
//    - Leadership team (About page) — full CRUD, own table
//    - Featured images: Spa (Services page), Fine Dining &
//      Sky Bar (Restaurant page)
//  Images use the `settings` key/value table (same pattern as
//  admin/settings.php hero_image / about_image). Leadership
//  uses its own `team_members` table (same pattern as
//  admin/gallery.php) because it's a repeating list, not a
//  single value.
// ============================================================

$page_title = 'Site Content';

require_once '../includes/functions.php';
require_admin_login();

$errors = [];
$action = isset($_GET['action']) ? sanitize($_GET['action']) : 'list';
$member_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$upload_dir = __DIR__ . '/../assets/images/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

/**
 * Shared upload handler for the single-image settings below.
 * Returns the new relative path on success, or the existing path
 * unchanged if no new file was submitted / it failed validation.
 */
function handle_settings_image_upload(string $field, string $prefix, string $existing, array &$errors): string {
    global $upload_dir;
    if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
        $file_tmp  = $_FILES[$field]['tmp_name'];
        $file_name = $_FILES[$field]['name'];
        $ext       = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp']) && @getimagesize($file_tmp) !== false) {
            $new_name = $prefix . '_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
            $dest     = $upload_dir . $new_name;
            if (resize_and_save_upload($file_tmp, $dest, $ext)) {
                return 'assets/images/' . $new_name;
            }
            $errors[] = "Failed to move uploaded $prefix image.";
        } else {
            $errors[] = "Invalid $prefix image. Only real JPG, JPEG, PNG, or WEBP images are allowed.";
        }
    }
    return $existing;
}

// ---- Handle: Update Founder / Featured Images ------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_images']) && csrf_verify()) {
    $founder_path = handle_settings_image_upload('founder_image', 'founder', $_POST['existing_founder_image'] ?? '', $errors);
    $spa_path     = handle_settings_image_upload('spa_image',     'spa',     $_POST['existing_spa_image'] ?? '', $errors);
    $dining_path  = handle_settings_image_upload('dining_image',  'dining',  $_POST['existing_dining_image'] ?? '', $errors);
    $skybar_path  = handle_settings_image_upload('skybar_image',  'skybar',  $_POST['existing_skybar_image'] ?? '', $errors);
    $pool_path    = handle_settings_image_upload('pool_image',    'pool',    $_POST['existing_pool_image'] ?? '', $errors);

    if (empty($errors)) {
        $upsert = $conn->prepare(
            "INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)"
        );
        foreach ([
            'founder_image' => $founder_path,
            'spa_image'     => $spa_path,
            'dining_image'  => $dining_path,
            'skybar_image'  => $skybar_path,
            'pool_image'    => $pool_path,
        ] as $key => $value) {
            $upsert->bind_param('ss', $key, $value);
            $upsert->execute();
        }
        set_flash('success', 'Featured images updated successfully.');
        redirect('content.php');
    }
}

// ---- Handle: Add / Edit Team Member -----------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['add', 'edit']) && isset($_POST['save_member']) && csrf_verify()) {
    $name       = sanitize($_POST['name'] ?? '');
    $role       = sanitize($_POST['role'] ?? '');
    $linkedin   = sanitize($_POST['linkedin_url'] ?? '');
    $twitter    = sanitize($_POST['twitter_url'] ?? '');
    $sort_order = (int)($_POST['sort_order'] ?? 0);
    $status     = ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active';

    $image_path = $_POST['existing_image'] ?? '';
    $member_upload_dir = __DIR__ . '/../assets/images/team/';
    if (!is_dir($member_upload_dir)) {
        mkdir($member_upload_dir, 0755, true);
    }
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp  = $_FILES['image']['tmp_name'];
        $file_name = $_FILES['image']['name'];
        $ext       = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp']) && @getimagesize($file_tmp) !== false) {
            $new_name = 'team_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
            $dest     = $member_upload_dir . $new_name;
            if (resize_and_save_upload($file_tmp, $dest, $ext)) {
                $image_path = 'assets/images/team/' . $new_name;
            } else {
                $errors[] = 'Failed to move uploaded photo.';
            }
        } else {
            $errors[] = 'Invalid photo. Only real JPG, JPEG, PNG, or WEBP images are allowed.';
        }
    }

    if ($name === '' || $role === '') {
        $errors[] = 'Name and role are required.';
    }
    if (empty($image_path)) {
        $errors[] = 'A photo is required (upload one, or keep the existing photo when editing).';
    }

    if (empty($errors)) {
        if ($action === 'add') {
            $ins = $conn->prepare(
                "INSERT INTO team_members (name, role, image, linkedin_url, twitter_url, status, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            $ins->bind_param('ssssssi', $name, $role, $image_path, $linkedin, $twitter, $status, $sort_order);
            if ($ins->execute()) {
                set_flash('success', 'Team member added successfully.');
                redirect('content.php');
            } else {
                $errors[] = 'Failed to insert team member.';
            }
        } elseif ($action === 'edit' && $member_id) {
            $upd = $conn->prepare(
                "UPDATE team_members SET name=?, role=?, image=?, linkedin_url=?, twitter_url=?, status=?, sort_order=? WHERE id=?"
            );
            $upd->bind_param('ssssssii', $name, $role, $image_path, $linkedin, $twitter, $status, $sort_order, $member_id);
            if ($upd->execute()) {
                set_flash('success', 'Team member updated successfully.');
                redirect('content.php');
            } else {
                $errors[] = 'Failed to update team member.';
            }
        }
    }
}

// ---- Handle: Delete Team Member ---------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_member']) && csrf_verify()) {
    $del_id = (int)$_POST['member_id'];
    $existing = get_team_member($conn, $del_id);

    $del = $conn->prepare("DELETE FROM team_members WHERE id = ?");
    $del->bind_param('i', $del_id);
    if ($del->execute()) {
        if ($existing && !empty($existing['image']) && !str_starts_with($existing['image'], 'http')) {
            $file_path = __DIR__ . '/../' . $existing['image'];
            if (is_file($file_path)) {
                @unlink($file_path);
            }
        }
        set_flash('success', 'Team member removed.');
    } else {
        set_flash('error', 'Failed to delete team member.');
    }
    redirect('content.php');
}

// Prepare editing variables if in Edit mode
$edit_member = null;
if ($action === 'edit' && $member_id) {
    $edit_member = get_team_member($conn, $member_id);
    if (!$edit_member) {
        set_flash('error', 'Team member not found.');
        redirect('content.php');
    }
}

require_once 'includes/header.php';

$current = get_all_settings($conn);
$team_members = get_all_team_members($conn);
?>

<div style="margin-bottom: var(--space-8);">
    <h1 style="font-family: var(--font-serif); font-size: var(--text-3xl); color: var(--navy); margin-bottom: var(--space-2);">Site Content</h1>
    <p style="color: var(--gray-500);">Manage the About page founder photo &amp; leadership team, and the featured Spa / Dining / Sky Bar images.</p>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-error">
    <div><strong>Form Errors:</strong><ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div>
</div>
<?php endif; ?>

<?php if ($action === 'list'): ?>

<!-- FEATURED IMAGES -->
<div class="admin-card">
    <h2 style="font-family: var(--font-serif); font-size: var(--text-xl); color: var(--navy); margin-bottom: var(--space-2);">Featured Images</h2>
    <p style="color: var(--gray-500); font-size: var(--text-sm); margin-bottom: var(--space-6);">These appear on the About, Services (Spa), and Restaurant pages.</p>
    <form method="POST" enctype="multipart/form-data">
        <?php csrf_field(); ?>
        <input type="hidden" name="existing_founder_image" value="<?= htmlspecialchars($current['founder_image'] ?? '') ?>">
        <input type="hidden" name="existing_spa_image"     value="<?= htmlspecialchars($current['spa_image'] ?? '') ?>">
        <input type="hidden" name="existing_dining_image"  value="<?= htmlspecialchars($current['dining_image'] ?? '') ?>">
        <input type="hidden" name="existing_skybar_image"  value="<?= htmlspecialchars($current['skybar_image'] ?? '') ?>">
        <input type="hidden" name="existing_pool_image"    value="<?= htmlspecialchars($current['pool_image'] ?? '') ?>">

        <div class="grid-2" style="gap: var(--space-6);">
            <div class="form-group">
                <label class="form-label">Hotel Founder Photo <span style="color:var(--gray-400); font-weight:400;">(About page)</span></label>
                <input class="form-control" type="file" name="founder_image" accept="image/*">
                <?php if (!empty($current['founder_image'])): ?>
                    <div style="margin-top: var(--space-3);">
                        <img src="<?= htmlspecialchars(image_url($current['founder_image'])) ?>" alt="Founder" style="width: 160px; height: 110px; object-fit: cover; border-radius: var(--radius-sm);">
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label class="form-label">Grand Azure Spa <span style="color:var(--gray-400); font-weight:400;">(Services page)</span></label>
                <input class="form-control" type="file" name="spa_image" accept="image/*">
                <?php if (!empty($current['spa_image'])): ?>
                    <div style="margin-top: var(--space-3);">
                        <img src="<?= htmlspecialchars(image_url($current['spa_image'])) ?>" alt="Spa" style="width: 160px; height: 110px; object-fit: cover; border-radius: var(--radius-sm);">
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label class="form-label">Azure Fine Dining <span style="color:var(--gray-400); font-weight:400;">(Restaurant page)</span></label>
                <input class="form-control" type="file" name="dining_image" accept="image/*">
                <?php if (!empty($current['dining_image'])): ?>
                    <div style="margin-top: var(--space-3);">
                        <img src="<?= htmlspecialchars(image_url($current['dining_image'])) ?>" alt="Fine Dining" style="width: 160px; height: 110px; object-fit: cover; border-radius: var(--radius-sm);">
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label class="form-label">Azure Sky Bar <span style="color:var(--gray-400); font-weight:400;">(Restaurant page)</span></label>
                <input class="form-control" type="file" name="skybar_image" accept="image/*">
                <?php if (!empty($current['skybar_image'])): ?>
                    <div style="margin-top: var(--space-3);">
                        <img src="<?= htmlspecialchars(image_url($current['skybar_image'])) ?>" alt="Sky Bar" style="width: 160px; height: 110px; object-fit: cover; border-radius: var(--radius-sm);">
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label class="form-label">Infinity Pool <span style="color:var(--gray-400); font-weight:400;">(Services page)</span></label>
                <input class="form-control" type="file" name="pool_image" accept="image/*">
                <?php if (!empty($current['pool_image'])): ?>
                    <div style="margin-top: var(--space-3);">
                        <img src="<?= htmlspecialchars(image_url($current['pool_image'])) ?>" alt="Pool" style="width: 160px; height: 110px; object-fit: cover; border-radius: var(--radius-sm);">
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <button type="submit" name="update_images" class="btn btn-primary" style="margin-top: var(--space-6);">
            <i class="fas fa-save"></i> Save Featured Images
        </button>
    </form>
</div>

<!-- LEADERSHIP TEAM -->
<div class="admin-card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: var(--space-6);">
        <div>
            <h2 style="font-family: var(--font-serif); font-size: var(--text-xl); color: var(--navy);">Leadership Team</h2>
            <p style="color: var(--gray-500); font-size: var(--text-sm);">Shown in "Meet Our Leadership" on the About page.</p>
        </div>
        <a href="content.php?action=add" class="btn btn-primary"><i class="fas fa-plus"></i> Add Team Member</a>
    </div>

    <?php if (empty($team_members)): ?>
        <p style="color: var(--gray-400); text-align: center; padding: var(--space-8);">No team members yet. Let's add the first one.</p>
    <?php else: ?>
    <table class="admin-table">
        <thead>
            <tr><th>Photo</th><th>Name</th><th>Role</th><th>Status</th><th>Sort</th><th></th></tr>
        </thead>
        <tbody>
            <?php foreach ($team_members as $m): ?>
            <tr>
                <td><img src="<?= htmlspecialchars(image_url($m['image'])) ?>" alt="<?= htmlspecialchars($m['name']) ?>" style="width:48px;height:48px;object-fit:cover;border-radius:50%;"></td>
                <td><?= htmlspecialchars($m['name']) ?></td>
                <td><?= htmlspecialchars($m['role']) ?></td>
                <td><span class="badge <?= $m['status'] === 'active' ? 'badge-success' : 'badge-secondary' ?>"><?= ucfirst($m['status']) ?></span></td>
                <td><?= (int)$m['sort_order'] ?></td>
                <td>
                    <div style="display:flex; gap: var(--space-2);">
                        <a href="content.php?action=edit&id=<?= $m['id'] ?>" class="btn btn-outline-dark btn-sm"><i class="fas fa-edit"></i></a>
                        <form method="POST" onsubmit="return confirm('Remove this team member? This action is permanent.');">
                            <?php csrf_field(); ?>
                            <input type="hidden" name="member_id" value="<?= $m['id'] ?>">
                            <button type="submit" name="delete_member" class="btn btn-outline-gold btn-sm" style="color: var(--danger); border-color: var(--danger);"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<?php else: ?>

<!-- ADD / EDIT TEAM MEMBER -->
<div class="admin-card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: var(--space-6);">
        <h2 style="font-family: var(--font-serif); font-size: var(--text-xl); color: var(--navy);">
            <?= $action === 'edit' ? 'Edit Team Member' : 'Add Team Member' ?>
        </h2>
        <a href="content.php" class="btn btn-outline-dark"><i class="fas fa-arrow-left"></i> Back to Site Content</a>
    </div>

    <form method="POST" enctype="multipart/form-data">
        <?php csrf_field(); ?>
        <?php if ($action === 'edit'): ?>
            <input type="hidden" name="existing_image" value="<?= htmlspecialchars($edit_member['image']) ?>">
        <?php endif; ?>

        <div class="grid-2" style="gap: var(--space-5);">
            <div class="form-group">
                <label class="form-label">Name <span class="required">*</span></label>
                <input class="form-control" type="text" name="name" value="<?= htmlspecialchars($edit_member['name'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Role / Title <span class="required">*</span></label>
                <input class="form-control" type="text" name="role" value="<?= htmlspecialchars($edit_member['role'] ?? '') ?>" placeholder="e.g. General Manager" required>
            </div>
            <div class="form-group">
                <label class="form-label">LinkedIn URL</label>
                <input class="form-control" type="text" name="linkedin_url" value="<?= htmlspecialchars($edit_member['linkedin_url'] ?? '') ?>" placeholder="https://linkedin.com/in/...">
            </div>
            <div class="form-group">
                <label class="form-label">Twitter / X URL</label>
                <input class="form-control" type="text" name="twitter_url" value="<?= htmlspecialchars($edit_member['twitter_url'] ?? '') ?>" placeholder="https://x.com/...">
            </div>
            <div class="form-group">
                <label class="form-label">Sort Order</label>
                <input class="form-control" type="number" name="sort_order" value="<?= htmlspecialchars($edit_member['sort_order'] ?? 0) ?>" placeholder="0">
                <span style="font-size: var(--text-xs); color: var(--gray-400);">Lower numbers appear first.</span>
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select class="form-control" name="status">
                    <option value="active"   <?= ($edit_member['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active (shown on site)</option>
                    <option value="inactive" <?= ($edit_member['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive (hidden)</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Photo <?= $action === 'add' ? '<span class="required">*</span>' : '' ?></label>
            <input class="form-control" type="file" name="image" accept="image/*" <?= $action === 'add' ? 'required' : '' ?>>
            <?php if (!empty($edit_member['image'])): ?>
                <div style="margin-top: var(--space-3);">
                    <img src="<?= htmlspecialchars(image_url($edit_member['image'])) ?>" alt="Current photo" style="width: 120px; height: 120px; object-fit: cover; border-radius: 50%;">
                    <span style="font-size: var(--text-xs); color: var(--gray-400); display: block; margin-top: .25rem;">Leave blank to keep this current photo.</span>
                </div>
            <?php endif; ?>
        </div>

        <button type="submit" name="save_member" class="btn btn-primary">
            <i class="fas fa-save"></i> Save Team Member
        </button>
    </form>
</div>

<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
