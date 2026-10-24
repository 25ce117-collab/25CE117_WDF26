/**
 * Practical 15: StudentHub Client Engine
 * Handles theme toggling, canvas captcha, and asynchronous helpers
 */

document.addEventListener('DOMContentLoaded', () => {
    initTheme();
});

function initTheme() {
    const btn = document.getElementById('themeToggleBtn');
    if (!btn) return;

    const savedTheme = localStorage.getItem('studenthub_theme') || 'light';
    applyTheme(savedTheme);

    btn.addEventListener('click', () => {
        const cur = document.documentElement.getAttribute('data-theme') || 'light';
        const next = cur === 'dark' ? 'light' : 'dark';
        applyTheme(next);
        localStorage.setItem('studenthub_theme', next);
    });

    function applyTheme(t) {
        document.documentElement.setAttribute('data-theme', t);
        btn.textContent = t === 'dark' ? '☀️ Light' : '🌙 Dark';
    }
}
