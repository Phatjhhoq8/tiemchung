# Scope: Real-time AJAX Filtering & Flexible Day/Month/Year Filtering

## Architecture
- **Framework**: Laravel 11.x, Blade templates, Tailwind CSS, Vanilla JS / Axios / Fetch.
- **Target Modules / Controllers & Views**:
  1. Registrations: `AdminRegistrationController` & `resources/views/admin/registrations/index.blade.php` (or module views)
  2. Customers: `AdminCustomerController` & `resources/views/admin/customers/index.blade.php`
  3. Consultation Leads: `AdminConsultationLeadController` & `resources/views/admin/consultations/index.blade.php`
  4. Vaccines: `AdminVaccineController` & `resources/views/admin/vaccines/index.blade.php`
  5. Centers/Branches: `AdminCenterController` & `resources/views/admin/centers/index.blade.php`
- **Frontend AJAX Standard**:
  - Debounce 300ms on search text inputs.
  - Listen to filter inputs/dropdown changes.
  - Visual loading state (table fade-out / spinner with Medicare theme colors).
  - Sync query parameters with browser URL via `history.pushState`.
  - Handle `popstate` event for Back/Forward browser navigation.
  - Intercept pagination link clicks to load via AJAX without full page reload.

## Milestones
| # | Name | Scope | Dependencies | Status |
|---|------|-------|-------------|--------|
| 1 | M1: Codebase Exploration & Target Mapping | Investigate 5 admin controllers, blade views, date columns, JS setup | None | DONE |
| 2 | M2: Backend Controller Filter & AJAX Responses | Add flexible Day (1-31), Month (1-12), Year query logic & AJAX partial table rendering | M1 | DONE |
| 3 | M3: Frontend Real-Time AJAX, Debounce, URL Sync & Loading UI | Implement JS real-time filter, 300ms debounce, pushState, loading UI across 5 blade views | M2 | DONE |
| 4 | M4: Automated Test Suite & CHANGELOG Update | Feature tests in `tests/Feature/AdminAjaxFilteringTest.php` + update CHANGELOG.md | M3 | DONE |
| 5 | M5: Code Review, Adversarial Testing & Forensic Audit | Reviewer approve, Challenger pass 100% tests, Forensic Auditor CLEAN verdict | M4 | DONE |

## Interface Contracts
### Flexible Date Filter Backend Logic
- Parameters: `filter_day` (1-31), `filter_month` (1-12), `filter_year` (YYYY), `search` (keyword).
- Logic:
  - If `filter_day` present: `whereDay(column, filter_day)`
  - If `filter_month` present: `whereMonth(column, filter_month)`
  - If `filter_year` present: `whereYear(column, filter_year)`
  - Combinations supported automatically when multiple parameters are present.

### AJAX Table & Pagination Payload
- When request is AJAX (`request()->ajax()` or `request()->wantsJson()` or `X-Requested-With` header):
  - Return JSON `{ html: '...', pagination: '...' }` or partial view rendering the table and pagination links.

### Brand Standards
- Colors: Medicare Red `#c8102e`, Medicare Gold `#eaaa00`, Medicare Navy `#004b8f`.
- Emojis/Icons: NO unauthorized icons/emojis added to UI components.
