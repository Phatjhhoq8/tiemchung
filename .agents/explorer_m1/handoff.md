# Handoff Report: Milestone 1 - Weekly Calendar Grid Implementation Exploration

## 1. Observation

Direct observations from examining the project files and database migrations:

### A. Routes Configuration
- **File**: `modules/VaccineRegistration/routes/web.php` (Lines 140-145)
- **Current Defined Routes**:
  ```php
  Route::get('/default-slots', [AdminDefaultSlotController::class, 'index'])->name('default-slots.index');
  Route::post('/default-slots/update', [AdminDefaultSlotController::class, 'update'])->name('default-slots.update');
  Route::resource('schedules', AdminScheduleController::class)->only(['index', 'store', 'update', 'destroy']);
  Route::post('/schedules/{schedule}/slots', [AdminScheduleController::class, 'storeSlot'])->name('schedules.slots.store');
  Route::resource('slots', AdminSlotController::class)->only(['store', 'update', 'destroy']);
  ```
- **Finding**: There is currently no `copy` route defined for schedules (e.g. `POST /admin/schedules/copy`).

### B. Controllers & Business Logic
- **`AdminScheduleController.php`** (`modules/VaccineRegistration/Http/Controllers/Admin/AdminScheduleController.php`):
  - `index(Request $request)` (Lines 17-42): Calls `Schedule::generateFromDefaults($selectedCenterId, today(), today()->addDays(30))` when a center is selected, then queries `Schedule::with(['center', 'slots'])` filtered by `$request->input('date')` if present, and returns `latest('date')->paginate(15)`.
  - `store(Request $request)` (Lines 47-101): Validates `center_id`, `date`, `note`, `is_active`, and `slots` array (`start_at`, `end_at`, `capacity`, `is_active`). Performs `Schedule::updateOrCreate` and `firstOrCreate` on slots.
  - `update(Request $request, $id)` (Lines 125-148): Updates `date`, `note`, `is_active` status of a schedule after checking `AdminContext::assertCanManageCenter`.
  - `destroy(Request $request, $id)` (Lines 153-169): Deletes schedule by ID after checking `AdminContext::assertCanManageCenter`.
  - `storeSlot(Request $request, $scheduleId)` (Lines 174-204): Creates a new slot for an existing schedule.
- **`AdminSlotController.php`** (`modules/VaccineRegistration/Http/Controllers/Admin/AdminSlotController.php`):
  - `update(Request $request, $id)` (Lines 79-107): Checks `capacity >= reserved_count` before updating slot capacity.
  - `destroy(Request $request, $id)` (Lines 112-128): Deletes a slot after checking `AdminContext::assertCanManageCenter`.
- **`AdminDefaultSlotController.php`** (`modules/VaccineRegistration/Http/Controllers/Admin/AdminDefaultSlotController.php`):
  - Manages weekly default slots (day 1 = Monday to 7 = Sunday) stored in `default_slots` table.

### C. Views
- **`index.blade.php`** (`modules/VaccineRegistration/resources/views/admin/schedules/index.blade.php`):
  - Lines 15-53: Top card with form to open custom schedule for a single date (`<input type="date">`) with dynamically added slot rows.
  - Lines 55-118: Bottom card with date filter and a paginated vertical list (`$schedules->paginate(15)`) displaying schedules and inline slot badges.
  - Lines 121-172: Edit Slot Modal (`editSlotModal`) supporting slot edit and delete.
  - **Finding**: Currently displays a paginated list of schedules, NOT a 7-column weekly grid. Needs total UI redesign to present a 7-column calendar grid (Monday to Sunday) with week navigation controls.

### D. Data Models & Database Schema
- **`Schedule.php`** (`modules/VaccineRegistration/Models/Schedule.php`):
  - Table `schedules`: `id`, `center_id`, `date` (date), `is_active` (bool), `note`, `created_at`, `updated_at`.
  - Unique index: `['center_id', 'date']` (Migration `2026_08_02_000003_harden_schedule_slot_indexes.php`).
  - Helper method `Schedule::generateFromDefaults($centerId, $startDate, $endDate)` (Lines 34-73): Uses `firstOrCreate` to safely generate missing schedules and default slots from `default_slots` without overwriting existing schedules.
