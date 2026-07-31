## 2026-07-31T15:59:17Z
You are teamwork_preview_worker for Milestone 3 (M3): R2 RBAC & Multi-branch Data Isolation.

Working directory: /home/hongphuoc/Desktop/thue/.agents/worker_m3
Project root: /home/hongphuoc/Desktop/thue

MANDATORY INTEGRITY WARNING:
DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A Forensic Auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected.

Tasks to implement for M3 (R2 Requirements):
1. **Master Catalog vs Branch Data Separation**:
   - Ensure `super_admin` has full CRUD permissions over master vaccine catalog (`vaccines` table: Name, Origin, Category, Description, etc.).
   - Ensure `branch_admin` can ONLY manage branch-specific local settings (`price`, `sale_price`, `stock_status`, `stock_quantity`, `is_featured`, `sort_order` in `center_vaccines` table).
   - If a `branch_admin` attempts to modify master vaccine catalog fields (Name, Origin, Category), return HTTP `403 Forbidden`.

2. **Laravel Policies & Access Control Enforcement**:
   - Implement/register Policies for key resources (`VaccinePolicy`, `CenterVaccinePolicy`, `RegistrationPolicy`, etc.).
   - Bind/enforce policies across admin controllers (`AdminVaccineController`, `AdminCenterVaccineController`, `AdminCenterController`, `AdminBannerController`, `AdminArticleController`, `AdminRegistrationController`).

3. **Anti-IDOR & Cross-Branch Protection**:
   - Enforce server-side checks blocking cross-branch access. If a `branch_admin` assigned to Branch A attempts to access, edit, or delete data (vaccines, registrations, appointments) belonging to Branch B via AJAX request or page load, the system MUST return HTTP `403 Forbidden`.

4. **Fix Identified Authorization Holes**:
   - Add `SuperAdminOnly` middleware or policy checks to `AdminCenterController`, `AdminBannerController`, `AdminArticleController`.
   - Update `AdminVaccineController::toggleFeatured` permission check so `super_admin` or `branch_admin` for that center can toggle featured state (`AdminContext::isSuperAdmin() || AdminContext::isBranchAdmin()`).

5. **Testing & Verification**:
   - Write comprehensive feature tests in `tests/Feature/RbacMultiBranchTest.php` testing `super_admin` vs `branch_admin` permissions, anti-IDOR cross-branch 403 responses, and master catalog edit protection.
   - Run tests using `/opt/lampp/bin/php artisan test --filter RbacMultiBranchTest` and ensure 100% pass.
   - Update `CHANGELOG.md` according to project rules.

Deliver handoff report in `/home/hongphuoc/Desktop/thue/.agents/worker_m3/handoff.md`.
Send message to parent when done.
