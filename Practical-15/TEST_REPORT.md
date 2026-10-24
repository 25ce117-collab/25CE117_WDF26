# StudentHub - Master End-to-End Test Report
## Web Development Frameworks (ITUE203) - Practical 15
**Semester:** 3rd Semester, 2026-27 (ODD)  
**Evaluator:** Faculty of Technology & Engineering (FTE), CHARUSAT

---

## 🧪 Comprehensive Verification Matrix (Practicals 1 to 15)

| Practical No. | Feature Under Test | Test Input / Action | Expected Result | Actual Outcome | Status |
|:---:|:---|:---|:---|:---|:---:|
| **PR-01** | Information Architecture & Sitemap | View `sitemap.html` & `wireframes.html` | Visual navigation tree renders 10+ pages with low-fidelity wireframes | Fully interactive wireframes and tree | ✅ Pass |
| **PR-02** | Semantic HTML5 & Accessibility | Audit semantic landmarks and skip links | Skip link shifts focus to `<main>`; `<nav>`, `<header>`, `<footer>` validated | WCAG 2.1 Level AA compliant | ✅ Pass |
| **PR-03** | Mobile-First Responsive Design | Resize viewport (<640px, 640-1024px, >1024px) | Mobile 1-col stacks cleanly; Tablet 2-col cards; Desktop 3-col grid and sidebar | Fluid breakpoints across all viewports | ✅ Pass |
| **PR-04** | Client UI Interactivity & DOM | Trigger accordion, modal, slider, dark theme | DOM classes mutate dynamically; dark theme persists in `localStorage` | Smooth transitions; local theme saved | ✅ Pass |
| **PR-05** | Form Regex & Canvas CAPTCHA | Submit invalid email/password; test CAPTCHA | Inline errors displayed near inputs; distorted Canvas CAPTCHA generated | Strong regex & Canvas verified | ✅ Pass |
| **PR-06** | Fetch API & Offline LocalStorage | Search & filter JSON dataset; simulate offline | Live search filters cards; cached data displayed if network disconnects | Instant filter; offline cache active | ✅ Pass |
| **PR-07** | PHP POST & File Locking (`LOCK_EX`) | Submit registration form with CSRF token | Server sanitizes data; appends to CSV and JSON files under exclusive lock | Safe concurrent file write verified | ✅ Pass |
| **PR-08** | MySQL Schema & PDO Connection | Execute `test_connection.php` | PDO singleton connects; executes parameterized query; confirms 3NF tables | Database health 100% operational | ✅ Pass |
| **PR-09** | Secure User Registration | Register student with duplicate email check | Rejects duplicate email; hashes password via Bcrypt; records audit log | Account created with salted hash | ✅ Pass |
| **PR-10** | Session Security & Inactivity Timeout | Sign in as Admin; wait 15 min or test logout | Inactivity terminates session; fixation defended via `session_regenerate_id` | RBAC and timeout verified | ✅ Pass |
| **PR-11** | Student Management CRUD | Search by name/roll; test soft-delete & CSV | Soft-delete toggles `status = 'deleted'`; CSV streams directly to browser | Full CRUD & streaming export | ✅ Pass |
| **PR-12** | Event CRUD & Poster Upload | Upload 1.5MB PNG poster; test `.htaccess` | File validated via MIME `finfo_file`; renamed securely; scripts blocked | Upload validated & hardened | ✅ Pass |
| **PR-13** | Asynchronous REST JSON API | Search events with 300ms debounce; AJAX edit | Single Page App updates DOM table without reloading browser | Debounced search & async CRUD active | ✅ Pass |
| **PR-14** | Admin KPI Dashboard & Canvas Chart | View KPI counts; toggle user status via AJAX | Aggregated counts computed; HTML5 Canvas chart renders; status toggled | Dynamic menu & analytics verified | ✅ Pass |
| **PR-15** | Unified Full-Stack Integration | End-to-end traversal from registration to admin | Complete portal workflow runs smoothly under XAMPP / Apache | Production-ready full-stack system | ✅ Pass |
