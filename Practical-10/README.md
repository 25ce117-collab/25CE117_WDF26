# Practical 10: Secure Login/Logout with Sessions, Role-Based Access, Timeout, and Remember-Me Token
**Course:** Web Development Frameworks (ITUE203)  
**Academic Term:** 3rd Semester, 2026-27 (ODD)  
**Institution:** Faculty of Technology & Engineering (FTE), CHARUSAT  
**CO Mapping:** CO1, CO5  
**PO Mapping:** PO1(3), PO2(3), PO3(3), PO4(3), PO5(3), PO9(1), PO10(3), PO11(2), PO12(3)

---

## 🎯 1. Problem Definition
Develop an enterprise-grade authentication and session governance framework for the **StudentHub** platform:
1. Authenticate users using PHP's native `password_verify()` against salted Bcrypt password hashes.
2. Defend against **Session Fixation** by regenerating session identifiers upon authentication via `session_regenerate_id(true)`.
3. Enforce **Role-Based Access Control (RBAC)** redirecting administrators to [`admin_dashboard.php`](./admin_dashboard.php) and students to [`student_dashboard.php`](./student_dashboard.php).
4. Implement a 15-minute (900-second) **Inactivity Timeout** terminating abandoned sessions.
5. Record the user's `last_login` timestamp dynamically in the database upon successful sign-in.
6. Build a secure **Remember-Me** persistent authentication system that avoids custom JWTs by storing a cryptographically random token hash (SHA-256) in the database and passing the raw token via an `HttpOnly`, `SameSite` cookie.
7. Implement complete, secure logout procedures purging database tokens, destroying server sessions, and expiring client cookies.

---

## 💡 2. Key Questions, Analysis & Viva Answers

### Q1: How are sessions started and destroyed securely?
**Answer:**  
- **Secure Starting:** Session parameters are hardened via `session_set_cookie_params()` before calling `session_start()`:
  - `httponly => true`: Denies JavaScript access to `PHPSESSID`, eliminating document cookie extraction via XSS.
  - `samesite => 'Lax'`: Protects against Cross-Site Request Forgery during external link navigation.
- **Fixation Defense:** Immediately after credential verification, `session_regenerate_id(true)` invalidates the unauthenticated pre-login session ID and issues a brand-new cryptographically random ID.
- **Secure Destruction:** In [`logout.php`](./logout.php), `$_SESSION` is emptied, session cookies are overwritten with a past timestamp (`time() - 42000`), persistent cookies are cleared, and `session_destroy()` invalidates the session file on the web server.

---

### Q2: How is `password_verify()` used correctly?
**Answer:**  
`password_verify($password, $user['password_hash'])` extracts the algorithmic salt and work factor embedded directly within the 60-character Bcrypt hash. It hashes the incoming plaintext password using those identical parameters and executes a constant-time comparison against the stored hash, eliminating timing attack vectors.

---

### Q3: How is unauthorized dashboard access blocked (RBAC)?
**Answer:**  
The central middleware function `checkAuth($requiredRole)` in [`auth_check.php`](./auth_check.php) acts as a strict route guard. If an unauthenticated user or an authenticated student tries to open `admin_dashboard.php`, the condition:
```php
if ($requiredRole !== null && $_SESSION['role'] !== $requiredRole) {
    header('Location: login.php?msg=unauthorized');
    exit;
}
```
immediately blocks execution and redirects the unauthorized user to the login screen.

---

### Q4: How is the 15-minute session timeout implemented?
**Answer:**  
Each authenticated request records the current timestamp: `$_SESSION['last_activity'] = time()`. On every subsequent request, the elapsed time is evaluated:
```php
if (($now - $_SESSION['last_activity']) > 900) {
    session_unset();
    session_destroy();
    header('Location: login.php?msg=timeout');
    exit;
}
```
If more than 900 seconds (15 minutes) have elapsed without user interaction, the session is cleared.

---

### Q5: How does the Remember-Me system operate without insecure custom tokens?
**Answer:**  
1. When "Remember Me" is checked, PHP generates a 256-bit cryptographically secure random token: `$raw = bin2hex(random_bytes(32))`.
2. The server stores only its cryptographic SHA-256 hash in the database table `auth_tokens` (`token_hash = hash('sha256', $raw)`).
3. The raw token is stored in the user's browser in an `HttpOnly` cookie for 30 days.
4. If the database is compromised, the attacker only sees irreversible SHA-256 hashes and cannot authenticate. When the user returns, the server re-hashes the cookie token and verifies it against the database table.

---

## 📂 3. Directory Layout for Practical 10

```text
Practical-10/
├── README.md               # Practical documentation, theory, and answers
├── test_report.md          # Authentication test suite (RBAC, timeout, fixation)
├── login.php               # Login form, password_verify, last-login update
├── logout.php              # Session destruction, cookie purging, token revocation
├── auth_check.php          # Session guard middleware with 15-min timeout & auto-login
├── admin_dashboard.php     # Role-protected Admin Dashboard
├── student_dashboard.php   # Role-protected Student Dashboard
├── db.php                  # PDO database connection provider
├── schema.sql              # Users and auth_tokens relational tables
└── css/
    └── style.css           # Authentication UI & dashboard styles
```

---

## 🌟 4. Extensions Implemented
- **Intermediate Extension:** Automated recording of the **Last-Login Timestamp** (`UPDATE users SET last_login = NOW() WHERE id = :id`) on successful verification.
- **Advanced Extension:** Enterprise-grade **Remember-Me Persistent Authentication** using high-entropy random tokens stored as SHA-256 hashes in MySQL, completely avoiding vulnerable or custom JWT implementations.
