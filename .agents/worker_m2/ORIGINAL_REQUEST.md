## 2026-07-31T15:35:56Z

You are teamwork_preview_worker for Milestone 2 (M2): R1 Admin Account Normalization & Security Hardening.

Working directory: /home/hongphuoc/Desktop/thue/.agents/worker_m2
Project root: /home/hongphuoc/Desktop/thue

MANDATORY INTEGRITY WARNING:
DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A Forensic Auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected.

Tasks to implement for M2 (R1 Requirements):
1. **Artisan Command `php artisan admin:create`**:
   - Create Artisan command `CreateAdminCommand` (e.g. `app/Console/Commands/CreateAdminCommand.php` or `modules/VaccineRegistration/Console/Commands/CreateAdminCommand.php`).
   - Signature: `admin:create {--name=} {--username=} {--email=} {--password=} {--role=} {--center_id=}`.
   - Interactive prompt when options are omitted. Validation on email/username uniqueness, password strength, role (`super_admin` or `branch_admin`), and `center_id` validation if `branch_admin`.
   - Ensure the command is registered in Laravel Artisan commands.

2. **Remove Default Super Admin Auto-creation**:
   - Edit `database/seeders/DatabaseSeeder.php`: Remove the default auto-creation of `admin/admin123` super admin account on app run / seeding.

3. **Schema & Model Updates for Account Lifecycle**:
   - Update migration `modules/VaccineRegistration/Database/Migrations/2026_07_31_000004_add_admin_fields_to_users_table.php` (or add a new migration) to include:
     - `status` (string, default `'active'`)
     - `must_change_password` (boolean, default `false`)
     - `password_changed_at` (timestamp, nullable)
     - `last_login_at` (timestamp, nullable)
     - `locked_until` (timestamp, nullable)
     - `failed_login_count` (integer, default `0`)
   - Update `app/Models/User.php`:
     - Add new fields to `$fillable` and `$casts`.
     - Add helper methods: `isLocked(): bool`, `recordSuccessfulLogin()`, `recordFailedLogin()`.

4. **Login Controller Audit & Hardening**:
   - In `modules/VaccineRegistration/Http/Controllers/Admin/AdminAuthController.php`:
     - Check if user is locked (`isLocked()`). If locked, prevent login, return HTTP 423 / 403 or error message "Tài khoản tạm thời bị khóa do đăng nhập sai quá nhiều lần." and log security event.
     - On wrong password attempt: increment `failed_login_count`. If `failed_login_count >= 5`, set `locked_until` (e.g. 15 minutes from now) and log security warning log.
     - On successful login: reset `failed_login_count` to 0, update `last_login_at`, log successful login security event.
     - Add rate limiter / throttling to prevent brute-force attacks.

5. **Verification**:
   - Run tests / Artisan commands to verify `php artisan admin:create` works.
   - Test login failure locking logic.
   - Update `CHANGELOG.md` according to project rules.

Deliver report in `/home/hongphuoc/Desktop/thue/.agents/worker_m2/handoff.md`.
Send message to parent when done.
