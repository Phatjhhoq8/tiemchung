# Handoff Report — Milestone M6: CRM Consultation Leads, Registration Standardization & Idempotency

## 1. Observation

- **CRM Leads Table & Model**:
  - Created migration `modules/VaccineRegistration/Database/Migrations/2026_08_01_000001_create_consultation_leads_table.php` with columns `id`, `name`, `phone`, `source`, `status` (default 'new'), `note`, `center_id`, `created_at`, `updated_at`.
  - Created Eloquent model `Modules\VaccineRegistration\Models\ConsultationLead` in `modules/VaccineRegistration/Models/ConsultationLead.php`.
  - Public consultation requests (`postDiseaseConsult` in `VaccineController.php` lines 673–758) and public `/consultations` route (`ConsultationLeadController@store`) now create records in `consultation_leads` table without creating any records or dummy patient profiles in `registrations` table.

- **Registration Pivot & Transaction Model Standardization**:
  - Updated `Registration::vaccines()` (`modules/VaccineRegistration/Models/Registration.php` lines 32–37) and `Vaccine::registrations()` (`modules/VaccineRegistration/Models/Vaccine.php` lines 154–159) to include `quantity`, `price`, `sale_price` via `withPivot(['quantity', 'price', 'sale_price'])`.
  - Added migration `modules/VaccineRegistration/Database/Migrations/2026_08_01_000002_add_sale_price_to_registration_vaccines_table.php`.
  - Updated `VaccineController::postRegister` (lines 393–540) to attach vaccines to `registration_vaccines` pivot with `quantity`, `price`, and `sale_price`, and calculate total prices accurately.

- **Backend Idempotency (Chống gửi trùng)**:
  - Created middleware `Modules\VaccineRegistration\Http\Middleware\IdempotencyMiddleware` in `modules/VaccineRegistration/Http/Middleware/IdempotencyMiddleware.php`.
  - Added controller-level idempotency handling in `VaccineController::postRegister` checking `Idempotency-Key` / `X-Idempotency-Key` / `idempotency_key` headers or request payload.
  - Cached response payload and HTTP status code under key `idempotency:<md5(key)>` for 24 hours. Subsequent duplicate requests with the same idempotency key return the cached 200/201 response directly, creating 0 new DB records in `registrations`.

- **Feature Tests & Verification Output**:
  - Created test file `tests/Feature/CrmLeadsAndRegistrationIdempotencyTest.php`.
  - Updated legacy consultation test in `tests/Feature/ExampleTest.php` to assert `lead_id` and `consultation_leads` record creation.
  - M6 Specific Test Command: `/opt/lampp/bin/php ./vendor/bin/phpunit tests/Feature/CrmLeadsAndRegistrationIdempotencyTest.php`
  - Output verbatim:
    ```text
    PHPUnit 11.5.56 by Sebastian Bergmann and contributors.

    Runtime:       PHP 8.2.12
    Configuration: /home/hongphuoc/Desktop/thue/phpunit.xml

    DDDD                                                                4 / 4 (100%)

    Time: 00:01.243, Memory: 36.50 MB

    OK, but there were issues!
    Tests: 4, Assertions: 25, Deprecations: 2.
    ```
  - Full Suite Test Command: `/opt/lampp/bin/php ./vendor/bin/phpunit`
  - Output verbatim:
    ```text
    PHPUnit 11.5.56 by Sebastian Bergmann and contributors.

    Runtime:       PHP 8.2.12
    Configuration: /home/hongphuoc/Desktop/thue/phpunit.xml

    .DDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDD        58 / 58 (100%)

    Time: 02:07.966, Memory: 52.50 MB

    OK, but there were issues!
    Tests: 58, Assertions: 349, Deprecations: 2, PHPUnit Deprecations: 9.
    ```

- **Documentation**:
  - Updated `CHANGELOG.md` with release notes for `## [v3.8.0] - 2026-08-01` in English.

---

## 2. Logic Chain

