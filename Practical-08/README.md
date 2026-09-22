# Practical 8: MySQL Schema Design, ER Model, PDO Connectivity, and Prepared Statements
**Course:** Web Development Frameworks (ITUE203)  
**Academic Term:** 3rd Semester, 2026-27 (ODD)  
**Institution:** Faculty of Technology & Engineering (FTE), CHARUSAT  
**CO Mapping:** CO1, CO5  
**PO Mapping:** PO1(3), PO2(3), PO3(3), PO4(3), PO5(3), PO9(1), PO10(3), PO11(2), PO12(3)

---

## 🎯 1. Problem Definition
Design, normalize, and implement a relational database architecture for the **StudentHub** platform:
1. Normalize database entities (`users`, `students`, `events`, `registrations`, `auth_tokens`, `audit_logs`) up to **Third Normal Form (3NF)**.
2. Formulate explicit Primary Keys (`PK`), Unique Constraints (`UK`), and Foreign Key constraints (`FK`) with `ON DELETE CASCADE` and `ON DELETE SET NULL` cascade actions.
3. Establish a secure **PHP PDO** database singleton connection class ([`db.php`](./db.php)) enforcing `PDO::ERRMODE_EXCEPTION`, native prepared statements (`ATTR_EMULATE_PREPARES => false`), and `utf8mb4` encoding.
4. Provide comprehensive SQL seed scripts ([`seed_data.sql`](./seed_data.sql)) with 20+ realistic student records and active events.
5. Implement advanced database routines ([`stored_procedures.sql`](./stored_procedures.sql)) including `sp_register_student_event`, `sp_get_event_attendees`, `sp_get_dashboard_metrics`, and database views.
6. Build an interactive diagnostic script ([`test_connection.php`](./test_connection.php)) validating database health and prepared statements.

---

## 💡 2. Key Questions, Analysis & Viva Answers

### Q1: Is the schema normalized properly (1NF, 2NF, 3NF)?
**Answer:**  
Yes, the schema strictly adheres to third normal form principles:
- **1NF (Atomicity):** Every table attribute stores scalar, indivisible values. No multi-valued repeating groups or comma-separated lists exist. Each table is uniquely identified by an unsigned surrogate primary key.
- **2NF (No Partial Dependency):** All non-key attributes depend fully on the complete primary key. In the `registrations` junction table, event attributes (such as `venue`, `event_date`) reside strictly in `events`, and student attributes reside in `students`.
- **3NF (No Transitive Dependency):** No non-key attribute depends on another non-key attribute. For example, student department contact information is not duplicated within registrations; registrations store only foreign keys `student_id` and `event_id`.

---

### Q2: Are primary keys and foreign keys correctly defined?
**Answer:**  
- **Primary Keys:** Every table features `id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY`.
- **Foreign Keys:**
  - `students.user_id` $\to$ `users.id` with `ON DELETE CASCADE ON UPDATE CASCADE`.
  - `registrations.student_id` $\to$ `students.id` with `ON DELETE CASCADE`.
  - `registrations.event_id` $\to$ `events.id` with `ON DELETE CASCADE`.
  - `audit_logs.user_id` $\to$ `users.id` with `ON DELETE SET NULL` (preserving compliance audit trails even if a user account is removed).
- **Uniqueness Safeguards:** A composite unique key `CONSTRAINT uk_student_event UNIQUE (student_id, event_id)` prevents a student from enrolling in the same event multiple times.

---

### Q3: Why is PDO preferred over legacy MySQLi, and how is it secured?
**Answer:**  
1. **Prepared Statements with Parameter Binding:** PDO separates the SQL query parsing phase from data transmission. When executing `$stmt = $pdo->prepare("SELECT * FROM students WHERE email = :email"); $stmt->execute([':email' => $userInput]);`, the database engine treats `:email` purely as raw literal data, rendering SQL Injection mathematically impossible.
2. **Native Prepared Statements:** `PDO::ATTR_EMULATE_PREPARES => false` ensures that query preparation occurs natively within the MySQL engine rather than PHP client-side string replacement.
3. **Database Portability:** PDO offers a unified data-access interface supporting MySQL, PostgreSQL, SQLite, and Oracle through identical method calls (`prepare()`, `execute()`, `fetch()`).
4. **Exception Handling:** `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION` replaces silent failures with catchable `PDOException` instances.

---

## 📂 3. Directory Layout for Practical 8

```text
Practical-08/
├── README.md               # Practical documentation, theory, and answers
├── schema.sql              # 3NF normalized DDL statements (6 relational tables)
├── seed_data.sql           # Seed script with 20+ students and campus events
├── stored_procedures.sql   # Advanced Extension: Stored procedures & views
├── db.php                  # Secure PDO singleton database connection class
├── test_connection.php     # Diagnostics web interface testing PDO & prepared queries
└── er_diagram.md           # Entity-Relationship diagram & cardinality documentation
```

---

## 🌟 4. Extensions Implemented
- **Intermediate Extension:** Robust **PHP PDO Connection** with strict exception management and parameterized prepared statements.
- **Advanced Extension:** Enterprise-grade **Stored Procedures** (`sp_register_student_event` with capacity verification and ticket generation, `sp_get_dashboard_metrics`), and relational database views (`v_active_registrations`).
