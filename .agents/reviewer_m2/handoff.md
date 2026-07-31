# Milestone 2 (M2) Review Report: R1 Admin Account Normalization & Security Hardening

## Review Summary

**Verdict**: APPROVE

All implementation deliverables for Milestone 2 (M2) have been thoroughly inspected, verified, and stress-tested. The default super admin account auto-creation (`admin/admin123`) has been completely purged from database seeders. The `admin:create` Artisan CLI command is implemented with rigorous input validation and role-based assignment rules. Database migrations and model security attributes (`status`, `must_change_password`, `password_changed_at`, `last_login_at`, `locked_until`, `failed_login_count`) are cleanly structured with full cast definitions and helper logic. Authentication logic in `AdminAuthController.php` enforces IP rate-limiting, 5-attempt lockout, session fixation defense, and structured security event logging. Target M2 feature test suite passed 100% cleanly.

---

## 1. Observation

### Codebase Inspections & Evidence

1. **Default Admin Removal**:
   - Inspection of `database/seeders/DatabaseSeeder.php` (lines 1-26) confirms lines auto-creating `admin/admin123` have been completely removed.
   - Grep search for string `admin123` across the entire codebase (`/home/hongphuoc/Desktop/thue`) returned 0 occurrences in application source files (found only in documentation/logs).

2. **Artisan CLI Command (`admin:create`)**:
   - Inspected `app/Console/Commands/CreateAdminCommand.php` & `modules/VaccineRegistration/Console/Commands/CreateAdminCommand.php`.
   - Command signature: `admin:create {--name=} {--username=} {--email=} {--password=} {--role=} {--center_id=}`.
   - Validation rules (lines 97-127):
     - `username`: `required`, `unique:users,username`.
     - `email`: `required`, `email`, `unique:users,email`.
     - `password`: `required`, `Password::min(8)`.
     - `role`: `required`, `in:super_admin,branch_admin`.
     - `center_id`: `required_if:role,branch_admin`, `exists:centers,id`. If `role === 'super_admin'`, `center_id` is automatically set to `null`.

3. **Database Schema & User Model**:
   - Migrations `2026_07_31_000004_add_admin_fields_to_users_table.php` and `2026_07_31_000007_add_account_security_fields_to_users_table.php` add `status`, `must_change_password`, `password_changed_at`, `last_login_at`, `locked_until`, and `failed_login_count` using safe `Schema::hasColumn` checks.
   - `app/Models/User.php`:
     - `$fillable` (lines 21-35) includes: `status`, `must_change_password`, `password_changed_at`, `last_login_at`, `locked_until`, `failed_login_count`.
     - `$casts` (lines 52-64) casts timestamps to `datetime`, `must_change_password` to `boolean`, `failed_login_count` to `integer`.
     - Helpers: `isLocked(): bool` (lines 84-95), `recordSuccessfulLogin()` (lines 100-107), `recordFailedLogin(int $maxAttempts = 5, int $lockoutMinutes = 15)` (lines 112-121).

4. **Authentication & Security Hardening (`AdminAuthController.php`)**:
   - Inspected `modules/VaccineRegistration/Http/Controllers/Admin/AdminAuthController.php`.
   - Lockout Check (lines 53-64): Calls `$user->isLocked()` before credential validation and logs security event warning if account is locked.
   - Rate Limiter (lines 67-80): Restricts login attempts to 5 per IP/username pair using `RateLimiter::tooManyAttempts`.
   - Inactive Check (lines 83-93): Blocks accounts where `$user->is_active == false` or `$user->status === 'inactive'`.
   - Success Flow (lines 96-117): Resets rate limiter, clears `failed_login_count`, records `last_login_at`, logs security success event, regenerates session ID via `$request->session()->regenerate()`.
   - Failure Flow (lines 120-154): Increments rate limiter, calls `$user->recordFailedLogin(5, 15)`. Upon 5th consecutive failure, triggers 15-minute lock and logs security event warning.

5. **Test Execution Analysis**:
   - Command: `/opt/lampp/bin/php artisan test --filter AdminAccountSecurityTest`
     - Result: `PASS  Tests\Feature\AdminAccountSecurityTest`
     - 7/7 tests passed cleanly (18 assertions, duration 0.58s).
   - Command: `/opt/lampp/bin/php artisan test`
     - Result: `AdminAccountSecurityTest` passed 100% cleanly.
     - Note: `Tests\Feature\ExampleTest` (legacy test file from initial setup) failed on legacy assertions (e.g. expecting hardcoded 15 vaccines vs 43 in database, missing `consultType` payload added in v3.5.20 security rules). These failures in `ExampleTest` are pre-existing legacy test debt and are unrelated to M2 changes.

