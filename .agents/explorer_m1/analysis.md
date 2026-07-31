# Milestone 1 Codebase Analysis Report
**Project**: Medicare Vaccination System (Tiemchung)  
**Scope**: Audit of Refactoring Tasks R1, R2, R3, R4  
**Date**: 2026-07-31  

---

## 1. Executive Summary

This report presents a full codebase audit of the Medicare Vaccination System (Tiemchung) at `/home/hongphuoc/Desktop/thue`. The system is a Laravel 11 SPA/hybrid application featuring a modular architecture under `modules/VaccineRegistration`. The analysis covers administrative authentication (R1), role-based access control and multi-branch data context (R2), consultation lead handling and registration pivot structures (R3), and content security, upload hardening, and CSV export protection (R4).

---

## 2. R1 Audit: Admin Account & Authentication Structure

### 2.1 Admin Account Seeding & Creation Logic
- **Seeder Location**: `database/seeders/DatabaseSeeder.php`, lines 18–30.
- **Seeding Execution**: Checks if a user with `role = 'super_admin'` exists. If not, it reads `ADMIN_USERNAME` (defaulting to `'admin'`) and `ADMIN_PASSWORD` (defaulting to `'admin123'`) from `.env` and creates a default super admin record:
  ```php
  \App\Models\User::create([
      'name' => 'Admin Gốc',
      'username' => $username,
      'email' => $username . '@medicare.local',
      'password' => \Illuminate\Support\Facades\Hash::make($password),
      'role' => 'super_admin',
      'is_active' => true,
  ]);
  ```
- **Admin Management Controller**: `modules/VaccineRegistration/Http/Controllers/Admin/AdminUserController.php`.
  - Methods: `index()`, `create()`, `store()`, `edit()`, `update()`, `destroy()`.
  - Allowed Roles: Reserved strictly for `super_admin` (`abort_unless(AdminContext::isSuperAdmin(), 403)`).
  - Validation: Requires `username` (unique), `email` (unique), `role` (`super_admin` or `branch_admin`), and `center_id` (required if `role === 'branch_admin'`).

### 2.2 User Migration & Model Structure
- **Base Migration**: `database/migrations/0001_01_01_000000_create_users_table.php` defines standard fields (`id`, `name`, `email`, `password`, `remember_token`, `timestamps`).
- **Extended Migration**: `modules/VaccineRegistration/Database/Migrations/2026_07_31_000004_add_admin_fields_to_users_table.php`:
  - `username` (string, nullable, unique)
  - `role` (string, default `'branch_admin'`)
  - `center_id` (foreignId referencing `centers.id`, nullable, `nullOnDelete()`)
  - `is_active` (boolean, default `true`)
- **User Model**: `app/Models/User.php`:
  - `$fillable` includes `name`, `username`, `email`, `password`, `role`, `center_id`, `is_active`.
  - Casts: `is_active` => `boolean`, `password` => `hashed`.
  - Helper methods:
    - `center()`: `belongsTo(Center::class)` (lines 55–58).
    - `isSuperAdmin()`: `return $this->role === 'super_admin';` (lines 60–63).
    - `isBranchAdmin()`: `return $this->role === 'branch_admin';` (lines 65–68).

### 2.3 Authentication Controller & Middleware Analysis
- **Controller**: `modules/VaccineRegistration/Http/Controllers/Admin/AdminAuthController.php`.
  - **Login Method** (`login(Request $request)`, lines 30–62):
    - Validates `username` and `password`.
    - Queries `User::where(...)` matching `username` or `email`, restricted to `role` in `['super_admin', 'branch_admin']`.
    - Verifies `$user->is_active` and `Hash::check(...)`.
    - Custom Session Logic: Does NOT use Laravel standard `Auth::guard('web')->login($user)`. Instead sets session variables:
      - `session()->put('admin_logged_in', true)`
      - `session()->put('admin_user_id', $user->id)`
      - `session()->put('admin_role', $user->role)`
      - `session()->put('admin_center_id', $user->center_id)`
      - Regenerates session ID with `$request->session()->regenerate()`.
  - **Logout Method** (`logout(Request $request)`, lines 67–76):
    - Clears session variables, calls `$request->session()->invalidate()` and `regenerateToken()`.
