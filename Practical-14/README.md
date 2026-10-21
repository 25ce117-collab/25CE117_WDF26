# Practical 14: Admin Dashboard with Role-Based Access Control, Dynamic Menu, and Audit Log
**Course:** Web Development Frameworks (ITUE203)  
**Academic Term:** 3rd Semester, 2026-27 (ODD)  
**Institution:** Faculty of Technology & Engineering (FTE), CHARUSAT  
**CO Mapping:** CO1, CO4, CO5  
**PO Mapping:** PO1(3), PO2(3), PO3(3), PO4(3), PO5(3), PO9(1), PO10(3), PO11(2)

---

## 🎯 1. Problem Definition
Develop an administrative governance and analytics dashboard for **StudentHub**:
1. Dynamically compute and display system-wide KPI metrics (Active Students, Scheduled Events, Total Registrations, Active Accounts) via high-performance **SQL Aggregation** (`COUNT()`).
2. Restrict dashboard access exclusively to users with the `admin` role.
3. Generate **Dynamic Role-Based Navigation Menus** derived from session credentials.
4. Render an interactive **HTML5 Canvas Analytics Chart** plotting monthly registration trajectories.
5. Provide an AJAX-powered **Active / Inactive User Status Toggle** ([`toggle_user.php`](./toggle_user.php)) updating states instantly without page refreshes.
6. Record all critical administrative actions in the immutable `audit_logs` table.
7. Build a dedicated **Audit Trail Viewer** ([`audit_logs.php`](./audit_logs.php)) inspecting timestamps, admin identifiers, action names, and client IP addresses.

---

## 💡 2. Key Questions, Analysis & Viva Answers

### Q1: How are dashboard values computed dynamically from the database?
**Answer:**  
In [`dashboard.php`](./dashboard.php), aggregated statistical figures are queried directly from the MySQL engine:
```php
$totalStudents = (int)$pdo->query("SELECT COUNT(*) FROM students WHERE status != 'deleted'")->fetchColumn();
$totalEvents   = (int)$pdo->query("SELECT COUNT(*) FROM events WHERE status != 'cancelled'")->fetchColumn();
$totalRegs     = (int)$pdo->query("SELECT COUNT(*) FROM registrations")->fetchColumn();
$activeUsers   = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE status = 'active'")->fetchColumn();
```
SQL aggregation computes metrics at the database engine level, avoiding the overhead of transferring entire datasets into PHP memory.

---

### Q2: How do dynamic menus adapt based on the user's role?
**Answer:**  
The server-side layout inspects `$_SESSION['role']`. If the authenticated identity is `admin`, the navigation structure exposes administrative routes (`dashboard.php`, `users.php`, `audit_logs.php`). If the identity is `student`, the menu renders student-specific modules (`student_dashboard.php`, `events.php`).

---

### Q3: Why is audit logging critical in modern web applications?
**Answer:**  
1. **Security Traceability:** If an account is deactivated or an event cancelled, the audit trail documents who initiated the action, their IP address, and the exact timestamp.
2. **Accountability & Forensics:** Post-incident forensics can trace brute-force attempts or privilege abuse back to specific session identifiers.
3. **Regulatory Compliance:** Educational institutions must prove data governance and adhere to standards for user privacy and credential handling.

---

## 📂 3. Directory Layout for Practical 14

```text
Practical-14/
├── README.md           # Practical documentation, theory, and answers
├── dashboard.php       # KPI metrics, dynamic menu & latest registrations
├── users.php           # User governance table with instant AJAX toggle
├── toggle_user.php     # Intermediate Extension: AJAX endpoint updating user state & audit log
├── audit_logs.php      # Security audit trail viewer with client IP tracking
├── api/
│   └── chart_data.php  # Advanced Extension: JSON API for chart analytics
├── js/
│   └── dashboard.js    # Canvas chart renderer & AJAX event bindings
├── db.php              # Secure PDO database connector
└── css/
    └── style.css       # Administrative console styling & KPI cards
```

---

## 🌟 4. Extensions Implemented
- **Intermediate Extension:** **AJAX User Status Toggle** allowing administrators to activate or deactivate accounts with zero page reload while appending an audit trail entry.
- **Advanced Extension:** Client-side **HTML5 Canvas Analytics Chart** plotting registration volumes across the Odd Semester with custom gradients and numerical callouts.
