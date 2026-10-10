# Practical 11: Student Management CRUD with Search, Filter, and Prepared MySQL Statements
**Course:** Web Development Frameworks (ITUE203)  
**Academic Term:** 3rd Semester, 2026-27 (ODD)  
**Institution:** Faculty of Technology & Engineering (FTE), CHARUSAT  
**CO Mapping:** CO1, CO4, CO5  
**PO Mapping:** PO1(3), PO2(3), PO3(3), PO4(3), PO5(3), PO9(1), PO10(3), PO11(2), PO12(3)

---

## 🎯 1. Problem Definition
Build an administrative Student Management CRUD module for **StudentHub**:
1. Implement full data operations (**Create, Read, Update, Delete**) powered by **PDO Prepared Statements**.
2. Support live multi-criteria search by student name, roll number, or institutional email address.
3. Provide multi-level filtering by academic department and account status.
4. Integrate paginated result sets (8 records per page) with record count metrics.
5. Provide safe deletion with interactive JavaScript confirmation dialogs (`confirm()`).
6. Deliver CSV export functionality ([`export_csv.php`](./export_csv.php)) streaming filtered datasets directly to the browser.
7. Implement **Soft Deletion** using a `status` column (`'active'`, `'inactive'`, `'deleted'`), allowing accidental deletions to be safely restored.

---

## 💡 2. Key Questions, Analysis & Viva Answers

### Q1: Are SELECT, INSERT, UPDATE, and DELETE operations properly implemented and secure?
**Answer:**  
Yes, all four core operations utilize parameterized PDO statements:
1. **SELECT (Read):** Dynamic paginated retrieval in [`index.php`](./index.php) using `LIMIT :limit OFFSET :offset` with bound integer parameters.
2. **INSERT (Create):** In [`add_student.php`](./add_student.php), validates inputs before executing `INSERT INTO students (...) VALUES (...)`.
3. **UPDATE (Edit):** In [`edit_student.php`](./edit_student.php), pre-populates existing data and persists modifications safely with parameter binding.
4. **DELETE (Delete/Soft-Delete):** In [`delete_student.php`](./delete_student.php), soft-deletes via `UPDATE students SET status = 'deleted' WHERE id = :id` or hard-deletes if explicitly invoked.

---

### Q2: How does the search and filter query execute without SQL injection risks?
**Answer:**  
In [`index.php`](./index.php), search clauses are constructed programmatically:
```php
if ($search !== '') {
    $whereClauses[] = "(roll_number LIKE :s1 OR full_name LIKE :s2 OR email LIKE :s3)";
    $params[':s1'] = "%{$search}%";
    $params[':s2'] = "%{$search}%";
    $params[':s3'] = "%{$search}%";
}
```
Because placeholders (`:s1`, `:s2`, `:s3`) are passed directly to `PDOStatement::execute($params)`, user input is never concatenated into raw SQL strings, neutralizing SQL injection vectors.

---

### Q3: Why is Soft Deletion superior to permanent deletion?
**Answer:**  
1. **Referential Integrity Preservation:** Hard deletion of a student record causes foreign key conflicts in dependent tables (such as `registrations` or `audit_logs`). Soft-deletion leaves foreign key relationships intact.
2. **Accidental Deletion Recovery:** If an administrator mistakenly removes a student, the record is not lost. The student can be filtered under the "Deleted" tab and restored with one click (`UPDATE students SET status = 'active'`).
3. **Historical Auditing:** Regulatory compliance often requires academic institutions to retain audit trails of historical registrations.

---

### Q4: How does the CSV Export feature stream data efficiently?
**Answer:**  
[`export_csv.php`](./export_csv.php) uses `fopen('php://output', 'w')` combined with HTTP streaming headers (`Content-Type: text/csv`). Instead of generating a temporary file on the server's hard drive, data is written directly to the HTTP response stream using `fputcsv()`, minimizing server disk I/O and RAM overhead.

---

## 📂 3. Directory Layout for Practical 11

```text
Practical-11/
├── README.md           # Practical documentation, theory, and answers
├── students.sql        # Database seed SQL with 22 student profiles
├── index.php           # Master student directory with search, filter, and pagination
├── add_student.php     # Student creation interface with validation
├── edit_student.php    # Student update interface with pre-populated values
├── delete_student.php  # Advanced Extension: Soft delete and restore controller
├── export_csv.php      # Intermediate Extension: Filtered CSV streaming download
├── db.php              # Secure PDO database connector
└── css/
    └── style.css       # Clean table, badge, and form styling
```

---

## 🌟 4. Extensions Implemented
- **Intermediate Extension:** Direct HTTP **CSV Streaming Export** ([`export_csv.php`](./export_csv.php)) respecting active search and department filters.
- **Advanced Extension:** **Soft Deletion Architecture** using `status = 'deleted'` with instant **One-Click Restore** and JavaScript confirmation safeguards.
