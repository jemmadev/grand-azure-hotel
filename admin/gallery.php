<?php
// ============================================================
//  Grand Azure Hotel — Admin Gallery Management
// ============================================================

$page_title = 'Manage Gallery';

// Load functions and check admin login WITHOUT printing any HTML yet,
// so redirect() calls below still work (a redirect must happen before
// any HTML has been sent to the browser).
require_once '../includes/functions.php';
require_admin_login();

$errors  = [];
$action  = isset($_GET['action']) ? sanitize($_GET['action']) : 'list';
$item_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$categories = ['rooms', 'restaurant', 'pool', 'lobby', 'exterior', 'events', 'spa'];

// Handle Add / Edit form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['add', 'edit']) && csrf_verify()) {
    $caption    = sanitize($_POST['caption'] ?? '');
    $category   = in_array($_POST['category'] ?? '', $categories) ? $_POST['category'] : 'lobby';
    $sort_order = (int)($_POST['sort_order'] ?? 0);

    // File upload logic (same validation approach as admin/rooms.php:
    // check real extension AND real image content via getimagesize()).
    $image_path = $_POST['existing_image'] ?? '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp  = $_FILES['image']['tmp_name'];
        $file_name = $_FILES['image']['name'];
        $ext       = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp']) && @getimagesize($file_tmp) !== false) {
            $upload_dir = __DIR__ . '/../assets/images/gallery/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $new_name = 'gallery_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
            $dest     = $upload_dir . $new_name;
            if (move_uploaded_file($file_tmp, $dest)) {
                $image_path = 'assets/images/gallery/' . $new_name;
            } else {
                $errors[] = 'Failed to move uploaded image.';
            }
        } else {
            $errors[] = 'Invalid file. Only real JPG, JPEG, PNG, and WEBP images are allowed.';
        }
    }

    if (empty($image_path)) {
        $errors[] = 'An image is required (upload one, or keep the existing image when editing).';
    }

    if (empty($errors)) {
        if ($action === 'add') {
            $ins = $conn->prepare(
                "INSERT INTO gallery (image, caption, category, sort_order) VALUES (?, ?, ?, ?)"
            );
            $ins->bind_param('sssi', $image_path, $caption, $category, $sort_order);
            if ($ins->execute()) {
                set_flash('success', 'Gallery image added successfully.');
                redirect('gallery.php');
            } else {
                $errors[] = 'Failed to insert gallery record.';
            }
        } elseif ($action === 'edit' && $item_id) {
            $upd = $conn->prepare(
                "UPDATE gallery SET image = ?, caption = ?, category = ?, sort_order = ? WHERE id = ?"
            );
            $upd->bind_param('sssii', $image_path, $caption, $category, $sort_order, $item_id);
            if ($upd->execute()) {
                set_flash('success', 'Gallery image updated successfully.');
                redirect('gallery.php');
            } else {
                $errors[] = 'Failed to update gallery record.';
            }
        }
    }
}

// Handle Delete action — POST-only with CSRF, same reasoning as
// admin/rooms.php: a GET-based delete can be triggered by just
// visiting a URL, which is not a real confirmation.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_item']) && csrf_verify()) {
    $del_id = (int)$_POST['item_id'];

    // Look up the file path first so we can remove the physical file
    // too, not just the database row (avoids orphaned uploads piling
    // up in assets/images/gallery/ over time).
    $existing = get_gallery_item($conn, $del_id);

    $del = $conn->prepare("DELETE FROM gallery WHERE id = ?");
    $del->bind_param('i', $del_id);
    if ($del->execute()) {
        if ($existing && !empty($existing['image']) && !str_starts_with($existing['image'], 'http')) {
            $file_path = __DIR__ . '/../' . $existing['image'];
            if (is_file($file_path)) {
                @unlink($file_path);
            }
        }
        set_flash('success', 'Gallery image has been deleted.');
    } else {
        set_flash('error', 'Failed to delete gallery record.');
    }
    redirect('gallery.php');
}

// Prepare editing variables if in Edit mode
$edit_item = null;
if ($action === 'edit' && $item_id) {
    $edit_item = get_gallery_item($conn, $item_id);
    if (!$edit_item) {
        set_flash('error', 'Gallery image not found.');
        redirect('gallery.php');
    }
}

// Now it's safe to print the page (no more redirects after this point)
require_once 'includes/header.php';

// Get all gallery items for listing
$gallery_items = get_all_gallery($conn);
?>

