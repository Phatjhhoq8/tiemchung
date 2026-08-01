# Handoff Report: Milestone M9 Code Review & Security Audit

## 1. Observation
- **Test Executions**:
  - Command: `/opt/lampp/bin/php ./vendor/bin/phpunit tests/Feature/PatientVaccinationWorkflowTest.php`
  - Result: `OK (4 tests, 28 assertions, 0 failures)`.
  - Command: `/opt/lampp/bin/php ./vendor/bin/phpunit`
  - Result: Completed successfully.
- **Migration & Database Schema**:
  - File: `modules/VaccineRegistration/Database/Migrations/2026_08_01_000005_create_patients_and_vaccination_workflow_tables.php`
  - Creates `patients` table with indexed `identity_card` and `phone`.
  - Extends `registrations` table with foreign key `patient_id` (nullOnDelete), `screening_status`, and `screening_notes`.
  - Creates `administered_doses` table storing `registration_id`, `patient_id`, `vaccine_id`, `inventory_lot_id`, `center_id`, `administered_by`, `administered_at`, `screening_status`, `screening_notes`, `observation_notes`, `observation_ended_at`, `status`.
- **Centralized Patients & Models**:
  - File: `modules/VaccineRegistration/Models/Patient.php`
  - `Patient::findOrCreateCentralized(array $data)` queries existing records by `identity_card` first, then `phone`. If a match exists, it returns the existing patient instance, preventing duplicate patient profiles.
  - File: `modules/VaccineRegistration/Models/AdministeredDose.php`
  - Defines Eloquent relationships (`registration`, `patient`, `vaccine`, `inventoryLot`, `center`, `administrator`).
  - File: `modules/VaccineRegistration/Models/Registration.php`
  - Defines 3-step workflow domain methods: `checkIn()`, `screening()`, and `administer()`.
- **3-Step Workflow Controller & Anti-IDOR Security**:
  - File: `modules/VaccineRegistration/Http/Controllers/Admin/VaccinationWorkflowController.php`
  - `checkIn($request, $id)`: Line 17 calls `Registration::findOrFail($id)` and executes `$registration->checkIn()`. Updates status to `checked_in`.
  - `screening($request, $id)`: Line 38 calls `Registration::findOrFail($id)`. Validates `screening_status` against `in:eligible,deferred,contraindicated`.
  - `administer($request, $id)`: Line 65 calls `Registration::findOrFail($id)`. Validates `$registration->screening_status === 'eligible'`. Rejects `deferred` or `contraindicated` with a 422 JSON response. Creates `AdministeredDose` record with `administered_by = auth()->id()`, lot ID, and observation timer.
  - **CRITICAL SECURITY GAP**: Lines 17, 38, 65 in `VaccinationWorkflowController.php` perform `Registration::findOrFail($id)` without verifying branch authorization (e.g. `AdminContext::isBranchAdmin()` and `$registration->center_id !== AdminContext::centerId()`).

## 2. Logic Chain
1. Step 1 of requirement mandates verifying centralized patient creation without duplicate records. `Patient::findOrCreateCentralized` checks `identity_card` and `phone` before creation, which was confirmed via `test_centralized_patient_profile_management_without_duplicate_records`.
2. Step 2 mandates verifying that Step 1 updates status to `checked_in`, Step 2 records screening and rejects `deferred`/`contraindicated` with 422, and Step 3 creates `AdministeredDose`. These were confirmed via `test_step1_check_in_updates_registration_status_to_checked_in`, `test_step2_screening_logic_permits_eligible_and_blocks_deferred_or_contraindicated`, and `test_step3_vaccination_execution_creates_administered_dose`.
3. Requirement 4 mandates verifying cross-branch anti-IDOR security in admin controllers `AdminPatientController` and `VaccinationWorkflowController`.
4. Inspection of `VaccinationWorkflowController.php` reveals that `checkIn`, `screening`, and `administer` directly look up `Registration::findOrFail($id)` without calling `AdminContext::isBranchAdmin()` or comparing `$registration->center_id` to `AdminContext::centerId()`.
5. Under multi-branch RBAC rules established across the platform (`AdminRegistrationController`, `AdminScheduleController`, `AdminInventoryLotController`), Branch Admins must be restricted to operating only on registrations within their assigned center (`center_id`). Without this check, a Branch Admin from Branch A can inspect or execute workflow actions (check-in, screening, dose administration) on registrations registered at Branch B.
6. Therefore, `VaccinationWorkflowController` contains an unmitigated cross-branch IDOR vulnerability.

## 3. Caveats
- `AdminPatientController` manages centralized patient profiles. Patients are intentionally centralized across the network (Requirement 1), so patient profiles themselves are shared across centers. However, `VaccinationWorkflowController` acts on specific registrations which belong to specific branch centers (`center_id`).
- All 4 workflow feature tests in `PatientVaccinationWorkflowTest.php` pass, but existing test cases only run actions as `super_admin` or single-center admin without testing cross-branch IDOR access on `VaccinationWorkflowController`.

## 4. Conclusion
- **Verdict**: `REQUEST_CHANGES`
- **Rationale**:
  - The migration, model logic (`Patient::findOrCreateCentralized`), 3-step workflow implementation, and HTTP 422 rejection for deferred/contraindicated screening status are functionally correct, clean, and comply with Ponytail style principles.
  - However, `VaccinationWorkflowController` fails the Requirement 4 cross-branch anti-IDOR security check.
  - **Required Fix**: In `VaccinationWorkflowController.php` (`checkIn`, `screening`, and `administer`), add cross-branch anti-IDOR verification:
    ```php
    if (\Modules\VaccineRegistration\Support\AdminContext::isBranchAdmin() && (int) $registration->center_id !== (int) \Modules\VaccineRegistration\Support\AdminContext::centerId()) {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền thực hiện trên đăng ký thuộc cơ sở khác.'], 403);
        }
        return redirect()->back()->withErrors(['error' => 'Bạn không có quyền thực hiện trên đăng ký thuộc cơ sở khác.']);
    }
    ```
  - Additionally, add a test case in `PatientVaccinationWorkflowTest.php` to verify that a Branch Admin from Branch A receives 403 Forbidden when attempting workflow operations on a Branch B registration.

## 5. Verification Method
1. Inspect `modules/VaccineRegistration/Http/Controllers/Admin/VaccinationWorkflowController.php` to confirm addition of `AdminContext` cross-branch check.
2. Run feature tests:
   `/opt/lampp/bin/php ./vendor/bin/phpunit tests/Feature/PatientVaccinationWorkflowTest.php`
3. Verify all tests pass with 0 errors and 0 IDOR vulnerabilities.
