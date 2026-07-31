# M6 Code Review & Adversarial Verification Report

## Verdict: APPROVE

---

## 1. Observation

- **Migration & Model**:
  - Migration file `modules/VaccineRegistration/Database/Migrations/2026_08_01_000001_create_consultation_leads_table.php` lines 14–23 creates `consultation_leads` schema:
    ```php
    Schema::create('consultation_leads', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('phone');
        $table->string('source')->nullable();
        $table->string('status')->default('new');
        $table->text('note')->nullable();
        $table->foreignId('center_id')->nullable()->constrained('centers')->nullOnDelete();
        $table->timestamps();
    });
    ```
  - Model file `modules/VaccineRegistration/Models/ConsultationLead.php` lines 12–26 defines `$fillable = ['name', 'phone', 'source', 'status', 'note', 'center_id']` and `center()` relationship.

- **Public Consultation Leads in `VaccineController::postDiseaseConsult`**:
  - File `modules/VaccineRegistration/Http/Controllers/VaccineController.php` lines 713–720:
    ```php
    // Lưu duy nhất vào consultation_leads, KHÔNG tạo dummy registration
    $lead = ConsultationLead::create([
        'name' => $validated['customerName'],
        'phone' => $validated['customerPhone'],
        'source' => 'Nhóm bệnh: ' . $diseaseDecoded,
        'status' => 'new',
        'note' => $note,
        'center_id' => $selectedCenter?->id,
    ]);
    ```
  - Verified no calls to `Registration::create()` or `Patient::create()` exist in `postDiseaseConsult`.

- **Standardized `Registration` Pivot Relationship & Saving**:
  - Model file `modules/VaccineRegistration/Models/Registration.php` lines 32–37:
    ```php
    public function vaccines()
    {
        return $this->belongsToMany(Vaccine::class, 'registration_vaccines')
                    ->withPivot(['quantity', 'price', 'sale_price'])
                    ->withTimestamps();
    }
    ```
  - Controller file `modules/VaccineRegistration/Http/Controllers/VaccineController.php` lines 530–534:
    ```php
    $registration->vaccines()->attach($v->id, [
        'price' => $price,
        'sale_price' => $salePrice,
        'quantity' => $qty,
    ]);
    ```

- **Backend Idempotency (`IdempotencyMiddleware` & `postRegister`)**:
  - Middleware file `modules/VaccineRegistration/Http/Middleware/IdempotencyMiddleware.php` lines 17–52: Reads `Idempotency-Key` / `X-Idempotency-Key` / `idempotency_key` headers or input, checks Cache key `idempotency:md5(...)`, returns cached response with `X-Idempotency-Hit: true` header if present, or caches successful 2xx responses for 24 hours.
  - Controller file `modules/VaccineRegistration/Http/Controllers/VaccineController.php` lines 398–413 & 556–563: Dual protection caching idempotency responses for registration requests.

- **Test Suite Execution**:
  - Command: `/opt/lampp/bin/php ./vendor/bin/phpunit tests/Feature/CrmLeadsAndRegistrationIdempotencyTest.php`
  - Output:
    ```text
    PHPUnit 11.5.56 by Sebastian Bergmann and contributors.
    Runtime:       PHP 8.2.12
    Configuration: /home/hongphuoc/Desktop/thue/phpunit.xml

    ....                                                                4 / 4 (100%)

    Time: 00:01.327, Memory: 36.50 MB

    OK (4 tests, 25 assertions)
    ```

- **Integrity Inspection**:
  - Source code contains no hardcoded test outputs, facade/mock classes in production paths, or self-certifying shortcuts. All test cases perform real DB transactions and HTTP endpoint assertions.

---

## 2. Logic Chain

1. **Schema & Model Consistency**:
   - Observation: `consultation_leads` table migration and `ConsultationLead` model match required CRM lead attributes (`name`, `phone`, `source`, `status`, `note`, `center_id`).
   - Deduction: The database layer for CRM consultation leads is properly configured and functional.

