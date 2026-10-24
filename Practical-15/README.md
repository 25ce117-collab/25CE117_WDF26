# Practical 15: Complete Web Application - StudentHub Portal

**Course:** ITUE203 - Web Development Frameworks  
**Curriculum:** B.Tech Information Technology, CHARUSAT  
**Academic Year:** 2026-27 (ODD Semester)  
**Repository Location:** `WDF-final/Practical-15/`  

---

## 1. Aim and Objectives

### Aim
Design, develop, and deploy an end-to-end, production-ready, full-stack web application (**StudentHub**) integrating all competencies acquired across Practicals 01 through 14: Semantic HTML5 structure, modern CSS Grid/Flexbox responsive styling, Vanilla JavaScript client-side interactivity and asynchronous Fetch API, robust PHP backend architecture with secure session management, Role-Based Access Control (RBAC), MySQL 3NF database architecture with PDO prepared statements, secure file uploads with MIME validation, and an administrative dashboard with real-time audit logging.

### Key Objectives
1. **Unified Application Architecture:** Assemble a cohesive multi-tier web application adhering to separation of concerns (MVC-inspired layout).
2. **End-to-End Authentication & Authorization:** Implement multi-role authentication (Admin vs. Student) utilizing Bcrypt password hashing (`cost=12`), session fixation mitigation (`session_regenerate_id(true)`), auto-logout idle timers, and RBAC middleware guards.
3. **Comprehensive Data Management:** Provide full CRUD capabilities for student records and campus event listings with server-side validation and soft-deletion.
4. **Secure File Upload Pipeline:** Implement multi-layer file upload defenses including Fileinfo magic-byte MIME validation, randomized storage naming, and Apache `.htaccess` script execution prevention.
5. **Interactive Dashboard & Analytics:** Render key performance metrics, Canvas-rendered bar charts, recent registration feeds, and tamper-resistant audit logs.
6. **Mobile-First Responsive UI:** Ensure strict WCAG 2.1 AA accessibility, keyboard navigability, fluid typography, and dark/light mode toggling via CSS custom properties.

---

## 2. Directory Structure

```text
Practical-15/
├── DEPLOYMENT_GUIDE.md               # Step-by-step production deployment & Apache/Nginx guide
├── README.md                         # Practical documentation, architecture & verification
├── TEST_REPORT.md                    # Formal test execution logs & validation results
├── sql/
│   └── studenthub_complete.sql       # 3NF database schema, constraints, views & seed data
└── studenthub/                       # Complete deployable application source code
    ├── config/
    │   ├── config.php                # Global constants, secure session configuration & CSRF utilities
    │   └── db.php                    # PDO connection manager with singleton-pattern instance
    ├── includes/
    │   ├── auth_guard.php            # RBAC middleware (require_login, require_admin, require_student)
    │   ├── header.php                # Accessible semantic header, navigation & flash messages
    │   └── footer.php                # Semantic footer with dynamic copyright & system status
    ├── assets/
    │   ├── css/
    │   │   └── main.css              # Mobile-first CSS Grid/Flexbox design system with dark mode
    │   └── js/
    │       └── main.js               # Event delegation, debounced search, modal & Canvas chart
    ├── uploads/
    │   └── .htaccess                 # Strict Apache rule preventing script execution in upload directory
    ├── index.php                     # Public landing page with hero banner & upcoming events
    ├── login.php                     # Secure authentication portal with rate-limiting & CSRF protection
    ├── register.php                  # Multi-step student registration with password strength meter
    ├── logout.php                    # Secure session termination & cookie destruction
    ├── events.php                    # Public/student event catalog with search, filter & registration
    ├── student_dashboard.php         # Student personal portal showing registered events & profile info
    ├── admin_dashboard.php           # Administrative overview with metrics & Canvas visual analytics
    ├── students.php                  # Admin student directory with CRUD operations & CSV export
    ├── admin_events.php              # Admin event management with flyer image uploads
    ├── admin_users.php               # Admin user management with AJAX status activation toggles
    ├── audit_logs.php                # Security event log viewer with IP address & action tracking
    ├── about.php                     # Institutional background, mission & department profiles
    ├── faq.php                       # Interactive accessible accordion FAQ system
    ├── contact.php                   # Contact form with database logging & validation
    └── feedback.php                  # Student feedback submission portal with rating controls
```

