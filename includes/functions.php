<?php
// ============================================================
//  Grand Azure Hotel — Shared Helper Functions
// ============================================================

require_once __DIR__ . '/db.php';

// ---- Security Helpers ----------------------------------------

/**
 * Sanitize input to prevent XSS
 */
function sanitize(string $input): string {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Safely execute a prepared statement query
 * Returns the MySQLi result or false on failure
 */
function db_query($conn, string $sql, string $types = '', array $params = []) {
    $stmt = $conn->prepare($sql);
    if (!$stmt) return false;

    if ($types && $params) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt;
}

// ---- CSRF Protection ------------------------------------------
// A CSRF (Cross-Site Request Forgery) token stops another website from
// tricking a logged-in admin/user's browser into submitting a hidden
// form to this site without their knowledge.

/**
 * Get (or create) the CSRF token for this session
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Print a hidden input field containing the CSRF token.
 * Add this inside every form that changes data (POST forms).
 */
function csrf_field(): void {
    echo '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

/**
 * Check that the submitted token matches the session token.
 * Call this at the top of every POST handler, before touching the database.
 */
function csrf_verify(): bool {
    $submitted = $_POST['csrf_token'] ?? '';
    return !empty($submitted) && hash_equals(csrf_token(), $submitted);
}

// ---- Authentication Helpers ----------------------------------

/**
 * Check if a regular user is logged in
 */
function is_logged_in(): bool {
    return isset($_SESSION['user_id']);
}

/**
 * Check if an admin is logged in
 */
function is_admin_logged_in(): bool {
    return isset($_SESSION['admin_id']);
}

/**
 * Redirect to URL
 */
function redirect(string $url): void {
    header("Location: $url");
    exit;
}

/**
 * Require user login — redirect to login if not authenticated
 */
function require_user_login(): void {
    if (!is_logged_in()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        redirect(SITE_URL . '/login.php');
    }
}

/**
 * Require admin login — redirect to admin login if not authenticated
 */
function require_admin_login(): void {
    if (!is_admin_logged_in()) {
        redirect(SITE_URL . '/admin/login.php');
    }
}

// ---- Flash Messages -----------------------------------------

/**
 * Set a flash message
 * @param string $type  success | error | warning | info
 */
function set_flash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/**
 * Display and clear flash message (HTML)
 */
function show_flash(): void {
    if (!empty($_SESSION['flash'])) {
        $f    = $_SESSION['flash'];
        $type = sanitize($f['type']);
        $msg  = sanitize($f['message']);
        echo "<div class=\"alert alert-{$type}\">{$msg}<button class=\"alert-close\" onclick=\"this.parentElement.remove()\">&times;</button></div>";
        unset($_SESSION['flash']);
    }
}

// ---- Database Fetch Helpers ----------------------------------

/**
 * Fetch all rooms from the database
 */
function get_all_rooms($conn): array {
    $result = $conn->query("SELECT * FROM rooms ORDER BY price_per_night ASC");
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

/**
 * Fetch a single room by ID
 */
function get_room_by_id($conn, int $id): ?array {
    $stmt = $conn->prepare("SELECT * FROM rooms WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc() ?: null;
}

/**
 * Fetch available rooms (not booked for given dates)
 */
function get_available_rooms($conn, string $check_in, string $check_out): array {
    $sql = "SELECT * FROM rooms
            WHERE status = 'available'
              AND id NOT IN (
                SELECT room_id FROM bookings
                WHERE status NOT IN ('cancelled')
                  AND check_in  < ?
                  AND check_out > ?
              )
            ORDER BY price_per_night ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $check_out, $check_in);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Fetch bookings for a specific user
 */
function get_user_bookings($conn, int $user_id): array {
    $sql = "SELECT b.*, r.name AS room_name, r.category, r.image AS room_image
            FROM bookings b
            JOIN rooms r ON b.room_id = r.id
            WHERE b.user_id = ?
            ORDER BY b.created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get services from the database
 */
function get_services($conn, int $limit = 0): array {
    $sql    = "SELECT * FROM services WHERE status='active' ORDER BY sort_order ASC";
    if ($limit > 0) $sql .= " LIMIT $limit";
    $result = $conn->query($sql);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

/**
 * Get gallery images from the database
 */
function get_gallery($conn, string $category = '', int $limit = 0): array {
    $sql    = "SELECT * FROM gallery";
    $params = [];
    $types  = '';
    if ($category) {
        $sql   .= " WHERE category = ?";
        $types  = 's';
        $params = [$category];
    }
    $sql .= " ORDER BY sort_order ASC";
    if ($limit > 0) $sql .= " LIMIT $limit";

    $stmt = $conn->prepare($sql);
    if ($types) $stmt->bind_param($types, ...$params);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Fetch every row from the gallery table (admin listing — includes
 * every category, unlike get_gallery() which is used by public pages).
 */
function get_all_gallery($conn): array {
    $result = $conn->query("SELECT * FROM gallery ORDER BY sort_order ASC, id DESC");
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

/**
 * Fetch a single gallery item by ID
 */
function get_gallery_item($conn, int $id): ?array {
    $stmt = $conn->prepare("SELECT * FROM gallery WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc() ?: null;
}

// ---- Site Settings (key/value store, editable from Admin > Settings) ----

/**
 * Load every row from the settings table into an associative array,
 * cached per-request so repeated calls (header + footer + page body)
 * don't re-query the database each time.
 */
function get_all_settings($conn): array {
    static $cache = null;
    if ($cache !== null) return $cache;

    $cache  = [];
    $result = $conn->query("SELECT setting_key, setting_value FROM settings");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $cache[$row['setting_key']] = $row['setting_value'];
        }
    }
    return $cache;
}

/**
 * Get a single setting value, falling back to $default if the key
 * doesn't exist (e.g. the settings table hasn't been migrated in yet).
 */
function setting($conn, string $key, string $default = ''): string {
    $all = get_all_settings($conn);
    return ($all[$key] ?? '') !== '' ? $all[$key] : $default;
}

/**
 * Get testimonials
 */
function get_testimonials($conn, int $limit = 4): array {
    $sql    = "SELECT * FROM testimonials WHERE is_visible = 1 ORDER BY id DESC LIMIT ?";
    $stmt   = $conn->prepare($sql);
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// ---- Image Upload Resizing ------------------------------------

/**
 * Resize an uploaded image (if needed) and save it to disk as an
 * optimized, web-sized file. Caps the longest side at $max_dim and
 * re-compresses so a raw phone/camera photo (often 3-15MB) doesn't
 * get served to every visitor at full resolution — this is what
 * was causing pages to feel slow to load.
 *
 * Returns true on success, false on failure (caller should show an
 * error rather than assume the file was saved).
 */
function resize_and_save_upload(string $tmp_path, string $dest_path, string $ext, int $max_dim = 1920, int $jpeg_quality = 80): bool {
    $ext = strtolower($ext);

    $src = match ($ext) {
        'jpg', 'jpeg' => @imagecreatefromjpeg($tmp_path),
        'png'         => @imagecreatefrompng($tmp_path),
        'webp'        => @imagecreatefromwebp($tmp_path),
        default       => false,
    };
    if (!$src) {
        return false;
    }

    $width  = imagesx($src);
    $height = imagesy($src);

    if (max($width, $height) > $max_dim) {
        $ratio      = $max_dim / max($width, $height);
        $new_width  = (int) round($width  * $ratio);
        $new_height = (int) round($height * $ratio);

        $resized = imagecreatetruecolor($new_width, $new_height);
        if ($ext === 'png' || $ext === 'webp') {
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
        }
        imagecopyresampled($resized, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        imagedestroy($src);
        $src = $resized;
    }

    $ok = match ($ext) {
        'jpg', 'jpeg' => imagejpeg($src, $dest_path, $jpeg_quality),
        'png'         => imagepng($src, $dest_path, 6),
        'webp'        => imagewebp($src, $dest_path, $jpeg_quality),
        default       => false,
    };
    imagedestroy($src);

    return $ok;
}

// ---- Room Images (extra photos per room: bathroom, alternate
// angles, etc. — shown as a thumbnail gallery on room-detail.php) --

/**
 * Fetch the extra photos for one room, in display order.
 */
function get_room_images($conn, int $room_id): array {
    $stmt = $conn->prepare("SELECT * FROM room_images WHERE room_id = ? ORDER BY sort_order ASC, id ASC");
    $stmt->bind_param('i', $room_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

/**
 * Fetch a single room image record by its own ID (used by the
 * admin delete action, so we know which file to remove from disk).
 */
function get_room_image($conn, int $id): ?array {
    $stmt = $conn->prepare("SELECT * FROM room_images WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc() ?: null;
}

// ---- Team Members (About page "Meet Our Leadership") ---------

/**
 * Fetch active team members for the public About page, ordered
 * for display.
 */
function get_team_members($conn): array {
    $result = $conn->query("SELECT * FROM team_members WHERE status='active' ORDER BY sort_order ASC, id ASC");
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

/**
 * Fetch every team member for the admin listing (active and inactive).
 */
function get_all_team_members($conn): array {
    $result = $conn->query("SELECT * FROM team_members ORDER BY sort_order ASC, id ASC");
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

/**
 * Fetch a single team member by ID
 */
function get_team_member($conn, int $id): ?array {
    $stmt = $conn->prepare("SELECT * FROM team_members WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc() ?: null;
}

// ---- Formatting Helpers -------------------------------------

/**
 * Format a price with currency symbol
 */
function format_price(float $price): string {
    return CURRENCY . number_format($price, 2);
}

/**
 * Calculate number of nights between two dates
 */
function nights_between(string $check_in, string $check_out): int {
    $d1 = new DateTime($check_in);
    $d2 = new DateTime($check_out);
    return max(1, $d2->diff($d1)->days);
}

/**
 * Format a date for display
 */
function format_date(string $date): string {
    return date('F j, Y', strtotime($date));
}

/**
 * Generate a booking status badge HTML
 */
function status_badge(string $status): string {
    $classes = [
        'pending'   => 'badge-warning',
        'approved'  => 'badge-success',
        'cancelled' => 'badge-danger',
        'completed' => 'badge-info',
    ];
    $class = $classes[$status] ?? 'badge-secondary';
    return "<span class=\"badge {$class}\">" . ucfirst($status) . "</span>";
}

/**
 * Return star rating HTML
 */
function star_rating(int $rating): string {
    $html = '';
    for ($i = 1; $i <= 5; $i++) {
        $html .= $i <= $rating
            ? '<i class="fas fa-star"></i>'
            : '<i class="far fa-star"></i>';
    }
    return $html;
}

/**
 * Build a usable <img> src from a value stored in the database.
 * Room/gallery images can be either:
 *   - a full URL (e.g. the Unsplash demo photos), or
 *   - a relative path to a file uploaded via the admin panel
 *     (e.g. "assets/images/rooms/room_123.jpg").
 * This function returns the value unchanged if it is already a full
 * URL, and joins it onto SITE_URL otherwise.
 */
function image_url(?string $path, string $fallback = ''): string {
    $path = $path ?: $fallback;
    if ($path === '') return '';
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        return $path;
    }
    return SITE_URL . '/' . ltrim($path, '/');
}

/**
 * Truncate text to a given length
 */
function truncate(string $text, int $length = 120): string {
    return strlen($text) > $length
        ? substr($text, 0, $length) . '…'
        : $text;
}
