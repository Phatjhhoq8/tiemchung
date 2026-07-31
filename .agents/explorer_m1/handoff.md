# Explorer M1 Handoff Report

## 1. Observation
- **R1 Audit (Admin Auth & Account Setup)**:
  - Account seeding in `database/seeders/DatabaseSeeder.php:18-30` auto-creates a `super_admin` user from `.env` (`ADMIN_USERNAME`, `ADMIN_PASSWORD`).
  - `users` table schema expanded in `modules/VaccineRegistration/Database/Migrations/2026_07_31_000004_add_admin_fields_to_users_table.php` with `username`, `role`, `center_id`, `is_active`.
  - Auth handled in `modules/VaccineRegistration/Http/Controllers/Admin/AdminAuthController.php:30-62` using custom session variables (`admin_logged_in`, `admin_user_id`, `admin_role`, `admin_center_id`).
  - Absence of rate limiting (`throttle`) on login requests in `AdminAuthController.php`.
  - Absence of password change endpoints or views for administrators.

- **R2 Audit (RBAC & Multi-Branch Data)**:
  - Table `center_vaccines` (`2026_07_31_000002_create_center_vaccines_table.php`) links centers and vaccines with unique `['center_id', 'vaccine_id']`.
  - Scope `Vaccine::forCenter($centerId)` in `modules/VaccineRegistration/Models/Vaccine.php:95-113` joins `center_vaccines`.
  - Authorization middleware `SuperAdminOnly` (`modules/VaccineRegistration/Http/Middleware/SuperAdminOnly.php`) and helper `AdminContext` (`modules/VaccineRegistration/Support/AdminContext.php`) exist.
  - Absence of authorization checks in `AdminCenterController.php`, `AdminBannerController.php`, and `AdminArticleController.php`.
  - `AdminVaccineController::toggleFeatured` (`AdminVaccineController.php:226`) uses `abort_unless(AdminContext::isBranchAdmin(), 403);`, blocking `super_admin`.

- **R3 Audit (Consultation Leads & Registration Pivot)**:
  - `registrations` table (`2026_07_17_000002_create_registrations_table.php`) stores patient registrations.
  - `VaccineController::postDiseaseConsult()` (`VaccineController.php:683-684`) inserts consultation form requests directly into `registrations` with hardcoded fake DOB (`2000-01-01`) and gender (`Khác`).
  - `registration_vaccines` pivot migration (`2026_07_31_000006_add_quantity_to_registration_vaccines_table.php`) includes `quantity`.
  - Stock update in `AdminRegistrationController.php:96-105` subtracts a constant `1` instead of `$vaccine->pivot->quantity`.

- **R4 Audit (Security, Uploads & CSV Export)**:
  - HTML content sanitized using `SecurityHelper::cleanHtml()` (`SecurityHelper.php:7-46`) in `AdminArticleController.php:41, 81`.
  - Blade rendering `{!! $article->content !!}` in `articles/show.blade.php:51` displays sanitized content.
  - Image uploads in `AdminVaccineController`, `AdminBannerController`, `AdminArticleController` restrict extensions to `jpeg,png,jpg,gif,webp` (disallowing SVG). `AdminArticleController::uploadEditorImage` lacks explicit Laravel MIME validator.
  - `AdminRegistrationController::exportCsv()` (`AdminRegistrationController.php:261-268`) uses `safeCsvCell()` to prefix `=`, `-`, `+`, `@` with `'`.

## 2. Logic Chain
1. **R1 Analysis**: Custom session authentication bypasses standard `Auth::guard()`, meaning standard throttle middleware or password reset traits are missing unless explicitly implemented in `AdminAuthController`.
2. **R2 Analysis**: Because `AdminCenterController`, `AdminBannerController`, and `AdminArticleController` do not verify `AdminContext::isSuperAdmin()`, any authenticated `branch_admin` can mutate system-wide center, banner, and article data.
3. **R3 Analysis**: Mixing consultation leads (total_price=0, status='Chờ tư vấn') into `registrations` distorts real booking analytics. In addition, hardcoded quantity deductions in `AdminRegistrationController` lead to inventory discrepancies when multiple quantities are booked.
4. **R4 Analysis**: Security controls for CSV export and HTML sanitization are active and functional, though upload endpoints in TinyMCE editor require stricter MIME checks to complement extension checks.

## 3. Caveats
- Production database data was not directly inspected (read-only audit based on codebase structure and migrations).
- External storage drivers (e.g. S3) are not configured; local disk uploads (`public/images/*`) are used.

## 4. Conclusion
The codebase possesses a solid modular architecture and good baseline security helpers (CSV cell escaping, DOMDocument HTML cleaning). However, Phase 1 Refactoring must resolve critical authorization gaps (missing super admin checks on center/banner/article controllers), stock calculation defects in registration updates, login rate limiting, and consultation data separation.

## 5. Verification Method
- **Files to Inspect**:
  - `modules/VaccineRegistration/Http/Controllers/Admin/AdminCenterController.php`
  - `modules/VaccineRegistration/Http/Controllers/Admin/AdminBannerController.php`
  - `modules/VaccineRegistration/Http/Controllers/AdminArticleController.php`
  - `modules/VaccineRegistration/Http/Controllers/Admin/AdminVaccineController.php`
  - `modules/VaccineRegistration/Http/Controllers/Admin/AdminRegistrationController.php`
  - `modules/VaccineRegistration/Http/Controllers/VaccineController.php`
- **Full Analysis Report**: `/home/hongphuoc/Desktop/thue/.agents/explorer_m1/analysis.md`
