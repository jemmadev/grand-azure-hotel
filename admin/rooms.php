<?php
// ============================================================
//  Grand Azure Hotel — Admin Room Management
// ============================================================

$page_title = 'Manage Rooms';

// Load functions and check admin login WITHOUT printing any HTML yet,
// so redirect() calls below still work (a redirect must happen before
// any HTML has been sent to the browser).
require_once '../includes/functions.php';
require_admin_login();

$errors  = [];
$success = '';
$action  = isset($_GET['action']) ? sanitize($_GET['action']) : 'list';
$room_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle Add / Edit form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['add', 'edit']) && csrf_verify()) {
    $name            = sanitize($_POST['name'] ?? '');
    $category        = sanitize($_POST['category'] ?? 'Standard');
    $description     = sanitize($_POST['description'] ?? '');
    $amenities       = sanitize($_POST['amenities'] ?? '');
    $capacity        = (int)($_POST['capacity'] ?? 2);
    $price_per_night = floatval($_POST['price_per_night'] ?? 0.00);
    $floor           = (int)($_POST['floor'] ?? 1);
    $room_number     = sanitize($_POST['room_number'] ?? '');
    $status          = sanitize($_POST['status'] ?? 'available');
    
    // File upload logic
    $image_path = $_POST['existing_image'] ?? '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp  = $_FILES['image']['tmp_name'];
        $file_name = $_FILES['image']['name'];
        $ext       = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // getimagesize() confirms the uploaded file is really an image,
        // not just a file that has been renamed to look like one.
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp']) && @getimagesize($file_tmp) !== false) {
            $upload_dir = __DIR__ . '/../assets/images/rooms/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $new_name = 'room_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
            $dest     = $upload_dir . $new_name;
            if (resize_and_save_upload($file_tmp, $dest, $ext)) {
                $image_path = 'assets/images/rooms/' . $new_name;
            } else {
                $errors[] = 'Failed to move uploaded image.';
            }
        } else {
            $errors[] = 'Invalid file. Only real JPG, JPEG, PNG, and WEBP images are allowed.';
        }
    }

    if (empty($name) || !$price_per_night || empty($room_number)) {
        $errors[] = 'Name, Room Number, and Price per Night are required fields.';
    }

    if (empty($errors)) {
        if ($action === 'add') {
            $ins = $conn->prepare(
                "INSERT INTO rooms (name, category, description, amenities, capacity, price_per_night, image, floor, room_number, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $ins->bind_param('ssssidsiss', $name, $category, $description, $amenities, $capacity, $price_per_night, $image_path, $floor, $room_number, $status);
            
            if ($ins->execute()) {
                set_flash('success', 'Room added successfully.');
                redirect('rooms.php');
            } else {
                $errors[] = 'Failed to insert room record.';
            }
        } elseif ($action === 'edit' && $room_id) {
            $upd = $conn->prepare(
                "UPDATE rooms SET name = ?, category = ?, description = ?, amenities = ?, capacity = ?, price_per_night = ?, image = ?, floor = ?, room_number = ?, status = ?
                 WHERE id = ?"
            );
            $upd->bind_param('ssssidsissi', $name, $category, $description, $amenities, $capacity, $price_per_night, $image_path, $floor, $room_number, $status, $room_id);
            
            if ($upd->execute()) {
                set_flash('success', 'Room updated successfully.');
                redirect('rooms.php');
            } else {
                $errors[] = 'Failed to update room record.';
            }
        }
    }
}

// Handle Delete room action.
// This must be a POST request, not a GET link — a GET request can be
// triggered just by visiting a URL (e.g. a link shared in an email, or
// a malicious page), which would delete data with no real confirmation.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_room']) && csrf_verify()) {
    $del_id = (int)$_POST['room_id'];
    $del = $conn->prepare("DELETE FROM rooms WHERE id = ?");
    $del->bind_param('i', $del_id);
    if ($del->execute()) {
        set_flash('success', 'Room record has been deleted.');
    } else {
        set_flash('error', 'Failed to delete room record.');
    }
    redirect('rooms.php');
}

// Prepare room editing variables if in Edit mode
$edit_room = null;
if ($action === 'edit' && $room_id) {
    $stmt = $conn->prepare("SELECT * FROM rooms WHERE id = ?");
    $stmt->bind_param('i', $room_id);
    $stmt->execute();
    $edit_room = $stmt->get_result()->fetch_assoc();
}

// Now it's safe to print the page (no more redirects after this point)
require_once 'includes/header.php';

// Get all rooms for listing
$rooms = get_all_rooms($conn);
?>

<div style="margin-bottom: var(--space-8); display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 style="font-family: var(--font-serif); font-size: var(--text-3xl); color: var(--navy); margin-bottom: var(--space-2);">Manage Guest Rooms</h1>
        <p style="color: var(--gray-500);">Add, modify, and keep track of your luxury hotel rooms and suites.</p>
    </div>
    <?php if ($action === 'list'): ?>
        <a href="rooms.php?action=add" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Room</a>
    <?php else: ?>
        <a href="rooms.php" class="btn btn-outline-dark"><i class="fas fa-arrow-left"></i> Back to Room List</a>
    <?php endif; ?>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-error">
    <div><strong>Form Errors:</strong><ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div>
</div>
<?php endif; ?>

