<?php
// ============================================================
//  Grand Azure Hotel — Contact Page
// ============================================================

$page_title = 'Contact Us';
$meta_desc  = 'Get in touch with Grand Azure Hotel. Find our address, phone, email, and send us a message.';

require_once 'includes/functions.php';

$success = $error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    $name    = sanitize($_POST['name']    ?? '');
    $email   = sanitize($_POST['email']   ?? '');
    $phone   = sanitize($_POST['phone']   ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($message)) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $stmt = $conn->prepare("INSERT INTO contacts (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param('sssss', $name, $email, $phone, $subject, $message);
            if ($stmt->execute()) {
                $success = 'Thank you, ' . htmlspecialchars($name) . '! Your message has been sent. We\'ll respond within 24 hours.';
            } else {
                $error = 'Something went wrong. Please try again or call us directly.';
            }
        } else {
            $success = 'Message saved (demo mode — DB not connected).';
        }
    }
}

require_once 'includes/header.php';
?>

<!-- PAGE HERO -->
<section class="page-hero">
    <div class="container">
        <p class="page-hero-eyebrow">Get in Touch</p>
        <h1 class="page-hero-title">Contact Us</h1>
        <p class="page-hero-subtitle">We'd love to hear from you. Reach out to our team for reservations, enquiries, or feedback.</p>
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="index.php">Home</a><span class="breadcrumb-sep">›</span>
            <span aria-current="page">Contact</span>
        </nav>
    </div>
</section>

