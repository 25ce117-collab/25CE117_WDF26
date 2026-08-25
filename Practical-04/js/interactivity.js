/**
 * Practical 4: JavaScript DOM Manipulation, Event Handling, and UI Interactivity
 * Course: ITUE203 Web Development Frameworks
 */

document.addEventListener('DOMContentLoaded', () => {
    initThemeManager();
    initHamburgerMenu();
    initSlider();
    initAccordion();
    initModal();
    initNotificationSystem();
});

/* ==========================================================================
   1. LIGHT / DARK THEME MANAGER (LocalStorage Persistence)
   ========================================================================== */
function initThemeManager() {
    const themeBtn = document.getElementById('themeToggleBtn');
    if (!themeBtn) return;

    // Check localStorage preference or fallback to system preference
    const savedTheme = localStorage.getItem('studenthub_theme') || 
                       (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    
    applyTheme(savedTheme);

    themeBtn.addEventListener('click', () => {
        const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        applyTheme(newTheme);
        localStorage.setItem('studenthub_theme', newTheme);
        showToast(`Switched to ${newTheme.toUpperCase()} theme mode!`, 'info');
    });

    function applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        themeBtn.innerHTML = theme === 'dark' ? '☀️ Light Mode' : '🌙 Dark Mode';
        themeBtn.setAttribute('aria-label', `Switch to ${theme === 'dark' ? 'light' : 'dark'} mode`);
    }
}

/* ==========================================================================
   2. HAMBURGER MENU (Accessible Drawer Toggle)
   ========================================================================== */
function initHamburgerMenu() {
    const hamburger = document.getElementById('hamburgerBtn');
    const navMenu = document.getElementById('navMenu');
    if (!hamburger || !navMenu) return;

    hamburger.addEventListener('click', () => {
        const isExpanded = navMenu.classList.toggle('active');
        hamburger.setAttribute('aria-expanded', isExpanded);
        hamburger.innerHTML = isExpanded ? '✕' : '☰';
    });

    // Close when clicking outside
    document.addEventListener('click', (e) => {
        if (!hamburger.contains(e.target) && !navMenu.contains(e.target) && navMenu.classList.contains('active')) {
            navMenu.classList.remove('active');
            hamburger.setAttribute('aria-expanded', 'false');
            hamburger.innerHTML = '☰';
        }
    });
}

/* ==========================================================================
   3. CONTENT / IMAGE SLIDER (Carousel with Dots & Autoplay)
   ========================================================================== */
function initSlider() {
    const slides = document.querySelectorAll('.slider-slide');
    const dots = document.querySelectorAll('.slider-dot');
    const prevBtn = document.getElementById('sliderPrev');
    const nextBtn = document.getElementById('sliderNext');
    const sliderContainer = document.querySelector('.slider-container');
    if (!slides.length) return;

    let currentIndex = 0;
    let autoplayTimer = null;

    function goToSlide(index) {
        slides.forEach(slide => slide.classList.remove('active'));
        dots.forEach(dot => dot.classList.remove('active'));

        currentIndex = (index + slides.length) % slides.length;
        slides[currentIndex].classList.add('active');
        if (dots[currentIndex]) dots[currentIndex].classList.add('active');
    }

    function nextSlide() { goToSlide(currentIndex + 1); }
    function prevSlide() { goToSlide(currentIndex - 1); }

    if (nextBtn) nextBtn.addEventListener('click', () => { nextSlide(); resetAutoplay(); });
    if (prevBtn) prevBtn.addEventListener('click', () => { prevSlide(); resetAutoplay(); });

    dots.forEach((dot, idx) => {
        dot.addEventListener('click', () => {
            goToSlide(idx);
            resetAutoplay();
        });
    });

    function startAutoplay() {
        autoplayTimer = setInterval(nextSlide, 4500);
    }
    function resetAutoplay() {
        clearInterval(autoplayTimer);
        startAutoplay();
    }

    if (sliderContainer) {
        sliderContainer.addEventListener('mouseenter', () => clearInterval(autoplayTimer));
        sliderContainer.addEventListener('mouseleave', startAutoplay);
    }

    startAutoplay();
}

/* ==========================================================================
   4. COLLAPSIBLE FAQ ACCORDION (Event Delegation & Keyboard)
   ========================================================================== */
function initAccordion() {
    const accordion = document.getElementById('faqAccordion');
    if (!accordion) return;

    accordion.addEventListener('click', (e) => {
        const header = e.target.closest('.accordion-header');
        if (!header) return;

        const currentItem = header.parentElement;
        const isActive = currentItem.classList.contains('active');

        // Optional: Close siblings for single-accordion behavior
        document.querySelectorAll('.accordion-item').forEach(item => {
            item.classList.remove('active');
            item.querySelector('.accordion-header').setAttribute('aria-expanded', 'false');
        });

        if (!isActive) {
            currentItem.classList.add('active');
            header.setAttribute('aria-expanded', 'true');
        }
    });
}

/* ==========================================================================
   5. MODAL POPUP (Focus Trap, Esc Listener & Backdrop Click)
   ========================================================================== */
function initModal() {
    const modalOverlay = document.getElementById('eventModal');
    const openBtns = document.querySelectorAll('.open-modal-trigger');
    const closeBtns = document.querySelectorAll('.close-modal-trigger');
    if (!modalOverlay) return;

    function openModal(title, details) {
        if (title) document.getElementById('modalTitle').textContent = title;
        if (details) document.getElementById('modalBody').innerHTML = details;
        modalOverlay.classList.add('active');
        document.body.style.overflow = 'hidden'; // prevent scrolling
    }

    function closeModal() {
        modalOverlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    openBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const title = btn.getAttribute('data-title') || 'Campus Event Details';
            const text = btn.getAttribute('data-details') || 'Join this exciting campus activity!';
            openModal(title, `<p>${text}</p><p style="margin-top:10px;color:var(--text-secondary);">Registration confirmed for registered active students.</p>`);
        });
    });

    closeBtns.forEach(btn => btn.addEventListener('click', closeModal));

    // Close on backdrop click
    modalOverlay.addEventListener('click', (e) => {
        if (e.target === modalOverlay) closeModal();
    });

    // Close on Escape key press
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modalOverlay.classList.contains('active')) {
            closeModal();
        }
    });
}

/* ==========================================================================
   6. NOTIFICATION SYSTEM (Auto-dismissing Toast Banners)
   ========================================================================== */
function initNotificationSystem() {
    const testToastBtn = document.getElementById('triggerToastBtn');
    if (testToastBtn) {
        testToastBtn.addEventListener('click', () => {
            showToast('Event RSVP reserved successfully!', 'success');
        });
    }
}

function showToast(message, type = 'info') {
    let container = document.getElementById('toastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'toast-container';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div>
            <strong>${type === 'success' ? '✓ Success' : 'ℹ Notice'}:</strong> ${message}
        </div>
        <button class="toast-close-btn" aria-label="Dismiss notification">&times;</button>
    `;

    toast.querySelector('.toast-close-btn').addEventListener('click', () => removeToast(toast));

    container.appendChild(toast);

    // Auto dismiss after 4 seconds
    setTimeout(() => removeToast(toast), 4000);
}

function removeToast(toast) {
    if (!toast || toast.dataset.removing) return;
    toast.dataset.removing = 'true';
    toast.style.animation = 'fadeOutRight 0.3s forwards';
    setTimeout(() => {
        if (toast.parentElement) toast.remove();
    }, 300);
}