- **Identified Gaps & Weaknesses in R1**:
  1. **No Login Rate Limiting**: The `login()` method lacks rate limiting (e.g. `RateLimiter` or `throttle` middleware), leaving it susceptible to brute-force credential stuffing.
  2. **No Password Change Flow**: There is no controller endpoint or form view for an authenticated admin to change their password or force password updates.
  3. **Custom Session Auth vs Standard Laravel Auth**: Session-based custom state requires manual checks across helpers (`AdminContext`) instead of standard `Auth::user()` guards.

---

## 3. R2 Audit: RBAC & Multi-Branch Data Access

### 3.1 Model & Migration Infrastructure
- **Migrations**:
  - `centers` table: `modules/VaccineRegistration/Database/Migrations/2026_07_18_000002_create_centers_table.php` & `2026_07_31_000001_extend_centers_for_branch_context.php`.
  - `center_vaccines` table: `modules/VaccineRegistration/Database/Migrations/2026_07_31_000002_create_center_vaccines_table.php`. Columns: `id`, `center_id`, `vaccine_id`, `price`, `sale_price`, `stock_quantity`, `stock_status`, `is_active`, `is_featured`, `sort_order`, `timestamps`. Unique constraint on `['center_id', 'vaccine_id']`.
- **Models**:
  - `Center` (`modules/VaccineRegistration/Models/Center.php`): Has `centerVaccines()` relation (`hasMany(CenterVaccine::class)`).
  - `CenterVaccine` (`modules/VaccineRegistration/Models/CenterVaccine.php`): Belongs to `Center` and `Vaccine`.
  - `Vaccine` (`modules/VaccineRegistration/Models/Vaccine.php`): Scope `forCenter($query, ?int $centerId)` joins `center_vaccines` and selects branch-specific price, stock quantity, stock status, and featured state.

### 3.2 Authorization Middleware & Context Helper
- `AdminAuth` Middleware (`modules/VaccineRegistration/Http/Middleware/AdminAuth.php`): Verifies `session('admin_logged_in') === true` and that the user ID exists and is active.
- `SuperAdminOnly` Middleware (`modules/VaccineRegistration/Http/Middleware/SuperAdminOnly.php`): Checks `AdminContext::isSuperAdmin()` and calls `abort(403)` if false.
- `AdminContext` Helper (`modules/VaccineRegistration/Support/AdminContext.php`):
  - `user()`: Resolves logged-in User instance from session `admin_user_id`.
  - `applyCenterScope(Builder $query, string $column = 'center_id')`: Appends `->where('center_id', self::centerId())` if the user is a `branch_admin`.

### 3.3 Authorization Holes & Cross-Branch Vulnerabilities
1. **`AdminCenterController` Lacks RBAC Checks**:
   - File: `modules/VaccineRegistration/Http/Controllers/Admin/AdminCenterController.php`.
   - Observation: Methods `index()`, `create()`, `store()`, `edit()`, `update()`, `destroy()` contain **NO** `AdminContext::isSuperAdmin()` or `SuperAdminOnly` middleware checks.
   - Impact: Any logged-in `branch_admin` can create, modify, or delete center records.
2. **`AdminBannerController` Lacks RBAC Checks**:
   - File: `modules/VaccineRegistration/Http/Controllers/Admin/AdminBannerController.php`.
   - Observation: No authorization checks. `branch_admin` can add, edit, or delete global homepage banners.
3. **`AdminArticleController` Lacks RBAC Checks**:
   - File: `modules/VaccineRegistration/Http/Controllers/AdminArticleController.php`.
   - Observation: No authorization checks in `store()`, `update()`, `destroy()`, or `uploadEditorImage()`.
