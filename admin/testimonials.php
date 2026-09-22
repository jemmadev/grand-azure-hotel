<?php
// ============================================================
//  Grand Azure Hotel — Admin Testimonials Management
// ============================================================

$page_title = 'Manage Testimonials';

require_once '../includes/functions.php';
require_admin_login();

$errors  = [];
$action  = isset($_GET['action']) ? sanitize($_GET['action']) : 'list';
$item_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle Add / Edit form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['add', 'edit']) && csrf_verify()) {
    $author_name = sanitize($_POST['author_name'] ?? '');
    $author_role = sanitize($_POST['author_role'] ?? 'Guest');
    $content     = sanitize($_POST['content'] ?? '');
    $rating      = max(1, min(5, (int)($_POST['rating'] ?? 5)));
    $is_visible  = isset($_POST['is_visible']) ? 1 : 0;

    // Avatar upload (optional)
    $avatar_path = $_POST['existing_avatar'] ?? '';
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $file_tmp  = $_FILES['avatar']['tmp_name'];
        $file_name = $_FILES['avatar']['name'];
        $ext       = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp']) && @getimagesize($file_tmp) !== false) {
            $upload_dir = __DIR__ . '/../assets/images/testimonials/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $new_name = 'avatar_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
            $dest     = $upload_dir . $new_name;
            if (resize_and_save_upload($file_tmp, $dest, $ext)) {
                $avatar_path = 'assets/images/testimonials/' . $new_name;
            } else {
                $errors[] = 'Failed to move uploaded avatar.';
            }
        } else {
            $errors[] = 'Invalid file. Only real JPG, JPEG, PNG, and WEBP images are allowed.';
        }
    }

    if (empty($author_name) || empty($content)) {
        $errors[] = 'Author name and testimonial content are required.';
    }

    if (empty($errors)) {
        if ($action === 'add') {
            $ins = $conn->prepare(
                "INSERT INTO testimonials (author_name, author_role, avatar, content, rating, is_visible)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            $ins->bind_param('ssssii', $author_name, $author_role, $avatar_path, $content, $rating, $is_visible);

            if ($ins->execute()) {
                set_flash('success', 'Testimonial added successfully.');
                redirect('testimonials.php');
            } else {
                $errors[] = 'Failed to insert testimonial.';
            }
        } elseif ($action === 'edit' && $item_id) {
            $upd = $conn->prepare(
                "UPDATE testimonials SET author_name = ?, author_role = ?, avatar = ?, content = ?, rating = ?, is_visible = ?
                 WHERE id = ?"
            );
            $upd->bind_param('ssssiis', $author_name, $author_role, $avatar_path, $content, $rating, $is_visible, $item_id);

            if ($upd->execute()) {
                set_flash('success', 'Testimonial updated successfully.');
                redirect('testimonials.php');
            } else {
                $errors[] = 'Failed to update testimonial.';
            }
        }
    }
}

// Handle Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_item']) && csrf_verify()) {
    $del_id = (int)$_POST['item_id'];
    $del = $conn->prepare("DELETE FROM testimonials WHERE id = ?");
    $del->bind_param('i', $del_id);
    if ($del->execute()) {
        set_flash('success', 'Testimonial has been deleted.');
    } else {
        set_flash('error', 'Failed to delete testimonial.');
    }
    redirect('testimonials.php');
}

// Prepare editing variables
$edit_item = null;
if ($action === 'edit' && $item_id) {
    $stmt = $conn->prepare("SELECT * FROM testimonials WHERE id = ?");
    $stmt->bind_param('i', $item_id);
    $stmt->execute();
    $edit_item = $stmt->get_result()->fetch_assoc();
    if (!$edit_item) {
        set_flash('error', 'Testimonial not found.');
        redirect('testimonials.php');
    }
}

require_once 'includes/header.php';

// Get all testimonials for listing
$res = $conn->query("SELECT * FROM testimonials ORDER BY id DESC");
$testimonials = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
?>

<div style="margin-bottom: var(--space-8); display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 style="font-family: var(--font-serif); font-size: var(--text-3xl); color: var(--navy); margin-bottom: var(--space-2);">Manage Testimonials</h1>
        <p style="color: var(--gray-500);">Add, edit, and curate guest reviews displayed on the public site.</p>
    </div>
    <?php if ($action === 'list'): ?>
        <a href="testimonials.php?action=add" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Testimonial</a>
    <?php else: ?>
        <a href="testimonials.php" class="btn btn-outline-dark"><i class="fas fa-arrow-left"></i> Back to Testimonial List</a>
    <?php endif; ?>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-error">
    <div><strong>Form Errors:</strong><ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div>
</div>
<?php endif; ?>

