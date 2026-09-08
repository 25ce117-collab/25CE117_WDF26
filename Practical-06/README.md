# Practical 6: Rendering External JSON Data using Fetch API, Search and Filter
**Course:** Web Development Frameworks (ITUE203)  
**Academic Term:** 3rd Semester, 2026-27 (ODD)  
**Institution:** Faculty of Technology & Engineering (FTE), CHARUSAT  
**CO Mapping:** CO3, CO4  
**PO Mapping:** PO1(3), PO2(3), PO3(3), PO4(2), PO5(3), PO9(1), PO10(3)

---

## 🎯 1. Problem Definition
Develop an asynchronous, client-side data consumer using JavaScript's native **Fetch API**:
1. Ingest, parse, and dynamically render data from three external JSON datasets:
   - [`data/events.json`](./data/events.json) (16 campus event records)
   - [`data/students.json`](./data/students.json) (16 student academic profile records)
   - [`data/faqs.json`](./data/faqs.json) (16 knowledge base records)
2. Implement live text search across multiple object keys (name, roll, title, tags, answer).
3. Provide category-based and department-based dropdown filtering.
4. Enable multi-criteria sorting (A-Z, Z-A, chronological dates, numerical GPA/capacities).
5. Build client-side pagination with configurable page sizes (6, 9, 12 items).
6. Implement animated loading spinners and graceful network error handlers.

---

## 💡 2. Key Questions, Analysis & Viva Answers

### Q1: How is JSON fetched, parsed, and rendered?
**Answer:**  
- **Fetching & Parsing:** We execute asynchronous network calls using `fetch('data/events.json')` combined with the ES6 `async / await` syntax:
  ```javascript
  const response = await fetch(`data/${datasetName}.json`);
  if (!response.ok) throw new Error(`HTTP Error: ${response.status}`);
  const data = await response.json(); // parses JSON string into native JS objects
  ```
- **Rendering:** Using JavaScript ES6 template literals (`${...}`) with `.map().join('')`, the structured objects are compiled into semantic HTML markup (cards, responsive tables, tags) and injected into the target container via `innerHTML`. HTML entity escaping (`escapeHTML()`) is applied across all rendered properties to prevent Cross-Site Scripting (XSS).

---

### Q2: Which array methods are used for search, filter, sort, and pagination?
**Answer:**  
Modern functional JavaScript array methods process the datasets immutably:
1. **`Array.prototype.filter()`:** Filters elements matching text queries (`item.title.toLowerCase().includes(query)`) and category predicates (`item.category === selectedFilter`).
2. **`Array.prototype.sort()`:** Orders records alphabetically (`String.prototype.localeCompare()`) or numerically (`(b.gpa - a.gpa)`).
3. **`Array.prototype.slice()`:** Computes pagination windows without mutating the master dataset:
   $$\text{Start Index} = (\text{currentPage} - 1) \times \text{pageSize}$$
   $$\text{Paginated Items} = \text{filteredData}.\text{slice}(\text{startIndex}, \text{startIndex} + \text{pageSize})$$
4. **`Array.prototype.map()`:** Transforms each data object into HTML table rows or card markup.

---

### Q3: How are loading and error states handled?
**Answer:**  
- **Loading State:** Before the asynchronous `fetch()` promise resolves, `showLoading()` renders an animated CSS keyframe spinner (`.spinner`), assuring users that background I/O is in progress.
- **Error State:** If the network request fails (e.g. offline status or missing file), a `try...catch` block intercepts the exception. It first attempts to load cached data from `localStorage`. If no cache exists, it renders an informative empty state informing the user of the error without crashing the script.

---

### Q4: How is modularity maintained in JavaScript files?
**Answer:**  
All functionality in [`js/app.js`](./js/app.js) is segregated into modular single-responsibility units:
- A centralized `state` store maintains active filters, sort orders, current tab, and pagination offsets.
- Distinct controller functions handle data fetching (`loadDataset`), filter application (`applyFiltersAndRender`), DOM rendering (`renderView`), pagination controls (`renderPagination`), and dependent dropdowns (`initDependentDropdowns`).

---

## 📂 3. Directory Layout for Practical 6

```text
Practical-06/
├── README.md               # Practical documentation, theory, and answers
├── index.html              # Dynamic tabbed interface with search & filters
├── css/
│   └── style.css           # Styling for grid, tables, pagination, and spinner
├── js/
│   └── app.js              # Fetch engine, array algorithms, and offline cache
└── data/
    ├── events.json         # 16 campus event records
    ├── students.json       # 16 student directory records
    └── faqs.json           # 16 campus FAQ records
```

---

## 🌟 4. Extensions Implemented
- **Intermediate Extension:** Three-level **Dependent Dropdowns** dynamically linking University Faculty $\rightarrow$ Institute / School $\rightarrow$ Academic Degree using hierarchical data structures.
- **Advanced Extension:** **LocalStorage Response Caching** with timestamping. If a network disruption occurs, the application seamlessly retrieves the cached JSON data from `localStorage`, ensuring offline accessibility.
