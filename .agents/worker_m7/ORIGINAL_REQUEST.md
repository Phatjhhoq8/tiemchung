## 2026-08-01T00:45:00Z
You are the Implementation Worker for Milestone M7: Schedules, Slots & Concurrency Control (R3, Ponytail Style).
Your working directory is: /home/hongphuoc/Desktop/thue/.agents/worker_m7

Task Requirements (Ponytail Style - Minimal, Native, Clean):
1. **Schedules & Slots Schema**:
   - Create migrations & models for `Schedule` (`center_id`, `date`, `is_active`, `note`) and `Slot` (`schedule_id`, `start_at`, `end_at`, `capacity`, `reserved_count`, `is_active`).
   - Add `slot_id` (foreign key to `slots`) to `registrations` table.

2. **Atomic Concurrency Control**:
   - When a user selects a time slot during registration, execute time slot reservation inside `DB::transaction()` and apply native database row-locking (`Slot::where('id', $slotId)->lockForUpdate()->first()`).
   - Check `reserved_count < capacity`. If reserved_count >= capacity, throw exception or return error message "Khung giờ đã đầy công suất" (http status 422 or redirect with error).
   - Increment `reserved_count` atomically on successful registration.

3. **Admin Schedule & Slot Management**:
   - Create simple admin routes and controller (`AdminSlotController` or `AdminScheduleController`) allowing admins to set center schedule dates and slot capacities.

4. **Testing**:
   - Write feature test suite `tests/Feature/SchedulesSlotsConcurrencyTest.php` covering:
     * Creation of schedules and slots with specified capacity.
     * Reservation of slot increments `reserved_count`.
     * Attempting to reserve a slot when `reserved_count >= capacity` is rejected (422/error) with zero overbooking.
     * Simulated concurrent reservations with `lockForUpdate` ensuring exact capacity enforcement.
   - Run `/opt/lampp/bin/php ./vendor/bin/phpunit tests/Feature/SchedulesSlotsConcurrencyTest.php` and verify all tests pass 100%.

5. **MANDATORY INTEGRITY WARNING**:
   DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A Forensic Auditor will independently verify your work.

6. Update `CHANGELOG.md` with release notes in English.
7. Produce handoff report in `/home/hongphuoc/Desktop/thue/.agents/worker_m7/handoff.md`.
8. Send a message to parent with handoff summary and file paths.
