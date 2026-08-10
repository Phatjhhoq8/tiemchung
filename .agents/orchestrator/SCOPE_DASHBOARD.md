# Scope: Admin Dashboard Improvements

## Architecture
- **Framework**: Laravel 11.x (PHP >= 8.2), Vite 6.x, Tailwind CSS 3.x, MySQL.
- **Target Files**:
  - `modules/VaccineRegistration/Http/Controllers/Admin/AdminDashboardController.php` (or similar controller path)
  - `resources/views/admin/dashboard.blade.php` (or module dashboard blade view)
  - `app/Models/ConsultationLead.php`, `app/Models/InventoryLot.php`, `app/Models/Registration.php`, `app/Models/Center.php`
  - `tests/Feature/AdminDashboardTest.php`
  - `CHANGELOG.md`

## Milestones
| # | Name | Scope | Dependencies | Status |
|---|------|-------|-------------|--------|
| 1 | M1: Codebase Exploration & Target Mapping | Investigate controller, Blade views, DB tables/models, center filter logic | None | DONE |
| 2 | M2: Implementation (R1, R2, R3) & Tests | Dynamic metrics, today's widget, SVG chart, tests, CHANGELOG | M1 | DONE |
| 3 | M3: Code Review, Challenger & Forensic Audit | Verification, stress test, forensic audit | M2 | DONE |

## Interface Contracts
### Dashboard Metrics & Filtering
- `center_id`: Filter query parameter (optional, null or empty string = all centers; integer = specific center).
- R1 `$consultCount`: Count of `consultation_leads` where `status` is `'pending'` or `'new'`. Filter by `center_id` when provided.
- R1 `$importedQuantity`: Sum of (`available_quantity` + `reserved_quantity`) from `inventory_lots`. Filter by `center_id` when provided.
- R1 `$soldQuantity`: Count/sum of `registrations` where `booking_status` = `'completed'`. Filter by `center_id` when provided.
- R2 Today's Injections: Count of `registrations` where `injection_date` = today's date (`NOW()` / `today()`). Filter by `center_id` when provided.
- R3 SVG Chart: Pure SVG rendering of revenue and registration trends over last 7 days or last 6 months. Strict Medicare colors: `#c8102e` (Medicare Red), `#eaaa00` (Medicare Gold), `#004b8f` (Medicare Navy).
