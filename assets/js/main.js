// ============================================================
//  Grand Azure Hotel — Main JavaScript
//  Handles: Navbar, Mobile Menu, Scroll effects, Animations,
//           Lightbox, Counters, Form validation UI
// ============================================================

'use strict';

// ---- DOM Ready -----------------------------------------------
document.addEventListener('DOMContentLoaded', () => {
    initNavbar();
    initMobileMenu();
    initScrollTop();
    initRevealAnimations();
    initCounters();
    initGalleryLightbox();
    initFormValidation();
    initHeroParallax();
    initBookingBar();
    initFaqAccordion();
    initDateConstraints();
    highlightActivePage();
});

// ---- 1. Navbar scroll behavior --------------------------------
function initNavbar() {
    const navbar = document.getElementById('navbar');
    if (!navbar) return;

    const handleScroll = () => {
        if (window.scrollY > 80) {
            navbar.classList.add('scrolled');
            navbar.classList.remove('transparent');
        } else {
            navbar.classList.remove('scrolled');
            navbar.classList.add('transparent');
        }
    };

    // Check if current page has a hero (transparent start)
    const hasHero = document.querySelector('.hero');
    if (!hasHero) {
        navbar.classList.add('scrolled');
        navbar.classList.remove('transparent');
    }

    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll(); // run on load
}

// ---- 2. Mobile Menu -------------------------------------------
function initMobileMenu() {
    const toggle  = document.getElementById('navToggle');
    const menu    = document.getElementById('navMenu');
    const actions = document.getElementById('navActions');
    if (!toggle || !menu) return;

    toggle.addEventListener('click', () => {
        const isOpen = menu.classList.toggle('open');
        actions?.classList.toggle('open', isOpen);
        toggle.setAttribute('aria-expanded', isOpen);
        toggle.classList.toggle('active', isOpen);
        document.body.style.overflow = isOpen ? 'hidden' : '';

        // Animate hamburger to X
        const spans = toggle.querySelectorAll('span');
        if (isOpen) {
            spans[0].style.transform = 'rotate(45deg) translate(5px, 5px)';
            spans[1].style.opacity = '0';
            spans[2].style.transform = 'rotate(-45deg) translate(5px, -5px)';
        } else {
            spans.forEach(s => { s.style.transform = ''; s.style.opacity = ''; });
        }
    });

    // Close on nav link click (mobile)
    menu.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', () => {
            menu.classList.remove('open');
            actions?.classList.remove('open');
            toggle.setAttribute('aria-expanded', 'false');
            toggle.classList.remove('active');
            document.body.style.overflow = '';
            toggle.querySelectorAll('span').forEach(s => { s.style.transform = ''; s.style.opacity = ''; });
        });
    });
}

// ---- 3. Scroll-to-Top Button ----------------------------------
function initScrollTop() {
    const btn = document.getElementById('scrollTop');
    if (!btn) return;

    window.addEventListener('scroll', () => {
        btn.classList.toggle('visible', window.scrollY > 400);
    }, { passive: true });

    btn.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}

// ---- 4. Reveal on Scroll (Intersection Observer) --------------
function initRevealAnimations() {
    const elements = document.querySelectorAll('.reveal');
    if (!elements.length) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

    elements.forEach(el => observer.observe(el));
}

// ---- 5. Animated Counters ------------------------------------
function initCounters() {
    const counters = document.querySelectorAll('[data-count]');
    if (!counters.length) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            const el    = entry.target;
            const end   = parseInt(el.getAttribute('data-count'), 10);
            const dur   = 1800;
            const step  = Math.ceil(dur / end);
            let current = 0;

            const timer = setInterval(() => {
                current += Math.ceil(end / (dur / 16));
                if (current >= end) { current = end; clearInterval(timer); }
                el.textContent = current.toLocaleString() + (el.getAttribute('data-suffix') || '');
            }, 16);

            observer.unobserve(el);
        });
    }, { threshold: 0.5 });

    counters.forEach(c => observer.observe(c));
}

