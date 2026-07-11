<?php
// ============================================================
//  Grand Azure Hotel — FAQ Page
// ============================================================

$page_title = 'Frequently Asked Questions';
$meta_desc  = 'Find answers to the most common questions about Grand Azure Hotel — check-in, cancellation, amenities, and more.';

require_once 'includes/functions.php';

// Fetch FAQs from DB
$faqs = [];
$res = $conn->query("SELECT * FROM faqs WHERE is_visible=1 ORDER BY sort_order ASC");
if ($res) $faqs = $res->fetch_all(MYSQLI_ASSOC);

// Fallback static FAQs
if (empty($faqs)) {
    $faqs = [
        ['question'=>'What are the check-in and check-out times?',     'answer'=>'Check-in is from 3:00 PM and check-out is at 12:00 PM (noon). Early check-in and late check-out are available upon request, subject to availability.',    'category'=>'General'],
        ['question'=>'Is breakfast included in the room rate?',         'answer'=>'Breakfast is included in our Deluxe, Executive, and Presidential Suite packages. Standard and Family rooms can add breakfast for $25 per person per day.','category'=>'Dining'],
        ['question'=>'Do you offer airport transfers?',                 'answer'=>'Yes, we provide complimentary airport transfers for Presidential Suite guests. Other room categories can book transfers at an additional fee of $45 per trip.','category'=>'Transport'],
        ['question'=>'Is there free parking at the hotel?',            'answer'=>'Yes, we offer complimentary secure underground parking for all guests.',                                                                                       'category'=>'General'],
        ['question'=>'What is your cancellation policy?',              'answer'=>'Cancellations made 48 hours or more before check-in receive a full refund. Cancellations within 48 hours are charged one night\'s room rate.',               'category'=>'Bookings'],
        ['question'=>'Are pets allowed?',                              'answer'=>'We are a pet-friendly hotel! Pets are welcome in designated rooms for a $30 per night fee. Please inform us at the time of booking.',                          'category'=>'General'],
        ['question'=>'Do you have a swimming pool?',                   'answer'=>'Yes! Our temperature-controlled outdoor infinity pool is open daily from 6:00 AM to 10:00 PM. The poolside bar serves refreshments throughout the day.',       'category'=>'Amenities'],
        ['question'=>'Is there a gym/fitness center?',                 'answer'=>'Our fully-equipped fitness center is open 24 hours a day, 7 days a week. Personal trainers are available by appointment.',                                     'category'=>'Amenities'],
        ['question'=>'Can I request a specific room or floor?',        'answer'=>'Yes, we do our best to accommodate special requests. Please note that specific room/floor requests are subject to availability and cannot be guaranteed.',      'category'=>'Bookings'],
        ['question'=>'Do you offer wedding or event packages?',        'answer'=>'Absolutely! Our dedicated events team offers customized packages for weddings, corporate events, and private celebrations. Contact us for a personalized quote.','category'=>'Events'],
    ];
}

// Group by category
$grouped = [];
foreach ($faqs as $faq) {
    $grouped[$faq['category']][] = $faq;
}