4. **`AdminVaccineController::toggleFeatured` Logic Defect**:
   - File: `modules/VaccineRegistration/Http/Controllers/Admin/AdminVaccineController.php:226`.
   - Code: `abort_unless(AdminContext::isBranchAdmin(), 403);`
   - Bug: If a `super_admin` attempts to toggle the featured state of a vaccine for a center, they receive an unexpected HTTP 403 Forbidden response. The check should allow both `branch_admin` and `super_admin`.

---

## 4. R3 Audit: Consultation Leads & Registration Pivot Data Integrity

### 4.1 `registrations` Table Schema & Fake Data Creation
- **Migration**: `modules/VaccineRegistration/Database/Migrations/2026_07_17_000002_create_registrations_table.php`.
  - Columns: `id`, `registration_code`, `patient_name`, `patient_dob`, `patient_gender`, `patient_phone`, `patient_address`, `guardian_name`, `guardian_phone`, `center_id`, `center_name`, `injection_date`, `status`, `payment_method`, `total_price`, `timestamps`.
- **Consultation Form Handling Issue**:
  - File: `modules/VaccineRegistration/Http/Controllers/VaccineController.php`, method `postDiseaseConsult(Request $request, $disease)` (lines 633–720).
  - Observation: When a user submits a consultation request from the disease detail page, the system creates a record in the `registrations` table with hardcoded fake patient data (lines 683–684):
    ```php
    'patient_dob' => '2000-01-01',
    'patient_gender' => 'Khác',
    'status' => 'Chờ tư vấn',
    'total_price' => 0,
    ```
  - Analysis: Consultation leads (requests for advice) are mixed into the main `registrations` table intended for actual vaccination appointments. This pollute reports, analytics, and date-based scheduling.

### 4.2 Pivot Table Schema & Stock Deduction Logic
- **Pivot Migration**: `2026_07_17_000003_create_registration_vaccines_table.php` & `2026_07_31_000006_add_quantity_to_registration_vaccines_table.php`.
  - Table `registration_vaccines`: `registration_id`, `vaccine_id`, `price`, `quantity` (default 1).
- **Model Relationships**:
  - `Registration::vaccines()` (`modules/VaccineRegistration/Models/Registration.php:32-37`):
    `belongsToMany(Vaccine::class, 'registration_vaccines')->withPivot(['price', 'quantity'])->withTimestamps();`
  - `Vaccine::registrations()` (`modules/VaccineRegistration/Models/Vaccine.php:137-142`):
    `belongsToMany(Registration::class, 'registration_vaccines')->withPivot(['price', 'quantity'])->withTimestamps();`
- **Stock Movement Defect in Status Update**:
  - File: `modules/VaccineRegistration/Http/Controllers/Admin/AdminRegistrationController.php`, lines 87–141.
  - Code Snippet:
    ```php
    $centerVaccine->stock_quantity = (int) $centerVaccine->stock_quantity - 1;
    ```
  - Defect: When status changes to `'Đã thanh toán'` or `'Đã tiêm'`, the controller subtracts a hardcoded `$quantity = 1` from `CenterVaccine` stock instead of reading `$vaccine->pivot->quantity`. If a patient registers multiple doses or quantities of a vaccine in a single order, stock calculation becomes inaccurate.

---

## 5. R4 Audit: Content Security, Upload Hardening & CSV Export

### 5.1 Article Content XSS Sanitization & Rendering
- **Sanitization Helper**: `modules/VaccineRegistration/Support/SecurityHelper.php:7-46`.
  - `cleanHtml(?string $html)` utilizes `DOMDocument` to enforce an HTML tag allowlist (`div`, `p`, `br`, `strong`, `em`, `span`, `ul`, `ol`, `li`, `h2`, `h3`, `h4`, `h5`, `h6`, `blockquote`, `a`, `img`, `table`, `thead`, `tbody`, `tr`, `th`, `td`) and strips inline JS event handlers (`on*`) and unsafe protocols (`javascript:`, `data:`).