<div style="margin-bottom: var(--space-8); display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 style="font-family: var(--font-serif); font-size: var(--text-3xl); color: var(--navy); margin-bottom: var(--space-2);">Manage Gallery</h1>
        <p style="color: var(--gray-500);">Upload, edit, and organize the photos shown on the public Gallery page.</p>
    </div>
    <?php if ($action === 'list'): ?>
        <a href="gallery.php?action=add" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Photo</a>
    <?php else: ?>
        <a href="gallery.php" class="btn btn-outline-dark"><i class="fas fa-arrow-left"></i> Back to Gallery List</a>
    <?php endif; ?>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-error">
    <div><strong>Form Errors:</strong><ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div>
</div>
<?php endif; ?>

<!-- ADD / EDIT GALLERY ITEM FORM -->
<?php if (in_array($action, ['add', 'edit'])): ?>
<div class="admin-card">
    <h2 style="font-family: var(--font-serif); font-size: var(--text-xl); color: var(--navy); margin-bottom: var(--space-6);">
        <?= $action === 'edit' ? 'Edit Gallery Photo' : 'Upload New Gallery Photo' ?>
    </h2>
    <form method="POST" enctype="multipart/form-data">
        <?php csrf_field(); ?>
        <?php if ($action === 'edit'): ?>
            <input type="hidden" name="existing_image" value="<?= htmlspecialchars($edit_item['image']) ?>">
        <?php endif; ?>

        <div class="grid-2" style="gap: var(--space-5);">
            <div class="form-group">
                <label class="form-label">Category <span class="required">*</span></label>
                <select class="form-control" name="category">
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat ?>" <?= isset($edit_item['category']) && $edit_item['category'] === $cat ? 'selected' : '' ?>><?= ucfirst($cat) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Sort Order</label>
                <input class="form-control" type="number" name="sort_order" value="<?= htmlspecialchars($edit_item['sort_order'] ?? 0) ?>" placeholder="0">
                <span style="font-size: var(--text-xs); color: var(--gray-400);">Lower numbers appear first on the Gallery page.</span>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Caption</label>
            <input class="form-control" type="text" name="caption" value="<?= htmlspecialchars($edit_item['caption'] ?? '') ?>" placeholder="e.g. Rooftop Infinity Pool at Sunset">
        </div>

        <div class="form-group">
            <label class="form-label">Photo <?= $action === 'add' ? '<span class="required">*</span>' : '' ?></label>
            <input class="form-control" type="file" name="image" accept="image/*" <?= $action === 'add' ? 'required' : '' ?>>
            <?php if (!empty($edit_item['image'])): ?>
                <div style="margin-top: var(--space-3);">
                    <img src="<?= htmlspecialchars(image_url($edit_item['image'])) ?>" alt="Current photo" style="width: 160px; height: 100px; object-fit: cover; border-radius: var(--radius-sm);">
                    <span style="font-size: var(--text-xs); color: var(--gray-400); display: block; margin-top: .25rem;">Leave blank to keep this current image.</span>
                </div>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Save Photo
        </button>
    </form>
</div>

<!-- GALLERY GRID VIEW -->
<?php else: ?>
<div class="admin-card">
    <?php if (empty($gallery_items)): ?>
        <p style="color: var(--gray-400); text-align: center; padding: var(--space-8);">No gallery photos yet. Let's upload the first one.</p>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: var(--space-5);">
            <?php foreach ($gallery_items as $g): ?>
            <div style="border: 1px solid var(--gray-100); border-radius: var(--radius-md); overflow: hidden;">
                <img src="<?= htmlspecialchars(image_url($g['image'])) ?>" alt="<?= htmlspecialchars($g['caption'] ?? '') ?>" style="width: 100%; height: 150px; object-fit: cover; display: block;">
                <div style="padding: var(--space-4);">
                    <span class="badge badge-secondary" style="margin-bottom: var(--space-2); display: inline-block;"><?= ucfirst($g['category']) ?></span>
                    <p style="font-size: var(--text-sm); color: var(--gray-700); margin-bottom: var(--space-3); min-height: 1.5em;"><?= htmlspecialchars($g['caption'] ?: '—') ?></p>
                    <div style="display: flex; gap: var(--space-2);">
                        <a href="gallery.php?action=edit&id=<?= $g['id'] ?>" class="btn btn-outline-dark btn-sm"><i class="fas fa-edit"></i> Edit</a>
                        <form method="POST" onsubmit="return confirm('Delete this gallery photo? This action is permanent.');">
                            <?php csrf_field(); ?>
                            <input type="hidden" name="item_id" value="<?= $g['id'] ?>">
                            <button type="submit" name="delete_item" class="btn btn-outline-gold btn-sm" style="color: var(--danger); border-color: var(--danger);"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
