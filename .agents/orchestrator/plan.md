# Execution Plan: Real-time AJAX Filtering & Flexible Date Filters

## Overview
Implement real-time AJAX filtering with 300ms debounce, history.pushState URL synchronization, visual loading indicators, AJAX pagination, and flexible Day/Month/Year dropdown filters across 5 Admin Panel management pages: Registrations, Customers, Consultation Leads, Vaccines, and Centers/Branches.

## Planned Milestones

### Milestone 1: Codebase Exploration & Target Mapping (M1)
- Map the exact controller files and Blade template files for the 5 admin modules:
  1. Registrations (`AdminRegistrationController`)
  2. Customers (`AdminCustomerController`)
  3. Consultation Leads (`AdminConsultationLeadController`)
  4. Vaccines (`AdminVaccineController`)
  5. Centers/Branches (`AdminCenterController`)
- Examine existing query logic, search inputs, pagination Blade components, and JS scripts.
- Identify date fields used for filtering in each module (e.g. `created_at`, `appointment_date`, etc.).

### Milestone 2: Backend Controller Filter & AJAX Responses (M2)
- Add flexible Day (1-31), Month (1-12), Year query filters to each controller:
  - Day only (`whereDay`)
  - Month only (`whereMonth`)
  - Year only (`whereYear`)
  - Any combination (Day+Month, Month+Year, Day+Month+Year)
- Update controllers to detect AJAX requests (`$request->ajax()`) and return partial table + pagination Blade components or JSON HTML data.

### Milestone 3: Frontend Real-Time AJAX, Debounce, URL Sync & Loading UI (M3)
- Create or update reusable JavaScript module / script to handle:
  - 300ms debounce on search inputs.
  - Immediate trigger on dropdown change (Day, Month, Year, Status, Branch, etc.).
  - `history.pushState` to sync query params without full page reload.
  - `popstate` listener to restore form state on browser back/forward.
  - Visual loading state (Medicare Red/Navy styled loading overlay or table fade-out).
  - AJAX pagination link click interception.
- Integrate frontend script into all 5 management pages.

### Milestone 4: Automated Test Suite & CHANGELOG Update (M4)
- Create `tests/Feature/AdminAjaxFilteringTest.php` covering:
  - AJAX search and filtering for all 5 admin controllers.
  - Day-only, Month-only, Year-only, and combination date filters.
  - Pagination AJAX responses.
  - Preservation of query parameters.
- Update `CHANGELOG.md` at top in English concisely.

### Milestone 5: Code Review, Adversarial Testing & Forensic Audit (M5)
- Dispatch Code Reviewer (`teamwork_preview_reviewer`) to verify code quality, ponytail standards, and Medicare color compliance.
- Dispatch Adversarial Challenger (`teamwork_preview_challenger`) to stress-test filter combinations, pagination edge cases, and run unit/feature tests.
- Dispatch Forensic Auditor (`teamwork_preview_auditor`) to verify zero integrity violations.
