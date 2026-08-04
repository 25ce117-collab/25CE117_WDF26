# Practical 1: Software Requirements Specification (SRS)
## StudentHub - Campus Portal System
**Course:** Web Development Frameworks (ITUE203)  
**Academic Year:** 2026-27 (Semester 3)  
**Institution:** Faculty of Technology & Engineering (FTE), CHARUSAT

---

## 1. Introduction & Problem Scope

### 1.1 Purpose
The **StudentHub Portal** addresses the fragmentation of campus information services. Traditionally, academic announcements, extracurricular event registrations, student club activities, and student directory services are dispersed across notice boards, disparate social groups, and static bulletin boards. StudentHub centralizes these essential workflows into a unified, accessible, and responsive web application.

### 1.2 User Roles & Personas
1. **Student User:**
   - Registers for an account, verifies personal and academic details.
   - Discovers upcoming campus events, workshops, technical symposiums, and webinars.
   - Registers for events in a single click, viewing registered tickets and history.
   - Searches the student directory to collaborate on peer projects.
   - Provides feedback and accesses FAQ knowledge base.

2. **Faculty / Event Coordinator:**
   - Publishes new events with promotional posters, registration deadlines, and seat caps.
   - Moderates event participation and views registration rosters.
   - Downloads attendee lists in CSV format for attendance tracking.

3. **System Administrator:**
   - Oversees role-based access control (RBAC) and user account statuses (Active/Suspended).
   - Monitors live system metrics (total students, active events, monthly registrations).
   - Inspects immutable security audit logs tracking critical actions.
   - Manages global taxonomy (categories, departments, batches).

---

## 2. Key Modules & Functional Requirements

### Module 1: Authentication & Access Control
- **FR-1.1:** Secure user registration with email uniqueness validation and password hashing (Bcrypt).
- **FR-1.2:** Login authentication with session regeneration to prevent session fixation.
- **FR-1.3:** Role-Based Access Control (RBAC) redirecting administrators to `/admin_dashboard.php` and students to `/student_dashboard.php`.
- **FR-1.4:** Session inactivity timeout (15 minutes) and cryptographically secure Remember-Me token authentication using database-stored SHA-256 token hashes.

### Module 2: Student Directory & Profile Management
- **FR-2.1:** Student directory with live multi-criteria search (by name, roll number, department) and pagination.
- **FR-2.2:** Full CRUD capabilities for administrators to manage student records with client- and server-side validation.
- **FR-2.3:** Soft-deletion mechanism preserving referential integrity in registrations.
- **FR-2.4:** Instant CSV export of student datasets.

### Module 3: Event & Workshop Management
- **FR-3.1:** Event publishing interface with validated poster image upload (MIME type verification, size limit <= 2MB, sanitized unique filename).
- **FR-3.2:** Event status lifecycle: Upcoming, Ongoing, Closed, Cancelled.
- **FR-3.3:** Real-time seat reservation with race-condition prevention and capacity checks.
- **FR-3.4:** Interactive event gallery with responsive grid layout, category filters, and modal details view.

### Module 4: Asynchronous RESTful JSON API
- **FR-4.1:** REST-compliant endpoints (`GET`, `POST`, `PUT`, `DELETE`) delivering JSON payloads with RFC-standard HTTP status codes (200, 201, 400, 404, 500).
- **FR-4.2:** Single-page asynchronous client (AJAX / Fetch API) updating UI components without full page reload.
- **FR-4.3:** Live search debouncing (300ms) to conserve server resources.

### Module 5: Admin Analytics & Security Audit Trail
- **FR-5.1:** Dynamic metric counters: Total Students, Total Events, Total Registrations, and Active Users.
- **FR-5.2:** Visual analytics charts for monthly registrations and category breakdown.
- **FR-5.3:** Comprehensive audit trail recording user ID, action type, client IP address, and timestamp for all destructive operations.

---

## 3. Minimum 10 Required Pages & Navigation Flow

1. `index.html / index.php`: Public Landing & Home Page (Featured events, quick statistics, mission statement).
2. `about.html / about.php`: About StudentHub, campus partners, leadership team, and institutional accreditation.
3. `register.html / register.php`: Student registration form with real-time Regex validation, password strength meter, and Canvas CAPTCHA.
4. `login.html / login.php`: Secure authentication form with Remember-Me checkbox and session redirect.
5. `student_dashboard.php`: Student personal hub showing enrolled events, profile summary, and quick links.
6. `events.php`: Public and student event catalog with search, category filtering, and modal detail views.
7. `profile.php`: Student personal profile editor with password change and details update.
8. `contact.php`: Contact form with server-side validation and storage.
9. `admin_dashboard.php`: Administrator portal featuring analytics cards, quick actions, and recent activity.
10. `faq.html / faq.php`: Interactive accordion FAQ knowledge base with keyboard accessibility.
11. `feedback.php`: Campus experience rating and feedback submission module.

---

## 4. Non-Functional Requirements (NFR)
- **Usability & Accessibility:** Compliance with WCAG 2.1 AA standards; full keyboard navigation, skip-to-content links, ARIA landmarks, and high contrast ratios.
- **Performance:** Initial page load under 1.5 seconds; asynchronous queries respond within 200ms.
- **Security:** Protection against SQL Injection (PDO Prepared Statements), XSS (`htmlspecialchars`), CSRF (cryptographic session tokens), and directory traversal.
- **Cross-Device Responsiveness:** Fluid adaptation across Mobile (<576px), Tablet (576px-992px), and Desktop (>992px) using CSS Flexbox and Grid.