---

## 3. Technology Stack and Architecture

```
+-------------------------------------------------------------------------+
|                          CLIENT BROWSER                                 |
|  Semantic HTML5  *  CSS3 (Grid/Flexbox/Vars)  *  Vanilla JS (ES6/Fetch) |
+-------------------------------------------------------------------------+
                                    |
                            HTTP/HTTPS Requests
                        (CSRF Token, Secure Cookies)
                                    |
                                    v
+-------------------------------------------------------------------------+
|                          APACHE WEB SERVER                              |
|   Rewrite Rules  *  .htaccess Upload Guard  *  Security Headers         |
+-------------------------------------------------------------------------+
                                    |
                                    v
+-------------------------------------------------------------------------+
|                          PHP 8.x RUNTIME                                |
|  - config.php (Session Hardening, CSRF Generator/Validator)             |
|  - auth_guard.php (Role-Based Access Control Middleware)                 |
|  - Input Validation & Sanitization (filter_var, htmlspecialchars)        |
|  - Secure File Processing (finfo_file MIME inspection, bin2hex renaming) |
+-------------------------------------------------------------------------+
                                    |
                           PDO Prepared Statements
                                    |
                                    v
+-------------------------------------------------------------------------+
|                        MySQL 8.x / MariaDB                              |
|  - 3NF Schema (users, students, events, registrations, audit_logs)      |
|  - Foreign Key Constraints & Cascading Rules                            |
|  - B-Tree Indexes on High-Frequency Lookups (email, roll_number, dates) |
+-------------------------------------------------------------------------+
```

---

## 4. Security Implementation Matrix

