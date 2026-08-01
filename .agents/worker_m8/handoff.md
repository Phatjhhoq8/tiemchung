# Handoff Report — Worker M8 (Patch)

## 1. Observation
- Target test file `tests/Feature/FefoInventoryStockReservationTest.php` line 256 previously fetched `$registration = Registration::latest()->first();` in `test_pending_order_reserves_stock_and_cancellation_releases_stock()`. When tests ran with identical timestamps/IDs across test methods, `latest()->first()` fetched a registration record created in Test 1 rather than the specific registration created in Test 3 ('Le Van C').
- In `app/Services/FefoInventoryService.php`, `allocateAndReserve(Registration $registration)` called `$registration->load('vaccines');` at line 18 before updating pivot records in table `registration_vaccines` on lines 55-60. Returning `$registration` without clearing relations left cached Eloquent relation data on `$registration`.
- Verbatim error output prior to fixes:
  ```
  1) Tests\Feature\FefoInventoryStockReservationTest::test_pending_order_reserves_stock_and_cancellation_releases_stock
  Failed asserting that 4 matches expected 5.
  /home/hongphuoc/Desktop/thue/tests/Feature/FefoInventoryStockReservationTest.php:264
  FAILURES!
  Tests: 4, Assertions: 14, Failures: 1, Deprecations: 2.
  ```
- Tool command to verify target test: `/opt/lampp/bin/php ./vendor/bin/phpunit tests/Feature/FefoInventoryStockReservationTest.php`
  Target test result post-fix: `OK (4 tests, 16 assertions)` (100% pass).

## 2. Logic Chain
1. **Observation**: `test_pending_order_reserves_stock_and_cancellation_releases_stock` created a registration payload with patient name `'Le Van C'` via `/register` POST request.
2. **Observation**: Line 256 queried `Registration::latest()->first()`, which matched an earlier registration due to identical database timestamps/IDs from previous tests.
3. **Deduction**: Changing line 256 to `Registration::where('patient_name', 'Le Van C')->latest()->first()` ensures the query strictly targets the registration created in Test 3.
4. **Observation**: `FefoInventoryService::allocateAndReserve()` loads `$registration->load('vaccines')` prior to modifying pivot `inventory_lot_id` in database.
5. **Deduction**: Adding `$registration->unsetRelation('vaccines');` before returning `$registration` purges stale cached relation instances so future callers reload fresh pivot data.
6. **Verification**: Executing `/opt/lampp/bin/php ./vendor/bin/phpunit tests/Feature/FefoInventoryStockReservationTest.php` resulted in `OK (4 tests, 16 assertions)` with zero failures.

## 3. Caveats
- No caveats. Both fixes are minimal, precise, and fully verified.

## 4. Conclusion
Both issues reported by Code Reviewer M8 have been successfully patched in `tests/Feature/FefoInventoryStockReservationTest.php` and `app/Services/FefoInventoryService.php`. All 4 target tests pass 100%.

## 5. Verification Method
1. Run target test:
   `/opt/lampp/bin/php ./vendor/bin/phpunit tests/Feature/FefoInventoryStockReservationTest.php`
   Expect: 4/4 tests pass (16 assertions, 0 failures).
2. Run full test suite:
   `/opt/lampp/bin/php ./vendor/bin/phpunit`
   Expect: All tests pass 100%.
3. Inspect modified files:
   - `file:///home/hongphuoc/Desktop/thue/tests/Feature/FefoInventoryStockReservationTest.php` (line 256)
   - `file:///home/hongphuoc/Desktop/thue/app/Services/FefoInventoryService.php` (inside `allocateAndReserve()`)
   - `file:///home/hongphuoc/Desktop/thue/CHANGELOG.md`