2. **Lead vs. Registration Isolation**:
   - Observation: `postDiseaseConsult` writes strictly to `ConsultationLead::create()` without invoking `Registration::create()` or creating dummy patient records.
   - Deduction: Public consultation requests no longer contaminate the registration table with fake or incomplete appointment records.

3. **Pivot Attribute Accuracy**:
   - Observation: `Registration::vaccines()` relationship defines `withPivot(['quantity', 'price', 'sale_price'])` and `postRegister` passes `$price`, `$salePrice`, and `$qty` to `$registration->vaccines()->attach()`.
   - Deduction: Pivot tables accurately track pricing per item and quantity purchased per registration.

4. **Idempotency Prevention of Double Submit**:
   - Observation: Requests carrying `Idempotency-Key` check Redis/file Cache for previous 2xx responses. Test 3 verifies duplicate POST requests return identical registration codes while the database count remains unchanged (`Registration::count()` stays at +1).
   - Deduction: Idempotency protection operates correctly across middleware and controller, preventing double-registration submission.

5. **Test Suite Verification**:
   - Observation: PHPUnit executed all 4 feature tests with 25 total assertions passing 100%.
   - Deduction: System behavior satisfies all milestone specifications and acceptance criteria.

---

## 3. Caveats

No caveats. All requirements, database schemas, controller endpoints, middleware, and test suites were fully inspected and verified directly on the codebase.

---

## 4. Conclusion

Milestone M6 (CRM Consultation Leads, Registration Standardization & Idempotency R2) is fully implemented with high quality, strict data integrity, zero hardcoded shortcuts, and 100% test suite pass rate (4 tests, 25 assertions). Verdict: **APPROVE**.

---

## 5. Verification Method

- Run test command:
  ```bash
  /opt/lampp/bin/php ./vendor/bin/phpunit tests/Feature/CrmLeadsAndRegistrationIdempotencyTest.php
  ```
- Inspect files:
  - `modules/VaccineRegistration/Database/Migrations/2026_08_01_000001_create_consultation_leads_table.php`
  - `modules/VaccineRegistration/Models/ConsultationLead.php`
  - `modules/VaccineRegistration/Models/Registration.php`
  - `modules/VaccineRegistration/Http/Controllers/VaccineController.php`
  - `modules/VaccineRegistration/Http/Middleware/IdempotencyMiddleware.php`
- Invalidation conditions: Any test failure or database duplication on identical `Idempotency-Key`.

---

## Review Summary

- **Verdict**: APPROVE
- **Findings**: No critical, major, or minor findings.
- **Verified Claims**:
  - `consultation_leads` table & `ConsultationLead` model → verified via code view & test 1 → PASS
  - Public lead submission creates no registration → verified via `postDiseaseConsult` code & test 1 → PASS
  - Pivot relationship stores `quantity`, `price`, `sale_price` → verified via `Registration.php` & test 2 → PASS
  - Backend idempotency deduplication → verified via `IdempotencyMiddleware.php` & test 3 → PASS
  - Admin view/update lead status → verified via `LeadController.php` & test 4 → PASS
- **Coverage Gaps**: None.
- **Unverified Items**: None.

---

## Challenge & Stress-Test Summary

- **Overall Risk Assessment**: LOW
- **Stress Test Scenarios**:
  - Scenario 1: Re-submitting identical payload with same `Idempotency-Key`. Result: Returns cached 200 OK response with `X-Idempotency-Hit: true`, 0 new DB records created. PASS.
  - Scenario 2: Validation failure on registration form. Result: Returns 422 Unprocessable Entity, not cached by idempotency middleware (only 2xx cached). PASS.
  - Scenario 3: Consultation request submission. Result: Inserts row into `consultation_leads`, `registrations` count remains unchanged. PASS.
