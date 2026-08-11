# Practical 2: Web Accessibility Conformance Checklist
## StudentHub - WCAG 2.1 Level AA Audit
**Course:** ITUE203 Web Development Frameworks  
**Tested Artifacts:** 11 Semantic HTML5 Pages (Home, About, Register, Login, Dashboard, Events, Profile, Contact, Admin, FAQ, Feedback)

---

## 📋 Conformance Verification Matrix

| Category | Guideline / Requirement | Implementation in StudentHub | Conformance Status |
|:---|:---|:---|:---:|
| **Semantic Landmarks** | Proper use of `<header>`, `<nav>`, `<main>`, `<aside>`, `<footer>` | Every page encapsulates top chrome in `<header role="banner">`, primary menu in `<nav aria-label="Main Navigation">`, core view in `<main id="main-content" role="main">`, sidebars in `<aside>`, and footer in `<footer role="contentinfo">`. | ✅ Pass |
| **Keyboard Navigation** | Full interactive operability using only keyboard (`Tab`, `Shift+Tab`, `Enter`, `Space`) | All navigational hyperlinks, form controls, and submit buttons possess prominent `:focus-visible` styling (3px high-contrast outline). | ✅ Pass |
| **Skip-to-Content Link** | Bypass block mechanisms for repetitive navigation headers | A top-level hidden skip link `<a href="#main-content" class="skip-link">Skip to main content</a>` appears upon initial `Tab` keypress and jumps directly to `<main id="main-content">`. | ✅ Pass (Advanced) |
| **Breadcrumb Navigation** | Orientation mechanism indicating current document location in site hierarchy | Structured semantic `<nav aria-label="Breadcrumb"><ol>...</ol></nav>` with `aria-current="page"` deployed across all secondary views. | ✅ Pass (Intermediate) |
| **Form Association** | Explicit label-to-input association for assistive screen readers | Every form control explicitly links its descriptive `<label for="FIELD_ID">` with matching `id="FIELD_ID"`. Mandatory fields include `<span class="required-star">*</span>` and `aria-required="true"`. | ✅ Pass |
| **Heading Hierarchy** | Logical, unskipped heading nesting (`h1` -> `h2` -> `h3`) | Exactly one unique `<h1>` element per document designating page purpose, followed by logical `<h2>` section headers and `<h3>` component sub-headings. | ✅ Pass |
| **Color Contrast** | Minimum 4.5:1 text-to-background contrast ratio | Text color `#0f172a` against white `#ffffff` yields a contrast ratio of **15.8:1**; primary blue `#1d4ed8` on white yields **7.1:1**, surpassing WCAG AA standards. | ✅ Pass |
| **Active State Indication** | Distinguishing current page visually and semantically | Current navigation link features `aria-current="page"` and distinctive border/color styling. | ✅ Pass |
| **Accessible Disclosures** | Semantic accordion implementation | FAQ page utilizes native `<details>` and `<summary>` elements providing screen reader accessible expand/collapse functionality with zero JavaScript dependency. | ✅ Pass |

---

## 🧪 Testing Methodology
- **Screen Reader Emulation:** Verified navigation order with DOM tree hierarchy.
- **Keyboard Traversal Test:** Validated 100% interactive control access using only `Tab` and `Shift+Tab` keys.
- **Focus Ring Verification:** Inspected that `:focus-visible` states are never suppressed with `outline: none`.
