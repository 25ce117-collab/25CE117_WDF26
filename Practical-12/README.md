# Practical 12: Event Management CRUD with Poster Upload and File Validation
**Course:** Web Development Frameworks (ITUE203)  
**Academic Term:** 3rd Semester, 2026-27 (ODD)  
**Institution:** Faculty of Technology & Engineering (FTE), CHARUSAT  
**CO Mapping:** CO1, CO4, CO5  
**PO Mapping:** PO1(3), PO2(3), PO3(3), PO4(3), PO5(3), PO9(1), PO10(3), PO11(2), PO12(3)

---

## 🎯 1. Problem Definition
Develop an administrative Event Management CRUD module with secure file upload handling for **StudentHub**:
1. Implement full database operations (**Create, Read, Update, Delete**) for campus workshops, hackathons, and symposiums.
2. Build a file upload pipeline with strict server-side validation:
   - Size limitation: Maximum 2 MB (`2,097,152` bytes).
   - Strict MIME type verification using `finfo_file()` (permitting only `image/jpeg`, `image/png`, `image/webp`).
   - Cryptographic renaming (`event_HEX_TIME.ext`) preventing directory traversal and filename collisions.
3. Secure the file upload directory via an Apache [`.htaccess`](./uploads/.htaccess) configuration file disabling script execution.
4. Support multi-criteria filtering by event category and lifecycle status (**Upcoming / Open**, **Ongoing**, **Completed / Closed**, **Cancelled**).
5. Clean up old poster files from the filesystem via `unlink()` upon replacement or event deletion.
6. Record audit entries in `audit_logs` tracking event creation and deletion.

---

## 💡 2. Key Questions, Analysis & Viva Answers

### Q1: Why is validating `$_FILES['poster']['type']` alone dangerous, and how does `finfo_file()` solve it?
**Answer:**  
- **Vulnerability:** The client-provided `$_FILES['file']['type']` header is easily manipulated via HTTP proxies (such as Burp Suite) or custom curl scripts. An attacker can upload a malicious script `shell.php` and set the HTTP `Content-Type: image/jpeg`.
- **Solution:** In [`add_event.php`](./add_event.php), we inspect the binary file magic bytes directly on the server filesystem using PHP's Fileinfo extension:
  ```php
  $finfo = finfo_open(FILEINFO_MIME_TYPE);
  $mimeType = finfo_file($finfo, $file['tmp_name']);
  finfo_close($finfo);
  ```
  `finfo_file()` reads the actual binary header of the uploaded file regardless of its filename, guaranteeing authentic image formats.

---

### Q2: Why is the `.htaccess` configuration in the `uploads/` folder vital?
**Answer:**  
Even if a file bypasses validation (e.g. polyglot file containing PHP code hidden inside EXIF metadata), the web server could execute it if a user visits its URL. Our [`uploads/.htaccess`](./uploads/.htaccess) disables PHP engines completely:
```apache
php_flag engine off
<FilesMatch "\.(php|phtml|phar|sh|pl|cgi|exe)$">
    Deny from all
</FilesMatch>
```
This renders Remote Code Execution (RCE) impossible, treating all files in the directory strictly as static binary data.

---

### Q3: How does image renaming eliminate directory traversal attacks?
**Answer:**  
Attackers frequently submit malicious filenames such as `../../etc/passwd` or `../../../index.php`. By discarding the original client filename completely and generating a randomized unique name:
```php
$uniqueFileName = 'event_' . bin2hex(random_bytes(8)) . '_' . time() . '.' . $ext;
```
the system completely neutralizes directory traversal and prevents overwriting existing assets.

---

## 📂 3. Directory Layout for Practical 12

```text
Practical-12/
├── README.md           # Practical documentation, theory, and answers
├── events.sql          # Seed dataset with 6 diverse campus events
├── events.php          # Event management dashboard with category & status filters
├── add_event.php       # Event creation form with validated poster upload
├── edit_event.php      # Event editor with replacement upload logic
├── delete_event.php    # Deletion controller with file unlinking & audit logging
├── db.php              # Secure PDO connection helper
└── uploads/
    ├── .htaccess       # Advanced Extension: Apache script execution guard
    └── posters/        # Secure target directory for event posters
```

---

## 🌟 4. Extensions Implemented
- **Intermediate Extension:** Multi-parameter **Category and Open/Closed Status Filter** in [`events.php`](./events.php).
- **Advanced Extension:** **Cryptographic Image Renaming**, **Upload Directory Hardening** (`.htaccess`), and **Audit Trail Logging** for event creation and destruction.
