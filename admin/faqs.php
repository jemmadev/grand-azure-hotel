<?php
// ============================================================
//  Grand Azure Hotel — Admin FAQ Management
// ============================================================

$page_title = 'Manage FAQs';

require_once '../includes/functions.php';
require_admin_login();

$errors  = [];
$action  = isset($_GET['action']) ? sanitize($_GET['action']) : 'list';
$item_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$faq_categories = ['General', 'Bookings', 'Dining', 'Transport', 'Amenities', 'Events', 'Policies'];

// Handle Add / Edit form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['add', 'edit']) && csrf_verify()) {
    $question   = sanitize($_POST['question'] ?? '');
    $answer     = sanitize($_POST['answer'] ?? '');
    $category   = in_array($_POST['category'] ?? '', $faq_categories) ? $_POST['category'] : 'General';
    $sort_order = (int)($_POST['sort_order'] ?? 0);
    $is_visible = isset($_POST['is_visible']) ? 1 : 0;

    if (empty($question) || empty($answer)) {
        $errors[] = 'Both question and answer are required.';
    }

    if (empty($errors)) {
        if ($action === 'add') {
            $ins = $conn->prepare(
                "INSERT INTO faqs (question, answer, category, sort_order, is_visible)
                 VALUES (?, ?, ?, ?, ?)"
            );
            $ins->bind_param('sssii', $question, $answer, $category, $sort_order, $is_visible);

            if ($ins->execute()) {
                set_flash('success', 'FAQ added successfully.');
                redirect('faqs.php');
            } else {
                $errors[] = 'Failed to insert FAQ.';
            }
        } elseif ($action === 'edit' && $item_id) {
            $upd = $conn->prepare(
                "UPDATE faqs SET question = ?, answer = ?, category = ?, sort_order = ?, is_visible = ?
                 WHERE id = ?"
            );
            $upd->bind_param('sssiii', $question, $answer, $category, $sort_order, $is_visible, $item_id);

            if ($upd->execute()) {
                set_flash('success', 'FAQ updated successfully.');
                redirect('faqs.php');
            } else {
                $errors[] = 'Failed to update FAQ.';
            }
        }
    }
}

// Handle Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_item']) && csrf_verify()) {
    $del_id = (int)$_POST['item_id'];
    $del = $conn->prepare("DELETE FROM faqs WHERE id = ?");
    $del->bind_param('i', $del_id);
    if ($del->execute()) {
        set_flash('success', 'FAQ has been deleted.');
    } else {
        set_flash('error', 'Failed to delete FAQ.');
    }
    redirect('faqs.php');
}

// Prepare editing variables
$edit_item = null;
if ($action === 'edit' && $item_id) {
    $stmt = $conn->prepare("SELECT * FROM faqs WHERE id = ?");
    $stmt->bind_param('i', $item_id);
    $stmt->execute();
    $edit_item = $stmt->get_result()->fetch_assoc();
    if (!$edit_item) {
        set_flash('error', 'FAQ not found.');
        redirect('faqs.php');
    }
}

require_once 'includes/header.php';

// Get all FAQs for listing
$res = $conn->query("SELECT * FROM faqs ORDER BY sort_order ASC, id DESC");
$faqs = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
?>

<div style="margin-bottom: var(--space-8); display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 style="font-family: var(--font-serif); font-size: var(--text-3xl); color: var(--navy); margin-bottom: var(--space-2);">Manage FAQs</h1>
        <p style="color: var(--gray-500);">Add, edit, and organize the frequently asked questions shown on the public FAQ page.</p>
    </div>
    <?php if ($action === 'list'): ?>
        <a href="faqs.php?action=add" class="btn btn-primary"><i class="fas fa-plus"></i> Add New FAQ</a>
    <?php else: ?>
        <a href="faqs.php" class="btn btn-outline-dark"><i class="fas fa-arrow-left"></i> Back to FAQ List</a>
    <?php endif; ?>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-error">
    <div><strong>Form Errors:</strong><ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div>
