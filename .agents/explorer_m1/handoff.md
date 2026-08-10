# Handoff Report: Admin Dashboard Exploration & Requirements Analysis

- **Agent**: explorer_m1
- **Working Directory**: `/home/hongphuoc/Desktop/thue/.agents/explorer_m1`
- **Date**: 2026-08-10

---

## 1. Observation

### 1.1. Controller Analysis
- **File**: `file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Http/Controllers/Admin/AdminDashboardController.php`
- **Lines 23-32**: Reads center list and resolves branch context using `AdminContext::resolveListCenterId($request)`:
  ```php
  $centers = Center::active()->orderBy('sort_order')->orderBy('id')->get();
  $selectedCenterId = AdminContext::resolveListCenterId($request);
  ```
- **Lines 34-39**: Aggregates registration stats via a single query:
  ```php
  $stats = (clone $registrationQuery)->selectRaw("
      COUNT(*) as total_registrations,
      COALESCE(SUM(CASE WHEN payment_status = 'paid' THEN total_price - points_discount_amount ELSE 0 END), 0) as total_revenue,
      COALESCE(SUM(CASE WHEN payment_status = 'unpaid' AND booking_status != 'cancelled' THEN 1 ELSE 0 END), 0) as pending_count,
      COALESCE(SUM(CASE WHEN booking_status = 'completed' THEN 1 ELSE 0 END), 0) as completed_count
  ")->first();
  ```
- **Lines 45-47**: Hardcoded zero counts:
  ```php
  $consultCount = 0;
  $importedQuantity = 0;
  $soldQuantity = 0;
  ```
- **Lines 49-66**: Loads 8 recent registrations, active vaccine count (`vaccinesCount`), center count (`centersCount`), and passes them to view `vaccine::admin.dashboard`.
- **Missing Features**:
  - `consultCount` is not dynamically queried from `consultation_leads`.
  - `importedQuantity` and `soldQuantity` are not dynamically queried from `inventory_lots`.
  - No trend calculations or datasets for 7-day or 6-month revenue & registration charts.

### 1.2. Blade View & Design System
- **View File**: `file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/resources/views/admin/dashboard.blade.php`
- **Layout File**: `file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/resources/views/layouts/admin.blade.php`
- **Layout CSS Variables (Lines 40-71 in admin.blade.php)**:
  ```css
  --primary-color: #c8102e;       /* Medicare Red */
  --primary-hover: #a00d24;
  --secondary-color: #eaaa00;     /* Medicare Gold */
  --accent-color: #004b8f;        /* Medicare Navy */
  --accent-hover: #00386c;
  ```
- **Contrast & Style Rules (`file:///home/hongphuoc/Desktop/thue/.agents/COLOR_RULE.md`)**:
  - Red background (`#c8102e`) requires white text (`#ffffff`).
  - White background (`#ffffff`) requires dark text (`#0f172a` / `#1e293b`).
  - Gold (`#eaaa00`) is restricted to text highlights / accent icons, NOT solid background fill for buttons or cards.

### 1.3. Database Schema & Entities
1. **`consultation_leads` Table & `ConsultationLead` Model**:
   - Model: `file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Models/ConsultationLead.php`
   - Migration: `file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Database/Migrations/2026_08_01_000001_create_consultation_leads_table.php`
   - Columns: `id`, `name`, `phone`, `source`, `status` (default `'new'`), `note`, `center_id` (foreign key to `centers`, nullable), `created_at`, `updated_at`.
   - Relationship: `center()` belongs to `Center`.
2. **`inventory_lots` Table & `InventoryLot` Model**:
   - Model: `file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Models/InventoryLot.php`
   - Migration: `file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Database/Migrations/2026_08_01_000004_create_inventory_lots_and_stock_movements_tables.php`
   - Columns: `id`, `vaccine_id`, `center_id`, `lot_number`, `initial_quantity`, `available_quantity`, `reserved_quantity` (default 0), `expires_at`, `status` (default `'active'`), `created_at`, `updated_at`.
   - Relationships: `vaccine()`, `center()`, `stockMovements()`.
3. **`registrations` Table & `Registration` Model**:
   - Model: `file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Models/Registration.php`
   - Migrations:
     - `file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Database/Migrations/2026_07_17_000002_create_registrations_table.php`
     - `file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Database/Migrations/2026_07_31_000003_add_center_id_to_registrations_table.php`
     - `file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Database/Migrations/2026_08_02_000002_extend_registrations_for_customer_booking_and_payment.php`
   - Key Columns: `id`, `registration_code`, `patient_name`, `patient_phone`, `center_id`, `center_name`, `injection_date`, `status`, `booking_status`, `payment_status`, `total_price`, `points_discount_amount`, `paid_at`, `created_at`.
   - Built-in composite indexes:
     - `['center_id', 'injection_date']`
     - `['center_id', 'payment_status', 'created_at']`
     - `['customer_id', 'created_at']`
4. **`centers` Table & `Center` Model**:
   - Model: `file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Models/Center.php`
   - Key Scope: `Center::active()` filters `is_active = true`.