| Security Domain | Identified Threat | Implementation in StudentHub | File Reference |
| :--- | :--- | :--- | :--- |
| **Authentication** | Brute force, Rainbow tables | `password_hash($password, PASSWORD_BCRYPT, ['cost' => 12])` | [`login.php`](file:///c:/Users/satya/Desktop/WDF-final/Practical-15/studenthub/login.php) |
| **Session Security** | Session fixation & hijacking | `session_regenerate_id(true)` upon login; strict cookie flags (`HttpOnly`, `SameSite=Strict`); 15-minute idle timeout | [`config.php`](file:///c:/Users/satya/Desktop/WDF-final/Practical-15/studenthub/config/config.php) |
| **Cross-Site Scripting (XSS)** | Malicious script injection | Context-aware encoding via `htmlspecialchars($val, ENT_QUOTES, 'UTF-8')` on all outputs | [`header.php`](file:///c:/Users/satya/Desktop/WDF-final/Practical-15/studenthub/includes/header.php) |
| **Cross-Site Request Forgery (CSRF)** | Unauthorized state-changing POSTs | Synchronizer Token Pattern using `bin2hex(random_bytes(32))` and `hash_equals()` verification | [`config.php`](file:///c:/Users/satya/Desktop/WDF-final/Practical-15/studenthub/config/config.php) |
| **SQL Injection (SQLi)** | Data exfiltration / database destruction | 100% Parameterized queries via PDO with `PDO::ATTR_EMULATE_PREPARES => false` | [`db.php`](file:///c:/Users/satya/Desktop/WDF-final/Practical-15/studenthub/config/db.php) |
| **Arbitrary File Execution** | Uploading PHP webshells | Binary MIME inspection via `finfo_file()`, cryptographic filename randomization, and `.htaccess` execution denial | [`uploads/.htaccess`](file:///c:/Users/satya/Desktop/WDF-final/Practical-15/studenthub/uploads/.htaccess) |
| **Access Control (RBAC)** | Privilege escalation (horizontal & vertical) | `require_admin()` and `require_student()` guards verifying session state before rendering protected views | [`auth_guard.php`](file:///c:/Users/satya/Desktop/WDF-final/Practical-15/studenthub/includes/auth_guard.php) |
| **Non-Repudiation** | Unaccounted administrative changes | Centralized `log_audit()` function tracking User ID, Action, IP address, and timestamp | [`config.php`](file:///c:/Users/satya/Desktop/WDF-final/Practical-15/studenthub/config/config.php) |

---

## 5. Setup and Execution Instructions

### Prerequisites
- PHP 8.0 or higher with `pdo_mysql`, `fileinfo`, `mbstring`, and `openssl` extensions enabled.
- MySQL 8.0+ or MariaDB 10.4+ server.
- Apache HTTP Server with `mod_rewrite` and `mod_headers` enabled (e.g., via XAMPP, WampServer, or standalone).

### Step 1: Database Initialization
1. Start MySQL via XAMPP Control Panel or command line:
   ```bash
   mysql -u root -p
   ```
2. Execute the complete initialization script:
   ```sql
   SOURCE c:/Users/satya/Desktop/WDF-final/Practical-15/sql/studenthub_complete.sql;
   ```
3. This creates the database `studenthub_db` and seeds default administrative and student accounts along with test events and department records.

### Step 2: Application Configuration
Open `studenthub/config/db.php` and verify your local database credentials:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'studenthub_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
```

### Step 3: Web Server Deployment
1. Copy or symlink the `studenthub/` directory into your Apache web root:
   - For XAMPP: `C:\xampp\htdocs\studenthub`
   - For WAMP: `C:\wamp64\www\studenthub`
2. Ensure write permissions exist on `uploads/`:
   ```bash
   chmod 755 studenthub/uploads
   ```
3. Open your browser and navigate to:
   ```text
   http://localhost/studenthub/
   ```

### Step 4: Default Test Credentials

| Role | Email | Password | Access Scope |
| :--- | :--- | :--- | :--- |
| **System Administrator** | `admin@charusat.edu.in` | `Admin@12345` | Full administrative control, student CRUD, event creation/flyers, user toggles, audit logs |
| **Student** | `student@charusat.edu.in` | `Student@12345` | Personal dashboard, browse upcoming events, register for events, submit feedback |

---

## 6. Functional Verification and Feature Tour

### 1. Public Landing & Navigation
- The landing page (`index.php`) features a responsive hero banner, quick statistics counter, upcoming event cards with dynamic status badges, and quick links.
- The top navigation adapts dynamically based on session status: guests see *Login* and *Register*; logged-in students see *Dashboard* and *My Events*; administrators see *Admin Console*, *Students*, *Events*, *Users*, and *Audit Logs*.

### 2. User Authentication & Multi-Role Access Control
- Forms implement real-time validation (email regex, password length, confirmation match).
- Passwords are submitted via POST, validated against CSRF tokens, and verified using `password_verify()`.
- Unauthorized attempts to access administrative pages (e.g. `students.php`) immediately trigger an HTTP 403 access denial or redirect to `login.php`.

### 3. Student Record CRUD Management
- Navigating to `students.php` displays a paginated data table with real-time search and filter controls.
- Administrators can add new students, update details (GPA, semester, department, active status), soft-delete records, and export filtered datasets directly to CSV.

### 4. Event Management & Secure File Uploads
- In `admin_events.php`, administrators create events and upload image flyers.
- The server inspects binary magic bytes, rejects non-image uploads (even if renamed to `.jpg`), generates an unguessable filename, and stores it in `uploads/`.

### 5. Asynchronous User Status Management
- In `admin_users.php`, account status toggles (`active` vs `inactive`) can be switched instantly without full page reloads via asynchronous Fetch requests.

### 6. Interactive Visual Analytics & Audit Logging
- `admin_dashboard.php` calculates live metrics: Total Students, Active Events, Total Registrations, and Active Accounts.
- An HTML5 Canvas element renders an animated bar chart illustrating department distribution.
- The security audit log (`audit_logs.php`) tracks every login, registration, record creation, and status toggle with IP addresses and timestamps.

---

## 7. Learning Outcomes Achieved

Upon completion of this capstone practical, the following engineering competencies have been demonstrated:
1. **Architectural Cohesion:** Successfully integrating disparate web technologies into a unified, secure application architecture.
2. **Defensive Web Development:** Implementing defense-in-depth principles addressing the OWASP Top 10 vulnerabilities (SQL Injection, XSS, CSRF, Session Hijacking, Insecure File Uploads).
3. **Database Normalization:** Designing 3NF relational schemas with referential integrity constraints and transaction support.
4. **Accessible Front-End Engineering:** Developing semantic, responsive, mobile-first interfaces compliant with WCAG 2.1 AA standards.
5. **Operational Readiness:** Packaging web applications with structured documentation, migration scripts, and deployment guidelines for production environments.
