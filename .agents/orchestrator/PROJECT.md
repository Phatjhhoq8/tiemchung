# PROJECT: Medicare Vaccination System - Phases 1 to 6 Refactoring (Ponytail Style)

## Architecture
- **Framework**: Laravel 11.x (PHP >= 8.2), Vite 6.x, Tailwind CSS 3.x, MySQL.
- **Backend Architecture**: MVC with Eloquent Models, Form Requests, Policies, Middleware, Queue Jobs, Service layer for security/sanitization/inventory.
- **Multi-Branch Data Isolation**: Master `vaccines` table (super_admin managed) separated from `center_vaccines` pivot/local table (branch_admin managed: price, sale_price, stock_status, is_featured, sort_order).
- **Ponytail Architecture Principles**: Minimal, effective, standard Laravel & database capabilities (`lockForUpdate()`, database-backed audit logs, simple status attributes, native queue drivers, clean Eloquent relations).

---

## Milestones
| # | Name | Scope | Dependencies | Status |
|---|------|-------|-------------|--------|
| 1 | M1: Codebase Exploration | Audit current models, migrations, controllers, auth, policies, routes, uploads, exports | None | DONE |
| 2 | M2: R1 Admin Account & Security | `admin:create` command, `users` schema fields, login audit logging & 5-fail lockout | M1 | DONE |
| 3 | M3: R2 RBAC & Multi-branch Isolation | Policies, IDOR prevention (403 on cross-branch access), master catalog protection | M2 | DONE |
| 4 | M4: Content Security & Hardening | HTML Sanitizer, SVG upload blocking, dangerous URL scheme filtering, CSV injection guard | M3 | DONE |
| 5 | M5: Audit Logs & Resource Status | `audit_logs` table, automatic log on price/stock/order changes, deactivation (`is_active` / `status`) | M4 | DONE |
| 6 | M6: CRM Leads & Registration Transactions | `consultation_leads` table, `registrations` transaction pivot `registration_vaccines`, `idempotency_key` | M5 | DONE |
| 7 | M7: Slots & Concurrency Control | `schedules`, `slots` tables, `DB::transaction` with `lockForUpdate()` on slot reservations | M6 | DONE |
| 8 | M8: FEFO Inventory & Reservation | `inventory_lots`, `stock_movements`, FEFO allocation logic, pending order reservation/release | M7 | DONE |
| 9 | M9: Patient Profiles & 3-Step Workflow | `patients` table, 3-step workflow (check-in, screening, execution `administered_doses`) | M8 | DONE |
| 10 | M10: Payment Webhook & Queue Jobs | Server-to-server payment webhook verification, background Queue Jobs for Email/SMS | M9 | DONE |
| 11 | M11: Migration & Forensic Audit | `php artisan migrate:fresh --seed`, full feature test pass & forensic audit verification | M10 | DONE |

---

## Interface Contracts

### Admin Account Management
- Command: `php artisan admin:create`
- Options/Arguments: Name, Email, Password, Branch ID / Role.
- User status fields: `status`, `must_change_password`, `password_changed_at`, `last_login_at`, `locked_until`, `failed_login_count`.

### Access Control Policies
- `super_admin`: Full access to master catalog (`vaccines` table CRUD).
- `branch_admin`: Restricted to assigned `center_id`. Cross-branch request returns `403 Forbidden`. Can edit only `center_vaccines` (`price`, `sale_price`, `stock_status`, `is_featured`, `sort_order`).

### Content Security & File Uploads
- `HtmlSanitizer::clean($html)`: Strips `<script>`, `onload`, `onerror`, `javascript:` URLs.
- Image uploads: Rejects MIME `image/svg+xml` or `.svg` extensions across all admin forms. Allowed: jpg, jpeg, png, webp.
- Link fields (banners, map embeds): Strip `javascript:`, `data:` schemes.
- CSV export: Escapes values starting with `=`, `+`, `-`, `@`.

### Audit Logs & Soft Delete Deactivation
- Table: `audit_logs` (`id`, `actor_id`, `center_id`, `action`, `resource_type`, `resource_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `created_at`).
- Automatic trigger/event on: price change, inventory movement, order status change, refund.
- Deactivation: Use `is_active = false` or `status = 'inactive'` on `vaccines`, `centers`, `users`, `banners`, `articles`.

### Consultation Leads & Registration Standardization
- Table: `consultation_leads` (`id`, `name`, `phone`, `source`, `status`, `note`, `center_id`). No dummy registration or patient created.
- Table: `registrations` linked to `vaccines` via pivot `registration_vaccines` with `quantity` and `price`.
- Idempotency: `idempotency_key` checked on backend transaction creation.

### Schedules, Slots & Concurrency Control
- Tables: `schedules`, `slots` (`id`, `schedule_id`, `start_at`, `end_at`, `capacity`, `reserved_count`).
- Concurrency: `DB::transaction(function() use (...) { $slot = Slot::where('id', $id)->lockForUpdate()->first(); ... })`.

### FEFO Inventory & Reservation
- Tables: `inventory_lots` (`id`, `vaccine_id`, `center_id`, `lot_number`, `expires_at`, `available_quantity`, `status`), `stock_movements`.
- FEFO Logic: Sort active, non-expired lots by `expires_at ASC`. Exclude recalled/quarantined lots.
- Stock Reservation: Deduct/reserve available_quantity on `pending` order, release back on cancellation/expiry.

### Patients & 3-Step Vaccination Workflow
- Table: `patients` (`id`, `identity_card`, `full_name`, `dob`, `gender`, `phone`, `address`, `medical_history`).
- 3 Steps:
  1. Check-in (`status = 'checked_in'`)
  2. Screening (`screening_status = 'eligible' / 'deferred' / 'contraindicated'`)
  3. Execution (`administered_doses`: `vaccine_id`, `lot_id`, `administered_by`, `administered_at`, `observation_notes`, `observation_ended_at`).

### Payment Webhook & Queue Jobs
- Webhook route: `POST /api/webhooks/payment` with signature validation, transaction reference check, amount validation.
- Queue Jobs: `SendRegistrationEmailJob`, `SendNotificationSmsJob`.

---

## Code Layout
- `app/Console/Commands/` -> Artisan commands (`CreateAdminCommand.php`)
- `app/Http/Controllers/` -> Auth, Admin, Branch, Vaccine, Consultation, Slot, Inventory, Vaccination, Webhook controllers
- `app/Http/Requests/` -> Input validation requests
- `app/Http/Middleware/` -> RBAC, Auth, Idempotency middleware
- `app/Jobs/` -> Notification queue jobs
- `app/Models/` -> `User.php`, `Vaccine.php`, `Center.php`, `CenterVaccine.php`, `Registration.php`, `ConsultationLead.php`, `AuditLog.php`, `Schedule.php`, `Slot.php`, `InventoryLot.php`, `StockMovement.php`, `Patient.php`, `AdministeredDose.php`
- `app/Policies/` -> Policies for model access control
- `app/Services/` -> `HtmlSanitizer.php`, `CsvSanitizer.php`, `FefoInventoryService.php`, `SlotBookingService.php`
- `database/migrations/` -> Migration files for database schema updates
- `database/seeders/` -> Database seeders