- **`Slot.php`** (`modules/VaccineRegistration/Models/Slot.php`):
  - Table `slots`: `id`, `schedule_id`, `start_at` (H:i), `end_at` (H:i), `capacity` (int), `reserved_count` (int), `is_active` (bool), `created_at`, `updated_at`.
  - Unique index: `['schedule_id', 'start_at', 'end_at']` (Migration `2026_08_02_000004_harden_slot_uniqueness.php`).
- **`Registration.php`** (`modules/VaccineRegistration/Models/Registration.php`):
  - Table `registrations`: `slot_id` foreign key referencing `slots.id`.
  - `reserved_count` on `Slot` tracks active patient bookings.

### E. Branch Security Context
- **`AdminContext.php`** (`modules/VaccineRegistration/Support/AdminContext.php`):
  - `AdminContext::resolveListCenterId($request)` enforces branch selection: branch admin is locked to `user->center_id` (returns 403 if trying to access other centers).
  - `AdminContext::assertCanManageCenter($centerId)` checks if user is super admin or branch admin assigned to `$centerId`.

### F. Tests
- `tests/Feature/SchedulesSlotsConcurrencyTest.php`: Verifies schedule creation, slot capacity limits, and branch isolation.
- `tests/Feature/AdminDefaultSlotsTest.php`: Verifies default slot updates and `Schedule::generateFromDefaults()` behavior.

---

## 2. Logic Chain

From the observations above, the logic chain to implement Milestone 1 is as follows:

1. **R1: 7-Column Weekly Grid Implementation**:
   - **Week Resolution**: In `AdminScheduleController::index`, calculate week start (Monday) and week end (Sunday) using Carbon based on `$request->input('week')` or `$request->input('date')` (defaulting to current week `now()->startOfWeek()`).
   - **Auto-Generation Range**: Change the generator call from `today() -> today()->addDays(30)` to `Schedule::generateFromDefaults($selectedCenterId, $weekStart->toDateString(), $weekEnd->toDateString())` so the displayed 7 days are always populated if default template slots exist.
   - **Data Structure**: Build an array of 7 day items (Monday to Sunday). For each date, fetch the `Schedule` model with its `slots`. Calculate total capacity and total `reserved_count` for each day.
   - **UI Component Structure**:
     - Top bar: Week navigation buttons ("Tuần trước", "Tuần hiện tại", "Tuần sau", `<input type="date">` picker) and displaying the current week date range header (e.g. `10/08/2026 - 16/08/2026`).
     - 7-Column CSS Grid (`grid-template-columns: repeat(7, 1fr)` responsive layout).
     - Day Column Header: Displays Day Name (Thứ 2 → Chủ nhật), formatted date (`d/m/Y`), status badge (Active/Closed toggle button), summary metrics (`reserved_count / capacity`), "Thêm khung giờ" button, "Sao chép lịch" button, and "Xóa lịch ngày" button.
     - Slots List in Column: Displays slot badges with time range, `reserved_count/capacity`, active state toggle, and edit button triggering `editSlotModal`.

2. **R2: Copy Schedule Feature & Safety Validation Guard**:
   - **New Route**: `POST /admin/schedules/copy` mapped to `AdminScheduleController::copySchedule` (`admin.schedules.copy`).
   - **Validation Logic**:
     - Request validation: `center_id`, `source_date`, `target_dates` (array of target date strings).
     - Scope check: `AdminContext::assertCanManageCenter($centerId)`.
     - Source Schedule check: Ensure `source_date` schedule exists and has slots.
     - **Safety Guard (`reserved_count > 0`)**: For each date in `target_dates`, check if a schedule exists on `target_date` for `$centerId`. If existing target slots have `reserved_count > 0` (or linked `Registration` records), **REJECT** the copy operation for that day with a clear error: `"Không thể sao chép đè lịch ngày {date} vì đã có {count} lượt đặt tiêm!"`.
     - Transaction Execution: For target dates with zero bookings (`reserved_count == 0`), delete existing slots, update/create target `Schedule`, and clone slots from source schedule.