6. **Changelog**:
   - Inspected `CHANGELOG.md` (lines 3-12): Contains section `## [v3.5.21] - 2026-07-31` with detailed concise English entries for M2 changes.

---

## 2. Logic Chain

1. **Seeder Safety**: Observation 1 proves that hardcoded default credentials (`admin/admin123`) were removed from `DatabaseSeeder.php` and no longer exist in source code. Therefore, fresh installations or re-seedings cannot expose predictable admin credentials.
2. **Account Creation Control**: Observation 2 demonstrates that admin creation is strictly controlled via `php artisan admin:create`. Input validation prevents invalid emails, duplicate usernames, passwords < 8 characters, invalid roles, or unassigned branch admins.
3. **Data Integrity & Model Security**: Observation 3 confirms schema columns and model casting match the specifications. Helper methods cleanly handle status checks and login counters without raw query duplication.
4. **Lockout & Logging Enforcement**: Observation 4 demonstrates multi-layered protection: brute-force rate-limiting, 5-attempt account lock with 15-minute expiration, session fixation prevention, and detailed `Log::warning` audit trails.
5. **Independent Verification**: Observation 5 shows all M2 target unit/feature tests execute without errors or failures, verifying zero regressions for M2 scope.

---

## 3. Findings

### Integrity Violation Check
- **Hardcoded test results / expected outputs**: None found.
- **Dummy / facade implementations**: None found; all methods execute real database queries and Eloquent persistence.
- **Shortcuts / bypassed core work**: None found.
- **Fabricated verification outputs**: None found; live artisan test execution confirmed.

### Findings Summary
- **M2 Scope**: No Critical, Major, or Minor findings. Implementation conforms strictly to security and project requirements.
- **Legacy Debt (Minor / Pre-existing)**: `tests/Feature/ExampleTest.php` contains legacy assertions (e.g., hardcoded count of 15 vaccines vs 43 present, missing `consultType` in POST payload) that should be updated in a future test refactoring task.

---

## 4. Verified Claims

| Claim | Verification Method | Result |
|---|---|---|
| Auto-creation of `admin/admin123` removed | Viewed `DatabaseSeeder.php` & `grep_search` | PASS |
| `admin:create` command validates role, email, password, center_id | Inspected `CreateAdminCommand.php` & ran unit test | PASS |
| `User` model security attributes & helper methods present | Inspected `User.php` & ran unit test | PASS |
| Account locking after 5 failed attempts & security logging | Inspected `AdminAuthController.php` & ran feature test | PASS |
| `AdminAccountSecurityTest` suite passes cleanly | Ran `/opt/lampp/bin/php artisan test --filter AdminAccountSecurityTest` | PASS |
| `CHANGELOG.md` updated in English | Inspected top section of `CHANGELOG.md` | PASS |

---

## 5. Stress-Test & Adversarial Challenge Summary

- **Overall Risk Assessment**: LOW
- **Scenarios Tested**:
  1. *Submitting invalid center ID to branch admin creation*: Handled correctly (exit code 1, validation error returned, record not created in DB).
  2. *Attempting 5 consecutive failed logins*: Account lock `locked_until` set to +15 minutes, 6th attempt blocked immediately with `auth_failed` error even if password is subsequently correct.
  3. *Successful login after failed attempts*: Clears failed login counter (`0`), updates `last_login_at`, regenerates session token.

---

## 6. Coverage Gaps & Pre-existing Test Debt

- `tests/Feature/ExampleTest.php`: Contains pre-existing outdated assertions (hardcoded count of 15 vaccines vs 43 seeded in v3.5, missing `consultType` parameter required by recent validation updates). Recommendation: Update `ExampleTest.php` in future test maintenance task.

---

## 7. Conclusion

Milestone 2 (M2) meets all requirements for security hardening, account normalization, validation logic, logging, and test coverage. The verdict is **APPROVE**.

---

## 8. Verification Method

To independently verify this review:
1. Run target test suite:
   ```bash
   /opt/lampp/bin/php artisan test --filter AdminAccountSecurityTest
   ```
2. Verify seeder code:
   ```bash
   cat database/seeders/DatabaseSeeder.php
   ```
3. Test `admin:create` command validation:
   ```bash
   /opt/lampp/bin/php artisan admin:create --name="Test" --username="test" --email="invalid-email" --password="123" --role="branch_admin" --no-interaction
   ```
   (Should return exit code 1 with error list).
