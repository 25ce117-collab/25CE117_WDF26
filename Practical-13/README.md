# Practical 13: PHP JSON API and AJAX/Fetch-Based CRUD without Page Reload
**Course:** Web Development Frameworks (ITUE203)  
**Academic Term:** 3rd Semester, 2026-27 (ODD)  
**Institution:** Faculty of Technology & Engineering (FTE), CHARUSAT  
**CO Mapping:** CO3, CO4, CO5  
**PO Mapping:** PO1(3), PO2(3), PO3(3), PO4(3), PO5(3), PO9(1), PO10(3), PO11(2), PO12(3)

---

## 🎯 1. Problem Definition
Develop a decoupled, Single Page Application (**SPA**) architecture for **StudentHub**:
1. Implement RESTful PHP backend endpoints ([`api/events.php`](./api/events.php)) supporting standard HTTP verbs:
   - `GET /api/events.php`: List events with optional search/category queries.
   - `GET /api/events.php?id={id}`: Fetch single event details.
   - `POST /api/events.php`: Create new event record (Returns `201 Created`).
   - `PUT /api/events.php?id={id}`: Update an existing event (Returns `200 OK`).
   - `DELETE /api/events.php?id={id}`: Delete an event (Returns `200 OK`).
2. Centralize standard HTTP response codes (200, 201, 400, 404, 500) and uniform JSON payloads via [`api/response.php`](./api/response.php).
3. Consume endpoints asynchronously using the modern JavaScript **Fetch API** with zero page reload.
4. Implement **Debounced Search** (300ms delay) to minimize redundant network I/O.
5. Provide user feedback states: loading placeholders, animated modal forms, and auto-dismissing toast notifications.

---

## 💡 2. Key Questions, Analysis & Viva Answers

### Q1: Do the PHP endpoints return valid JSON responses?
**Answer:**  
Yes:
- The response header explicitly sets `Content-Type: application/json; charset=utf-8`.
- Responses are generated using `json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)`.
- All outputs adhere to a consistent JSON envelope schema:
  ```json
  {
      "status": "success",
      "statusCode": 200,
      "message": "Event #1 updated successfully.",
      "data": { ... },
      "timestamp": "2026-10-18 11:00:00"
  }
  ```

---

### Q2: How are HTTP GET, POST, PUT, and DELETE methods handled in PHP?
**Answer:**  
In [`api/events.php`](./api/events.php), the request method is identified via `$_SERVER['REQUEST_METHOD']`:
- **`GET`:** Reads parameters from `$_GET` (`?id=...`, `?search=...`).
- **`POST`, `PUT`, `DELETE`:** Reads raw JSON payloads from the `php://input` read-only stream (`$body = json_decode(file_get_contents('php://input'), true)`), allowing full support for RESTful `PUT` and `DELETE` requests.

---

### Q3: How does Debounced Live Search improve performance?
**Answer:**  
Without debouncing, an API request is fired on every keystroke (e.g., typing "HackFest" triggers 8 separate HTTP network requests). With debouncing:
```javascript
searchInput.addEventListener('input', () => {
    clearTimeout(searchDebounceTimer);
    searchDebounceTimer = setTimeout(() => {
        loadEvents(searchInput.value.trim());
    }, 300);
});
```
The timer is cancelled and reset with each keystroke. An HTTP request is dispatched only after the user pauses typing for 300 milliseconds, eliminating up to 80% of unnecessary server queries.

---

### Q4: How is input validation enforced on the API backend?
**Answer:**  
Before executing any SQL query, inputs in `$body` are checked:
- Required string fields (`title`, `venue`, `event_date`) cannot be empty.
- Integer capacities must be positive numbers.
- If validation fails, `ApiResponse::error('Validation failure', 400, $errors)` sends an immediate HTTP 400 Bad Request with details, preventing malformed records from reaching the database.

---

## 📂 3. Directory Layout for Practical 13

```text
Practical-13/
├── README.md           # Practical documentation, theory, and answers
├── index.html          # SPA client interface consuming REST API
├── css/
│   └── style.css       # Clean table, modal, and toast styling
├── js/
│   └── app.js          # Fetch CRUD engine with 300ms debouncing
├── db.php              # Secure PDO database connector
└── api/
    ├── events.php      # RESTful JSON endpoint (GET, POST, PUT, DELETE)
    └── response.php    # Centralized JSON response helper & HTTP status codes
```

---

## 🌟 4. Extensions Implemented
- **Intermediate Extension:** **300ms Debounced AJAX Search** optimizing client-server bandwidth.
- **Advanced Extension:** Full **RESTful Architecture** incorporating RFC-compliant HTTP status codes (`200 OK`, `201 Created`, `400 Bad Request`, `404 Not Found`, `500 Server Error`) with centralized response serialization.
