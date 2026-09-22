<?php
// ============================================================
//  Grand Azure Hotel — Admin Services Management
// ============================================================

$page_title = 'Manage Services';

require_once '../includes/functions.php';
require_admin_login();

$errors  = [];
$action  = isset($_GET['action']) ? sanitize($_GET['action']) : 'list';
$item_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle Add / Edit form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['add', 'edit']) && csrf_verify()) {
    $name        = sanitize($_POST['name'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $icon        = sanitize($_POST['icon'] ?? '');
    $status      = in_array($_POST['status'] ?? '', ['active', 'inactive']) ? $_POST['status'] : 'active';
    $sort_order  = (int)($_POST['sort_order'] ?? 0);

    // Image upload (optional for services)
    $image_path = $_POST['existing_image'] ?? '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp  = $_FILES['image']['tmp_name'];
        $file_name = $_FILES['image']['name'];
        $ext       = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp']) && @getimagesize($file_tmp) !== false) {
            $upload_dir = __DIR__ . '/../assets/images/services/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $new_name = 'service_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
            $dest     = $upload_dir . $new_name;
            if (resize_and_save_upload($file_tmp, $dest, $ext)) {
                $image_path = 'assets/images/services/' . $new_name;
            } else {
                $errors[] = 'Failed to move uploaded image.';
            }
        } else {
            $errors[] = 'Invalid file. Only real JPG, JPEG, PNG, and WEBP images are allowed.';
        }
    }

    if (empty($name)) {
        $errors[] = 'Service name is required.';
    }

    if (empty($errors)) {
        if ($action === 'add') {
            $ins = $conn->prepare(
                "INSERT INTO services (name, description, icon, image, status, sort_order)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            $ins->bind_param('sssssi', $name, $description, $icon, $image_path, $status, $sort_order);

            if ($ins->execute()) {
                set_flash('success', 'Service added successfully.');
                redirect('services.php');
            } else {
                $errors[] = 'Failed to insert service record.';
            }
        } elseif ($action === 'edit' && $item_id) {
            $upd = $conn->prepare(
                "UPDATE services SET name = ?, description = ?, icon = ?, image = ?, status = ?, sort_order = ?
                 WHERE id = ?"
            );
            $upd->bind_param('sssssii', $name, $description, $icon, $image_path, $status, $sort_order, $item_id);

            if ($upd->execute()) {
                set_flash('success', 'Service updated successfully.');
                redirect('services.php');
            } else {
                $errors[] = 'Failed to update service record.';
            }
        }
    }
}

// Handle Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_item']) && csrf_verify()) {
    $del_id = (int)$_POST['item_id'];
    $del = $conn->prepare("DELETE FROM services WHERE id = ?");
    $del->bind_param('i', $del_id);
    if ($del->execute()) {
        set_flash('success', 'Service has been deleted.');
    } else {
        set_flash('error', 'Failed to delete service record.');
    }
    redirect('services.php');
}

// Prepare editing variables if in Edit mode
$edit_item = null;
if ($action === 'edit' && $item_id) {
    $stmt = $conn->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->bind_param('i', $item_id);
    $stmt->execute();
    $edit_item = $stmt->get_result()->fetch_assoc();
    if (!$edit_item) {
        set_flash('error', 'Service not found.');
        redirect('services.php');
    }
}

require_once 'includes/header.php';

// Get all services for listing
$res = $conn->query("SELECT * FROM services ORDER BY sort_order ASC, id DESC");
$services = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
?>

<div style="margin-bottom: var(--space-8); display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 style="font-family: var(--font-serif); font-size: var(--text-3xl); color: var(--navy); margin-bottom: var(--space-2);">Manage Services</h1>
        <p style="color: var(--gray-500);">Add, edit, and organize the hotel services and amenities shown on the public site.</p>
    </div>
    <?php if ($action === 'list'): ?>
        <a href="services.php?action=add" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Service</a>
    <?php else: ?>
        <a href="services.php" class="btn btn-outline-dark"><i class="fas fa-arrow-left"></i> Back to Service List</a>
    <?php endif; ?>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-error">
    <div><strong>Form Errors:</strong><ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div>
</div>
<?php endif; ?>