require_once 'includes/header.php';
?>
<style>
.faq-item { background:var(--white); border-radius:var(--radius-md); margin-bottom:var(--space-3); overflow:hidden; box-shadow:var(--shadow-sm); border:1px solid var(--gray-100); }
.faq-question { width:100%; display:flex; justify-content:space-between; align-items:center; padding:var(--space-5) var(--space-6); cursor:pointer; background:none; border:none; font-family:var(--font-sans); font-size:var(--text-base); font-weight:600; color:var(--navy); text-align:left; transition:background .2s; }
.faq-question:hover { background:var(--gray-50,#f9f9f9); }
.faq-icon { width:28px; height:28px; border-radius:50%; background:linear-gradient(135deg,var(--gold),var(--gold-dark)); color:var(--navy); display:flex; align-items:center; justify-content:center; font-size:var(--text-lg); flex-shrink:0; transition:transform .3s; }
.faq-answer { max-height:0; overflow:hidden; transition:max-height .4s ease; }
.faq-answer-inner { padding:0 var(--space-6) var(--space-5); color:var(--gray-500); line-height:var(--lh-relaxed); font-size:var(--text-base); }
.faq-category-title { font-family:var(--font-serif); font-size:var(--text-2xl); color:var(--navy); margin-bottom:var(--space-5); padding-bottom:var(--space-3); border-bottom:2px solid var(--gold); display:inline-block; }
.faq-search { width:100%; max-width:500px; margin:0 auto var(--space-8); display:flex; gap:var(--space-3); }
</style>

<!-- PAGE HERO -->
<section class="page-hero">
    <div class="container">
        <p class="page-hero-eyebrow">Help Center</p>
        <h1 class="page-hero-title">Frequently Asked Questions</h1>
        <p class="page-hero-subtitle">Find answers to common questions about your stay at Grand Azure Hotel.</p>
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="index.php">Home</a><span class="breadcrumb-sep">›</span>
            <span aria-current="page">FAQ</span>
        </nav>
    </div>
</section>

<section class="section bg-cream">
    <div class="container" style="max-width:860px;">

        <!-- Search -->
        <div class="text-center reveal" style="margin-bottom:var(--space-12);">
            <div class="faq-search">
                <div class="booking-field" style="flex:1;">
                    <div class="field-inner" style="background:var(--white);">
                        <i class="fas fa-search"></i>
                        <input type="text" id="faqSearch" placeholder="Search questions..." oninput="filterFaqs(this.value)">
                    </div>
                </div>
                <button class="btn btn-primary" onclick="document.getElementById('faqSearch').value=''; filterFaqs('')">
                    Clear
                </button>
            </div>
        </div>

        <!-- FAQ Groups -->
        <?php foreach ($grouped as $category => $items): ?>
        <div class="faq-group reveal" data-category="<?= htmlspecialchars($category) ?>" style="margin-bottom:var(--space-10);">
            <h2 class="faq-category-title">
                <i class="fas fa-tag" style="font-size:var(--text-base); margin-right:var(--space-2);"></i>
                <?= htmlspecialchars($category) ?>
            </h2>
            <?php foreach ($items as $j => $faq): ?>
            <div class="faq-item" data-question="<?= strtolower(htmlspecialchars($faq['question'])) ?>" data-answer="<?= strtolower(htmlspecialchars($faq['answer'])) ?>">
                <button class="faq-question" aria-expanded="false">
                    <span><?= htmlspecialchars($faq['question']) ?></span>
                    <span class="faq-icon" aria-hidden="true">+</span>
                </button>
                <div class="faq-answer" role="region">
                    <div class="faq-answer-inner"><?= htmlspecialchars($faq['answer']) ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>

        <!-- Still have questions -->
        <div class="reveal" style="background:var(--navy); color:var(--white); border-radius:var(--radius-lg); padding:var(--space-10); text-align:center; margin-top:var(--space-8);">
            <i class="fas fa-headset" style="font-size:3rem; color:var(--gold); margin-bottom:var(--space-5);"></i>
            <h2 style="font-family:var(--font-serif); font-size:var(--text-3xl); margin-bottom:var(--space-4);">Still Have Questions?</h2>
            <p style="color:rgba(255,255,255,.7); margin-bottom:var(--space-8);">Our concierge team is available 24/7 to assist you with any enquiries.</p>
            <div style="display:flex; gap:var(--space-4); justify-content:center; flex-wrap:wrap;">
                <a href="contact.php" class="btn btn-primary">
                    <i class="fas fa-envelope"></i> Send a Message
                </a>
                <a href="tel:<?= str_replace([' ', '(', ')', '-'], '', $settings['phone'] ?? SITE_PHONE) ?>" class="btn btn-outline">
                    <i class="fas fa-phone"></i> <?= htmlspecialchars($settings['phone'] ?? SITE_PHONE) ?>
                </a>
            </div>
        </div>

    </div>
</section>

<script>
function filterFaqs(query) {
    const q = query.trim().toLowerCase();
    document.querySelectorAll('.faq-item').forEach(item => {
        const match = !q || item.dataset.question.includes(q) || item.dataset.answer.includes(q);
        item.style.display = match ? '' : 'none';
    });
    // Hide empty groups
    document.querySelectorAll('.faq-group').forEach(group => {
        const visible = Array.from(group.querySelectorAll('.faq-item')).some(i => i.style.display !== 'none');
        group.style.display = visible ? '' : 'none';
    });
}
</script>

<?php require_once 'includes/footer.php'; ?>