</div>
<?php endif; ?>

<!-- ADD / EDIT FAQ FORM -->
<?php if (in_array($action, ['add', 'edit'])): ?>
<div class="admin-card">
    <h2 style="font-family: var(--font-serif); font-size: var(--text-xl); color: var(--navy); margin-bottom: var(--space-6);">
        <?= $action === 'edit' ? 'Edit FAQ' : 'Add New FAQ' ?>
    </h2>
    <form method="POST">
        <?php csrf_field(); ?>

        <div class="form-group">
            <label class="form-label">Question <span class="required">*</span></label>
            <input class="form-control" type="text" name="question" required value="<?= htmlspecialchars($edit_item['question'] ?? '') ?>" placeholder="e.g. What are the check-in and check-out times?">
        </div>

        <div class="form-group">
            <label class="form-label">Answer <span class="required">*</span></label>
            <textarea class="form-control" name="answer" rows="5" required placeholder="Provide a detailed answer..."><?= htmlspecialchars($edit_item['answer'] ?? '') ?></textarea>
        </div>

        <div class="grid-3" style="gap: var(--space-5);">
            <div class="form-group">
                <label class="form-label">Category</label>
                <select class="form-control" name="category">
                    <?php foreach ($faq_categories as $cat): ?>
                        <option value="<?= $cat ?>" <?= isset($edit_item['category']) && $edit_item['category'] === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Sort Order</label>
                <input class="form-control" type="number" name="sort_order" value="<?= htmlspecialchars($edit_item['sort_order'] ?? 0) ?>" placeholder="0">
                <span style="font-size: var(--text-xs); color: var(--gray-400);">Lower numbers appear first.</span>
            </div>

            <div class="form-group">
                <label class="form-label">Visibility</label>
                <div style="display: flex; align-items: center; gap: var(--space-3); padding-top: var(--space-2);">
                    <input type="checkbox" name="is_visible" id="is_visible" value="1"
                        <?= ($action === 'add' || (isset($edit_item['is_visible']) && $edit_item['is_visible'])) ? 'checked' : '' ?>
                        style="width: 18px; height: 18px;">
                    <label for="is_visible" style="margin: 0; font-size: var(--text-sm); color: var(--gray-600);">Show on public FAQ page</label>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Save FAQ
        </button>
    </form>
</div>

<!-- FAQ LIST VIEW -->
<?php else: ?>
<div class="admin-card">
    <?php if (empty($faqs)): ?>
        <p style="color: var(--gray-400); text-align: center; padding: var(--space-8);">No FAQs found. Add your first question.</p>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Question</th>
                        <th>Category</th>
                        <th>Order</th>
                        <th>Visible</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($faqs as $f): ?>
                    <tr>
                        <td style="max-width: 400px;">
                            <strong><?= htmlspecialchars(truncate($f['question'], 80)) ?></strong>
                            <div style="font-size: var(--text-xs); color: var(--gray-400); margin-top: 4px;">
                                <?= htmlspecialchars(truncate($f['answer'], 100)) ?>
                            </div>
                        </td>
                        <td><span class="badge badge-secondary"><?= htmlspecialchars($f['category']) ?></span></td>
                        <td><?= $f['sort_order'] ?></td>
                        <td>
                            <span class="badge <?= $f['is_visible'] ? 'badge-success' : 'badge-warning' ?>">
                                <?= $f['is_visible'] ? 'Visible' : 'Hidden' ?>
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <a href="faqs.php?action=edit&id=<?= $f['id'] ?>" class="btn btn-outline-dark btn-sm"><i class="fas fa-edit"></i> Edit</a>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this FAQ? This action is permanent.');">
                                <?php csrf_field(); ?>
                                <input type="hidden" name="item_id" value="<?= $f['id'] ?>">
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