### 1.4. Center Context & Filtering Mechanism
- Helper: `file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Support/AdminContext.php`
- Method: `AdminContext::resolveListCenterId(Request $request)`
  - SuperAdmin: Resolves `$request->input('center_id')` or session `admin_selected_center_id`. Returns `int` or `null` (All Centers). Automatically updates session when `$request->has('center_id')`.
  - BranchAdmin: Strictly returns user's `$user->center_id`. Throws `403` if a branch admin tries to pass another center's ID.

### 1.5. Existing Test Suite Patterns
- Test Files: `file:///home/hongphuoc/Desktop/thue/tests/Feature/AdminRootGlobalBranchContextTest.php`, `file:///home/hongphuoc/Desktop/thue/tests/Feature/AdminAjaxFilteringTest.php`
- Uses `DatabaseTransactions` trait.
- Uses `actingAsAdmin(User $user)` / `actingAsSuperAdmin()` helper methods setting session keys (`admin_logged_in`, `admin_user_id`, `admin_role`, `admin_center_id`).

---

## 2. Logic Chain

1. **Hardcoded Stats Replacement**:
   - **Observation 1.1**: Lines 45-47 set `$consultCount = 0`, `$importedQuantity = 0`, `$soldQuantity = 0`.
   - **Observation 1.3**: `consultation_leads` has `center_id` and `status`. `inventory_lots` has `center_id`, `initial_quantity`, `available_quantity`, `reserved_quantity`.
   - **Deduction**: We can replace hardcoded values with real queries scoped by `$selectedCenterId`:
     - `$consultCount`: `ConsultationLead::when($selectedCenterId, fn($q) => $q->where('center_id', $selectedCenterId))->count()` (or `where('status', 'new')` depending on business decision; total count is standard).
     - `$importedQuantity`: `InventoryLot::when($selectedCenterId, fn($q) => $q->where('center_id', $selectedCenterId))->sum('initial_quantity')`.
     - `$soldQuantity`: `InventoryLot::when($selectedCenterId, fn($q) => $q->where('center_id', $selectedCenterId))->sum('reserved_quantity')` or `sum(initial_quantity - available_quantity)`.

2. **Revenue Trends & Registration Counts (7 Days / 6 Months)**:
   - **Observation 1.3**: `registrations` table has composite index `['center_id', 'payment_status', 'created_at']`.
   - **Observation 1.4**: `$selectedCenterId` is provided by `AdminContext::resolveListCenterId($request)`.
   - **Deduction**:
     - **Last 7 Days (Daily)**: Query `Registration::query()`, filter by `$selectedCenterId` if set, where `created_at >= now()->subDays(6)->startOfDay()`, select `DATE(created_at) as date`, `COUNT(*) as count`, `SUM(CASE WHEN payment_status = 'paid' THEN total_price - points_discount_amount ELSE 0 END) as revenue`, grouped by `DATE(created_at)`. Map over the 7 dates to fill missing zero dates.
     - **Last 6 Months (Monthly)**: Query `Registration::query()`, filter by `$selectedCenterId` if set, where `created_at >= now()->subMonths(5)->startOfMonth()`, select `DATE_FORMAT(created_at, '%Y-%m') as month`, `COUNT(*) as count`, `SUM(CASE WHEN payment_status = 'paid' THEN total_price - points_discount_amount ELSE 0 END) as revenue`, grouped by `DATE_FORMAT(created_at, '%Y-%m')`. Map over the 6 months to fill missing zero months.

3. **Brand Palette & Design System Compliance**:
   - **Observation 1.2**: Brand colors are defined as Primary Red (`#c8102e`), Secondary Gold (`#eaaa00`), Accent Navy (`#004b8f`).
   - **Deduction**: All UI widgets and trend charts added to `dashboard.blade.php` must strictly use these CSS variables (`var(--primary-color)`, `var(--secondary-color)`, `var(--accent-color)`) and satisfy text contrast rules.

---

## 3. Caveats

- **No Caveats**: All models, database migrations, controllers, views, design rules, and test patterns were fully inspected.

---

## 4. Conclusion

- `AdminDashboardController` requires updating to:
  1. Calculate dynamic values for `$consultCount`, `$importedQuantity`, `$soldQuantity`.
  2. Compute 7-day daily and 6-month monthly trend data arrays for registrations and paid revenue filtered by `$selectedCenterId`.
- `dashboard.blade.php` requires updating to:
  1. Display the real stats for Consultation Leads, Imported Stock, and Sold Stock.
  2. Render 7-day and 6-month trend visualization components matching the Medicare 3-color brand theme.
- All center filtering must utilize `AdminContext::resolveListCenterId($request)` to preserve SuperAdmin and BranchAdmin security boundaries.

---

## 5. Verification Method

1. **Codebase Inspection**:
   - Verify controller: `view_file` on `modules/VaccineRegistration/Http/Controllers/Admin/AdminDashboardController.php`.
   - Verify view: `view_file` on `modules/VaccineRegistration/resources/views/admin/dashboard.blade.php`.
2. **Automated Test Execution**:
   - Run feature tests: `php artisan test tests/Feature/AdminRootGlobalBranchContextTest.php` and `php artisan test tests/Feature/AdminAjaxFilteringTest.php`.
