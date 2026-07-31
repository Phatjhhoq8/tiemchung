# PROJECT: Medicare Vaccination System - Phase 1 Refactoring

## Architecture
- **Framework**: Laravel 11.x (PHP >= 8.2), Vite 6.x, Tailwind CSS 3.x, MySQL.
- **Backend Architecture**: MVC with Eloquent Models, Form Requests, Policies, Middleware, Artisan Commands, Service layer for security/sanitization.
- **Multi-Branch Data Isolation**: Master `vaccines` table (super_admin managed) separated from `center_vaccines` pivot/local table (branch_admin managed: price, sale_price, stock_status, is_featured, sort_order).

---

## Milestones
| # | Name | Scope | Dependencies | Status |
|---|------|-------|-------------|--------|
| 1 | M1: Codebase Exploration | Audit current models, migrations, controllers, auth, policies, routes, uploads, exports | None | IN_PROGRESS |
| 2 | M2: R1 Admin Account & Security | `admin:create` command, `users` schema fields, login audit logging & 5-fail lockout | M1 | PLANNED |
| 3 | M3: R2 RBAC & Multi-branch Isolation | Policies, IDOR prevention (403 on cross-branch access), master catalog protection | M2 | PLANNED |
| 4 | M4: R4 Content Security & Hardening | HTML Sanitizer, SVG upload blocking, dangerous URL scheme filtering, CSV injection guard | M3 | PLANNED |
| 5 | M5: R3 Consultation Leads & Schema | `consultation_leads` table, `registration_vaccines` pivot `quantity` & Eloquent relationships | M4 | PLANNED |
| 6 | M6: Migration & Forensic Audit | `php artisan migrate:fresh --seed`, full test pass & forensic audit verification | M5 | PLANNED |

---

## Interface Contracts
### Admin Account Management
- Command: `php artisan admin:create`
- Options/Arguments: Name, Email, Password, Branch ID / Role.
- User status fields: `status`, `must_change_password`, `password_changed_at`, `last_login_at`, `locked_until`, `failed_login_count`.

### Access Control Policies
- `super_admin`: Full access to master catalog (`vaccines` table CRUD).
- `branch_admin`: Restricted to assigned `center_id`. Cross-branch request returns `403 Forbidden`. Can edit only `center_vaccines` (`price`, `sale_price`, `stock_status`, `is_featured`, `sort_order`).

### Consultation Leads
- Table: `consultation_leads`
- Fields: `id`, `full_name`, `phone`, `email`, `notes`/`content`, `status`, `center_id` (optional/nullable), `created_at`, `updated_at`.
- Frontend consultation form saves strictly to `consultation_leads` (no `registrations` dummy row created).

### Security Filters & CSV Sanitization
- `HtmlSanitizer::clean($html)`: Strips `<script>`, `onload`, `onerror`, `javascript:` URLs.
- Image uploads: Rejects MIME `image/svg+xml` or `.svg` extensions. Allowed: jpg, jpeg, png, webp.
- Link fields (banners, map embeds): Strip `javascript:`, `data:` schemes.
- CSV export: Prepends apostrophe `'` or escapes values starting with `=`, `+`, `-`, `@`.

---

## Code Layout
- `app/Console/Commands/` -> Artisan commands (`CreateAdminCommand.php`)
- `app/Http/Controllers/` -> Auth, Admin, Branch, Vaccine, Consultation controllers
- `app/Http/Requests/` -> Input validation requests
- `app/Http/Middleware/` -> RBAC & Auth middleware
- `app/Models/` -> `User.php`, `Vaccine.php`, `Center.php`, `CenterVaccine.php`, `Registration.php`, `ConsultationLead.php`
- `app/Policies/` -> `VaccinePolicy.php`, `CenterVaccinePolicy.php`, `RegistrationPolicy.php`
- `app/Services/Security/` -> `HtmlSanitizer.php`, `CsvSanitizer.php`
- `database/migrations/` -> Migration files for database schema updates
- `database/seeders/` -> Database seeders
