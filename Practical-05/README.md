# Practical 5: Registration Form with Frontend Validation and User-Friendly Error Handling
**Course:** Web Development Frameworks (ITUE203)  
**Academic Term:** 3rd Semester, 2026-27 (ODD)  
**Institution:** Faculty of Technology & Engineering (FTE), CHARUSAT  
**CO Mapping:** CO1, CO3  
**PO Mapping:** PO1(3), PO2(3), PO3(3), PO4(2), PO5(3), PO9(1), PO10(3)

---

## 🎯 1. Problem Definition
Develop a student registration form implementing HTML5 input controls and robust JavaScript validation:
1. **Full Name:** Letters and spaces only, 3 to 50 characters (`/^[A-Za-z\s]{3,50}$/`).
2. **Institutional Email:** Standard RFC-compliant email pattern (`/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/`).
3. **Mobile Number:** 10 digits starting with Indian telecom prefixes 6-9 (`/^[6-9]\d{9}$/`).
4. **Password:** Minimum 8 characters with at least one uppercase letter, one lowercase letter, one numeric digit, and one special symbol (`/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/`).
5. **Password Strength Meter:** Real-time visual progress bar evaluating entropy (None -> Weak -> Medium -> Strong).
6. **Confirm Password:** Real-time equivalence check against the primary password.
7. **Course / Degree Selection:** Validated dropdown choice.
8. **Academic Year & Gender:** Validated radio button groups.
9. **Terms Acceptance:** Required legal checkbox.
10. **Custom HTML5 Canvas CAPTCHA:** Distorted alphanumeric rendering with interference lines and refresh capabilities.

---

## 💡 2. Key Questions, Analysis & Viva Answers

### Q1: Are correct input types and attributes used?
**Answer:**  
Yes, standard HTML5 semantic input types and autocomplete attributes are used:
- `type="text"` with `autocomplete="name"` for full name.
- `type="email"` with `autocomplete="email"` for email address.
- `type="tel"` with `maxlength="10"` for phone numbers.
- `type="password"` with `autocomplete="new-password"` to avoid unintended autofill conflicts.
- Forms include `novalidate` to bypass default browser tooltips and activate custom, styled UX error feedback.

---

### Q2: Are validation errors displayed near the relevant fields?
**Answer:**  
Yes. Each form group maintains a dedicated error container (`<div class="error-feedback">`) located immediately beneath the corresponding input control. CSS sibling selectors activate these error messages only when the `.is-invalid` class is applied:
```css
.input-control.is-invalid ~ .error-feedback {
    display: block;
}
```
This guarantees contextual, localized feedback without confusing users with distant global error alerts.

---

### Q3: How is password strength checked?
**Answer:**  
Password strength is calculated dynamically on every keystroke by evaluating five distinct entropy criteria:
1. Length $\ge 8$ characters
2. Length $\ge 12$ characters
3. Contains uppercase characters `[A-Z]`
4. Contains numeric digits `[0-9]`
5. Contains special symbols `[^A-Za-z0-9]`

A visual progress meter maps the score to colored categories:
- **0–2 points:** Red bar (33% width) – "Weak"
- **3–4 points:** Amber bar (66% width) – "Medium"
- **5 points:** Green bar (100% width) – "Strong"

---

### Q4: Is the form accessible using keyboard and screen-reader-friendly labels?
**Answer:**  
Yes:
- Every field links an explicit `<label for="FIELD_ID">` matching `id="FIELD_ID"`.
- Error messages are associated and can be announced to assistive tech via `aria-describedby`.
- All controls, radio buttons, checkboxes, and the CAPTCHA refresh button are fully navigable via `Tab` and toggleable via `Space` / `Enter`.

---

## 📂 3. Directory Layout for Practical 5

```text
Practical-05/
├── README.md               # Practical documentation, theory, and answers
├── test-cases.md           # 20+ test scenarios matrix for valid/invalid inputs
├── index.html              # Registration form view with Canvas CAPTCHA
├── css/
│   └── validation.css      # Field state styling, strength meter, and error badges
└── js/
    └── validation.js       # Real-time regex engine & Canvas CAPTCHA generator
```

---

## 🌟 4. Extensions Implemented
- **Intermediate Extension:** Real-time instant validation listening to `input`, `blur`, and `change` events with dynamic color feedback.
- **Advanced Extension:** Client-side **HTML5 Canvas CAPTCHA** with random background noise lines, dot speckling, character rotation, and live regeneration button.