3. **R3: Branch Scope, SPA AJAX Updates & Compatibility**:
   - **Branch Security**: Pass `$selectedCenterId` through `AdminContext::resolveListCenterId($request)` and ensure all mutate endpoints invoke `AdminContext::assertCanManageCenter($centerId)`.
   - **SPA AJAX Navigation & Updates**:
     - Week switching, day status toggle, slot add/edit/delete, day schedule delete, and schedule copying support AJAX JSON/HTML responses.
     - Return standard JSON responses `{ success: true, message: '...', html: '...' }` so front-end JavaScript can dynamically re-render the 7-column grid without full page reloads.
   - **Auto-Schedule Generator Integration**:
     - Keep `Schedule::generateFromDefaults()` untouched in logic, but call it for the selected week's 7 dates. Because it uses `firstOrCreate`, manually edited or copied target schedules will never be overwritten by the auto-generator.

---

## 3. Caveats

1. **Responsive Viewport**: On smaller screen resolutions (e.g. tablet/mobile), 7 columns side-by-side may overflow horizontally. The UI layout should use `overflow-x: auto` or a responsive flex/grid fallback to guarantee readability on mobile/tablet screens.
2. **Copying to Past Dates**: Copying schedule to past dates should be restricted or guarded by validation (`target_dates.*` >= `today()`) unless retroactive editing is explicitly desired.
3. **Partial Copy Rollback vs Atomic Fail**: When copying a schedule to multiple target dates where 1 target date has existing bookings (`reserved_count > 0`), the entire copy request should fail atomically with a validation error identifying the blocked target dates, preventing partial inconsistent schedule states.

---

## 4. Conclusion

The codebase already has robust foundational models (`Schedule`, `Slot`, `DefaultSlot`, `Center`), database indexes, security scoping (`AdminContext`), and auto-schedule generation logic (`Schedule::generateFromDefaults`). 

To achieve Milestone 1:
1. Define a new route `POST /admin/schedules/copy` in `modules/VaccineRegistration/routes/web.php`.
2. Update `AdminScheduleController.php`:
   - Enhance `index()` to resolve the 7 dates of the target week, auto-generate missing days, and return weekly grid data (or HTML partials for AJAX).
   - Add `copySchedule()` method with strict `reserved_count > 0` safety validation guard.
3. Redesign `modules/VaccineRegistration/resources/views/admin/schedules/index.blade.php`:
   - Replace the single-day form & 15-item paginated list with a 7-column weekly grid layout, week navigation bar (Prev / Current / Next / Date Picker), day status toggle buttons, copy schedule modal, and AJAX JS grid interactions.

---

## 5. Verification Method

To independently verify the implementation after code changes:

1. **Automated Feature Tests**:
   - Run PHPUnit / Pest tests for schedules and default slots:
     ```bash
     php artisan test --filter=SchedulesSlotsConcurrencyTest
     php artisan test --filter=AdminDefaultSlotsTest
     ```
   - Add a new test class `tests/Feature/AdminWeeklyScheduleGridTest.php` to verify:
     - Week navigation (fetching 7 dates for specified week).
     - Copy schedule to target days when target days have 0 bookings (success).
     - Copy schedule blocked when target day has `reserved_count > 0` (ValidationException error).
     - Branch admin authorization checks (Branch admin blocked from viewing/copying another center's schedule).

2. **Manual Visual & Interaction Verification**:
   - Log in as Super Admin and Branch Admin.
   - Navigate to `/admin/schedules`.
   - Verify 7 columns (Monday to Sunday) render correctly with dates, slot metrics, and status badges.
   - Click "Tuần trước", "Tuần sau", and pick dates using the Date Picker to verify AJAX week switching.
   - Test "Sao chép lịch" from Monday to Tuesday & Wednesday; verify slots are duplicated on target days.
   - Create a test booking on Tuesday, then try copying Monday's schedule to Tuesday; verify the system blocks the copy operation with a warning.
