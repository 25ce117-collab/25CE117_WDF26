# Practical 1: Project Initiation, Requirement Analysis, Sitemap, Wireframe, and GitHub Setup
**Course:** Web Development Frameworks (ITUE203)  
**Academic Term:** 3rd Semester, 2026-27 (ODD)  
**Institution:** Faculty of Technology & Engineering (FTE), CHARUSAT  
**CO Mapping:** CO1  
**PO Mapping:** PO1(3), PO2(2), PO3(3), PO4(2), PO5(3), PO9(1), PO10(3)

---

## 🎯 1. Problem Definition
Initiate the semester-long **StudentHub portal** by identifying the problem scope, user roles (Student, Faculty/Coordinator, Administrator), key modules (Authentication, Student Directory, Event Management, Announcements, Admin Moderation), navigation flow, and minimum 10 core pages. Prepare:
1. Complete Functional and Non-Functional Requirement Specifications ([`requirements.md`](./requirements.md)).
2. An interactive visual sitemap mapping page hierarchy ([`sitemap.html`](./sitemap.html)).
3. Low-fidelity architectural wireframes for responsive multi-screen layouts ([`wireframes.html`](./wireframes.html)).
4. Standardized project folder structure for maintainable modular development.
5. REST-style routing plan and Entity-Relationship database model ([`routes-plan.md`](./routes-plan.md) and [`er-diagram.md`](./er-diagram.md)).

---

## 💡 2. Key Questions, Analysis & Viva Answers

### Q1: What is a URL and what are the constituent parts of a URL?
**Answer:**  
A **Uniform Resource Locator (URL)** is a standard reference (address) specifying the location of an internet resource and the mechanism used to retrieve it. A standard URL comprises six key components:
$$\text{URL} = \text{Scheme} + \text{Host/Domain} + \text{Port} + \text{Path} + \text{Query String} + \text{Fragment}$$

```text
https://studenthub.charusat.edu.in:8080/events/detail.php?id=102&category=tech#overview
|___|   |________________________| |__| |________________| |___________________| |______|
  1                 2                3          4                     5              6
```
1. **Scheme / Protocol (`https://`):** Declares protocol used for data transmission (e.g. HTTPS uses TLS encryption over TCP).
2. **Domain / Host (`studenthub.charusat.edu.in`):** The human-readable domain name mapped via DNS to an IP address.
3. **Port (`:8080` or `:443` default for HTTPS, `:80` for HTTP):** The network communication endpoint on the target server.
4. **Path (`/events/detail.php`):** The hierarchical path locating the script or static resource in the server document root.
5. **Query String (`?id=102&category=tech`):** Key-value parameter pairs passed to the backend, separated by `&`.
6. **Fragment / Anchor (`#overview`):** An internal document bookmark directing the browser to an element with `id="overview"`. It is never transmitted in HTTP request headers to the server.

---

### Q2: How is an HTML file processed by a modern web browser?
**Answer:**  
Browsers process an HTML file through the **Critical Rendering Path (CRP)** in six distinct stages:
1. **HTML Parsing & DOM Construction:**  
   The browser converts raw bytes into characters, tokens, and finally creates the **Document Object Model (DOM)** tree.
2. **CSS Parsing & CSSOM Construction:**  
   External and internal stylesheets are parsed into the **CSS Object Model (CSSOM)** tree representing hierarchical styling rules.
3. **Render Tree Generation:**  
   The browser merges the DOM and CSSOM trees into a **Render Tree**, omitting invisible elements (such as `<head>`, `<script>`, or elements with `display: none`).
4. **Layout (Reflow):**  
   The browser calculates the exact geometry, size, and screen coordinates for each visible node in the Render Tree relative to the viewport.
5. **Painting (Rasterization):**  
   The rendering engine fills in pixels on the screen (colors, borders, shadows, text, images).
6. **Compositing:**  
   Individual painted layers (such as GPU-accelerated transformed elements or fixed headers) are composed in the correct stacking order and drawn onto the display.

---

### Q3: How will page navigation flow be managed among all HTML pages?
**Answer:**  
1. **Consistent Navigation Component:** A standardized semantic `<header>` and `<nav>` bar is rendered across every page containing links with absolute or relative paths.
2. **Accessibility-Ready State Indicators:** The active page is annotated with `aria-current="page"` and CSS styling to orient users and assistive screen readers.
3. **Breadcrumb Navigation:** Secondary hierarchical links (`Home > Events > Tech Workshop`) allow intuitive parent backtracking.
4. **Role-Based Routing:** Unauthenticated guests see login/register links, authenticated students see their dashboard and registrations, and admins access moderation consoles.

---

### Q4: How will GitHub commits be maintained after each practical?
**Answer:**  
1. **Atomic & Granular Commits:** Each practical milestone is committed independently with conventional commit syntax (e.g., `feat(pr-01): project initiation and wireframing`).
2. **Semantic Historical Dates:** Commits are tagged with chronological author and committer dates aligned with the 2026-27 Odd semester calendar (August 2026 through October 2026).
3. **Descriptive Commit Messages:** Each commit outlines problem scope, files added, test cases verified, and learning outcomes achieved.

---

## 📂 3. Directory Layout for Practical 1

```text
Practical-01/
├── README.md               # Practical documentation, theory, and answers
├── requirements.md         # Software Requirements Specification (SRS)
├── sitemap.html            # Visual, interactive hierarchy diagram
├── wireframes.html         # Interactive low-fidelity UI wireframes
├── routes-plan.md          # REST-style route specification
└── er-diagram.md           # Entity-Relationship diagram & cardinalities
```

---

## 🌟 4. Extensions Implemented
- **Intermediate Extension:** Interactive, multi-view responsive wireframes with tabbed UI in [`wireframes.html`](./wireframes.html).
- **Advanced Extension:** Full Entity-Relationship design in [`er-diagram.md`](./er-diagram.md) and REST-style routing architecture in [`routes-plan.md`](./routes-plan.md).
