# Practical 8: Database Architecture, ER Model & Normalization Analysis
## StudentHub - Entity-Relationship Design Specification
**Course:** ITUE203 Web Development Frameworks

---

## 1. Entity-Relationship Diagram (Mermaid)

```mermaid
erDiagram
    USERS ||--o{ STUDENTS : "profiles"
    USERS ||--o{ AUTH_TOKENS : "owns"
    USERS ||--o{ AUDIT_LOGS : "causes"
    STUDENTS ||--o{ REGISTRATIONS : "enrolls"
    EVENTS ||--o{ REGISTRATIONS : "has"

    USERS {
        int id PK
        varchar username UK
        varchar email UK
        varchar password_hash
        enum role "admin, student"
        enum status "active, inactive, suspended"
        datetime last_login
        timestamp created_at
        timestamp updated_at
    }

    STUDENTS {
        int id PK
        int user_id FK "REFERENCES users(id) ON DELETE CASCADE"
        varchar roll_number UK
        varchar full_name
        varchar email UK
        varchar phone
        varchar department
        tinyint semester
        decimal gpa
        varchar city
        enum status "active, inactive, deleted"
        timestamp created_at
        timestamp updated_at
    }

    EVENTS {
        int id PK
        varchar title
        varchar category
        varchar department
        text description
        date event_date
        time event_time
        varchar venue
        int max_capacity
        decimal registration_fee
        varchar poster_path
        enum status "upcoming, ongoing, completed, cancelled"
        timestamp created_at
        timestamp updated_at
    }

    REGISTRATIONS {
        int id PK
        int student_id FK "REFERENCES students(id) ON DELETE CASCADE"
        int event_id FK "REFERENCES events(id) ON DELETE CASCADE"
        varchar ticket_code UK
        enum attendance_status "registered, attended, absent, cancelled"
        timestamp registered_at
    }

    AUTH_TOKENS {
        int id PK
        int user_id FK "REFERENCES users(id) ON DELETE CASCADE"
        varchar token_hash UK
        datetime expires_at
        timestamp created_at
    }

    AUDIT_LOGS {
        int id PK
        int user_id FK "REFERENCES users(id) ON DELETE SET NULL"
        varchar action
        text details
        varchar ip_address
        timestamp created_at
    }
```

---

## 2. Normalization Analysis (1NF to 3NF)

### First Normal Form (1NF)
- **Requirement:** Each column contains atomic (indivisible) values, and each record is identified by a unique Primary Key.
- **Application in StudentHub:**
  - Phone numbers, email addresses, and event titles are stored as single atomic values (no comma-separated lists of events in the student table).
  - Every table includes an unsigned integer surrogate Primary Key (`id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY`).

### Second Normal Form (2NF)
- **Requirement:** Must be in 1NF, and all non-key attributes must be fully functionally dependent on the entire Primary Key (no partial dependencies).
- **Application in StudentHub:**
  - In `registrations`, attributes like `ticket_code`, `attendance_status`, and `registered_at` depend on the registration instance. Event title and venue remain in `events`, and student name remains in `students`, eliminating partial functional dependency.

### Third Normal Form (3NF)
- **Requirement:** Must be in 2NF, and no non-key attribute is transitively dependent on another non-key attribute (no transitive dependencies: $X \to Y \to Z$).
- **Application in StudentHub:**
  - In `registrations`, we only store foreign keys `student_id` and `event_id`. Storing `student_name` or `event_title` inside `registrations` would introduce transitive dependency (`registration_id` $\to$ `student_id` $\to$ `student_name`), which is completely avoided.
