# Practical 9: Secure User Registration with Database Insert, Duplicate Email Check, and Password Hashing
**Course:** Web Development Frameworks (ITUE203)  
**Academic Term:** 3rd Semester, 2026-27 (ODD)  
**Institution:** Faculty of Technology & Engineering (FTE), CHARUSAT  
**CO Mapping:** CO1, CO3, CO5  
**PO Mapping:** PO1(3), PO2(3), PO3(3), PO4(3), PO5(3), PO9(1), PO10(3), PO11(2), PO12(3)

---

## 🎯 1. Problem Definition
Implement a secure, multi-tier user registration module for the **StudentHub** platform:
1. Validate inputs consistently across both client-side and server-side boundaries.
2. Query the database using **PDO Prepared Statements** to verify email and username uniqueness before inserting.
3. Cryptographically hash passwords using PHP's native `password_hash()` with the Bcrypt algorithm (cost factor 12) and automatic per-user salt generation.
4. Execute user and profile creation inside an ACID-compliant database transaction (`beginTransaction()`, `commit()`, `rollBack()`).
5. Protect against Cross-Site Request Forgery (**CSRF**) using one-time cryptographic tokens.
6. Record an immutable audit log entry in the `audit_logs` table detailing IP address, timestamp, and account creation events.

---

## 💡 2. Key Questions, Analysis & Viva Answers

### Q1: Why must `password_hash()` be used instead of legacy hashing functions like MD5 or SHA-256?
**Answer:**  
1. **Computational Speed vs. Security:** Fast cryptographic hashes like MD5 and SHA-256 were designed for integrity checking, not password hashing. Modern GPUs can calculate billions of SHA-256 hashes per second, making brute-force and dictionary attacks trivially fast.
2. **Key Stretching & Work Factor:** `password_hash()` utilizes **Bcrypt** (`PASSWORD_BCRYPT`), an adaptive hash function based on the Blowfish cipher. It incorporates an algorithmic work factor (`cost => 12`), requiring $2^{12} = 4,096$ iterations. This deliberately slows down computation to thwart hardware attacks.
3. **Automatic Cryptographic Salting:** `password_hash()` generates a unique 128-bit cryptographically secure random salt for every single password. Even if two users choose the identical password `Student@123`, their resulting stored hashes will be completely different, defeating pre-computed Rainbow Table attacks.

---

### Q2: How is duplicate email and username verification executed securely?
**Answer:**  
In [`register.php`](./register.php):
```php
$checkStmt = $pdo->prepare("SELECT email, username FROM users WHERE email = :email OR username = :username LIMIT 1");
$checkStmt->execute([':email' => $email, ':username' => $username]);
$existing = $checkStmt->fetch();
```
By utilizing prepared statements, the check executes safely without SQL injection vulnerabilities. If a match is found, specific error messages notify the user without revealing sensitive internal primary keys.

---

### Q3: Why is maintaining consistency between frontend and backend validation critical?
**Answer:**  
- **Frontend Validation (User Experience):** Instant feedback guides users to correct formatting errors (e.g. invalid phone number length or weak password) before submitting network requests.
- **Backend Validation (Security Authority):** Client-side scripts can be bypassed via direct API calls (`curl`, Postman, or disabled browser JavaScript). The server is the ultimate gatekeeper and must re-validate all business constraints (`preg_match`, `filter_var`) before database insertion.

---

## 📂 3. Directory Layout for Practical 9

```text
Practical-09/
├── README.md           # Practical documentation, theory, and answers
├── test_report.md      # Security test matrix (duplicate checks, SQLi, CSRF)
├── register.php        # Registration page with Bcrypt hashing & transactions
├── csrf.php            # CSRF token generator & validator
├── db.php              # Secure PDO database connector
├── schema.sql          # Table definitions for users, students & audit_logs
└── css/
    └── style.css       # Clean registration UI & alert styling
```

---

## 🌟 4. Extensions Implemented
- **Intermediate Extension:** Full implementation using **PDO Prepared Statements** for all database queries and transactions.
- **Advanced Extension:** **CSRF Token Validation** combined with automated **Audit Log Logging** into the `audit_logs` table recording user creation events with IP tracking.
