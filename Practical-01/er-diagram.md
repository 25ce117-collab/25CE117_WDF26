# Practical 1: Entity-Relationship (ER) Architecture
## StudentHub - Advanced Extension Plan
**Course:** ITUE203 Web Development Frameworks

---

## 1. Entity-Relationship Diagram (Mermaid)

```mermaid
erDiagram
    USERS ||--o{ STUDENTS : "profiles"
    USERS ||--o{ AUDIT_LOGS : "triggers"
    USERS ||--o{ AUTH_TOKENS : "maintains"
    STUDENTS ||--o{ REGISTRATIONS : "registers"
    EVENTS ||--o{ REGISTRATIONS : "has"

    USERS {
        int id PK
        varchar username UK
        varchar email UK
        varchar password_hash
        enum role "admin, student"
        enum status "active, inactive"
        datetime last_login
        datetime created_at
    }

    STUDENTS {
        int id PK
        int user_id FK
        varchar roll_number UK
        varchar full_name
        varchar email UK
        varchar phone
        varchar department
        int semester
        decimal gpa
        enum status "active, inactive, deleted"
        datetime created_at
        datetime updated_at
    }

    EVENTS {
        int id PK
        varchar title
        varchar category
        text description
        date event_date
        time event_time
        varchar venue
        int max_capacity
        varchar poster_path
        enum status "upcoming, ongoing, completed, cancelled"
        datetime created_at
        datetime updated_at
    }

    REGISTRATIONS {
        int id PK
        int student_id FK
        int event_id FK
        datetime registration_date
        enum attendance_status "registered, attended, absent, cancelled"
        varchar ticket_code UK
    }

    AUTH_TOKENS {
        int id PK
        int user_id FK
        varchar token_hash UK
        datetime expires_at
        datetime created_at
    }

    AUDIT_LOGS {
        int id PK
        int user_id FK
        varchar action
        text details
        varchar ip_address
        varchar user_agent
        datetime created_at
    }
```

---

## 2. Relational Schema & Cardinality Specifications

1. **`USERS` to `STUDENTS` (1 to 0..1):**
   - Each student user record in `USERS` links to exactly one extended profile in `STUDENTS`.
   - Admin users may not have a corresponding `STUDENTS` record.
   - Foreign key constraint: `FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.

2. **`STUDENTS` to `REGISTRATIONS` (1 to N):**
   - A single student can register for multiple university events.
   - Foreign key constraint: `FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE`.

3. **`EVENTS` to `REGISTRATIONS` (1 to N):**
   - A single event can have many student registrations up to `max_capacity`.
   - Unique composite constraint: `UNIQUE KEY uk_student_event (student_id, event_id)` prevents duplicate registrations by the same student.
   - Foreign key constraint: `FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE`.

4. **`USERS` to `AUTH_TOKENS` (1 to N):**
   - Stores hashed remember-me tokens for multi-device logins.
   - Foreign key constraint: `FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.

5. **`USERS` to `AUDIT_LOGS` (1 to N):**
   - Tracks security-relevant actions performed by users and administrators.
   - Foreign key constraint: `FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL`.