// ---- 6. Gallery Lightbox -------------------------------------
function initGalleryLightbox() {
    const items = document.querySelectorAll('.gallery-item');
    if (!items.length) return;

    // Create lightbox DOM
    const lb = document.createElement('div');
    lb.id = 'lightbox';
    lb.setAttribute('role', 'dialog');
    lb.setAttribute('aria-modal', 'true');
    lb.setAttribute('aria-label', 'Image viewer');
    lb.innerHTML = `
        <div class="lb-backdrop"></div>
        <button class="lb-close" aria-label="Close lightbox">&times;</button>
        <button class="lb-prev" aria-label="Previous image">&#8249;</button>
        <button class="lb-next" aria-label="Next image">&#8250;</button>
        <div class="lb-content">
            <img class="lb-img" src="" alt="">
            <p class="lb-caption"></p>
        </div>`;
    document.body.appendChild(lb);

    // Inline lightbox styles
    const style = document.createElement('style');
    style.textContent = `
        #lightbox { position:fixed; inset:0; z-index:9999; display:flex; align-items:center; justify-content:center; opacity:0; pointer-events:none; transition:opacity .3s; }
        #lightbox.active { opacity:1; pointer-events:all; }
        .lb-backdrop { position:absolute; inset:0; background:rgba(0,0,0,.92); }
        .lb-content { position:relative; z-index:1; text-align:center; max-width:90vw; }
        .lb-img { max-height:80vh; max-width:90vw; border-radius:8px; box-shadow:0 20px 60px rgba(0,0,0,.5); }
        .lb-caption { color:rgba(255,255,255,.75); margin-top:12px; font-size:.9rem; letter-spacing:.05em; }
        .lb-close { position:absolute; top:20px; right:24px; background:rgba(255,255,255,.15); border:none; color:#fff; font-size:2rem; width:48px; height:48px; border-radius:50%; cursor:pointer; z-index:2; transition:background .2s; display:flex; align-items:center; justify-content:center; }
        .lb-close:hover { background:rgba(255,255,255,.3); }
        .lb-prev, .lb-next { position:absolute; top:50%; transform:translateY(-50%); background:rgba(255,255,255,.12); border:none; color:#fff; font-size:2.5rem; width:52px; height:52px; border-radius:50%; cursor:pointer; z-index:2; transition:background .2s; display:flex; align-items:center; justify-content:center; }
        .lb-prev { left:20px; } .lb-next { right:20px; }
        .lb-prev:hover, .lb-next:hover { background:rgba(201,168,76,.4); }
    `;
    document.head.appendChild(style);

    const lbEl     = document.getElementById('lightbox');
    const lbImg    = lbEl.querySelector('.lb-img');
    const lbCap    = lbEl.querySelector('.lb-caption');
    const lbClose  = lbEl.querySelector('.lb-close');
    const lbPrev   = lbEl.querySelector('.lb-prev');
    const lbNext   = lbEl.querySelector('.lb-next');
    let currentIdx = 0;
    const imgList  = Array.from(items);

    function openLightbox(idx) {
        currentIdx = idx;
        const item = imgList[idx];
        const img  = item.querySelector('img');
        const cap  = item.querySelector('.gallery-caption');
        lbImg.src  = img ? img.src : '';
        lbImg.alt  = img ? img.alt : '';
        lbCap.textContent = cap ? cap.textContent : '';
        lbEl.classList.add('active');
        document.body.style.overflow = 'hidden';
        lbClose.focus();
    }

    function closeLightbox() {
        lbEl.classList.remove('active');
        document.body.style.overflow = '';
    }

    items.forEach((item, i) => item.addEventListener('click', () => openLightbox(i)));
    lbClose.addEventListener('click', closeLightbox);
    lbEl.querySelector('.lb-backdrop').addEventListener('click', closeLightbox);
    lbPrev.addEventListener('click', () => openLightbox((currentIdx - 1 + imgList.length) % imgList.length));
    lbNext.addEventListener('click', () => openLightbox((currentIdx + 1) % imgList.length));
    document.addEventListener('keydown', e => {
        if (!lbEl.classList.contains('active')) return;
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowLeft') lbPrev.click();
        if (e.key === 'ArrowRight') lbNext.click();
    });
}

