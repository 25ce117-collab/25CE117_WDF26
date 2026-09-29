# Practical 9: User Registration Security Test Report
## StudentHub - Database Insert, Duplicate Checks & Password Hashing
**Course:** ITUE203 Web Development Frameworks

---

## 🧪 Security Test Scenarios

| Test Case | Objective | Input Parameters | Expected System Behavior | Actual Result | Status |
|:---|:---|:---|:---|:---|:---:|
| **SEC-01** | Valid Registration | `username: satya_p`, `email: new@charusat.edu.in`, `pass: Secure@2026!` | Record inserted into `users` and `students`; password stored as 60-char Bcrypt hash; audit log entry added | Successfully committed with transaction | ✅ Pass |
| **SEC-02** | Duplicate Email | `email: 23it042@charusat.edu.in` (Already exists) | Query detects duplicate email via prepared statement; aborts transaction with clear error message | "This email address is already registered" | ✅ Pass |
| **SEC-03** | Duplicate Username | `username: superadmin` (Already exists) | Prepared statement identifies matching username; prevents database collision | "This username is already taken" | ✅ Pass |
| **SEC-04** | Password Hashing Verification | Inspect database `password_hash` column | Raw password string never appears in database; begins with `$2y$12$...` | Verified 60-char Bcrypt string | ✅ Pass |
| **SEC-05** | SQL Injection in Email Field | `email: admin' OR '1'='1` | PDO Prepared statement treats input as literal string; regex rejects format; 0% injection vector | Rejected by email validator | ✅ Pass |
| **SEC-06** | CSRF Token Tampering | Submit form with altered or empty `csrf_token` | Request rejected before database query; session token mismatch reported | "Security verification failed" | ✅ Pass |
| **SEC-07** | Audit Log Traceability | Inspect `audit_logs` table after SEC-01 | New row with `action = 'USER_REGISTERED'`, client IP, user ID, and timestamp | Verified audit row created | ✅ Pass |

---

## 🔒 Security Best Practices Implemented
1. **Password Salt & Work Factor:** `password_hash(..., PASSWORD_BCRYPT, ['cost' => 12])` generates a unique cryptographically secure 128-bit salt per user and uses 4,096 hashing rounds, rendering rainbow table attacks completely ineffective.
2. **Database Transactions:** Insertion into `users`, `students`, and `audit_logs` is wrapped inside `$pdo->beginTransaction()` and `$pdo->commit()`. If an exception occurs, `$pdo->rollBack()` ensures no orphaned records exist.
