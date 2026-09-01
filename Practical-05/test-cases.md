# Practical 5: Comprehensive Form Validation Test Case Matrix
## StudentHub - Client-Side Validation Specification
**Course:** ITUE203 Web Development Frameworks

---

## 🧪 Detailed Test Case Specifications

| TC ID | Input Field | Test Scenario | Input Value | Expected Result | Actual Result | Status |
|:---:|:---|:---|:---|:---|:---|:---:|
| **TC-01** | Full Name | Valid Alphabetic Name | `Satyajit Parmar` | Field border turns green (`is-valid`), error hidden | Green border, valid | ✅ Pass |
| **TC-02** | Full Name | Invalid: Numbers & Special Chars | `Satya123!` | Red border (`is-invalid`), "3-50 letters only" error | Field flagged red, error shown | ✅ Pass |
| **TC-03** | Full Name | Invalid: Too Short (<3 chars) | `Al` | Red border (`is-invalid`), error shown | Error message displayed | ✅ Pass |
| **TC-04** | Email | Valid Institutional Email | `23it042@charusat.edu.in` | Valid status, green border | `is-valid` assigned | ✅ Pass |
| **TC-05** | Email | Invalid: Missing Domain | `satya@` | Invalid status, red border, error shown | Flagged invalid | ✅ Pass |
| **TC-06** | Email | Invalid: Missing `@` symbol | `satya.charusat.in` | Invalid status, error shown | Flagged invalid | ✅ Pass |
| **TC-07** | Mobile | Valid Indian Mobile Number | `9876543210` | 10 digits starting with 9 accepted (`is-valid`) | Valid green indicator | ✅ Pass |
| **TC-08** | Mobile | Invalid: Starts with Invalid Digit (2-5) | `3214567890` | Red border, Indian mobile format error | Error displayed | ✅ Pass |
| **TC-09** | Mobile | Invalid: Length < 10 digits | `98765` | Flagged invalid until 10 digits completed | Error displayed | ✅ Pass |
| **TC-10** | Password | Weak Password (<8 chars, simple) | `abc` | Strength bar 33% red ("Weak"), validation fails | Red indicator, error shown | ✅ Pass |
| **TC-11** | Password | Medium Password (8 chars, letters+num) | `Welcome2026` | Strength bar 66% amber ("Medium"), missing symbol | Amber indicator | ✅ Pass |
| **TC-12** | Password | Strong Password (upper, lower, num, sym)| `Student@Hub2026!` | Strength bar 100% green ("Strong"), validation passes | 100% green bar, valid | ✅ Pass |
| **TC-13** | Confirm Pass| Matching Password | `Student@Hub2026!` | Field marked valid, passwords synchronized | Green checkmark | ✅ Pass |
| **TC-14** | Confirm Pass| Mismatched Password | `Student@Hub9999!` | Red border, "Passwords do not match" error | Error displayed | ✅ Pass |
| **TC-15** | Course | Not Selected (Empty default) | `""` | Flagged invalid on submit | Error displayed | ✅ Pass |
| **TC-16** | Year / Gender| Unselected Radio Options | None checked | Group flagged invalid, prompt error shown | Flagged invalid | ✅ Pass |
| **TC-17** | CAPTCHA | Correct Generated String | Matches Canvas | CAPTCHA verified, field marked valid | Valid green checkmark | ✅ Pass |
| **TC-18** | CAPTCHA | Incorrect / Mismatched String | `ABCXYZ` | Red border, "CAPTCHA text does not match" | Error displayed | ✅ Pass |
| **TC-19** | CAPTCHA | Refresh Button Clicked | Click 🔄 | Canvas redraws new random string with noise | Canvas instantly updates | ✅ Pass |
| **TC-20** | Terms | Checkbox Unchecked | `false` | Form submission rejected, error shown | Submit blocked | ✅ Pass |
