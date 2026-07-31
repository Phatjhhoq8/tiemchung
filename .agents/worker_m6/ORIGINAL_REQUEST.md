## 2026-08-01T00:40:49Z

<USER_REQUEST>
You are the Implementation Worker for Milestone M6: CRM Consultation Leads, Registration Standardization & Idempotency (R2, Ponytail Style).
Your working directory is: /home/hongphuoc/Desktop/thue/.agents/worker_m6

Task Requirements (Ponytail Style - Minimal, Native, Clean):
1. **CRM Leads**:
   - Create migration and model `ConsultationLead` for table `consultation_leads` (`name`, `phone`, `source`, `status`, `note`, `center_id`, timestamps).
   - Ensure consultation requests submitted via public endpoint/form save exclusively to `consultation_leads` without creating dummy patient accounts or fake registrations in `registrations` table.
   - Create controller/service/routes for lead creation and admin viewing/status management.

2. **Registration Standardization**:
   - Standardize `registrations` transaction model & `registration_vaccines` pivot table.
   - Ensure pivot table `registration_vaccines` contains `quantity` and `price` (and `sale_price` if applicable), properly mapped via Eloquent `belongsToMany(Vaccine::class, 'registration_vaccines')->withPivot(['quantity', 'price'])` relationship on `Registration` model.

3. **Backend Idempotency (Chống gửi trùng)**:
   - Implement backend idempotency mechanism (e.g. middleware or controller logic) checking `idempotency_key` header or payload.
   - Store idempotency key in Cache/DB for a time window. If duplicate request received with same key, return existing registration response (200/201) without creating duplicate registration records.

4. **Testing**:
   - Write feature tests in `tests/Feature/CrmLeadsAndRegistrationIdempotencyTest.php` covering:
     * Public lead submission creates `consultation_leads` record and NO `registrations` record.
     * Registration creation correctly calculates and stores `quantity` and `price` in `registration_vaccines` pivot.
     * Duplicate registration request with identical `idempotency_key` returns existing registration without creating a second record in DB.
   - Run `/opt/lampp/bin/php ./vendor/bin/phpunit tests/Feature/CrmLeadsAndRegistrationIdempotencyTest.php` and verify all tests pass 100%.

5. **MANDATORY INTEGRITY WARNING**:
   DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A Forensic Auditor will independently verify your work.

6. Produce a handoff report in `/home/hongphuoc/Desktop/thue/.agents/worker_m6/handoff.md` with modified files list, implementation summary, test commands and output.
7. Send a message to parent with your handoff summary and file paths.
</USER_REQUEST>
