/**
 * Practical 5: Registration Form Validation & Custom HTML5 Canvas CAPTCHA
 * Course: ITUE203 Web Development Frameworks
 */

document.addEventListener('DOMContentLoaded', () => {
    initFormValidation();
    initCaptcha();
});

// Regex Patterns
const PATTERNS = {
    name: /^[A-Za-z\s]{3,50}$/,
    email: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/,
    phone: /^[6-9]\d{9}$/,
    password: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/
};

let generatedCaptcha = '';

/* ==========================================================================
   1. HTML5 CANVAS CAPTCHA GENERATOR (Advanced Extension)
   ========================================================================== */
function initCaptcha() {
    const canvas = document.getElementById('captchaCanvas');
    const refreshBtn = document.getElementById('refreshCaptchaBtn');
    if (!canvas) return;

    function generateRandomString(length = 6) {
        const chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz';
        let result = '';
        for (let i = 0; i < length; i++) {
            result += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return result;
    }

    window.drawCaptcha = function() {
        const ctx = canvas.getContext('2d');
        const width = canvas.width;
        const height = canvas.height;

        generatedCaptcha = generateRandomString(6);

        // Background
        ctx.fillStyle = '#f8fafc';
        ctx.fillRect(0, 0, width, height);

        // Add random interference lines
        for (let i = 0; i < 6; i++) {
            ctx.strokeStyle = `hsl(${Math.random() * 360}, 60%, 75%)`;
            ctx.lineWidth = 1.5;
            ctx.beginPath();
            ctx.moveTo(Math.random() * width, Math.random() * height);
            ctx.lineTo(Math.random() * width, Math.random() * height);
            ctx.stroke();
        }

        // Add noise dots
        for (let i = 0; i < 40; i++) {
            ctx.fillStyle = `hsl(${Math.random() * 360}, 50%, 60%)`;
            ctx.beginPath();
            ctx.arc(Math.random() * width, Math.random() * height, 1.2, 0, Math.PI * 2);
            ctx.fill();
        }

        // Render characters with distortion & rotation
        ctx.font = 'bold 24px monospace, sans-serif';
        ctx.textBaseline = 'middle';

        const charSpacing = width / (generatedCaptcha.length + 1);
        for (let i = 0; i < generatedCaptcha.length; i++) {
            const char = generatedCaptcha[i];
            ctx.save();
            ctx.fillStyle = `hsl(${Math.random() * 260}, 80%, 25%)`;

            const x = (i + 1) * charSpacing;
            const y = height / 2 + (Math.random() * 6 - 3);
            const angle = (Math.random() * 30 - 15) * (Math.PI / 180);

            ctx.translate(x, y);
            ctx.rotate(angle);
            ctx.fillText(char, -8, 0);
            ctx.restore();
        }
    };

    drawCaptcha();
    if (refreshBtn) refreshBtn.addEventListener('click', drawCaptcha);
}

/* ==========================================================================
   2. FORM VALIDATION LOGIC (Intermediate Extension: Real-Time Checking)
   ========================================================================== */
function initFormValidation() {
    const form = document.getElementById('registrationForm');
    const nameInput = document.getElementById('fullName');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const courseSelect = document.getElementById('course');
    const captchaInput = document.getElementById('captchaInput');
    const termsCheckbox = document.getElementById('terms');

    // Real-time Event Listeners (Input & Blur)
    nameInput.addEventListener('input', () => validateField(nameInput, PATTERNS.name.test(nameInput.value.trim())));
    emailInput.addEventListener('input', () => validateField(emailInput, PATTERNS.email.test(emailInput.value.trim())));
    phoneInput.addEventListener('input', () => validateField(phoneInput, PATTERNS.phone.test(phoneInput.value.trim())));

    passwordInput.addEventListener('input', () => {
        updatePasswordStrength(passwordInput.value);
        validateField(passwordInput, PATTERNS.password.test(passwordInput.value));
        if (confirmPasswordInput.value) {
            validateField(confirmPasswordInput, confirmPasswordInput.value === passwordInput.value);
        }
    });

    confirmPasswordInput.addEventListener('input', () => {
        validateField(confirmPasswordInput, confirmPasswordInput.value === passwordInput.value && confirmPasswordInput.value !== '');
    });

    courseSelect.addEventListener('change', () => validateField(courseSelect, courseSelect.value !== ''));
    captchaInput.addEventListener('input', () => {
        validateField(captchaInput, captchaInput.value.trim() === generatedCaptcha);
    });

    // Form Submit Event
    form.addEventListener('submit', (e) => {
        e.preventDefault();

        const isNameValid = validateField(nameInput, PATTERNS.name.test(nameInput.value.trim()));
        const isEmailValid = validateField(emailInput, PATTERNS.email.test(emailInput.value.trim()));
        const isPhoneValid = validateField(phoneInput, PATTERNS.phone.test(phoneInput.value.trim()));
        const isPasswordValid = validateField(passwordInput, PATTERNS.password.test(passwordInput.value));
        const isConfirmMatch = validateField(confirmPasswordInput, confirmPasswordInput.value === passwordInput.value && confirmPasswordInput.value !== '');
        const isCourseValid = validateField(courseSelect, courseSelect.value !== '');
        const isGenderValid = validateRadioGroup('gender', 'genderError');
        const isYearValid = validateRadioGroup('year', 'yearError');
        const isTermsValid = validateCheckbox(termsCheckbox, 'termsError');
        const isCaptchaValid = validateField(captchaInput, captchaInput.value.trim() === generatedCaptcha);

        const isFormValid = isNameValid && isEmailValid && isPhoneValid && isPasswordValid && 
                            isConfirmMatch && isCourseValid && isGenderValid && isYearValid && 
                            isTermsValid && isCaptchaValid;

        if (isFormValid) {
            alert('🎉 Registration form validated successfully!\nAll frontend Regex, Password Strength, and Canvas CAPTCHA checks passed.');
            form.reset();
            resetValidationUI();
            drawCaptcha();
        } else {
            // Scroll to the first invalid element
            const firstInvalid = form.querySelector('.is-invalid, .is-invalid-parent');
            if (firstInvalid) firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

    function validateField(input, isValid) {
        if (isValid) {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
            return true;
        } else {
            input.classList.remove('is-valid');
            input.classList.add('is-invalid');
            return false;
        }
    }

    function validateRadioGroup(name, errorElementId) {
        const radios = document.getElementsByName(name);
        const errorEl = document.getElementById(errorElementId);
        const parent = radios[0].closest('.form-group');
        let checked = false;
        radios.forEach(r => { if (r.checked) checked = true; });

        if (checked) {
            parent.classList.remove('is-invalid-parent');
            if (errorEl) errorEl.style.display = 'none';
            return true;
        } else {
            parent.classList.add('is-invalid-parent');
            if (errorEl) errorEl.style.display = 'block';
            return false;
        }
    }

    function validateCheckbox(checkbox, errorElementId) {
        const errorEl = document.getElementById(errorElementId);
        const parent = checkbox.closest('.form-group');
        if (checkbox.checked) {
            parent.classList.remove('is-invalid-parent');
            if (errorEl) errorEl.style.display = 'none';
            return true;
        } else {
            parent.classList.add('is-invalid-parent');
            if (errorEl) errorEl.style.display = 'block';
            return false;
        }
    }

    function resetValidationUI() {
        form.querySelectorAll('.input-control').forEach(el => {
            el.classList.remove('is-valid', 'is-invalid');
        });
        document.getElementById('strengthFill').style.width = '0%';
        document.getElementById('strengthText').textContent = 'None';
    }
}

/* ==========================================================================
   3. PASSWORD STRENGTH METER (Entropy & Pattern Evaluation)
   ========================================================================== */
function updatePasswordStrength(password) {
    const fill = document.getElementById('strengthFill');
    const label = document.getElementById('strengthText');
    if (!fill || !label) return;

    let score = 0;
    if (password.length >= 8) score++;
    if (password.length >= 12) score++;
    if (/[A-Z]/.test(password)) score++;
    if (/[0-9]/.test(password)) score++;
    if (/[^A-Za-z0-9]/.test(password)) score++;

    let width = '0%';
    let color = '#e2e8f0';
    let text = 'None';

    if (password.length === 0) {
        width = '0%';
        text = 'None';
    } else if (score <= 2) {
        width = '33%';
        color = '#dc2626';
        text = 'Weak (Needs upper, number, symbol)';
    } else if (score === 3 || score === 4) {
        width = '66%';
        color = '#f59e0b';
        text = 'Medium (Good, can be stronger)';
    } else {
        width = '100%';
        color = '#16a34a';
        text = 'Strong (Excellent security)';
    }

    fill.style.width = width;
    fill.style.backgroundColor = color;
    label.textContent = text;
    label.style.color = color;
}
