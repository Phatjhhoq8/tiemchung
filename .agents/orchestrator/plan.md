# Master Execution Plan: Medicare Vaccination System - Phases 1 to 6 (Ponytail Style)

## Objective
Execute complete refactoring of Medicare Vaccination System (Phases 1-6, Ponytail Style) based on user requirements in `ORIGINAL_REQUEST.md`. Ensure minimal, effective, no-overengineering implementation with zero-defect data safety, strict security, seamless SPA experience, full test coverage, and clean `/opt/lampp/bin/php artisan migrate:fresh --seed` execution.

---

## Breakdown of Milestones

### Milestone 1: Comprehensive Codebase Exploration & Analysis (M1) [DONE]
- Static analysis of existing Laravel codebase, models, migrations, controllers, and routes.
- Target mapping for admin auth, policies, consultation leads, file uploads, CSV exports.

### Milestone 2: R1 - Admin Account Normalization & Security Hardening (M2) [DONE]
- Removed default `admin/admin123` auto-creation from codebase.
- Created `php artisan admin:create` CLI command.
- Updated `users` table schema (`status`, `must_change_password`, `password_changed_at`, `last_login_at`, `locked_until`, `failed_login_count`).
- Integrated security event logging & 5-failed-attempts account lockout.

### Milestone 3: R2 - RBAC & Multi-Branch Data Isolation (M3) [DONE]
- Master `vaccines` catalog protected (Super Admin only).
- Branch Admin restricted to local `center_vaccines` settings (`price`, `sale_price`, `stock_status`, `is_featured`, `sort_order`).
- Server-side Anti-IDOR enforcement returning `403 Forbidden` on cross-branch data access.

### Milestone 4: Content Security, SVG Blocking & Hardening (M4) [IN_PROGRESS]
- HTML Sanitizer service to clean article content prior to DB save (stripping `<script>`, `onload`, `onerror`, `javascript:` URLs).
- Reject `.svg` upload format on all admin upload forms (allowing only JPG, PNG, WEBP).
- Strip dangerous URL schemes (`javascript:`, `data:`) on banner links and map embed URLs.
- Prevent CSV Formula Injection (`=`, `+`, `-`, `@`) on registration CSV exports.

### Milestone 5: R1 - Audit Logs & Soft Delete / Resource Status Management (M5)
- Minimal `audit_logs` table (`actor_id`, `center_id`, `action`, `resource_type`, `resource_id`, `old_values`, `new_values`, `ip_address`, `user_agent`).
- Automatic audit logging on sensitive actions: vaccine price updates, stock changes, order status changes, refunds.
- Resource status normalization: soft delete / deactivation via `is_active = false` or `status = 'inactive'` for main resources (`vaccines`, `centers`, `users`, `banners`, `articles`).

### Milestone 6: R2 - CRM Leads, Registration Transaction Standardization & Idempotency (M6)
- Independent `consultation_leads` table (`name`, `phone`, `source`, `status`, `note`, `center_id`) for consultation requests without creating dummy patients or fake registrations.
- Standardized `registrations` transaction model with pivot `registration_vaccines` containing `quantity` and `price`.
- Backend `idempotency_key` verification on registration submissions to block duplicate orders from double clicks.

### Milestone 7: R3 - Schedules, Slots & Concurrency Control (M7)
- `schedules` and `slots` tables (`start_at`, `end_at`, `capacity`, `reserved_count`).
- Atomic concurrency control using `DB::transaction` and `lockForUpdate()` when reserving time slots to guarantee no overbooking under concurrent requests.

### Milestone 8: R4 - FEFO Inventory Lots, Movements & Stock Reservation (M8)
- `inventory_lots` (`lot_number`, `expires_at`, `available_quantity`, `status`) and `stock_movements` tracking.
- First Expired First Out (FEFO) automatic lot allocation logic prioritising nearest expiration date, rejecting expired or recalled/quarantined lots.
- Temporary stock reservation on `pending` registrations, automatically released on cancellation or payment expiration.

### Milestone 9: R5 - Centralized Patients & 3-Step Vaccination Workflow (M9)
- Centralized `patients` table managing medical profiles (eliminating duplicate patient info in registrations).
- 3-step vaccination workflow:
  1. Check-in (`checked_in`)
  2. Medical Screening (`eligible`, `deferred`, `contraindicated`)
  3. Vaccination Execution (`administered_doses` storing vaccine, lot number, vaccinator ID, post-vaccination observation time).

### Milestone 10: R6 - Payment Webhook & Background Queue Jobs (M10)
- Strict payment verification via server-to-server payment webhook (verifying signature, order reference, amount) before marking orders `paid`.
- Asynchronous Queue Jobs for Email/SMS notification dispatches to prevent blocking transaction flow.

### Milestone 11: E2E Integration, Fresh Migration & Seeding Verification, Forensic Audit (M11)
- Verify `/opt/lampp/bin/php artisan migrate:fresh --seed` runs cleanly on blank database without syntax or constraint errors.
- Execute full PHPUnit feature test suites covering all requirements (RBAC, Audit logs, Leads, Slots, FEFO Lots, Vaccination Workflow, Webhooks).
- Conduct Forensic Auditor integrity check to guarantee clean verdict.

---

## Execution Methodology & Quality Gates
Each milestone follows the standard cycle:
1. **Explorer**: Technical analysis & target specification.
2. **Worker**: Implementation, migrations, models, controllers, services, commands, tests execution.
3. **Reviewer**: Code review & test verification.
4. **Challenger**: Empirical verification & edge case stress testing.
5. **Forensic Auditor**: Integrity verification (Binary Veto).