- **Controller Usage**:
  - `AdminArticleController.php:41, 81` invokes `SecurityHelper::cleanHtml()` prior to persisting article content.
- **Blade Rendering Audit**:
  - File: `modules/VaccineRegistration/resources/views/articles/show.blade.php:51`:
    `{!! $article->content !!}`
  - File: `modules/VaccineRegistration/resources/views/disease.blade.php:360`:
    `{!! $info['description'] !!}`
  - Assessment: The content stored in `articles` table is properly sanitized on write via `SecurityHelper::cleanHtml()`.

### 5.2 Image Upload Handling & File Security
- **Upload Locations & Validation Audit**:
  1. `AdminVaccineController.php:281`:
     `'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'`
     Files moved to `public_path('images/vaccines')`.
  2. `AdminBannerController.php:42`:
     `'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'`
     Files moved to `public_path('images/banners')`.
  3. `AdminArticleController.php:35, 77`:
     `'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'`
     Files moved to `public_path('images/vaccines')`.
  4. `AdminArticleController::uploadEditorImage()` (lines 121–135):
     Validates extension via `in_array(strtolower($file->getClientOriginalExtension()), ['jpg', 'jpeg', 'png', 'gif', 'webp'])`. Missing explicit mime-type verification via Laravel Validator.
  5. `AdminLiveEditorController::storeTeamAvatar()` (lines 240–265):
     Parses Base64 data strings `data:image/(jpeg|jpg|png|webp|gif);base64,...` and writes up to 4MB to `public_path('images/team')`.
  - SVG Status: SVG files are **not** permitted in image uploads (`mimes:jpeg,png,jpg,gif,webp`), avoiding SVG XSS vectors.

### 5.3 Link & Map Embed URL Validation
- **Map Embed URL**: `AdminCenterController.php:44-52` uses a custom closure to ensure map URLs start with `https://www.google.com/maps/embed`, `https://www.google.com/maps/place`, or `https://www.google.com/maps/`.
- **Banner Link URL**: `AdminBannerController.php:43-53` uses a closure checking that the URL does not start with `javascript:` or `data:`.

### 5.4 CSV Export Formula Injection Hardening
- **Controller Method**: `AdminRegistrationController::exportCsv()` (lines 212–268).
- **Formula Injection Defense**: `safeCsvCell(?string $value)` method (lines 261–268):
  ```php
  private function safeCsvCell(?string $value): string
  {
      $value = (string) $value;
      if (preg_match('/^[=\-+@]/', $value)) {
          return "'" . $value;
      }
      return $value;
  }
  ```
- Assessment: CSV export prepends a single quote `'` to any field starting with `=`, `-`, `+`, or `@`, preventing CSV formula execution in spreadsheet applications.

---

## 6. Recommendations & Implementation Roadmap

1. **R1 (Admin Auth)**:
   - Add rate limiting throttle middleware (`throttle:6,1`) on login routes.
   - Implement password change controller and form view for authenticated administrators.
2. **R2 (RBAC & Multi-Branch Data)**:
   - Enforce `SuperAdminOnly` middleware / checks on `AdminCenterController`, `AdminBannerController`, and `AdminArticleController`.
   - Update `AdminVaccineController::toggleFeatured` permission check to allow super admin access (`AdminContext::isSuperAdmin() || AdminContext::isBranchAdmin()`).
3. **R3 (Consultation Leads & Registration Pivot)**:
   - Refactor consultation leads out of main registration tables or ensure proper differentiation/scoping.
   - Update `AdminRegistrationController::updateStatus` stock movement logic to multiply stock deduction by `$vaccine->pivot->quantity`.
4. **R4 (Security & Uploads)**:
   - Add mime-type validation to `AdminArticleController::uploadEditorImage()`.
   - Maintain current SVG block and CSV cell prefix sanitization.
