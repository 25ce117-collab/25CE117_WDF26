# Practical 1: REST-Style Routing & Backend Architecture Plan
## StudentHub - Advanced Extension Plan
**Course:** ITUE203 Web Development Frameworks

---

## 1. Routing Architecture Overview

StudentHub adopts a RESTful architecture separating static presentation pages from dynamic transactional API endpoints. The plan outlines standard HTTP verbs, path definitions, required authentication, and response formats.

### 1.1 Public & Web View Routes

| HTTP Verb | Path | Controller / Page | Description | Access Level |
|:---|:---|:---|:---|:---|
| `GET` | `/` | `index.php` | Landing page featuring campus highlights | Public |
| `GET` | `/about.php` | `about.php` | About university, department, mission | Public |
| `GET` | `/faq.php` | `faq.php` | FAQ knowledge base with interactive accordion | Public |
| `GET` | `/contact.php` | `contact.php` | Contact form & office locations | Public |
| `POST` | `/process_contact.php` | `process.php` | Handles contact form data & CSV/JSON storage | Public |
| `GET` | `/register.php` | `register.php` | Registration form view | Public |
| `POST` | `/api/auth/register` | `register_handler.php` | Processes new student user registration | Public |
| `GET` | `/login.php` | `login.php` | Authentication form view | Public |
| `POST` | `/api/auth/login` | `login_handler.php` | Validates credentials & issues session/token | Public |
| `GET` | `/logout.php` | `logout.php` | Destroys session, revokes tokens, redirects | Authenticated |

---

### 1.2 Student Portal Routes (Authenticated: `role = student`)

| HTTP Verb | Path | Controller / Page | Description | Access Level |
|:---|:---|:---|:---|:---|
| `GET` | `/student_dashboard.php` | `student_dashboard.php` | Personal dashboard & enrolled events | Student |
| `GET` | `/events.php` | `events.php` | Browse all active campus events | Student / Public |
| `GET` | `/profile.php` | `profile.php` | View and edit student personal profile | Student |
| `POST` | `/profile_update.php` | `profile_update.php` | Update profile information & avatar | Student |
| `POST` | `/api/registrations/register` | `api/register_event.php` | Register current student for an event | Student |

---

### 1.3 Administrator Routes (Authenticated: `role = admin`)

| HTTP Verb | Path | Controller / Page | Description | Access Level |
|:---|:---|:---|:---|:---|
| `GET` | `/admin_dashboard.php` | `admin_dashboard.php` | System KPI cards, charts, recent activities | Admin |
| `GET` | `/students.php` | `students.php` | Student directory with search, filter, pagination | Admin |
| `POST` | `/api/students/add` | `add_student.php` | Create new student record | Admin |
| `POST` | `/api/students/update` | `edit_student.php` | Update existing student record | Admin |
| `POST` | `/api/students/delete` | `delete_student.php` | Soft-delete student record | Admin |
| `GET` | `/export_students_csv.php`| `export_csv.php` | Stream all student records to CSV download | Admin |
| `GET` | `/admin_events.php` | `admin_events.php` | Manage campus events with poster upload | Admin |
| `POST` | `/api/events/add` | `add_event.php` | Upload poster & create event | Admin |
| `POST` | `/api/events/update` | `edit_event.php` | Update event information | Admin |
| `POST` | `/api/events/delete` | `delete_event.php` | Delete event & unlink poster file | Admin |
| `GET` | `/admin_users.php` | `admin_users.php` | View user accounts & roles | Admin |
| `POST` | `/api/users/toggle_status`| `toggle_user.php` | Toggle active/inactive state via AJAX | Admin |
| `GET` | `/audit_logs.php` | `audit_logs.php` | View security audit logs with timestamps | Admin |

---

### 1.4 RESTful JSON API Endpoints (Practical 13)

| HTTP Verb | Endpoint | Description | Request Body | Response Codes |
|:---|:---|:---|:---|:---|
| `GET` | `/api/events.php` | Retrieve paginated list of events with query filters | None | `200 OK` |
| `GET` | `/api/events.php?id={id}`| Retrieve single event details by primary key | None | `200 OK`, `404 Not Found` |
| `POST` | `/api/events.php` | Create a new event record | JSON `{title, category, date, venue, seats}` | `201 Created`, `400 Bad Request` |
| `PUT` | `/api/events.php?id={id}`| Update an existing event record | JSON `{title, category, date, venue, seats}` | `200 OK`, `400 Bad Request` |
| `DELETE` | `/api/events.php?id={id}`| Delete an existing event record | None | `200 OK`, `404 Not Found` |
