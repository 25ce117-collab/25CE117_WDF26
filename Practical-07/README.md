# Practical 7: PHP Form Processing with Server-Side Validation and CSV/JSON File Storage
**Course:** Web Development Frameworks (ITUE203)  
**Academic Term:** 3rd Semester, 2026-27 (ODD)  
**Institution:** Faculty of Technology & Engineering (FTE), CHARUSAT  
**CO Mapping:** CO1, CO5  
**PO Mapping:** PO1(3), PO2(3), PO3(3), PO4(3), PO5(3), PO9(1), PO10(3), PO11(2), PO12(3)

---

## 🎯 1. Problem Definition
Develop a server-side PHP form processor that securely handles student pre-registration data:
1. Verify that incoming HTTP requests use the `POST` method.
2. Generate, embed, and validate cryptographically secure Cross-Site Request Forgery (**CSRF**) tokens.
3. Sanitize inputs (`filter_input()`, `htmlspecialchars()`, `trim()`).
4. Validate server-side business rules using Regular Expressions and type checks.
5. Persist submitted records safely into flat files:
   - Delimited CSV: [`data/registrations.csv`](./data/registrations.csv) using `fputcsv()`.
   - Structured JSON: [`data/registrations.json`](./data/registrations.json) using `json_encode()`.
6. Enforce mandatory file locking (`LOCK_EX` / `flock()`) to prevent race conditions during concurrent write operations.
7. Present user-friendly flash messages (success alerts or granular validation error lists).
8. Render stored records dynamically on a dedicated administrative report page ([`records.php`](./records.php)).

---

## 💡 2. Key Questions, Analysis & Viva Answers

### Q1: Why must the form be submitted using HTTP POST instead of GET?
**Answer:**  
1. **Payload Privacy:** HTTP `GET` appends input parameters directly onto the URL query string (`?fullName=John&phone=987...`), exposing sensitive details in browser history, proxy server logs, and Referer headers. `POST` transmits inputs within the HTTP message body.
2. **Idempotence & State Mutation:** Under HTTP/1.1 (RFC 7231), `GET` is an idempotent, read-only method. Since registration mutates server state by appending records to files, `POST` is semantically required.
3. **Payload Capacity:** URL lengths in `GET` are restricted by web servers and browsers (typically 2048 characters), whereas `POST` accommodates extensive payloads.

---

### Q2: How are inputs validated and sanitized server-side?
**Answer:**  
Never trust client-side data alone. In [`process.php`](./process.php):
- **Sanitization:** `filter_input(INPUT_POST, 'fullName', FILTER_SANITIZE_SPECIAL_CHARS)` strips or encodes control characters, neutralising reflected Cross-Site Scripting (XSS).
- **Validation:**
  - Name is verified with regex `preg_match('/^[A-Za-z\s]{3,50}$/', $fullName)`.
  - Email is strictly checked using PHP's native `filter_var($email, FILTER_VALIDATE_EMAIL)`.
  - Phone is verified against `^[6-9]\d{9}$`.
  - Dropdown values (department & event) are checked against a strict PHP whitelist array (`in_array($val, $whitelist)`), preventing unauthorized injection.

---

### Q3: How is file writing handled safely against race conditions?
**Answer:**  
In a multi-user web environment, two concurrent requests writing to the same file simultaneously cause data corruption and file truncation. To eliminate this:
1. **CSV Writing:** We acquire an advisory exclusive lock via `flock($fp, LOCK_EX)`. Other processes attempting to write block until the active process flushes data (`fflush($fp)`) and releases the lock (`flock($fp, LOCK_UN)`).
2. **JSON Writing:** We invoke `file_put_contents($jsonFile, $json, LOCK_EX)`, which internally enforces atomic exclusive locking during file writes.

---

### Q4: How does CSRF token protection operate?
**Answer:**  
1. In [`csrf.php`](./csrf.php), `getCsrfToken()` generates a 256-bit cryptographically secure pseudorandom token via `bin2hex(random_bytes(32))` and stores it in the user's `$_SESSION['csrf_token']`.
2. The form includes this token in a hidden input `<input type="hidden" name="csrf_token" value="...">`.
3. Upon form submission, `process.php` compares the posted token with the session token using `hash_equals()`, a timing-attack-safe string comparison. If an attacker tricks a user into submitting a forged request from an external site, the attacker cannot read the session token, and the request is immediately rejected.

---

## 📂 3. Directory Layout for Practical 7

```text
Practical-07/
├── README.md               # Practical documentation, theory, and answers
├── csrf.php                # CSRF token generator & validator
├── index.php               # Registration form with CSRF token & flash feedback
├── process.php             # POST processor, server validation, file lock writer
├── records.php             # Intermediate Extension: Web display of CSV & JSON records
├── css/
│   └── style.css           # Responsive form, alert banners, and table styling
└── data/
    ├── registrations.csv   # Safe delimited CSV repository (fputcsv + LOCK_EX)
    └── registrations.json  # Structured JSON repository (file_put_contents + LOCK_EX)
```

---

## 🌟 4. Extensions Implemented
- **Intermediate Extension:** [`records.php`](./records.php) provides an administrative dashboard rendering both CSV and JSON stored records in formatted HTML tables with direct download links.
- **Advanced Extension:** Full **CSRF Token Validation** preventing Cross-Site Request Forgery attacks, using 64-character hexadecimal cryptographically secure session tokens.