1. **CRM Leads Isolation**:
   - *Observation*: Previously, `postDiseaseConsult` inserted dummy patient rows into `registrations` with fake DOB '2000-01-01' and status 'Chờ tư vấn'.
   - *Deduction*: This violated the requirement that consultation inquiries should save exclusively as CRM leads.
   - *Action*: Created `consultation_leads` table/model, created `ConsultationLeadController`, and refactored `postDiseaseConsult` to save strictly to `consultation_leads`. Verified in test `test_public_lead_submission_creates_consultation_lead_and_no_registration` that 2 leads were created in `consultation_leads` and 0 records were created in `registrations`.

2. **Pivot Table Standardization**:
   - *Observation*: Registration items require storing quantity and itemized prices for auditability.
   - *Deduction*: Mapping `quantity`, `price`, and `sale_price` via Eloquent `withPivot` ensures pivot records preserve the exact transactional pricing and quantity purchased at registration time.
   - *Action*: Updated Eloquent relationships on `Registration` and `Vaccine` models and updated `postRegister` logic. Verified in test `test_registration_creation_calculates_and_stores_quantity_and_price_in_pivot` that total price was calculated as `1,000,000` (500,000 × 2) and pivot attributes `quantity` === 2, `price` === 500,000.

3. **Backend Idempotency Guarantee**:
   - *Observation*: Network retries or double form submissions could create duplicate registration records.
   - *Deduction*: Checking the idempotency key in cache before database execution prevents duplicate inserts while returning consistent HTTP responses.
   - *Action*: Implemented `IdempotencyMiddleware` and controller-level cache lookup. Verified in test `test_duplicate_registration_request_with_identical_idempotency_key_returns_existing_registration_without_second_record` that 2 consecutive POST requests with identical `Idempotency-Key` returned identical registration codes and DB registration count remained 1.

---

## 3. Caveats

- Idempotency key TTL is set to 24 hours (`now()->addHours(24)`). Requests with identical keys sent after 24 hours will be treated as new requests.
- No other caveats. All requirements were implemented cleanly with native Laravel stdlib features without unneeded third-party packages (Ponytail style).

---

## 4. Conclusion

Milestone M6 requirements are 100% complete and fully verified.
- Public consultation submissions save exclusively to `consultation_leads` without polluting `registrations`.
- Eloquent `Registration` model and `registration_vaccines` pivot table are standardized with `quantity`, `price`, `sale_price`.
- Backend idempotency mechanism guarantees zero duplicate registrations upon request retries.
- 100% test pass rate across all feature test cases in `CrmLeadsAndRegistrationIdempotencyTest.php` and the entire project test suite (58/58 tests pass).

---

## 5. Verification Method

To independently verify the implementation:

1. Run the feature test suite:
   ```bash
   /opt/lampp/bin/php ./vendor/bin/phpunit tests/Feature/CrmLeadsAndRegistrationIdempotencyTest.php
   ```
   *Expected result*: 4 tests, 25 assertions, 100% PASS.

2. Run the full test suite:
   ```bash
   /opt/lampp/bin/php ./vendor/bin/phpunit
   ```
   *Expected result*: 58 tests, 349 assertions, 100% PASS.

3. Inspect modified files:
   - Migration 1: `modules/VaccineRegistration/Database/Migrations/2026_08_01_000001_create_consultation_leads_table.php`
   - Migration 2: `modules/VaccineRegistration/Database/Migrations/2026_08_01_000002_add_sale_price_to_registration_vaccines_table.php`
   - Model 1: `modules/VaccineRegistration/Models/ConsultationLead.php`
   - Model 2: `modules/VaccineRegistration/Models/Registration.php`
   - Controller 1: `modules/VaccineRegistration/Http/Controllers/ConsultationLeadController.php`
   - Controller 2: `modules/VaccineRegistration/Http/Controllers/Admin/AdminConsultationLeadController.php`
   - Controller 3: `modules/VaccineRegistration/Http/Controllers/VaccineController.php`
   - Middleware: `modules/VaccineRegistration/Http/Middleware/IdempotencyMiddleware.php`
   - Routes: `modules/VaccineRegistration/routes/web.php`
   - Feature Test: `tests/Feature/CrmLeadsAndRegistrationIdempotencyTest.php`
   - Legacy Test Adaptation: `tests/Feature/ExampleTest.php`
   - Changelog: `CHANGELOG.md`
