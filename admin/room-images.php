<?php
// ============================================================
//  Grand Azure Hotel — Admin: Manage Photos for One Room
//  Lets the admin upload extra photos per room (bathroom, toilet,
//  alternate angle, etc.) shown as a thumbnail gallery on the
//  public room-detail.php page. The room's main "showcase" image
//  is still set from admin/rooms.php — this page only manages the
//  additional photos.
// ============================================================

$page_title = 'Room Photos';

require_once '../includes/functions.php';
require_admin_login();

$room_id = isset($_GET['room_id']) ? (int)$_GET['room_id'] : 0;
$room    = $room_id ? get_room_by_id($conn, $room_id) : null;

if (!$room) {
    set_flash('error', 'Room not found.');
    redirect('rooms.php');
}

$errors = [];
$upload_dir = __DIR__ . '/../assets/images/rooms/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// ---- Handle: Add Photo -------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_photo']) && csrf_verify()) {
    $caption    = sanitize($_POST['caption'] ?? '');
    $sort_order = (int)($_POST['sort_order'] ?? 0);
    $image_path = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp  = $_FILES['image']['tmp_name'];
        $file_name = $_FILES['image']['name'];
        $ext       = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp']) && @getimagesize($file_tmp) !== false) {
            $new_name = 'roomimg_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
            $dest     = $upload_dir . $new_name;
            if (resize_and_save_upload($file_tmp, $dest, $ext)) {
                $image_path = 'assets/images/rooms/' . $new_name;
            } else {
                $errors[] = 'Failed to move uploaded photo.';
            }
        } else {
            $errors[] = 'Invalid file. Only real JPG, JPEG, PNG, or WEBP images are allowed.';
        }
    } else {
        $errors[] = 'Please choose a photo to upload.';
    }

    if (empty($errors)) {
        $ins = $conn->prepare("INSERT INTO room_images (room_id, image, caption, sort_order) VALUES (?, ?, ?, ?)");
        $ins->bind_param('issi', $room_id, $image_path, $caption, $sort_order);
        if ($ins->execute()) {
            set_flash('success', 'Photo added.');
            redirect('room-images.php?room_id=' . $room_id);
        } else {
            $errors[] = 'Failed to save photo record.';
        }
    }
}

// ---- Handle: Delete Photo -----------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_photo']) && csrf_verify()) {
    $img_id = (int)$_POST['image_id'];
    $existing = get_room_image($conn, $img_id);

    $del = $conn->prepare("DELETE FROM room_images WHERE id = ? AND room_id = ?");
    $del->bind_param('ii', $img_id, $room_id);
    if ($del->execute()) {
        if ($existing && !empty($existing['image']) && !str_starts_with($existing['image'], 'http')) {
            $file_path = __DIR__ . '/../' . $existing['image'];
            if (is_file($file_path)) {
                @unlink($file_path);
            }
        }
        set_flash('success', 'Photo removed.');
    } else {
        set_flash('error', 'Failed to remove photo.');
    }
    redirect('room-images.php?room_id=' . $room_id);
}

require_once 'includes/header.php';
$photos = get_room_images($conn, $room_id);
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: var(--space-8);">
    <div>
        <h1 style="font-family: var(--font-serif); font-size: var(--text-3xl); color: var(--navy); margin-bottom: var(--space-2);">
            Photos — <?= htmlspecialchars($room['name']) ?>
        </h1>
        <p style="color: var(--gray-500);">Extra photos shown in the gallery strip on this room's detail page (bathroom, toilet, alternate angles, etc). The main showcase photo is set from the room's Edit form.</p>
    </div>
    <a href="rooms.php" class="btn btn-outline-dark"><i class="fas fa-arrow-left"></i> Back to Rooms</a>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-error">
    <div><strong>Form Errors:</strong><ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div>
</div>
<?php endif; ?>

<!-- ADD PHOTO -->
<div class="admin-card">
    <h2 style="font-family: var(--font-serif); font-size: var(--text-xl); color: var(--navy); margin-bottom: var(--space-5);">Add a Photo</h2>
    <form method="POST" enctype="multipart/form-data">
        <?php csrf_field(); ?>
        <div class="grid-3" style="gap: var(--space-4); align-items:end;">
            <div class="form-group" style="margin:0;">
                <label class="form-label">Photo <span class="required">*</span></label>
                <input class="form-control" type="file" name="image" accept="image/*" required>
            </div>
            <div class="form-group" style="margin:0;">
                <label class="form-label">Caption</label>
                <input class="form-control" type="text" name="caption" placeholder="e.g. Bathroom, Toilet, City view angle">
            </div>
            <div class="form-group" style="margin:0;">
                <label class="form-label">Sort Order</label>
                <input class="form-control" type="number" name="sort_order" value="0">
            </div>
        </div>
        <button type="submit" name="add_photo" class="btn btn-primary" style="margin-top: var(--space-5);">
            <i class="fas fa-plus"></i> Add Photo
        </button>
    </form>
</div>

<!-- PHOTO LIST -->
<div class="admin-card">
    <h2 style="font-family: var(--font-serif); font-size: var(--text-xl); color: var(--navy); margin-bottom: var(--space-5);">Current Photos (<?= count($photos) ?>)</h2>

    <?php if (empty($photos)): ?>
        <p style="color: var(--gray-400); text-align: center; padding: var(--space-8);">
            No extra photos yet — this room's detail page will show only the main showcase image until you add some here.
        </p>
    <?php else: ?>
    <div class="grid-4" style="gap: var(--space-4);">
        <?php foreach ($photos as $p): ?>
        <div style="border:1px solid var(--gray-100); border-radius: var(--radius-md); overflow:hidden;">
            <img src="<?= htmlspecialchars(image_url($p['image'])) ?>" alt="<?= htmlspecialchars($p['caption'] ?: 'Room photo') ?>" style="width:100%; height:140px; object-fit:cover;">
            <div style="padding: var(--space-3);">
                <p style="font-size: var(--text-sm); color: var(--gray-600); margin-bottom: var(--space-2);"><?= htmlspecialchars($p['caption'] ?: '—') ?></p>
                <form method="POST" onsubmit="return confirm('Remove this photo?');">
                    <?php csrf_field(); ?>
                    <input type="hidden" name="image_id" value="<?= $p['id'] ?>">
                    <button type="submit" name="delete_photo" class="btn btn-outline-gold btn-sm" style="width:100%; color: var(--danger); border-color: var(--danger);"><i class="fas fa-trash"></i> Remove</button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