// ---- 7. Client-side Form Validation UI -----------------------
function initFormValidation() {
    document.querySelectorAll('form[data-validate]').forEach(form => {
        form.addEventListener('submit', function (e) {
            let valid = true;

            // Clear previous errors
            form.querySelectorAll('.form-error').forEach(el => el.remove());
            form.querySelectorAll('.form-control').forEach(el => el.classList.remove('is-invalid'));

            // Validate required fields
            form.querySelectorAll('[required]').forEach(field => {
                if (!field.value.trim()) {
                    valid = false;
                    showFieldError(field, 'This field is required.');
                }
            });

            // Validate email fields
            form.querySelectorAll('input[type="email"]').forEach(field => {
                if (field.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(field.value)) {
                    valid = false;
                    showFieldError(field, 'Please enter a valid email address.');
                }
            });

            // Validate password match
            const pass  = form.querySelector('#password');
            const conf  = form.querySelector('#confirm_password');
            if (pass && conf && pass.value !== conf.value) {
                valid = false;
                showFieldError(conf, 'Passwords do not match.');
            }

            if (!valid) e.preventDefault();
        });
    });
}

function showFieldError(field, msg) {
    field.classList.add('is-invalid');
    const err = document.createElement('span');
    err.className = 'form-error';
    err.textContent = msg;
    field.parentNode.insertBefore(err, field.nextSibling);
}

// ---- 8. Hero Parallax ----------------------------------------
function initHeroParallax() {
    const heroBg = document.querySelector('.hero-bg');
    if (!heroBg) return;

    window.addEventListener('scroll', () => {
        heroBg.style.transform = `translateY(${window.scrollY * 0.3}px)`;
    }, { passive: true });
}

// ---- 9. Booking Bar (quick search) ---------------------------
function initBookingBar() {
    const form = document.getElementById('quickBookingForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        const checkIn  = new Date(form.querySelector('#bar_check_in')?.value);
        const checkOut = new Date(form.querySelector('#bar_check_out')?.value);
        if (checkOut <= checkIn) {
            e.preventDefault();
            alert('Check-out date must be after check-in date.');
        }
    });
}

// ---- 10. FAQ Accordion ----------------------------------------
function initFaqAccordion() {
    document.querySelectorAll('.faq-item').forEach(item => {
        const trigger = item.querySelector('.faq-question');
        const answer  = item.querySelector('.faq-answer');
        if (!trigger || !answer) return;

        trigger.addEventListener('click', () => {
            const isOpen = item.classList.contains('open');

            // Close all
            document.querySelectorAll('.faq-item.open').forEach(open => {
                open.classList.remove('open');
                open.querySelector('.faq-answer').style.maxHeight = '0';
                open.querySelector('.faq-icon').style.transform = '';
            });

            if (!isOpen) {
                item.classList.add('open');
                answer.style.maxHeight = answer.scrollHeight + 'px';
                item.querySelector('.faq-icon').style.transform = 'rotate(45deg)';
            }
        });
    });
}

// ---- 11. Date Constraints for booking inputs -----------------
function initDateConstraints() {
    const today = new Date().toISOString().split('T')[0];
    document.querySelectorAll('input[type="date"]').forEach(input => {
        if (input.name === 'check_in' || input.id?.includes('check_in')) {
            input.min = today;
            input.addEventListener('change', () => {
                const checkOut = document.querySelector('input[name="check_out"], #check_out, #bar_check_out');
                if (checkOut) {
                    const nextDay = new Date(input.value);
                    nextDay.setDate(nextDay.getDate() + 1);
                    checkOut.min = nextDay.toISOString().split('T')[0];
                    if (checkOut.value && checkOut.value <= input.value) {
                        checkOut.value = nextDay.toISOString().split('T')[0];
                    }
                }
            });
        }
    });
}

// ---- 12. Highlight active nav link by URL --------------------
function highlightActivePage() {
    const path  = window.location.pathname;
    const links = document.querySelectorAll('.nav-link');
    links.forEach(link => {
        if (link.getAttribute('href') && path.endsWith(link.getAttribute('href').split('/').pop())) {
            link.classList.add('active');
        }
    });
}

// ---- Utility: Debounce ----------------------------------------
function debounce(fn, delay = 200) {
    let timer;
    return (...args) => { clearTimeout(timer); timer = setTimeout(() => fn(...args), delay); };
}
