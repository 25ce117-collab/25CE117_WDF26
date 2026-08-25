# Practical 4: JavaScript DOM Manipulation, Event Handling, and UI Interactivity
**Course:** Web Development Frameworks (ITUE203)  
**Academic Term:** 3rd Semester, 2026-27 (ODD)  
**Institution:** Faculty of Technology & Engineering (FTE), CHARUSAT  
**CO Mapping:** CO3, CO4  
**PO Mapping:** PO1(3), PO2(3), PO3(3), PO4(2), PO5(3), PO9(1), PO10(3)

---

## 🎯 1. Problem Definition
Implement dynamic, accessible client-side UI interactivity using Vanilla JavaScript (ES6+):
1. **Collapsible FAQ Accordion:** Event delegation, animated height transition, active class toggling, and ARIA state updates.
2. **Modal Dialog Popup:** Dynamic data population, backdrop overlay click closing, and keyboard `Escape` dismissal with body scroll locking.
3. **Content / Image Slider:** Auto-playing banner carousel with Next/Previous buttons, dot indicators, and pause-on-hover logic.
4. **Notification Toast Banners:** Non-blocking dismissible alert toasts with slide-in animations and 4-second auto-expiration.
5. **Mobile Hamburger Menu:** Accessible drawer menu with `aria-expanded` toggle.
6. **Theme Switcher:** Light/Dark mode switcher with state persistence in `localStorage`.

---

## 💡 2. Key Questions, Analysis & Viva Answers

### Q1: How are DOM elements selected and modified?
**Answer:**  
- **Selection:** Modern standard selectors `document.querySelector()` and `document.querySelectorAll()` retrieve individual and NodeList collections respectively using standard CSS selector syntax.
- **Modification:**
  - Dynamic classes are managed using the `Element.classList` API (`.add()`, `.remove()`, `.toggle()`).
  - Text and markup content are mutated cleanly using `textContent` (safe against XSS) and `innerHTML` (when rendering formatted structural templates).
  - ARIA states are maintained via `setAttribute('aria-expanded', boolean)` and `setAttribute('data-theme', mode)`.

---

### Q2: Are event listeners attached correctly?
**Answer:**  
Yes, adhering to JavaScript best practices:
1. **Event Delegation:** Rather than attaching event listeners to every individual accordion header or list item, a single event listener is attached to the parent container (`#faqAccordion`). `e.target.closest('.accordion-header')` identifies the triggered target efficiently, preventing memory leaks.
2. **DOMContentLoaded Wrapper:** All script initialization is wrapped within `document.addEventListener('DOMContentLoaded', ...)` guaranteeing that the DOM tree is fully parsed before executing element queries.
3. **Global Cleanup:** Modal escape listeners and backdrop clicks clean up scroll locks (`document.body.style.overflow = ''`).

---

### Q3: Is localStorage used for remembering theme choice?
**Answer:**  
Yes. When the theme toggle button (`#themeToggleBtn`) is clicked:
```javascript
const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
document.documentElement.setAttribute('data-theme', newTheme);
localStorage.setItem('studenthub_theme', newTheme);
```
Upon initial document load, `initThemeManager()` retrieves the cached key:
```javascript
const savedTheme = localStorage.getItem('studenthub_theme') || 
                   (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
applyTheme(savedTheme);
```
This ensures zero theme flickering and preserves user preferences across browser sessions.

---

### Q4: Does interactivity improve usability without breaking accessibility?
**Answer:**  
Yes. All dynamic components retain strict WAI-ARIA semantics:
- The hamburger button updates `aria-expanded="true/false"`.
- Modals include `role="dialog"`, `aria-modal="true"`, and support closing via the keyboard `Escape` key.
- Notifications use `role="alert"` so screen readers immediately announce notices without interrupting the user.
- Focusable controls feature high-contrast focus rings and keyboard-friendly `tabindex`.

---

## 📂 3. Directory Layout for Practical 4

```text
Practical-04/
├── README.md               # Practical documentation, theory, and answers
├── index.html              # Interactive demonstration playground
├── css/
│   └── interactivity.css   # Styles for modal, accordion, slider, toast, and dark theme
└── js/
    └── interactivity.js    # Modular ES6 DOM engine & localStorage controller
```

---

## 🌟 4. Extensions Implemented
- **Intermediate Extension:** Smooth CSS cubic-bezier transitions for accordion collapse, button hover elevations, and modal dialog zoom-in.
- **Advanced Extension:** Full `localStorage` persistence restoring user theme preference immediately upon load, combined with OS `prefers-color-scheme` media query detection.
