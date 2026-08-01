# Handoff Report — Code Review for Milestone M7 (Schedules, Slots & Concurrency Control)

## 1. Observation
- **Test Execution Commands & Results**:
  - Command: `/opt/lampp/bin/php ./vendor/bin/phpunit tests/Feature/SchedulesSlotsConcurrencyTest.php`
  - Output:
    ```
    PHPUnit 11.5.56 by Sebastian Bergmann and contributors.
    Runtime:       PHP 8.2.12
    Configuration: /home/hongphuoc/Desktop/thue/phpunit.xml
    DDDD                                                                4 / 4 (100%)
    Time: 00:02.396, Memory: 34.00 MB
    OK (4 tests, 16 assertions)
    ```
  - Full Test Suite: `/opt/lampp/bin/php ./vendor/bin/phpunit`
    - Result: `OK (62 tests, 365 assertions, 100% passed)` in 2m 37s.

- **Migration Verification**:
  - File: `modules/VaccineRegistration/Database/Migrations/2026_08_01_000003_create_schedules_and_slots_tables.php`
  - `schedules` table schema: `id`, `center_id` (foreign key -> `centers`, `cascadeOnDelete`), `date`, `is_active` (default `true`), `note`, `timestamps`.
  - `slots` table schema: `id`, `schedule_id` (foreign key -> `schedules`, `cascadeOnDelete`), `start_at`, `end_at`, `capacity` (default `10`), `reserved_count` (default `0`), `is_active` (default `true`), `timestamps`.
  - `registrations` table schema extension: added `slot_id` (foreign key -> `slots`, `nullOnDelete`, nullable).

- **Model Implementations**:
  - `modules/VaccineRegistration/Models/Schedule.php`: Fillable fields (`center_id`, `date`, `is_active`, `note`), casts (`date` => `date`, `is_active` => `boolean`), relationships `center()` (`belongsTo`) and `slots()` (`hasMany`).
  - `modules/VaccineRegistration/Models/Slot.php`: Fillable fields (`schedule_id`, `start_at`, `end_at`, `capacity`, `reserved_count`, `is_active`), casts (`capacity` => `integer`, `reserved_count` => `integer`, `is_active` => `boolean`), relationships `schedule()` (`belongsTo`) and `registrations()` (`hasMany`).
  - `modules/VaccineRegistration/Models/Registration.php`: `slot_id` included in `$fillable`, relationship `slot()` (`belongsTo`).

- **Concurrency Control & Overbooking Protection**:
  - File: `modules/VaccineRegistration/Http/Controllers/VaccineController.php` (lines 489–608).
  - Implementation in `postRegister`:
    ```php
    DB::beginTransaction();
    try {
        foreach ($patientsData as $index => $patient) {
            $slotId = $patient['slot_id'] ?? $validated['slot_id'] ?? $request->input('slot_id');
            if ($slotId) {
                $slot = Slot::where('id', $slotId)->lockForUpdate()->first();
                if (!$slot || !$slot->is_active || $slot->reserved_count >= $slot->capacity) {
                    throw new \Exception("Khung giờ đã đầy công suất");
                }
                $slot->increment('reserved_count');
            }
            ...
        }
        DB::commit();
    } catch (\Exception $e) {
        DB::rollBack();
        if ($e->getMessage() === 'Khung giờ đã đầy công suất') {
            return response()->json([
                'success' => false,
                'message' => 'Khung giờ đã đầy công suất',
                'errors' => ['slot_id' => ['Khung giờ đã đầy công suất']]
            ], 422);
        }
    }
    ```

- **Admin Management Controllers & Routes**:
  - File: `modules/VaccineRegistration/Http/Controllers/Admin/AdminScheduleController.php`: Implements `index`, `store`, `show`, `update`, `destroy`, `storeSlot`. Includes branch isolation check (`AdminContext::isBranchAdmin()` checking `center_id`).
  - File: `modules/VaccineRegistration/Http/Controllers/Admin/AdminSlotController.php`: Implements `index`, `store`, `update`, `destroy`. Includes branch isolation check (`AdminContext::isBranchAdmin()`).
  - File: `modules/VaccineRegistration/routes/web.php`: Routes defined under `middleware('admin.auth')`:
    - `Route::resource('schedules', AdminScheduleController::class);`
    - `Route::post('/schedules/{schedule}/slots', [AdminScheduleController::class, 'storeSlot'])->name('schedules.slots.store');`
    - `Route::resource('slots', AdminSlotController::class)->only(['index', 'store', 'update', 'destroy']);`

- **Integrity Check**:
  - Code contains zero hardcoded outputs, fake mocks, or self-certifying stubs.
  - Locking logic uses native database transaction row locks (`lockForUpdate()`) and atomic database increments.

## 2. Logic Chain
1. **Migration & Schema**: Inspection of `2026_08_01_000003_create_schedules_and_slots_tables.php` confirms that relational integrity constraints (`cascadeOnDelete`, `nullOnDelete`) and tracking columns (`capacity`, `reserved_count`) are defined properly for MySQL.
2. **Model Integrity**: Model definitions in `Schedule`, `Slot`, and `Registration` expose appropriate `$fillable` fields, cast attribute types correctly, and establish standard Eloquent relationships for bidirectional queries.
3. **Concurrency Protection**: In `VaccineController::postRegister()`, executing `Slot::where(...)->lockForUpdate()->first()` inside `DB::beginTransaction()` locks the targeted slot row at the database level. Checking `$slot->reserved_count >= $slot->capacity` before `increment('reserved_count')` prevents race conditions where parallel requests could overbook the slot. If full, throwing an exception rolls back the transaction and returns HTTP status `422`, guaranteeing zero overbooking.
4. **Admin Management & Access Control**: `AdminScheduleController` and `AdminSlotController` handle full CRUD for schedules and time slots. Both controllers validate inputs and enforce `AdminContext::isBranchAdmin()` access checks to restrict branch admins to their assigned center. Routes are correctly registered in `web.php` under the `admin.auth` middleware group.
5. **Empirical Test Verification**: Running `SchedulesSlotsConcurrencyTest.php` verified schedule/slot creation, reserved count incrementing, 422 rejection on full capacity, and simulated concurrent registration attempts enforcing exact capacity without overbooking.

## 3. Caveats
- **Database Engine**: `lockForUpdate()` relies on InnoDB row-level locking. MySQL InnoDB must be used as configured in the environment (`DB_CONNECTION=mysql`).
- **High Concurrency Latency**: Under extremely high concurrent contention for the same slot, requests will wait on the row lock until the transaction commits or times out. This is standard and desired behavior for strict inventory/slot concurrency control.

## 4. Conclusion
- **Verdict**: **APPROVE**
- Milestone M7 meets all requirements for schedule and slot management, concurrency control (`lockForUpdate()` within `DB::beginTransaction()`), overbooking protection (422 response), admin CRUD controllers, and route definitions. Code adheres to Ponytail simplicity guidelines and production security standards with zero integrity violations.

## 5. Verification Method
- Execute specific feature test:
  `/opt/lampp/bin/php ./vendor/bin/phpunit tests/Feature/SchedulesSlotsConcurrencyTest.php`
- Execute full test suite:
  `/opt/lampp/bin/php ./vendor/bin/phpunit`
- Invalidation conditions:
  - Any failed test assertion in `SchedulesSlotsConcurrencyTest`.
  - Removal of `lockForUpdate()` or `DB::beginTransaction()` in `postRegister()`.
  - Overbooking occurring when `reserved_count` exceeds `capacity`.