<!-- ADD / EDIT TESTIMONIAL FORM -->
<?php if (in_array($action, ['add', 'edit'])): ?>
<div class="admin-card">
    <h2 style="font-family: var(--font-serif); font-size: var(--text-xl); color: var(--navy); margin-bottom: var(--space-6);">
        <?= $action === 'edit' ? 'Edit Testimonial' : 'Add New Testimonial' ?>
    </h2>
    <form method="POST" enctype="multipart/form-data">
        <?php csrf_field(); ?>
        <?php if ($action === 'edit'): ?>
            <input type="hidden" name="existing_avatar" value="<?= htmlspecialchars($edit_item['avatar'] ?? '') ?>">
        <?php endif; ?>

        <div class="grid-2" style="gap: var(--space-5);">
            <div class="form-group">
                <label class="form-label">Author Name <span class="required">*</span></label>
                <input class="form-control" type="text" name="author_name" required value="<?= htmlspecialchars($edit_item['author_name'] ?? '') ?>" placeholder="e.g. Emily Richardson">
            </div>

            <div class="form-group">
                <label class="form-label">Author Role / Title</label>
                <input class="form-control" type="text" name="author_role" value="<?= htmlspecialchars($edit_item['author_role'] ?? 'Guest') ?>" placeholder="e.g. Business Traveler">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Testimonial Content <span class="required">*</span></label>
            <textarea class="form-control" name="content" rows="4" required placeholder="What did the guest say about their experience?"><?= htmlspecialchars($edit_item['content'] ?? '') ?></textarea>
        </div>

        <div class="grid-3" style="gap: var(--space-5);">
            <div class="form-group">
                <label class="form-label">Rating (1–5 Stars)</label>
                <select class="form-control" name="rating">
                    <?php for ($r = 5; $r >= 1; $r--): ?>
                        <option value="<?= $r ?>" <?= isset($edit_item['rating']) && (int)$edit_item['rating'] === $r ? 'selected' : '' ?>><?= $r ?> Star<?= $r > 1 ? 's' : '' ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Visibility</label>
                <div style="display: flex; align-items: center; gap: var(--space-3); padding-top: var(--space-2);">
                    <input type="checkbox" name="is_visible" id="is_visible" value="1"
                        <?= ($action === 'add' || (isset($edit_item['is_visible']) && $edit_item['is_visible'])) ? 'checked' : '' ?>
                        style="width: 18px; height: 18px;">
                    <label for="is_visible" style="margin: 0; font-size: var(--text-sm); color: var(--gray-600);">Show on public site</label>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Author Photo (optional)</label>
                <input class="form-control" type="file" name="avatar" accept="image/*">
                <?php if (!empty($edit_item['avatar'])): ?>
                    <div style="margin-top: var(--space-3);">
                        <img src="<?= htmlspecialchars(image_url($edit_item['avatar'])) ?>" alt="Current avatar" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Save Testimonial
        </button>
    </form>
</div>

<!-- TESTIMONIAL LIST VIEW -->
<?php else: ?>
<div class="admin-card">
    <?php if (empty($testimonials)): ?>
        <p style="color: var(--gray-400); text-align: center; padding: var(--space-8);">No testimonials found. Add your first guest review.</p>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Author</th>
                        <th>Role</th>
                        <th>Content</th>
                        <th>Rating</th>
                        <th>Visible</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($testimonials as $t): ?>
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: var(--space-3);">
                                <?php if (!empty($t['avatar'])): ?>
                                    <img src="<?= htmlspecialchars(image_url($t['avatar'])) ?>" alt="" style="width: 36px; height: 36px; object-fit: cover; border-radius: 50%;">
                                <?php else: ?>
                                    <div style="width: 36px; height: 36px; border-radius: 50%; background: var(--gold); color: var(--navy); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: var(--text-sm);">
                                        <?= strtoupper(substr($t['author_name'], 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                                <strong><?= htmlspecialchars($t['author_name']) ?></strong>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($t['author_role']) ?></td>
                        <td style="max-width: 300px;"><?= htmlspecialchars(truncate($t['content'], 80)) ?></td>
                        <td style="color: var(--gold);"><?= star_rating($t['rating']) ?></td>
                        <td>
                            <span class="badge <?= $t['is_visible'] ? 'badge-success' : 'badge-warning' ?>">
                                <?= $t['is_visible'] ? 'Visible' : 'Hidden' ?>
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <a href="testimonials.php?action=edit&id=<?= $t['id'] ?>" class="btn btn-outline-dark btn-sm"><i class="fas fa-edit"></i> Edit</a>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this testimonial? This action is permanent.');">
                                <?php csrf_field(); ?>
                                <input type="hidden" name="item_id" value="<?= $t['id'] ?>">
                                <button type="submit" name="delete_item" class="btn btn-outline-gold btn-sm" style="color: var(--danger); border-color: var(--danger);"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
