# Handoff Report — Milestone 2 (M2): R1 Admin Account Normalization & Security Hardening

## 1. Observation
- **Artisan Command Creation**:
  - File: `app/Console/Commands/CreateAdminCommand.php`
  - File: `modules/VaccineRegistration/Console/Commands/CreateAdminCommand.php`
  - Registered in `modules/VaccineRegistration/Providers/VaccineServiceProvider.php`.
  - Signature: `admin:create {--name=} {--username=} {--email=} {--password=} {--role=} {--center_id=}`.
  - Command executes interactive prompts when options are omitted and enforces strict validation (unique email/username, password length >= 8, valid roles `super_admin` or `branch_admin`, and valid `center_id` for branch admins).
- **Default Super Admin Removal**:
  - File: `database/seeders/DatabaseSeeder.php`
  - Removed lines 18-30 that previously auto-created `admin/admin123` account on every app seeding run.
- **Database Schema & User Model Updates**:
  - File: `modules/VaccineRegistration/Database/Migrations/2026_07_31_000004_add_admin_fields_to_users_table.php`
  - File: `modules/VaccineRegistration/Database/Migrations/2026_07_31_000007_add_account_security_fields_to_users_table.php`
  - File: `app/Models/User.php`
  - Added columns to `users` table: `status` (default `'active'`), `must_change_password` (default `false`), `password_changed_at` (timestamp, nullable), `last_login_at` (timestamp, nullable), `locked_until` (timestamp, nullable), `failed_login_count` (integer, default `0`).
  - Added `$fillable` & `$casts` updates and helper methods `isLocked(): bool`, `recordSuccessfulLogin(): void`, and `recordFailedLogin(int $maxAttempts = 5, int $lockoutMinutes = 15): void`.
- **Login Controller Audit & Hardening**:
  - File: `modules/VaccineRegistration/Http/Controllers/Admin/AdminAuthController.php`
  - Implemented `isLocked()` check before processing credentials.
  - Added rate limiter throttling (`RateLimiter::tooManyAttempts($throttleKey, 5)`) to block brute-force attempts.
  - Incremented `failed_login_count` on wrong password attempt, automatically locking account for 15 minutes upon reaching 5 consecutive failures.
  - Added security event logging (`Log::warning` and `Log::info`) for failed, locked, and successful login attempts.
  - On successful login, reset `failed_login_count` to 0, cleared lock, and recorded `last_login_at`.
- **Documentation & Verification**:
  - File: `CHANGELOG.md` updated with `## [v3.5.21] - 2026-07-31` entry detailing M2 changes.
  - File: `tests/Feature/AdminAccountSecurityTest.php` created with 7 tests and 44 assertions.
  - Test command output: `/opt/lampp/bin/php artisan test --filter AdminAccountSecurityTest` -> 7 tests passed (44 assertions).

## 2. Logic Chain
1. **Command Normalization**: Administrative accounts must be explicitly provisioned with safe parameters rather than rely on hardcoded seeders. `CreateAdminCommand` guarantees credentials are validated prior to creation and assigned proper center context.
2. **Account Lifecycle & Security State**: Adding `status`, `locked_until`, `failed_login_count`, `last_login_at`, `must_change_password`, and `password_changed_at` to `users` schema allows the application to track login security metrics natively.
3. **Lockout Enforcement**: By checking `isLocked()` first in `AdminAuthController::login()`, locked users are immediately rejected with `"Tài khoản tạm thời bị khóa do đăng nhập sai quá nhiều lần."`. Automatically calling `$user->recordFailedLogin(5, 15)` locks any account exceeding 5 failed attempts for 15 minutes.
4. **Brute Force Protection**: Combining route-level throttling (`middleware('throttle:5,1')`) and controller-level `RateLimiter` prevents high-frequency automated login attacks.

## 3. Caveats
- No external HTTP requests made (CODE_ONLY network compliance verified).
- When running automated test suites against remote MySQL, `DatabaseTransactions` trait was used to preserve DB schema integrity across test iterations.

## 4. Conclusion
Milestone 2 (M2) R1 Admin Account Normalization & Security Hardening is fully implemented, verified with automated test suites, and documented in CHANGELOG.md.

## 5. Verification Method
Run the following commands in terminal to verify implementation:

1. **Verify Artisan Admin Creation Command**:
   ```bash
   /opt/lampp/bin/php artisan admin:create --name="Admin Verification" --username="adminverify" --email="adminverify@medicare.local" --password="Password123!" --role="super_admin"
   ```
2. **Run Feature Security Test Suite**:
   ```bash
   /opt/lampp/bin/php artisan test --filter AdminAccountSecurityTest
   ```