<!-- ADD / EDIT ROOM FORM -->
<?php if (in_array($action, ['add', 'edit'])): ?>
<div class="admin-card">
    <h2 style="font-family: var(--font-serif); font-size: var(--text-xl); color: var(--navy); margin-bottom: var(--space-6);">
        <?= $action === 'edit' ? 'Edit Details for ' . htmlspecialchars($edit_room['name']) : 'Register New Room' ?>
    </h2>
    <form method="POST" enctype="multipart/form-data">
        <?php csrf_field(); ?>
        <?php if ($action === 'edit'): ?>
            <input type="hidden" name="existing_image" value="<?= htmlspecialchars($edit_room['image']) ?>">
        <?php endif; ?>

        <div class="grid-2" style="gap: var(--space-5);">
            <div class="form-group">
                <label class="form-label">Room / Suite Name <span class="required">*</span></label>
                <input class="form-control" type="text" name="name" required value="<?= htmlspecialchars($edit_room['name'] ?? '') ?>" placeholder="e.g. Superior King Room">
            </div>

            <div class="form-group">
                <label class="form-label">Category <span class="required">*</span></label>
                <select class="form-control" name="category">
                    <?php foreach(['Standard', 'Deluxe', 'Executive', 'Family', 'Presidential Suite'] as $cat): ?>
                        <option value="<?= $cat ?>" <?= isset($edit_room['category']) && $edit_room['category'] === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Description</label>
            <textarea class="form-control" name="description" rows="4" placeholder="Detail the features, size, and layout of the room."><?= htmlspecialchars($edit_room['description'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Amenities (Comma separated list)</label>
            <input class="form-control" type="text" name="amenities" value="<?= htmlspecialchars($edit_room['amenities'] ?? '') ?>" placeholder="Free WiFi, Smart TV, Mini-bar, Jacuzzi, Balcony">
        </div>

        <div class="grid-4" style="gap: var(--space-4);">
            <div class="form-group">
                <label class="form-label">Capacity (Guests) <span class="required">*</span></label>
                <input class="form-control" type="number" name="capacity" min="1" max="10" required value="<?= htmlspecialchars($edit_room['capacity'] ?? 2) ?>">
            </div>

            <div class="form-group">
                <label class="form-label">Price per Night <span class="required">*</span></label>
                <input class="form-control" type="number" step="0.01" name="price_per_night" required value="<?= htmlspecialchars($edit_room['price_per_night'] ?? '') ?>" placeholder="0.00">
            </div>

            <div class="form-group">
                <label class="form-label">Room Number <span class="required">*</span></label>
                <input class="form-control" type="text" name="room_number" required value="<?= htmlspecialchars($edit_room['room_number'] ?? '') ?>" placeholder="e.g. 101">
            </div>

            <div class="form-group">
                <label class="form-label">Floor Number</label>
                <input class="form-control" type="number" name="floor" value="<?= htmlspecialchars($edit_room['floor'] ?? 1) ?>">
            </div>
        </div>

        <div class="grid-2" style="gap: var(--space-5);">
            <div class="form-group">
                <label class="form-label">Availability / Status</label>
                <select class="form-control" name="status">
                    <option value="available" <?= isset($edit_room['status']) && $edit_room['status'] === 'available' ? 'selected' : '' ?>>Available</option>
                    <option value="maintenance" <?= isset($edit_room['status']) && $edit_room['status'] === 'maintenance' ? 'selected' : '' ?>>Under Maintenance</option>
                    <option value="booked" <?= isset($edit_room['status']) && $edit_room['status'] === 'booked' ? 'selected' : '' ?>>Booked</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Room Showcase Image</label>
                <input class="form-control" type="file" name="image" accept="image/*">
                <?php if (!empty($edit_room['image'])): ?>
                    <span style="font-size: var(--text-xs); color: var(--gray-400); display: block; margin-top: .25rem;">Current Image: <code><?= htmlspecialchars($edit_room['image']) ?></code></span>
                <?php endif; ?>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Save Room Details
        </button>
    </form>
</div>

<!-- ROOM LIST VIEW -->
<?php else: ?>
<div class="admin-card">
    <?php if (empty($rooms)): ?>
        <p style="color: var(--gray-400); text-align: center; padding: var(--space-8);">No room records found. Let's register a new room.</p>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Room No.</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Capacity</th>
                        <th>Price/Night</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rooms as $r): ?>
                    <tr>
                        <td>
                            <img src="<?= htmlspecialchars(image_url($r['image'], 'assets/images/rooms/standard.jpg')) ?>" 
                                 alt="<?= htmlspecialchars($r['name']) ?>" 
                                 style="width: 60px; height: 40px; object-fit: cover; border-radius: var(--radius-sm);">
                        </td>
                        <td><strong>#<?= htmlspecialchars($r['room_number']) ?></strong> (Floor <?= $r['floor'] ?>)</td>
                        <td><strong><?= htmlspecialchars($r['name']) ?></strong></td>
                        <td><span class="badge badge-secondary"><?= htmlspecialchars($r['category']) ?></span></td>
                        <td><?= $r['capacity'] ?> Guests</td>
                        <td style="font-weight: 600; color: var(--gold);"><?= format_price($r['price_per_night']) ?></td>
                        <td>
                            <span class="badge <?= $r['status'] === 'available' ? 'badge-success' : ($r['status'] === 'booked' ? 'badge-info' : 'badge-warning') ?>">
                                <?= ucfirst($r['status']) ?>
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <a href="rooms.php?action=edit&id=<?= $r['id'] ?>" class="btn btn-outline-dark btn-sm"><i class="fas fa-edit"></i> Edit</a>
                            <a href="room-images.php?room_id=<?= $r['id'] ?>" class="btn btn-outline-dark btn-sm"><i class="fas fa-images"></i> Photos</a>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this room? This action is permanent.');">
                                <?php csrf_field(); ?>
                                <input type="hidden" name="room_id" value="<?= $r['id'] ?>">
                                <button type="submit" name="delete_room" class="btn btn-outline-gold btn-sm" style="color: var(--danger); border-color: var(--danger);"><i class="fas fa-trash"></i> Delete</button>
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
