# Practical 2: Semantic HTML5 Pages with Accessibility-Ready Structure
**Course:** Web Development Frameworks (ITUE203)  
**Academic Term:** 3rd Semester, 2026-27 (ODD)  
**Institution:** Faculty of Technology & Engineering (FTE), CHARUSAT  
**CO Mapping:** CO1, CO2  
**PO Mapping:** PO1(3), PO2(2), PO3(3), PO4(2), PO5(3), PO9(1), PO10(3)

---

## 🎯 1. Problem Definition
Develop static HTML5 skeletons for at least 10 StudentHub pages:
1. `index.html` (Home Page)
2. `about.html` (About University & Portal)
3. `register.html` (Student Registration)
4. `login.html` (Portal Authentication)
5. `dashboard.html` (Student Personal Dashboard)
6. `events.html` (Campus Events Catalog)
7. `profile.html` (Profile Management)
8. `contact.html` (Contact Support & Inquiries)
9. `admin.html` (Administrator Management Console)
10. `faq.html` (Frequently Asked Questions)
11. `feedback.html` (Student Experience Feedback)

All pages utilize semantic HTML5 tags (`<header>`, `<nav>`, `<main>`, `<section>`, `<article>`, `<aside>`, `<footer>`) and follow accessibility-friendly structures with WAI-ARIA attributes, explicit form labeling, hierarchical headings, and responsive accessibility stylesheets.

---

## 💡 2. Key Questions, Analysis & Viva Answers

### Q1: Are semantic tags such as `<header>`, `<nav>`, `<main>`, `<section>`, `<article>`, `<aside>`, and `<footer>` used properly?
**Answer:**  
Yes, semantic tags are purposefully applied across all 11 pages according to their W3C specifications:
- `<header role="banner">`: Houses the institutional brand identity, logo, and top-level navigation container.
- `<nav aria-label="...">`: Delimits primary site navigation as well as hierarchical secondary breadcrumb paths.
- `<main role="main">`: Wraps the central, unique document content. Only one `<main>` exists per HTML document.
- `<section>`: Divides major topical sections within the page (e.g., `<section aria-labelledby="...">` for event listings or statistics).
- `<article>`: Self-contained, independently distributable content items (e.g., individual event cards in `events.html` and `index.html`).
- `<aside>`: Encloses complementary information indirectly related to the main flow, such as academic deadlines or announcement sidebars.
- `<footer> role="contentinfo"`: Contains university copyright notices, accessibility disclaimers, and auxiliary institutional links.

---

### Q2: Are form labels, headings, image alt text, and navigation links accessible?
**Answer:**  
1. **Form Labels:** Every `<input>`, `<select>`, and `<textarea>` is tied to an explicit `<label for="FIELD_ID">` matching `id="FIELD_ID"`. Screen readers announce the exact field name upon focus. Required fields include `aria-required="true"`.
2. **Headings Hierarchy:** Every document starts with a single `<h1>` for page purpose, followed by sequential `<h2>` and `<h3>` tags without skipping levels, enabling screen reader users to jump between sections using heading shortcut keys (`H`, `1`, `2`, `3`).
3. **Navigation Links:** All anchors have descriptive, human-readable text instead of vague "click here" labels. Active pages are flagged using `aria-current="page"`.
4. **Accessible Disclosures:** The FAQ page leverages native `<details>` and `<summary>` elements, automatically providing screen readers with expanded/collapsed state without custom JavaScript.

---

### Q3: Is the page structure consistent across all pages?
**Answer:**  
Yes. Every page adheres to an identical structural boilerplate:
1. Universal document metadata (`<meta charset="UTF-8">`, viewport scaling, meta description).
2. Skip-to-content bypass anchor as the first focusable child of `<body>`.
3. Standardized top `<header>` and main `<nav>`.
4. Contextual breadcrumb bar on all inner pages.
5. Distinct `<main id="main-content">` container with responsive card padding.
6. Unified multi-column `<footer>` with copyright and support contacts.
7. Modular shared stylesheet ([`css/semantic.css`](./css/semantic.css)) providing uniform typography and focus ring indicators.

---

## 📂 3. Directory Layout for Practical 2

```text
Practical-02/
├── README.md                   # Practical documentation & viva analysis
├── accessibility-checklist.md  # Detailed WCAG 2.1 Level AA conformance audit
├── css/
│   └── semantic.css            # Accessibility-ready stylesheet & focus management
├── index.html                  # 1. Home Page
├── about.html                  # 2. About Portal & FTE Departments
├── register.html               # 3. Student Registration Form
├── login.html                  # 4. User Authentication Form
├── dashboard.html              # 5. Student Personal Dashboard
├── events.html                 # 6. Campus Events Catalog
├── profile.html                # 7. Student Profile Editor
├── contact.html                # 8. Campus Inquiry & Help Desk
├── admin.html                  # 9. Administrator Management Console
├── faq.html                    # 10. Frequently Asked Questions
└── feedback.html               # 11. Student Experience Feedback
```

---

## 🌟 4. Extensions Implemented
- **Intermediate Extension:** Structured semantic breadcrumb navigation (`<nav aria-label="Breadcrumb"><ol>...`) on every inner page.
- **Advanced Extension:** High-contrast, keyboard-focusable skip-to-content links (`<a href="#main-content" class="skip-link">`) allowing screen reader and keyboard-only users to bypass repetitive header navigation immediately.
