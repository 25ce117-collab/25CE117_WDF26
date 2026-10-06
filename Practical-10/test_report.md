# Practical 10: Authentication & Session Security Test Report
## StudentHub - RBAC, Session Hijacking Prevention & Remember-Me
**Course:** ITUE203 Web Development Frameworks

---

## 🧪 Comprehensive Authentication Test Cases

| Scenario ID | Test Condition | Input Data / Action | Expected Result | Actual Result | Status |
|:---:|:---|:---|:---|:---|:---:|
| **AUTH-01** | Valid Admin Authentication | `admin@charusat.edu.in` / `Admin@123` | Password verified; session regenerated; redirected to `admin_dashboard.php`; last_login updated | Directed to Admin Dashboard | ✅ Pass |
| **AUTH-02** | Valid Student Authentication | `23it042@charusat.edu.in` / `Student@123` | Password verified; redirected to `student_dashboard.php` | Directed to Student Dashboard | ✅ Pass |
| **AUTH-03** | Invalid Password Attempt | Correct email, wrong password `WrongPass99` | Verification fails; error banner "Invalid credentials" displayed | Login rejected with error | ✅ Pass |
| **AUTH-04** | Unauthorized Dashboard Access | Authenticated student visits `admin_dashboard.php` | Route guard detects `role !== 'admin'`; intercepts and redirects with unauthorized error | Access blocked | ✅ Pass |
| **AUTH-05** | Unauthenticated Direct Route | Unauthenticated guest visits `student_dashboard.php` | Redirected to `login.php?msg=login_required` | Redirected to login | ✅ Pass |
| **AUTH-06** | Session Inactivity Timeout | Idle for > 15 minutes (900 seconds) | Session destroyed; redirected to `login.php?msg=timeout` | Session expired cleanly | ✅ Pass |
| **AUTH-07** | Session Fixation Prevention | Compare session ID before and after login | `session_regenerate_id(true)` issues brand new ID, destroying old session token | New ID generated | ✅ Pass |
| **AUTH-08** | Remember-Me Persistent Login | Check "Remember Me", close browser, reopen dashboard | Cookie contains raw token; matched against SHA-256 hash in `auth_tokens`; auto-logged in | Persistent login verified | ✅ Pass |
| **AUTH-09** | Secure Logout | Click "Log Out" | Session destroyed; database token deleted; cookies cleared; redirected to login | Clean state, tokens purged | ✅ Pass |