<!-- ADD / EDIT SERVICE FORM -->
<?php if (in_array($action, ['add', 'edit'])): ?>
<div class="admin-card">
    <h2 style="font-family: var(--font-serif); font-size: var(--text-xl); color: var(--navy); margin-bottom: var(--space-6);">
        <?= $action === 'edit' ? 'Edit Service: ' . htmlspecialchars($edit_item['name']) : 'Add New Service' ?>
    </h2>
    <form method="POST" enctype="multipart/form-data">
        <?php csrf_field(); ?>
        <?php if ($action === 'edit'): ?>
            <input type="hidden" name="existing_image" value="<?= htmlspecialchars($edit_item['image'] ?? '') ?>">
        <?php endif; ?>

        <div class="grid-2" style="gap: var(--space-5);">
            <div class="form-group">
                <label class="form-label">Service Name <span class="required">*</span></label>
                <input class="form-control" type="text" name="name" required value="<?= htmlspecialchars($edit_item['name'] ?? '') ?>" placeholder="e.g. Swimming Pool">
            </div>

            <div class="form-group">
                <label class="form-label">Font Awesome Icon Class</label>
                <input class="form-control" type="text" name="icon" value="<?= htmlspecialchars($edit_item['icon'] ?? '') ?>" placeholder="e.g. fas fa-swimming-pool">
                <span style="font-size: var(--text-xs); color: var(--gray-400);">Browse icons at <a href="https://fontawesome.com/icons" target="_blank" style="color:var(--gold);">fontawesome.com/icons</a></span>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Description</label>
            <textarea class="form-control" name="description" rows="4" placeholder="Describe this service..."><?= htmlspecialchars($edit_item['description'] ?? '') ?></textarea>
        </div>

        <div class="grid-3" style="gap: var(--space-5);">
            <div class="form-group">
                <label class="form-label">Status</label>
                <select class="form-control" name="status">
                    <option value="active" <?= isset($edit_item['status']) && $edit_item['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= isset($edit_item['status']) && $edit_item['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Sort Order</label>
                <input class="form-control" type="number" name="sort_order" value="<?= htmlspecialchars($edit_item['sort_order'] ?? 0) ?>" placeholder="0">
                <span style="font-size: var(--text-xs); color: var(--gray-400);">Lower numbers appear first.</span>
            </div>

            <div class="form-group">
                <label class="form-label">Service Image (optional)</label>
                <input class="form-control" type="file" name="image" accept="image/*">
                <?php if (!empty($edit_item['image'])): ?>
                    <div style="margin-top: var(--space-3);">
                        <img src="<?= htmlspecialchars(image_url($edit_item['image'])) ?>" alt="Current image" style="width: 100px; height: 70px; object-fit: cover; border-radius: var(--radius-sm);">
                        <span style="font-size: var(--text-xs); color: var(--gray-400); display: block;">Leave blank to keep current image.</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Save Service
        </button>
    </form>
</div>

<!-- SERVICE LIST VIEW -->
<?php else: ?>
<div class="admin-card">
    <?php if (empty($services)): ?>
        <p style="color: var(--gray-400); text-align: center; padding: var(--space-8);">No services found. Add your first service.</p>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Icon</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Order</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $s): ?>
                    <tr>
                        <td>
                            <?php if (!empty($s['icon'])): ?>
                                <i class="<?= htmlspecialchars($s['icon']) ?>" style="font-size: var(--text-xl); color: var(--gold);"></i>
                            <?php else: ?>
                                <i class="fas fa-concierge-bell" style="font-size: var(--text-xl); color: var(--gray-300);"></i>
                            <?php endif; ?>
                        </td>
                        <td><strong><?= htmlspecialchars($s['name']) ?></strong></td>
                        <td style="max-width: 300px;"><?= htmlspecialchars(truncate($s['description'] ?? '', 80)) ?></td>
                        <td><?= $s['sort_order'] ?></td>
                        <td>
                            <span class="badge <?= $s['status'] === 'active' ? 'badge-success' : 'badge-warning' ?>">
                                <?= ucfirst($s['status']) ?>
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <a href="services.php?action=edit&id=<?= $s['id'] ?>" class="btn btn-outline-dark btn-sm"><i class="fas fa-edit"></i> Edit</a>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this service? This action is permanent.');">
                                <?php csrf_field(); ?>
                                <input type="hidden" name="item_id" value="<?= $s['id'] ?>">
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