<!-- CONTACT SECTION -->
<section class="section bg-cream">
    <div class="container">
        <div class="grid-2" style="gap:5rem; align-items:flex-start;">

            <!-- Contact Info Panel -->
            <div class="reveal">
                <span class="section-eyebrow">Reach Us</span>
                <h2 class="section-title" style="text-align:left; font-size:var(--text-3xl);">We're Always <span class="accent">Here</span></h2>
                <div class="sep sep-left"></div>
                <p style="color:var(--gray-500); margin-bottom:var(--space-8); line-height:var(--lh-relaxed);">
                    Our guest relations team is available 24 hours a day, 7 days a week to assist with reservations, special requests, and any enquiries you may have.
                </p>

                <!-- Info cards -->
                <?php
                $contact_phone = $settings['phone'] ?? SITE_PHONE;
                $contact_email = $settings['email'] ?? SITE_EMAIL;
                foreach([
                    ['fas fa-map-marker-alt', 'Our Address',    $settings['address'] ?? SITE_ADDRESS,   null],
                    ['fas fa-phone',          'Phone Number',   $contact_phone,                          'tel:'.str_replace([' ', '(', ')', '-'], '', $contact_phone)],
                    ['fas fa-envelope',       'Email Address',  $contact_email,                          'mailto:'.$contact_email],
                    ['fas fa-clock',          'Front Desk',     'Open 24 hours, 7 days a week', null],
                ] as $info): ?>
                <div style="display:flex; gap:var(--space-5); padding:var(--space-5); background:var(--white); border-radius:var(--radius-md); margin-bottom:var(--space-4); box-shadow:var(--shadow-sm); transition:box-shadow .2s;" onmouseover="this.style.boxShadow='var(--shadow-md)'" onmouseout="this.style.boxShadow='var(--shadow-sm)'">
                    <div style="width:52px;height:52px;background:linear-gradient(135deg,rgba(201,168,76,.15),rgba(201,168,76,.25));border-radius:var(--radius);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="<?= $info[0] ?>" style="color:var(--gold); font-size:var(--text-xl);"></i>
                    </div>
                    <div>
                        <div style="font-size:var(--text-xs); letter-spacing:.1em; text-transform:uppercase; color:var(--gray-400); margin-bottom:4px;"><?= $info[1] ?></div>
                        <?php if ($info[3]): ?>
                        <a href="<?= $info[3] ?>" style="font-weight:600; color:var(--navy); font-size:var(--text-base);"><?= $info[2] ?></a>
                        <?php else: ?>
                        <span style="font-weight:600; color:var(--navy); font-size:var(--text-base);"><?= $info[2] ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>

                <!-- Social -->
                <div style="margin-top:var(--space-8);">
                    <h3 style="font-size:var(--text-sm); font-weight:600; letter-spacing:.1em; text-transform:uppercase; color:var(--gray-400); margin-bottom:var(--space-4);">Follow Us</h3>
                    <div class="footer-social">
                        <?php foreach([['fab fa-facebook-f','Facebook'],['fab fa-instagram','Instagram'],['fab fa-x-twitter','Twitter'],['fab fa-tripadvisor','TripAdvisor'],['fab fa-youtube','YouTube']] as $soc): ?>
                        <a href="#" class="social-link" style="background:var(--gray-100); color:var(--gray-600);" aria-label="<?= $soc[1] ?>"><i class="<?= $soc[0] ?>"></i></a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="reveal delay-2">
                <div style="background:var(--white); border-radius:var(--radius-lg); padding:var(--space-10); box-shadow:var(--shadow-md);">
                    <h3 style="font-family:var(--font-serif); font-size:var(--text-2xl); color:var(--navy); margin-bottom:var(--space-6);">Send a Message</h3>

                    <?php if ($success): ?>
                    <div class="alert alert-success"><i class="fas fa-check-circle"></i><?= $success ?></div>
                    <?php elseif ($error): ?>
                    <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i><?= $error ?></div>
                    <?php endif; ?>

                    <form method="POST" action="contact.php" data-validate>
                        <div class="grid-2" style="gap:var(--space-4);">
                            <div class="form-group">
                                <label class="form-label" for="contact_name">Full Name <span class="required">*</span></label>
                                <input class="form-control" type="text" id="contact_name" name="name" placeholder="John Smith" required
                                       value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="contact_email">Email Address <span class="required">*</span></label>
                                <input class="form-control" type="email" id="contact_email" name="email" placeholder="john@example.com" required
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="grid-2" style="gap:var(--space-4);">
                            <div class="form-group">
                                <label class="form-label" for="contact_phone">Phone Number</label>
                                <input class="form-control" type="tel" id="contact_phone" name="phone" placeholder="+1 555 000 0000"
                                       value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="contact_subject">Subject</label>
                                <select class="form-control" id="contact_subject" name="subject">
                                    <option value="">Select a subject</option>
                                    <option <?= ($_POST['subject'] ?? '') === 'Reservation Enquiry' ? 'selected' : '' ?>>Reservation Enquiry</option>
                                    <option <?= ($_POST['subject'] ?? '') === 'Table Reservation' ? 'selected' : '' ?>>Table Reservation</option>
                                    <option <?= ($_POST['subject'] ?? '') === 'Event Planning' ? 'selected' : '' ?>>Event Planning</option>
                                    <option <?= ($_POST['subject'] ?? '') === 'General Enquiry' ? 'selected' : '' ?>>General Enquiry</option>
                                    <option <?= ($_POST['subject'] ?? '') === 'Feedback' ? 'selected' : '' ?>>Feedback</option>
                                    <option <?= ($_POST['subject'] ?? '') === 'Complaint' ? 'selected' : '' ?>>Complaint</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="contact_message">Message <span class="required">*</span></label>
                            <textarea class="form-control" id="contact_message" name="message" rows="5" placeholder="How can we help you?" required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                        </div>
                        <button type="submit" name="contact_submit" class="btn btn-primary btn-block">
                            <i class="fas fa-paper-plane"></i>
                            Send Message
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- MAP PLACEHOLDER -->
<section aria-label="Hotel location map">
    <div style="background:var(--navy); height:400px; display:flex; align-items:center; justify-content:center; position:relative; overflow:hidden;">
        <!-- Placeholder map with styled overlay -->
        <div style="position:absolute; inset:0; background:var(--navy-mid); display:flex; flex-direction:column; align-items:center; justify-content:center; gap:var(--space-5);">
            <div style="width:80px; height:80px; background:linear-gradient(135deg, var(--gold), var(--gold-dark)); border-radius:50%; display:flex; align-items:center; justify-content:center; animation:pulse 2s ease infinite;">
                <i class="fas fa-map-marker-alt" style="color:var(--navy); font-size:2rem;"></i>
            </div>
            <div style="text-align:center;">
                <div style="color:var(--white); font-family:var(--font-serif); font-size:var(--text-2xl); margin-bottom:var(--space-2);"><?= htmlspecialchars($hotel_name) ?></div>
                <div style="color:rgba(255,255,255,.6);"><?= htmlspecialchars($settings['address'] ?? SITE_ADDRESS) ?></div>
            </div>
            <a href="location.html" class="btn btn-primary"><i class="fas fa-map"></i> View Full Map</a>
        </div>
    </div>
</section>

<style>
@keyframes pulse { 0%,100%{box-shadow:0 0 0 0 rgba(201,168,76,.4);} 50%{box-shadow:0 0 0 20px rgba(201,168,76,0);} }
</style>

<?php require_once 'includes/footer.php'; ?>
