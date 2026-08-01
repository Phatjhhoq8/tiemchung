## 2026-08-01T10:50:43+07:00

You are Implementation Worker M9 (Patch).
Your working directory is /home/hongphuoc/Desktop/thue/.agents/worker_m9.

Code Reviewer M9 identified a Security Finding (Cross-Branch IDOR Vulnerability) in `VaccinationWorkflowController.php`:

Vulnerability: `VaccinationWorkflowController` methods (`checkIn`, `screening`, `administer`) call `Registration::findOrFail($id)` directly without checking `AdminContext::isBranchAdmin()` and `center_id`. A Branch Admin from Center A can manage vaccination workflow for Center B registrations.

Instructions:
1. Open `VaccinationWorkflowController.php` (under `modules/VaccineRegistration/Http/Controllers/Admin/VaccinationWorkflowController.php`).
   Add cross-branch anti-IDOR checks to `checkIn()`, `screening()`, and `administer()`:
   ```php
   if (AdminContext::isBranchAdmin() && $registration->center_id != AdminContext::centerId()) {
       return response()->json(['message' => 'Không có quyền truy cập cơ sở này.'], 403);
   }
   ```
2. Open `tests/Feature/PatientVaccinationWorkflowTest.php` and add test case:
   `test_branch_admin_cannot_access_cross_branch_registration_workflow()` verifying that a Branch Admin from Center 1 attempting check-in/screening/administration on a registration for Center 2 gets HTTP 403 Forbidden.
3. Run target test: `/opt/lampp/bin/php ./vendor/bin/phpunit tests/Feature/PatientVaccinationWorkflowTest.php`. Verify 5/5 tests pass 100%.
4. Run full test suite: `/opt/lampp/bin/php ./vendor/bin/phpunit`. Verify all tests pass 100%.
5. MANDATORY INTEGRITY WARNING:
   DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A Forensic Auditor will independently verify your work.
6. Update `/home/hongphuoc/Desktop/thue/.agents/worker_m9/handoff.md` and send a completion message with test results to parent.
