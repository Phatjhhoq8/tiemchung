# Release Notes

## [v6.4.2] - 2026-08-12

### Added & Restored

* **Restore Website Live Editor for Super Admin**:
  - Restored the deleted [AdminLiveEditorController.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Http/Controllers/Admin/AdminLiveEditorController.php) and its corresponding blade templates [live_editor.blade.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/resources/views/admin/live_editor.blade.php) and [live_editor_modals.blade.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/resources/views/admin/live_editor_modals.blade.php).
  - Registered all live editor routes under the `super.admin` middleware group in [web.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/routes/web.php) to restrict access strictly to Super Administrators.
  - Added the **"Chỉnh Sửa Trực Quan (Live)"** link back to the admin sidebar layout [layouts/admin.blade.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/resources/views/layouts/admin.blade.php) under the Super Admin check block.
  - Integrated `$isPreviewMode` checking in [HomeController.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Http/Controllers/HomeController.php) to load draft homepage configuration dynamically and display the Preview mode simulator banner in [home.blade.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/resources/views/home.blade.php).

## [v6.4.1] - 2026-08-11

### Added & Improved

* **Global Admin Medicare Custom DatePicker & Full Category CRUD Management**:
  - **Global Auto-Init DatePicker**: Added auto-initializer in `layouts/admin.blade.php` that automatically binds all `<input type="date">` filter elements across all admin list views (registrations, leads, customers, etc.) to the Medicare Custom DatePicker component, eliminating native OS popovers system-wide.
  - **Category CRUD & Pinned Right Actions**: Added `Sửa` (edit name) and `Xóa` (delete category) action buttons pinned strictly to the far right edge of each dropdown item row.
  - **Medicare Custom Category Edit Modal**: Replaced standard browser `prompt()` popups with a dedicated clean Medicare Edit Modal (`#cat_edit_modal`), allowing admins to seamlessly rename categories via AJAX and auto-update active option labels.
  - **Vaccine Deletion Warning Confirmation Modal**: When deleting a category, checks if vaccines are currently assigned. If vaccines exist, displays a clean modal listing affected vaccines and asking for explicit user confirmation before clearing category fields.

## [v6.4.0] - 2026-08-11

### Improved & Fixed

* **Admin Vaccine Form - UI Layout, SPA Interaction & Dynamic Discount %**:
  - **Flat Card Layout**: Cleaned up double card nesting on `admin/vaccines/edit.blade.php` and `create.blade.php`, removing redundant outer card borders and padding lamination. Removed verbose inline subcaptions from field labels for a clean, professional interface.
  - **Dynamic Discount % Tool**: Added `% Giảm giá` input field alongside Sale Price in `_form.blade.php`. Entering `0%` automatically detects no discount and clears the Sale Price field. Entering a discount percentage calculates the exact un-rounded Sale Price (`price * (1 - discount%)`). Manually editing Sale Price displays a plain text notice under the `sale_price` field (`Mức giảm hiện tại xấp xỉ X%. Gợi ý mức giảm nguyên: Z%`) formatted with 1-decimal actual percentage and `Math.floor` integer percentage suggestion without icons or button borders.
  - **SPA Branch Selection AJAX**: Replaced full-page reloads (`window.location.href`) on branch dropdown change (`center_id`) with AJAX fetching (`/admin/vaccines/{id}/center-data`), preserving unsaved form inputs while dynamically updating branch-specific price and inventory state.
  - **Searchable Category Dropdown**: Upgraded the `Nhóm bệnh` (Category) field from a raw text input to a modern searchable select dropdown component with real-time filtering, arrow indicator, and custom category creation support (`+ Thêm mới`).
  - **Standardized Form Select Dropdowns**: Standardized all form select fields (`center_id`, `center_is_active`) to use the exact same modern CSS dropdown component (arrow indicator, smooth option highlight, shadow elevation) without the custom creation option, ensuring 100% design consistency across all form controls.
  - **Minimalist Zero-Emoji Admin UI & Global Select/Date Standardization**: Stripped out all decorative emojis and icon clutter across Datepicker popups, field triggers, and form dropdowns. Standardized all `<select>` controls globally in `layouts/admin.blade.php` with a sleek SVG chevron arrow and Medicare Red focus highlights (`#c8102e`), while refining the DatePicker component to pure minimalist text formatting (`dd/mm/yyyy`, `Tháng 8, 2026`, `Hôm nay`, `Xóa`).
  - **Database Migration & Schedules 500 Fix**: Executed pending migrations for `default_slots` table across development and test MySQL databases, eliminating 500 internal server error on `/admin/schedules` page.
  - **Validation & Translation**: Added Vietnamese error message for `sale_price.lt` rule and handled disabled inputs safely during form submission.

## [v6.3.2] - 2026-08-10

### Removed

* **Remove "Trụ sở chính" address field**: Removed the headquarters address field from the admin Settings form (`settings/index.blade.php`, `AdminSettingController`), and cleaned up the unused `$address` fallback variable in `layouts/app.blade.php`. The existing `address` value in the database is preserved untouched.

## [v6.3.1] - 2026-08-10

### Removed

* **Admin Vaccine List - Remove irrelevant date filters**: Removed the Day/Month/Year filter dropdowns from the vaccine management list (`admin/vaccines/index.blade.php`) as vaccines have no expiry date, import date, or manufacture date fields. Also cleaned up corresponding validation rules and query logic in `AdminVaccineController`.

## [v6.3.0] - 2026-08-10

### Added & Improved

* **Admin Dashboard Improvements (Requirements R1, R2, R3)**:
  - **R1 Dynamic Metrics Integration**: Replaced hardcoded zeroes in `AdminDashboardController` with live database queries for unprocessed consultation leads (`consultation_leads` status in `pending`/`new`), total vaccine inventory (`inventory_lots` sum of `available_quantity + reserved_quantity`), and total vaccines sold (`registrations` status `completed`), all scoped dynamically by center ID when filtered.
  - **R2 Today's Injections Widget**: Added a prominent medical tracking widget to the admin dashboard (`dashboard.blade.php`) displaying the total expected injection appointments for today (`injection_date` = current date), styled with Medicare Navy and Gold highlights for medical staff tracking.
  - **R3 Pure SVG Revenue & Registrations Trend Charts**: Implemented visual SVG charts (`<svg viewBox="...">`, `<polyline>`, `<path>`, `<circle>`) for 7-day daily trends and 6-month monthly trends, supporting dual metric tracking (paid revenue and total registrations count) using the official Medicare color theme (`#c8102e` Medicare Red, `#004b8f` Medicare Navy, `#eaaa00` Medicare Gold) with interactive tab toggle between daily and monthly views.
  - **Automated Feature Test Suite**: Added `tests/Feature/AdminDashboardTest.php` to verify dashboard loading, DB dynamic metrics calculation, center scoping, today's injection count accuracy, and SVG chart rendering structure with 100% test pass rate.

## [v6.2.0] - 2026-08-10

### Added & Improved

* **Real-Time AJAX Filtering & Flexible Date Filters**:
  - **Backend Controller Scopes & Partial Views**: Enhanced all 5 admin controllers (`AdminRegistrationController`, `AdminCustomerController`, `AdminConsultationLeadController`, `AdminVaccineController`, `AdminCenterController`) to support flexible Day (1-31), Month (1-12), and Year dropdown query filtering in any combination (`whereDay`, `whereMonth`, `whereYear`) targeting module date fields (`injection_date` or `created_at`).
  - **Modular Blade Table Partial Extraction**: Extracted table markup and pagination controls into partial Blade views (`_table.blade.php`) across all 5 admin modules (`registrations`, `customers`, `leads`, `vaccines`, `centers`).
  - **Frontend Real-Time AJAX Engine & URL Sync**: Built a lightweight Vanilla JS engine with a 300ms debounce on search text input, instant refresh on dropdown changes, pagination link click interception, Medicare Red loading indicator, `pushState`/`popstate` browser URL synchronization, and automated `lucide.createIcons()` re-rendering.
  - **Automated Test Suite**: Added `tests/Feature/AdminAjaxFilteringTest.php` covering AJAX search & filter endpoints, flexible date combinations, AJAX pagination responses, and query parameter retention.

## [v6.1.2] - 2026-08-10

### Fixed

* **Remove Hardcoded Center Name in Global Pages**: Removed the hardcoded clinic name suffix `Medicare Cờ Đỏ` from the admin login page title and body text to align with the system-wide scope. Updated default settings fallbacks and all admin view page title suffixes to use generic `Medicare` instead of the specific `Medicare Cờ Đỏ` branch, ensuring consistency across all center administration interfaces.
* **Redesign Admin Action Buttons to Prevent Clutter**:
  - Implemented an action dropdown menu (`btn-action-trigger`, `action-dropdown-menu`, `dropdown-item-action` in [admin.blade.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/resources/views/layouts/admin.blade.php)) and integrated a global toggle JavaScript listener to streamline multi-button tables.
  - Refactored the Vaccine management list ([index.blade.php (vaccines)](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/resources/views/admin/vaccines/index.blade.php)) to compress 4 actions (`Featured`, `Stock`, `Edit`, `Delete`) into a single three-dot horizontal action trigger, shrinking the column width from ~350px to ~50px.
  - Revamped Banners ([index.blade.php (banners)](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/resources/views/admin/banners/index.blade.php)), Centers ([index.blade.php (centers)](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/resources/views/admin/centers/index.blade.php)), Articles ([index.blade.php (articles)](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/resources/views/admin/articles/index.blade.php)), and Users ([index.blade.php (users)](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/resources/views/admin/users/index.blade.php)) index views to use modern icon-only action buttons (Edit & Delete) with HTML `title` tooltips, narrowing action columns from 160px to ~90px.
* **Improve Admin Filters & Merge Redundant Vaccine Columns**:
  - Added dynamic date range filtering (`injection_date_from` and `injection_date_to`) and a reset filter button to the Registration management list ([index.blade.php (registrations)](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/resources/views/admin/registrations/index.blade.php) and [AdminRegistrationController.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Http/Controllers/Admin/AdminRegistrationController.php)) to facilitate date-based appointment screening and export operations.
  - Merged the redundant `Tồn kho` (Stock) and `Tình trạng` (Status) columns in the Vaccine management table ([index.blade.php (vaccines)](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/resources/views/admin/vaccines/index.blade.php)) into a single unified `Tồn kho / Trạng thái` cell, enhancing data density and aesthetics.
* **Fix Missing Center ID Validation on Schedule Creation**: Resolved validation exception `The center id field is required.` in the weekly schedule dashboard ([AdminScheduleController.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Http/Controllers/Admin/AdminScheduleController.php)) by automatically falling back to the first active center when no center is specified in session or request.

## [v6.1.1] - 2026-08-10

### Fixed & Improved

* **Faded Placeholder Inputs**: Added custom global stylesheet configurations in [style.css](file:///home/hongphuoc/Desktop/thue/public/css/style.css) for input and textarea placeholders (`::placeholder`) to make them faded to a clean slate gray (`#94a3b8` in light mode, `#64748b` in dark mode) ensuring clarity that they are guidance texts, and avoiding duplicate lookups on input forms.

## [v6.1.0] - 2026-08-10

### Added

* **7-Column Weekly Calendar Grid Interface**: Redesigned `schedules/index.blade.php` into a responsive 7-parallel-column calendar grid (Monday to Sunday) displaying schedule dates, open/closed active status badges, slot capacities, time slots with status dots, quick slot editing modals, and week navigation controls (Tuần trước, Tuần hiện tại, Tuần sau, Date Picker, Branch selector).
* **Schedule Copy Feature & Safety Guard**: Implemented `POST /admin/schedules/copy` allowing administrators to clone a day schedule across target week dates. Added strict safety validation guard (`reserved_count > 0` or linked registrations) returning HTTP 422 error `"Không thể sao chép đè lịch ngày {date} vì đã có {count} lượt đặt tiêm!"` to atomically block overwriting booked schedules.
* **Day Toggle Status & Day Schedule Deletion**: Added `POST /admin/schedules/toggle-day` and `DELETE /admin/schedules/day` API endpoints with safety check blocking deletion of days with active patient registrations.
* **Feature Test Suite**: Created `WeeklyCalendarDashboardTest.php` with 7 feature tests validating 7-day grid resolution, navigation filtering, slot CRUD AJAX actions, day status toggles, day deletion, copy schedule success, copy schedule 422 safety guard, and cross-branch access authorization (403 Forbidden).

## [v6.0.3] - 2026-08-10

### Added & Fixed

* **Branch-specific Zalo QR Code Integration & Accessor Fix**: Added `$appends = ['zalo_url', 'zalo_qr_url']` and accessor `getZaloQrUrlAttribute()` (with alias `getZaloQrCodeUrlAttribute()`) to `Center.php` model ([Center.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/Models/Center.php)) to fix `BadMethodCallException` and construct clean Zalo chat deep links (`https://zalo.me/{zalo_phone}`) and dynamic high-resolution QR codes via QR API.
* **Strict Loading Workflow (Zero Fallbacks)**: Refactored `renderSpaConsultForm()` in [app.js](file:///d:/Projects/tiemchung/public/js/app.js) into an async function with an explicit medical loading spinner (`Đang tải thông tin tư vấn Zalo chi nhánh...`). Completely removed all static fallback objects and guessing logic. Waits for 100% database response from `CenterContext::current()` before rendering the Zalo consultation card. Ensures exact branch details (name, hotline, actual address, QR code, and direct Zalo chat button) for whichever branch is selected (Medicare Cờ Đỏ, Medicare Thới Lai, Medicare Phong Điền, Medicare Trà Nóc, Medicare Vị Thanh).
* **Default Slots Configuration (Weekly Template)**: Added weekly default slot configuration template for centers. Created migration `2026_08_10_000001_create_default_slots_table.php`, model `DefaultSlot.php`, controller `AdminDefaultSlotController.php`, routes, and the management UI `default.blade.php`.
* **Auto-Generating Schedules**: Implemented static method `generateFromDefaults` in `Schedule` model to dynamically auto-generate schedules for the next 30 days based on the weekly default slot templates. Integrated it into `VaccineController`, `AdminRegistrationController`, and `AdminScheduleController`.
* **Schedule Deletion and Active Status Toggle**: Added features on the admin schedule list page to delete daily schedules and close/open them directly via inline buttons.
* **Interactive Time Slot Editing Modal**: Added a modal popup to edit and delete individual time slots (capacity, times, activity toggle) directly by clicking the slot badges on the schedule listing dashboard.
* **Testing suite**: Created feature test `AdminDefaultSlotsTest.php` covering default slots configuration, auto-generation, and deletion.

## [v6.0.2] - 2026-08-07

### Security Fixes

* **Fix Security Vulnerabilities (VAC-001 to VAC-020)**: Implemented comprehensive security fixes across multiple layers of the application including: PII masking for public booking lookup, rate limiting on registration requests, validation limits for multi-patient registration, idempotency fix for group bookings, blocking payment settlement on cancelled registrations, center isolation for customer points, uniform login error messages, device revocation on password changes, slot capacity mass-assignment prevention, auditing lot adjustments, and strict validation of clinical workflows.
* **Add Feature Test for Remediation**: Created [SecurityAuditRemediationTest.php](file:///c:/Users/Admin/Desktop/tiemchung/tests/Feature/SecurityAuditRemediationTest.php) covering 10 test cases for all security fixes.
* **Database & Testing Environment Support**: Added local test environment config in [.env.testing](file:///c:/Users/Admin/Desktop/tiemchung/.env.testing) and updated [CustomerLoyaltyAndManualPaymentTest.php](file:///c:/Users/Admin/Desktop/tiemchung/tests/Feature/CustomerLoyaltyAndManualPaymentTest.php), [AdminAuth.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/Http/Middleware/AdminAuth.php), and [AdminVaccineController.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/Http/Controllers/Admin/AdminVaccineController.php) to preserve 100% backward compatibility with the existing test suite (73 tests, 432 assertions passed).

### Bug Fixes

* **Fix Empty Vaccine State Alignment**: Refactored CSS class `.empty-vaccines` in [style.css](file:///c:/Users/Admin/Desktop/tiemchung/public/css/style.css) to use Flexbox layout (`display: flex; flex-direction: column; align-items: center; justify-content: center;`), centering the empty search icon, empty text warning, and reset action button cleanly and responsive on both desktop and mobile viewports.

## [v6.0.1] - 2026-08-06


### Bug Fixes

* **Fix Responsive TOC display**: Removed hardcoded inline `style.display = 'block'` from `initDynamicTOC()` in `public/js/app.js` to allow CSS media queries in `articles.css` and `vaccines.css` to properly hide the mobile inline table of contents on desktop screen widths.
* **Fix Tab Button Contrast**: Added CSS overrides for `.section-style-red` and `.section-style-dark` to prevent text of light-colored buttons (`bg-slate-100`) from being forced to white, fixing the invisible text bug on inactive vaccine catalog tabs.
* **Fix Views Count Text Color**: Added class `text-white` to the dynamic views count span elements in `featured_vaccines.blade.php` to bypass global `.section-style-red` CSS rule that forced inner span tags to dark gray, correcting readability of views count on dark backdrop.
* **Fix Services Section Icons Color**: Added `color: #ffffff !important;` inline style to Lucide icons inside home service card containers (`services.blade.php`) to bypass global `.section-style-red` CSS rule that forced text/div color to dark gray, ensuring icons are visible and stand out on their red/gold background wrappers.
* **Fix SPA Dynamic Stylesheet Injection**: Refactored `navigateSpa()` in `public/js/app.js` to automatically extract and inject newly returned stylesheet link tags (`<link rel="stylesheet">`) into the document head during cross-page transition, fixing the missing CSS bug on first SPA click to detail pages.
* **Fix Empty Cart Booking Flow & Restore SPA Register Modal**: Refactored `showRegister()` in `VaccineController.php` to return JSON data if requested via AJAX (always including `current_center` data), allowing details to load dynamically inside the restored SPA Register Modal drawer frame. Reconstructed the `modal-register.blade.php` view, exposed all necessary event handlers in `public/js/app.js` (fixing a duplicate `escapeHtml` syntax error), mapped all website booking links to trigger the modal, centered the empty cart warning text, updated the empty warning action pathway to redirect to the main vaccine list page ("Chọn vắc xin") instead of a filtered package-only catalog view, added `openSpaConsultationModal()` to open the modal directly onto the consultation form from the advice banner, ensured all consultation branch select dropdowns default to the currently selected active center, wrapped select tags in a relative container with a custom Chevron Down SVG icon to visually indicate re-selection capability, resolved CSS layout bugs inside the non-empty SPA Modal (e.g. sidebar compression, text truncation, and horizontal scrollbars) by implementing responsive `.spa-register-grid` and `.spa-form-row` styles with a `960px` mobile breakpoint and adding strict `min-width` / `flex-shrink` boundaries on columns, optimized `changeSpaRegisterCenter()` to fade out the grid and update center details asynchronously while preserving user patient inputs (name and phone) instead of blanking out the modal card screen, split the single date-and-time selector into two separate dependent dropdown fields ("Ngày tiêm" and "Khung giờ tiêm") across both the modal and standalone booking views, resolved ISO datetime parsing format bugs by strictly slicing the first 10 characters (yyyy-mm-dd) of data strings before client-side operations, restored the multi-patient registration feature (allowing users to add multiple profiles, assign custom vaccines from the cart to each person, validate minimum vaccine counts, handle minor age guardian constraints, and record multiple database registrations under a single checkout transaction) across both the client-side SPA modal and public booking routes, introduced a customized confirm dialog modal (`showConfirmDialog()`) with a cohesive brand styling and a backdrop-blur overlay to replace standard browser alert/confirm popups during booking validation and submission, integrated a visually placeholder panel for online QR payment method (temporarily disabled/locked) across the booking success view (`success.blade.php`) and booking lookup search page (`booking_lookup.blade.php`), restyled the booking status update action button on the admin registration details screen (`show.blade.php`) to use Medicare Navy solid theme background and white text, preventing it from appearing visually disabled, added a dynamic live search text input with accent-insensitive filtering and multiple dropdown selectors (type, category/disease group, origin, and age group) in the admin quick registration at the counter view (`create.blade.php`) to easily locate vaccines in long branch inventories, displayed the current real-time stock quantity (`stock_quantity`) for each vaccine option in this view, styling it with warning colors when inventory is low, added editable quantity input elements next to checkboxes in this view, enabling inputs on toggle and storing custom quantities with proper database calculations inside `AdminRegistrationController.php`, and modified the admin vaccine list directory (`vaccines/index.blade.php`) to show the raw numeric stock quantity under a "Tồn kho" header instead of generic colored status tags (Đầy đủ, Còn ít, Hết hàng).

## [Unreleased] - 2026-08-02

### Full System SPA Upgrade & Mobile UI Polish

* **Global SPA Router Engine**: Added seamless client-side single-page navigation (`navigateSpa()`) across all public pages (Homepage, About, Product Catalog, News, Branch Contact, Booking Lookup).
* **Fix Vaccines SPA Navigation**: Updated `VaccineController@index` to require `X-Vaccine-Filter` header for returning JSON grid partials, resolving SPA page navigation blockage for `/vaccines`.
* **Mobile Header Action Compression**: Compressed Hotline and Cart action pills on mobile viewports (`max-width: 768px`) to icon-only buttons with badge counters to fit cleanly without overflow.
* **Smooth TOC Section Jump Navigation**: Added `scroll-margin-top: 110px !important;` to section headers and updated `initDynamicTOC()` click handler to smoothly scroll and dock exactly 100px below the fixed site header when clicking any TOC item.
* **Vite Asset Rebuild**: Recompiled production assets with `npm run build`.

### Features

* **Add branch stock quick view**: Added a quick view button and modal popup on the admin vaccines list [index.blade.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/resources/views/admin/vaccines/index.blade.php) allowing Admin root to query and view vaccine inventory status, stock quantity, and pricing across all active branches dynamically via an AJAX endpoint in [AdminVaccineController.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Http/Controllers/Admin/AdminVaccineController.php).

### Bug Fixes

* **Fix admin UTF-8 encoding in database**: Corrected the double-encoded UTF-8 name of the super_admin user (from `Quáº£n trá»‹ viÃªn` back to `Quản trị viên`) in the database.
* **Fix admin layout header text clipping**: Increased the `max-width` of the branch selector dropdown in [admin.blade.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/resources/views/layouts/admin.blade.php) from 180px to 240px to prevent long branch names (like `MEDICARE PHONG ĐIỀN`) from being truncated and causing text display issues.
* **Fix admin permissions UI logic**: Fixed view logic in [index.blade.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/resources/views/admin/vaccines/index.blade.php) where `isSuperAdmin` condition was incorrectly reversed, hiding the "Add Vaccine" button and showing "Read Only" actions for Super Admin instead of Branch Admin. Now Super Admin can add, edit, and delete vaccines, while Branch Admin can only edit local prices/stocks and toggle featured status.
* **Fix registration page**: Passed `activeCenters` variable to `vaccine::register` view to resolve `Undefined variable $activeCenters` error.

### Branch booking and manual payment

* Reworked public booking around one patient, a branch-owned time slot, server-side branch pricing, normalized household phone customers, and immutable loyalty point transactions.
* Added customer, schedule, registration payment/refund, and branch price management screens; removed online payment/webhook, FEFO reservation, patient workflow, queue notification, and visual editor paths.
* Removed obsolete SPA booking code and Live Editor artifacts. Public pages now render only the published homepage layout; catalog filtering, cart, quick view, and disease consultation remain available.

## [v6.0.0] - 2026-08-01

### M11: E2E Integration, Migration & Seeding Verification (Ponytail Style)

* **Migration & Seeding Verification**: Executed `/opt/lampp/bin/php artisan migrate:fresh --seed` on a fresh database. Verified that all 27 database migrations and seeders (`VaccineSeeder`, `CenterSeeder`, `SettingSeeder`, `BannerSeeder`, `ArticleSeeder`) ran cleanly with zero errors, zero warnings, and 100% data integrity.
* **Full Test Suite Verification**: Ran complete project test suite using `/opt/lampp/bin/php ./vendor/bin/phpunit`. Verified that 100% of test suites (76 tests, 432 assertions) passed cleanly with 0 failures and 0 errors.
* **Cross-Module Cohesion & Integration Check**: Verified seamless inter-module cohesion across RBAC & Multi-branch isolation (`RbacMultiBranchTest`), Audit Logging (`AuditLogsAndResourceStatusTest`), CRM Lead Capturing & Idempotency (`CrmLeadsAndRegistrationIdempotencyTest`), Schedules & Slot Capacity Control via Pessimistic Locking (`SchedulesSlotsConcurrencyTest`), FEFO Stock Allocation & Movements (`FefoInventoryStockReservationTest`), Patient Profiles & 3-Step Vaccination Workflow (`PatientVaccinationWorkflowTest`), and Payment Webhooks & Async Queue Jobs (`PaymentWebhookAndQueueTest`).

## [v5.1.0] - 2026-08-01

### M10: Payment Webhook Verification & Background Queue Jobs (R6, Ponytail Style)

* **Server-to-Server Payment Webhook Endpoint**: Created [PaymentWebhookController.php](file:///home/hongphuoc/Desktop/thue/app/Http/Controllers/PaymentWebhookController.php) providing `POST /api/webhooks/payment` for secure payment verification. Validates HMAC SHA-256 signatures against `config('services.payment.webhook_secret')`, verifies registration transaction references, and checks payment amount against registration total amount. On valid signature and amount match, atomically updates registration status to `paid` inside a database transaction.
* **Browser Return URL Protection**: Implemented `handleBrowserReturn()` on `GET/POST /payment/return` and `/payment/callback` preventing unverified browser return URLs from directly mutating payment status to `paid` without verified server-to-server signature validation (rejects unauthorized status mutation attempts with HTTP 403 Forbidden).
* **Background Queue Jobs**: Created [SendRegistrationEmailJob.php](file:///home/hongphuoc/Desktop/thue/app/Jobs/SendRegistrationEmailJob.php) and [SendNotificationSmsJob.php](file:///home/hongphuoc/Desktop/thue/app/Jobs/SendNotificationSmsJob.php) implementing `ShouldQueue`. Moved Email and SMS notification dispatching on registration creation and payment into background queue jobs so main HTTP transaction requests are never blocked.
* **API Route & Configuration**: Configured `routes/api.php` in [bootstrap/app.php](file:///home/hongphuoc/Desktop/thue/bootstrap/app.php) and updated [services.php](file:///home/hongphuoc/Desktop/thue/config/services.php) with payment webhook secret configuration.
* **Feature Test Suite**: Created [PaymentWebhookAndQueueTest.php](file:///home/hongphuoc/Desktop/thue/tests/Feature/PaymentWebhookAndQueueTest.php) covering signature verification, amount validation, nonexistent reference handling (404), blocking unverified browser return status updates (403), and queue job dispatching for creation and payment events, achieving 100% test pass rate.

## [v5.0.0] - 2026-08-01

### M9: Centralized Patients & 3-Step Vaccination Workflow (R5, Ponytail Style)

* **Centralized Patients & Workflow Schema & Migration**: Created migration `2026_08_01_000005_create_patients_and_vaccination_workflow_tables.php` creating `patients` table (`identity_card`, `full_name`, `dob`, `gender`, `phone`, `address`, `medical_history`, `is_active`), updating `registrations` table (`patient_id`, `screening_status`, `screening_notes`), and creating `administered_doses` table (`registration_id`, `patient_id`, `vaccine_id`, `inventory_lot_id`, `center_id`, `administered_by`, `administered_at`, `screening_status`, `screening_notes`, `observation_notes`, `observation_ended_at`, `status`).
* **Models & Workflow Methods**: Created [Patient.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Models/Patient.php) with non-duplicating centralized lookup `findOrCreateCentralized()` and [AdministeredDose.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Models/AdministeredDose.php). Updated [Registration.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Models/Registration.php) with `patient()`, `administeredDoses()`, and 3-step workflow methods `checkIn()`, `screening()`, and `administer()`.
* **Controllers & Routes**: Implemented [AdminPatientController.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Http/Controllers/Admin/AdminPatientController.php) for central patient profiles and [VaccinationWorkflowController.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Http/Controllers/Admin/VaccinationWorkflowController.php) for the 3-step workflow (Check-in, Clinical Screening, Vaccine Administration with post-vaccination observation timer). Registered admin routes under `admin.auth` in [web.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/routes/web.php).
* **Feature Test Suite**: Created [PatientVaccinationWorkflowTest.php](file:///home/hongphuoc/Desktop/thue/tests/Feature/PatientVaccinationWorkflowTest.php) covering non-duplicate central patient profile management, Step 1 check-in status updates, Step 2 screening logic (permits eligible; blocks deferred/contraindicated with HTTP 422), and Step 3 administration execution creating `AdministeredDose` records with vaccinator ID, lot number, and observation timestamp.

## [v4.0.1] - 2026-08-01

### M8 (Patch): FEFO Inventory Reservation Test Fixes

* **Test Registration Query Fix**: Fixed `tests/Feature/FefoInventoryStockReservationTest.php` line 256 to query patient `Le Van C` specifically (`Registration::where('patient_name', 'Le Van C')->latest()->first()`), preventing retrieval of registrations created in prior test methods.
* **Cached Relation Invalidation**: Updated `allocateAndReserve()` in [FefoInventoryService.php](file:///home/hongphuoc/Desktop/thue/app/Services/FefoInventoryService.php) to call `$registration->unsetRelation('vaccines');` before returning `$registration`, ensuring stale cached relations are cleared.

## [v4.0.0] - 2026-08-01

### M8: FEFO Inventory Lots & Stock Reservation (R4, Ponytail Style)

* **Inventory Lots & Stock Movements Schema & Models**: Created migration `2026_08_01_000004_create_inventory_lots_and_stock_movements_tables.php` defining `inventory_lots` (`vaccine_id`, `center_id`, `lot_number`, `initial_quantity`, `available_quantity`, `reserved_quantity`, `expires_at`, `status`), `stock_movements` (`inventory_lot_id`, `user_id`, `type`, `quantity`, `reference_type`, `reference_id`, `note`), and added `inventory_lot_id` foreign key to `registration_vaccines` table. Created models [InventoryLot.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Models/InventoryLot.php) and [StockMovement.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Models/StockMovement.php).
* **FEFO Inventory Allocation Service**: Implemented [FefoInventoryService.php](file:///home/hongphuoc/Desktop/thue/app/Services/FefoInventoryService.php) with `allocateAndReserve`, `releaseStock`, and `commitDeduction` methods using atomic `DB::transaction()` with pessimistic row locks (`lockForUpdate()`). Prioritized non-expired active lots ordered by earliest expiration date (`expires_at ASC`) and rejected recalled, quarantined, or expired lots.
* **Controller & Route Integrations**: Integrated `FefoInventoryService` into [VaccineController.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Http/Controllers/VaccineController.php) for reservation on post-register, cancellation, and payment. Added center-restricted lot management controller [AdminInventoryLotController.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Http/Controllers/Admin/AdminInventoryLotController.php) under `admin.auth` in [web.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/routes/web.php).
* **Feature Test Suite**: Created [FefoInventoryStockReservationTest.php](file:///home/hongphuoc/Desktop/thue/tests/Feature/FefoInventoryStockReservationTest.php) covering earliest-expiration FEFO lot selection, rejection of recalled/quarantined/expired lots, stock reservation on pending order, stock release on cancellation, and stock deduction commit on paid order, achieving 100% test pass rate.

## [v3.9.0] - 2026-08-01

### M7: Schedules, Slots & Concurrency Control (R3, Ponytail Style)

* **Schedules & Slots Schema & Models**: Created migration `2026_08_01_000003_create_schedules_and_slots_tables.php` defining `schedules` (`center_id`, `date`, `is_active`, `note`) and `slots` (`schedule_id`, `start_at`, `end_at`, `capacity`, `reserved_count`, `is_active`), and added `slot_id` foreign key to `registrations` table. Created [Schedule.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Models/Schedule.php) and [Slot.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Models/Slot.php) models and updated [Registration.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Models/Registration.php).
* **Atomic Concurrency Control**: Implemented pessimistic row-locking (`Slot::where('id', $slotId)->lockForUpdate()->first()`) inside `DB::transaction()` within `postRegister` in [VaccineController.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Http/Controllers/VaccineController.php). Enforced capacity limits where attempts exceeding capacity are rejected with a 422 HTTP status and error message "Khung giờ đã đầy công suất", ensuring zero overbooking and atomic increment of `reserved_count`.
* **Admin Schedule & Slot Management**: Created [AdminScheduleController.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Http/Controllers/Admin/AdminScheduleController.php) and [AdminSlotController.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Http/Controllers/Admin/AdminSlotController.php) with admin routes in [web.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/routes/web.php) allowing administrators to manage center working dates and slot capacities.
* **Feature Test Suite**: Created [SchedulesSlotsConcurrencyTest.php](file:///home/hongphuoc/Desktop/thue/tests/Feature/SchedulesSlotsConcurrencyTest.php) covering schedule & slot creation, reservation increment, capacity overflow rejection (422 HTTP status with zero overbooking), and simulated concurrent reservation locking, achieving 100% test pass rate across 58 tests in the suite.

## [v3.8.0] - 2026-08-01

### M6: CRM Consultation Leads, Registration Standardization & Backend Idempotency (R2, Ponytail Style)

* **CRM Leads Model & Controller**: Added migration `2026_08_01_000001_create_consultation_leads_table.php`, model [ConsultationLead.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Models/ConsultationLead.php), public lead submission controller [ConsultationLeadController.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Http/Controllers/ConsultationLeadController.php), and administrative lead management controller [AdminConsultationLeadController.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Http/Controllers/Admin/AdminConsultationLeadController.php). Refactored public disease consult requests (`postDiseaseConsult`) to save exclusively to `consultation_leads` table without creating fake patient accounts or dummy registration records in `registrations`.
* **Registration Pivot & Transaction Standardization**: Standardized `Registration` and `Vaccine` Eloquent pivot relationships (`belongsToMany`) to map `quantity`, `price`, and `sale_price` via `withPivot(['quantity', 'price', 'sale_price'])`. Created migration `2026_08_01_000002_add_sale_price_to_registration_vaccines_table.php` and updated `postRegister` in [VaccineController.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Http/Controllers/VaccineController.php) to accurately store item quantities, effective prices, and calculate total prices into `registration_vaccines`.
* **Backend Idempotency Mechanism**: Implemented [IdempotencyMiddleware.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Http/Middleware/IdempotencyMiddleware.php) and controller-level idempotency handling checking `Idempotency-Key` / `X-Idempotency-Key` headers or request payload `idempotency_key`. Duplicate registration requests with identical keys return existing cached 200/201 responses without creating duplicate records in DB.
* **Feature Tests Suite**: Added comprehensive feature test suite in [CrmLeadsAndRegistrationIdempotencyTest.php](file:///home/hongphuoc/Desktop/thue/tests/Feature/CrmLeadsAndRegistrationIdempotencyTest.php) covering lead isolation, pivot quantity & price calculation, idempotency duplication prevention, and admin lead management, achieving 100% test pass rate.

## [v3.7.0] - 2026-07-31

### M4: Content Security, SVG Upload Blocking, HTML Sanitization, URL Scheme Filtering & CSV Formula Guard

* **HTML Sanitizer**: Implemented and verified `App\Services\Security\HtmlSanitizer` (wrapping `SecurityHelper::cleanHtml`) to eliminate stored XSS risks in articles, WYSIWYG editors, and user inputs using DOMDocument tag and attribute allowlisting. Enhanced recursive child cleaning order to ensure all `href` attributes (including `javascript:`, `data:`, `vbscript:`) are stripped from `<a>` tags even when wrapped inside unallowed parent elements.
* **SVG Upload Blocking & Content Inspection**: Enforced MIME type restrictions (`mimes:jpeg,png,jpg,webp`) and created `App\Rules\SafeImageFile` to inspect raw uploaded file contents (`<svg`, `<?xml`, `<script`) across all administrative upload endpoints (Articles, Live Editor banners and images, Vaccines, Banners, and Live Editor team avatar base64 decoding), blocking SVG XML files disguised with `.png`/`.jpg` extensions.
* **Dangerous URL Scheme Filtering**: Added validation against `javascript:`, `data:`, and `vbscript:` schemes across banner links, center map URLs, and live editor settings to block client-side DOM execution.
* **CSV Formula Injection Guard**: Created `App\Services\Security\CsvSanitizer` (`App\Services\Security\CsvSanitizer::sanitizeCell`) and integrated it into CSV export (`AdminRegistrationController`), safely prefixing cells starting with formula triggers (`=`, `+`, `-`, `@`) with a single quote (`'`).
* **Feature Security Test Suite**: Verified 100% test pass rate in [ContentSecurityAndHardeningTest.php](file:///home/hongphuoc/Desktop/thue/tests/Feature/ContentSecurityAndHardeningTest.php) across 17 security and adversarial test cases (140 assertions).

## [v3.6.0] - 2026-07-31

### M3: R2 RBAC & Multi-branch Data Isolation

* **Master Catalog vs Branch Data Separation**: Enforced role-based data separation where `super_admin` has full CRUD permissions over the master vaccine catalog (`vaccines` table). Restricted `branch_admin` to managing local branch settings (`price`, `sale_price`, `stock_status`, `stock_quantity`, `is_featured`, `sort_order` in `center_vaccines` table). Any attempt by a `branch_admin` to modify master catalog fields (Name, Origin, Category, Description, etc.) returns HTTP `403 Forbidden`.
* **Laravel Policies & Access Control Enforcement**: Implemented and registered resource policies ([VaccinePolicy.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Policies/VaccinePolicy.php), [CenterVaccinePolicy.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Policies/CenterVaccinePolicy.php), [RegistrationPolicy.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Policies/RegistrationPolicy.php), [CenterPolicy.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Policies/CenterPolicy.php), [BannerPolicy.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Policies/BannerPolicy.php), [ArticlePolicy.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Policies/ArticlePolicy.php)) in [VaccineServiceProvider.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Providers/VaccineServiceProvider.php). Synchronized session authentication with Laravel's `Auth::setUser($user)` inside [AdminAuth.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Http/Middleware/AdminAuth.php) and [AdminContext.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Support/AdminContext.php).
* **Anti-IDOR & Cross-Branch Protection**: Added strict server-side cross-branch checks across administrative controllers ([AdminVaccineController.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Http/Controllers/Admin/AdminVaccineController.php), [AdminRegistrationController.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Http/Controllers/Admin/AdminRegistrationController.php), [AdminStockController.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Http/Controllers/Admin/AdminStockController.php)). If a `branch_admin` assigned to Branch A attempts to view, edit, or modify data or pass a `center_id` for Branch B, the system returns HTTP `403 Forbidden`.
* **Authorization Hole Fixes**: Bound `SuperAdminOnly` middleware (`super.admin`) to [AdminCenterController.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Http/Controllers/Admin/AdminCenterController.php), [AdminBannerController.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Http/Controllers/Admin/AdminBannerController.php), [AdminArticleController.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Http/Controllers/AdminArticleController.php), [AdminSettingController.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Http/Controllers/Admin/AdminSettingController.php), and [AdminLiveEditorController.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Http/Controllers/Admin/AdminLiveEditorController.php). Updated `AdminVaccineController::toggleFeatured` permission checks to allow both `super_admin` and authorized `branch_admin` users to toggle featured states.
* **Feature Test Suite**: Created comprehensive test suite in [RbacMultiBranchTest.php](file:///home/hongphuoc/Desktop/thue/tests/Feature/RbacMultiBranchTest.php) with 10 feature test cases validating master catalog protection, cross-branch anti-IDOR responses, policy authorization, and `super_admin` vs `branch_admin` permissions, achieving 100% pass rate.

## [v3.5.21] - 2026-07-31

### M2: Admin Account Normalization & Security Hardening

* **Artisan `admin:create` Command Implementation**: Created `CreateAdminCommand` ([app/Console/Commands/CreateAdminCommand.php](file:///home/hongphuoc/Desktop/thue/app/Console/Commands/CreateAdminCommand.php) and [modules/VaccineRegistration/Console/Commands/CreateAdminCommand.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Console/Commands/CreateAdminCommand.php)) supporting option flags (`--name=`, `--username=`, `--email=`, `--password=`, `--role=`, `--center_id=`) with interactive prompts when options are omitted. Added input validation for email/username uniqueness, password strength (min 8 characters), role restriction (`super_admin` or `branch_admin`), and mandatory valid `center_id` validation for `branch_admin` accounts.
* **Removal of Default Admin Auto-Creation**: Removed default super admin (`admin/admin123`) auto-creation on database seeding from [DatabaseSeeder.php](file:///home/hongphuoc/Desktop/thue/database/seeders/DatabaseSeeder.php).
* **Account Lifecycle Database Schema & Model**: Added migrations [2026_07_31_000004_add_admin_fields_to_users_table.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Database/Migrations/2026_07_31_000004_add_admin_fields_to_users_table.php) and [2026_07_31_000007_add_account_security_fields_to_users_table.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Database/Migrations/2026_07_31_000007_add_account_security_fields_to_users_table.php) to add `status`, `must_change_password`, `password_changed_at`, `last_login_at`, `locked_until`, and `failed_login_count` columns to `users` table. Updated [User.php](file:///home/hongphuoc/Desktop/thue/app/Models/User.php) `$fillable`, `$casts`, and implemented helper methods `isLocked(): bool`, `recordSuccessfulLogin()`, and `recordFailedLogin()`.
* **Login Controller Security Hardening & Lockout Logic**: Hardened [AdminAuthController.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Http/Controllers/Admin/AdminAuthController.php) with rate limiting throttling, checking `isLocked()` before login, recording security log events, tracking failed login attempts, and automatically locking admin accounts for 15 minutes after 5 consecutive failed attempts.
* **Feature Tests Verification**: Added comprehensive test suite in [AdminAccountSecurityTest.php](file:///home/hongphuoc/Desktop/thue/tests/Feature/AdminAccountSecurityTest.php) validating admin account creation, seeder normalization, model helpers, account locking, and login security controls.

## [v3.5.20] - 2026-07-31

### P0 Security Vulnerabilities & Business Logic Vulnerability Fixes

* **Removed Admin Authentication Backdoor**: Deleted the automatic default super admin creation logic inside [AdminAuthController.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Http/Controllers/Admin/AdminAuthController.php) and moved the initial super admin account seeding process safely into [DatabaseSeeder.php](file:///home/hongphuoc/Desktop/thue/database/seeders/DatabaseSeeder.php).
* **Payment Status Integrity**: Updated [VaccineController.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Http/Controllers/VaccineController.php) to default all new patient registrations to 'Chờ thanh toán', removing browser-side client manipulation of payment status.
* **XSS Mitigation (Reflected, Stored, DOM)**:
  - Applied HTML-escaping to query parameters inside `diseaseDetail` in [VaccineController.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Http/Controllers/VaccineController.php).
  - Created [SecurityHelper.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Support/SecurityHelper.php) to filter out unsafe HTML elements and scripts using `DOMDocument`, and integrated it into [AdminArticleController.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Http/Controllers/AdminArticleController.php) before saving.
  - Implemented `escapeHtml` helper in [app.js](file:///home/hongphuoc/Desktop/thue/public/js/app.js) to secure template literal HTML interpolation.
* **Pessimistic Inventory Locking & REST API Sanitization**: Wrapped status transitions in [AdminRegistrationController.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Http/Controllers/Admin/AdminRegistrationController.php) using database transactions with `lockForUpdate()`. Prevented duplicate stock subtraction and enabled automatic stock restoration on cancellation/refunds.
* **Safe CSV Cell Exporting**: Added formula sanitization (`safeCsvCell`) in [AdminRegistrationController.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Http/Controllers/Admin/AdminRegistrationController.php) to disable CSV formula injection vulnerabilities.
* **Role-based Vaccine Management Permissions**: Restricted global vaccine creation, master property editing, and deletion in [AdminVaccineController.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Http/Controllers/Admin/AdminVaccineController.php) to `super_admin` only. Enabled branch administrators to only edit local price, stock, and featured statuses, disabling general master inputs in the [_form.blade.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/resources/views/admin/vaccines/_form.blade.php) view.
* **Data Deletion Safeguards**: Blocked hard-deleting vaccines in [AdminVaccineController.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Http/Controllers/Admin/AdminVaccineController.php) and centers in [AdminCenterController.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Http/Controllers/Admin/AdminCenterController.php) if they are linked to historical registration records.
* **File Upload and URL Restrictions**: Blocked SVG upload formats in [AdminVaccineController.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Http/Controllers/Admin/AdminVaccineController.php), [AdminArticleController.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Http/Controllers/AdminArticleController.php), and [AdminBannerController.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Http/Controllers/Admin/AdminBannerController.php). Validated map iframe domains in [AdminCenterController.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Http/Controllers/Admin/AdminCenterController.php) and link URLs in [AdminBannerController.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Http/Controllers/Admin/AdminBannerController.php).
* **Missing Column Migration**: Added [2026_07_31_000006_add_quantity_to_registration_vaccines_table.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/Database/Migrations/2026_07_31_000006_add_quantity_to_registration_vaccines_table.php) migration to define the missing `quantity` column on the intermediate table, updating both `Registration` and `Vaccine` models to include the pivot attribute.

## [v3.5.28] - 2026-08-02

### Header Action Pill Buttons Standardization

* **Unified 3 Header Action Buttons**: Standardized all 3 header action items (Branch Selector `📍 Medicare Trà Nóc ▾`, Hotline Direct Call `📞 092 1331233`, and Cart Pill `🛒 Giỏ hàng (0)`) into a single, identical `.header-action-pill` component system in [app.blade.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/resources/views/layouts/app.blade.php). All 3 buttons now share identical height (40px), 50px pill radius, font styling (13.5px font-weight 700), white background, subtle Medicare Red border (`#fecaca`), and active hover transition to solid Medicare Red (`#c8102e`).

## [v3.5.27] - 2026-08-02

### Sidebar Sticky Layout Fix & Related Articles Card Enclosure

* **Sidebar Sticky Height Fix**: Fixed `.article-sidebar` alignment from `align-self: flex-start` to `align-self: stretch` in [articles.css](file:///d:/Projects/tiemchung/public/css/articles.css). The sidebar container now stretches to match 100% of the main article content height, enabling `.sticky-sidebar-container` (TOC widget) to remain permanently sticky along the entire length of long articles.
* **Enclosed Related Articles Section**: Enclosed the bottom related articles section in a clean white rounded card container (`.related-card-container`) with `1px solid #e2e8f0` border, 20px radius, and subtle shadow in [articles/show.blade.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/resources/views/articles/show.blade.php), matching the exact design language of the site.

## [v3.5.26] - 2026-08-02

### Option A: Related Articles Grid & Independent Sticky TOC Sidebar Redesign

* **Independent Sticky TOC Sidebar**: Simplified the right sidebar in [articles/show.blade.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/resources/views/articles/show.blade.php) by setting `.sticky-sidebar-container` to `position: sticky; top: 95px`. The Table of Contents (TOC) widget now remains permanently fixed in view at the top of the reader's viewport, 100% immune to being scrolled away.
* **Bottom Related Articles Grid Layout (Option A)**: Relocated the 5 related articles from the sidebar down to the bottom section above multi-topic recommendations. Designed a 5-card responsive horizontal grid (`.related-carousel-grid`) with `Medicare Navy` badges (`Cùng Chuyên Mục`), hover zoom animations, and clear typography separation in [articles.css](file:///d:/Projects/tiemchung/public/css/articles.css).

## [v3.5.25] - 2026-08-02

### CSS Refactoring & Legacy Pagination Rule Cleanup

* **Clean CSS Optimization**: Removed over 120 lines of redundant, heavy Tailwind pagination overrides (`nav[role="navigation"]`, `.shadow-sm`, `.catalog-pagination a:hover`, `span[aria-disabled="true"] > span`) from [style.css](file:///d:/Projects/tiemchung/public/css/style.css). Streamlined the entire site-wide pagination stylesheet into a single, clean `.news-pagination-custom` system for optimal CSS payload size and rendering performance.

## [v3.5.24] - 2026-08-02

### Global Pagination System Standardization

* **Unified Dynamic Red-Pill Pagination Across All Views**: Standardized the approved dynamic red-pill pagination template (`partials.pagination`) across all site components: Product Catalog ([grid.blade.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/resources/views/partials/grid.blade.php)), News Catalog ([articles/index.blade.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/resources/views/articles/index.blade.php)), Article Detail recommendations ([articles/show.blade.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/resources/views/articles/show.blade.php)), and Product Detail pages. Synchronized global `.news-pagination-custom` and `.pagination-btn` CSS rules into [style.css](file:///d:/Projects/tiemchung/public/css/style.css), ensuring 100% visual consistency and dynamic page handling site-wide.

## [v3.5.23] - 2026-08-02

### Dynamic Laravel Pagination Template Engine & Global Default View

* **Dynamic Pagination Template Engine**: Refactored the custom pagination template in [pagination.blade.php](file:///d:/Projects/tiemchung/resources/views/partials/pagination.blade.php) and [modules/VaccineRegistration/resources/views/partials/pagination.blade.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/resources/views/partials/pagination.blade.php). Replaced the rigid 4-button hardcoded logic with Laravel's dynamic `$elements` array. Neighboring page links around the active page now expand dynamically across any total number of pages. Configured `Paginator::defaultView('partials.pagination')` in [AppServiceProvider.php](file:///d:/Projects/tiemchung/app/Providers/AppServiceProvider.php) so this clean red-highlighted pill-shaped pagination is used globally as the base design.

## [v3.5.22] - 2026-08-02

### Article & Product Detail Table of Contents (TOC) Dynamic Scroll Spy & Outline Elimination

* **Dynamic Scroll Position Tracking & Zero Layout Shift**: Refactored `initDynamicTOC()` in [app.js](file:///d:/Projects/tiemchung/public/js/app.js) to replace brittle `IntersectionObserver` thresholds with a high-performance scroll-position tracking algorithm. The Table of Contents (TOC) links on both Article Detail and Vaccine Product Detail pages now continuously and smoothly follow the reader's active section as they scroll. Removed browser default black focus ring outlines (`:focus`, `:focus-visible`) and heavy yellow callout box backgrounds from [articles.css](file:///d:/Projects/tiemchung/public/css/articles.css) and [vaccines.css](file:///d:/Projects/tiemchung/public/css/vaccines.css), eliminating text wrapping jumps and layout shifts.

## [v3.5.21] - 2026-08-02

### Header UX Redesign: Compact Branch Selector & Dedicated Call Action Button

* **Header Branch Selector & Hotline Action Split**: Refactored the header action items in [app.blade.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/resources/views/layouts/app.blade.php). Separated the combined long button into two distinct components: a compact, clean branch picker badge (`📍 Medicare Cờ Đỏ ▾`) for switching active centers, and a prominent Medicare Red call button (`📞 0938 60 38 39`) for direct telephone dialing (`tel:`). Centered the branch selection dropdown (`#branchDropdown`) directly under the branch picker badge using `left: 50%; transform: translateX(-50%)`. Integrated a direct contact/map link inside the branch dropdown view for seamless navigation to the contact page, and removed the duplicate 'Liên Hệ' text link from the main navigation menu to ensure a streamlined 4-item nav bar (`Trang Chủ | Giới Thiệu | Danh Mục Sản Phẩm | Tin Tức`).

## [v3.5.20] - 2026-08-02

### Pagination CSS Centering & Ellipsis Border Elimination

* **Strict Pagination Centering & Ellipsis Border Removal**: Updated pagination styles in [style.css](file:///d:/Projects/tiemchung/public/css/style.css). Completely stripped borders, backgrounds, and box-shadows from `...` (ellipsis) disabled spans, rendering them as clean text dots. Applied `justify-content: center` and hidden summary text overrides across all pagination containers (`.catalog-pagination`, `.news-pagination`, `nav[role="navigation"]`) to ensure the entire pagination bar is perfectly centered on all pages.

## [v3.5.19] - 2026-07-31

### Dynamic Branches, Register Center Selection, Admin Imports & Responsive Header Fix

* **Dynamic Home Branch Indicators**: Updated homepage partial view [centers.blade.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/resources/views/partials/home/centers.blade.php) and [quick_booking.blade.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/resources/views/partials/home/quick_booking.blade.php) to pull the active branches dynamically from `\Modules\VaccineRegistration\Support\CenterContext::activeCenters()`. Replaced the hardcoded '2' branch count and 'Chi nhánh Cờ Đỏ & Thới Lai' string with database-driven counts and concatenated branch names, resolving inconsistencies between the homepage and the contact page.
* **Local Register Center Selection**: Removed the automatic page reload and global center redirect behavior from the center dropdown in [register.blade.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/resources/views/register.blade.php). Changing the center in Step 2 now functions as a standard local form input selector, preserving filled form fields and preventing unnecessary redirect-back loops.
* **Admin Controller Namespace Fix**: Added the missing namespace import statement for `Modules\VaccineRegistration\Support\AdminContext` inside [AdminVaccineController.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/Http/Controllers/Admin/AdminVaccineController.php), fixing the PHP "Class AdminContext not found" runtime crash when loading the Admin Vaccine Catalog list view.
* **Responsive Header Style Updates**: Wrapped the phone number in the desktop hotline button in [app.blade.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/resources/views/layouts/app.blade.php) inside a `.hotline-phone-suffix` span, and added a media query to [style.css](file:///c:/Users/Admin/Desktop/tiemchung/public/css/style.css) for screens below 1250px. This query reduces the main header menu gaps, shrinks font sizes, and hides the hotline phone suffix to prevent navigation link wrap-arounds on medium-sized screens.
* **SPA Modal AJAX Center Selection**: Refactored the modal center selection handler `changeSpaRegisterCenter` in [app.js](file:///c:/Users/Admin/Desktop/tiemchung/public/js/app.js) to utilize AJAX request handling. Changing the branch dropdown in the booking modal now dynamically updates the selected center session, updates all local vaccine pricing and active/unavailable statuses inside the patient blocks, updates the cart summary sidebar, and synchronizes the main site header/topbar active branch texts, all without reloading the page or losing form inputs.

## [v3.5.18] - 2026-07-31

### Branch Maps Integration, CSS Dropdown/Button Refinements & Flash Messages to Floating Toast Transformation

* **Google Maps Integration**: Updated [CenterSeeder.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/Database/Seeders/CenterSeeder.php) to insert official Google Maps embed iframe URLs (`map_url`) for the 4 primary Medicare centers (Medicare Cờ Đỏ, Medicare Thới Lai, Medicare Phong Điền, and Medicare Trà Nóc) and executed the seeder to populate the database, which automatically renders interactive maps in the branch cards on the contact/selection page ([contact.blade.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/resources/views/contact.blade.php)).
* **Header Branch Dropdown Style Refinements**: Refactored the branch selector dropdown in the header layout ([app.blade.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/resources/views/layouts/app.blade.php)). Replaced inline styles with a dedicated class `.branch-item-btn`. Styled the active branch with a red-tinted background (`rgba(200,16,46,0.08)`) and Medicare Red text, and added a smooth background transition with a subtle hover effect (`#f8fafc` for inactive, `#ffe4e6` for active hover).
* **Contact Branch Selection Button Refinement**: Replaced the default `.btn-secondary` class on the "Chọn chi nhánh này" submit button in [contact.blade.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/resources/views/contact.blade.php) with a custom `.contact-branch-btn-select` class. Configured it to display a solid red border and red text on a clean white background, smoothly flipping to a solid Medicare Red background with white text when hovered, ensuring excellent contrast and high visibility.
* **Floating Toast Messages**: Transformed the static flash message alerts inside the main layout ([app.blade.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/resources/views/layouts/app.blade.php)) into responsive, non-blocking floating Toast notifications. Configured them in a fixed container (`top: 24px; right: 24px; z-index: 9999;`) with elegant glassmorphism backgrounds, custom color-coded borders (Success, Error, Warning, Info), a smooth slide-in/fade-out transition, and a Javascript auto-dismiss timeout of 4 seconds (with manual close button fallback).

## [v3.5.17] - 2026-07-28

### Admin Article Editor Block Deletion and Hover UX Fix

* **Fix Block Controls Positioning and Hover Issues**: Repositioned the `.cell-controls` container slightly outside the notebook cell boundaries (`top: -14px` instead of `top: 8px`) and raised its `z-index` to `99`. This prevents the block control buttons from being obscured or click-intercepted by the TinyMCE rich text editor and its toolbars, while avoiding layout overlaps.
* **Implement Active Cell State Tracking**: Added Javascript click listeners to the cells and a `focus` event listener to the TinyMCE initialization script to track the active cell via the `.active-cell` CSS class. This ensures the block controls stay permanently visible while a cell is being focused or edited, ensuring a smooth, bug-free block deletion and management UX.

## [v3.5.16] - 2026-07-28

### Admin Sidebar Styling, Banners, Vaccine Booking & Article Creation Enhancements

* **Cleaned Active Menu Style**: Removed the redundant inline background and duplicate red left-border style on the "Chỉnh Sửa Trực Quan (Live)" sidebar item in [admin.blade.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/resources/views/layouts/admin.blade.php) when the route is active, ensuring it matches the uniform active styling of other sidebar items and avoids displaying a dual left border.
* **Full-Width Hero Banners (Tràn viền)**: Realigned and updated sub-page banners on About ([about.blade.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/resources/views/about.blade.php)), News ([articles/index.blade.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/resources/views/articles/index.blade.php)), Vaccines ([index.blade.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/resources/views/index.blade.php)), and Contact ([contact.blade.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/resources/views/contact.blade.php)) pages. Moved hero sections outside of their boxed wrappers to stretch them 100% full-width across the viewport, removing rounded borders, and wrapped their inner contents with a 1200px responsive centered container.
* **Unified Banner Style & Homepage Slider Alignment**: Aligned all page banners and the homepage hero slider ([hero_slider.blade.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/resources/views/partials/home/hero_slider.blade.php#L4)) to share the exact same dark red gradient overlay (`linear-gradient(135deg, rgba(200, 16, 46, 0.93) 0%, rgba(145, 10, 33, 0.90) 100%)`) for color uniformity. Applied a unique and context-relevant background image for each sub-page: a clinic corridor for About, a tablet device for News, vaccine vials for Vaccines, and an office consultation desk for Contact.
* **Removed Services Page**: Completely removed the Services page. Deleted the services route in [web.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/routes/web.php#L25), deleted the view file `services.blade.php`, and removed all navigation menu links from the header, mobile drawer, and footer in [app.blade.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/resources/views/layouts/app.blade.php).
* **Direct Vaccine Booking Flow**: Implemented direct booking links on homepage featured vaccine cards ([qdenga_promo.blade.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/resources/views/partials/home/qdenga_promo.blade.php#L42)). Clicking "Đặt lịch" or the vaccine title triggers `selectVaccineAndBook` in `app.js` which automatically adds the vaccine to the cart via AJAX and launches the SPA registration drawer. Added backend session synchronization in `showRegister` of `VaccineController.php` as a fallback.
* **Cart Delete & Quantity Display Fixes**: Fixed the cart delete button bug where deleting an item from outside the catalog page incorrectly added it back to the cart and multiplied the price. Added a `forceRemove` flag to `toggleCart` in `app.js` and modified the trash icon handlers to call `toggleCart(id, true)`. Restrained selected vaccine quantities to 1 in `addToCart` inside `VaccineController.php` to prevent stacking, and added item quantity indicators (`SL: X`) to the header cart dropdown rows in both `app.blade.php` and `app.js`.
* **Multi-Patient Registration & Vaccine Assignment**: Added support for registering multiple patients in a single session. Replaced step 1 of the client form ([register.blade.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/resources/views/register.blade.php)) and the SPA modal ([app.js](file:///c:/Users/Admin/Desktop/tiemchung/public/js/app.js)) with a dynamic form that allows adding/removing patient profiles and checking specific vaccines for each person, rendering thumbnail images and prevention descriptions next to each checkbox row. Updated [VaccineController.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/Http/Controllers/VaccineController.php#L382) to record separate registrations in the database, calculate subtotal and grand total prices, and pass them to [success.blade.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/resources/views/success.blade.php) to display individual ticket cards and a single combined QR code.
* **AJAX Filter & Pagination Fix**: Fixed the vaccine catalog filtering and pagination engine. Updated `VaccineController@index` in [VaccineController.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/Http/Controllers/VaccineController.php) to properly detect and respond to AJAX/XMLHttpRequest requests by returning a JSON payload containing the rendered partial grid HTML and vaccine count instead of returning the full HTML page view, resolving the SPA filter engine failures.
* **Smooth Viewport Locking**: Added smooth scroll-to-catalog action on successful AJAX filter/pagination loads in `public/js/app.js` to ensure the viewport automatically docks at the top of the product listing (`.catalog-layout`) and prevents frame jumping during asynchronous DOM replacement.
* **Admin Article Form Submitting Bug Fix**: Fixed an issue in [create.blade.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/resources/views/admin/articles/create.blade.php) and [edit.blade.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/resources/views/admin/articles/edit.blade.php) where `document.querySelector('form')` selected the admin layout's logout form (`#logoutForm`) instead of the article form, preventing rich-text editor content from syncing into the hidden `<textarea name="content">` input upon submission. Assigned explicit IDs (`#articleCreateForm` and `#articleEditForm`) and updated the JS selector to fix content persistence.
* **Admin Article Edit/Create UI Refactoring**: Removed the complex block builder and replaced it with a single, full-featured TinyMCE editor. Restructured the layout into a professional 2-column format (70% main content, 30% metadata sidebar), created a dedicated thumbnail upload drag-and-drop preview block, and styled the excerpt/summary field explicitly for index card display.
* **Category Standardization Across Admin and Client**: Standardized the 8 medical article categories (`Tin Nóng Y Học`, `Khuyến Cáo Y Tế`, `Bệnh Truyền Nhiễm`, `Vắc Xin Mới`, `Chăm Sóc Trẻ Em`, `Tiêm Chủng Người Lớn`, `Tiêm Phòng Mẹ Bầu`, `Góc Chuyên Gia`) across [create.blade.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/resources/views/admin/articles/create.blade.php), [edit.blade.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/resources/views/admin/articles/edit.blade.php), and [ArticleController.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/Http/Controllers/ArticleController.php) to ensure newly created or edited articles properly map to client-side tabs.
* **Client Article Detail Sidebar & Dynamic TOC Enhancement**: Added the missing Sidebar Related Articles widget (`$relatedArticles`) and dynamic Table of Contents (TOC) JavaScript generation from `<h2>` elements in [show.blade.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/resources/views/articles/show.blade.php).

## [v3.5.15] - 2026-07-27

### Customer Layout Footer Restoration

* **Customer Footer Restoration**: Restored the main customer page layout footer in [app.blade.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/resources/views/layouts/app.blade.php) to the standard VNVC Mẫu Hình 2 style, correcting structural HTML syntax errors and layout issues introduced by unapproved modifications.

## [v3.5.14] - 2026-07-27

### Admin Dashboard Brand Color Palette Alignment

* **Admin Color Variables**: Updated `--accent-color` and `--accent-hover` to Medicare Navy (`#004b8f` and `#00386c`) instead of the unapproved deep red. Cleaned up border variables to Slate 200 (`#e2e8f0`) and set focus outline to Medicare Navy to remove unapproved pink/red borders.
* **Badge System Standardization**: Fixed status badges in the Admin layout. Corrected `.badge-modern-info` (e.g. for "Đã tiêm" status) to use the Medicare Navy theme instead of pink/red. Standardized success, warning, and danger badges to utilize clean, brand-approved color tones.
* **Flat Badge Design Upgrade**: Redesigned all admin badges to a modern Flat style without borders, featuring pastel background fills, uppercase-to-normal casing, semi-bold text, and a 6px border-radius. Enforced `white-space: nowrap` and increased padding to keep status descriptions strictly on a single line for optimal readability across tables (e.g., Vaccine Catalog and Appointment lists). Increased font size from `12.5px` to `13px` (`12px` in schedule cards) to enhance readability.
* **Action Buttons Text Wrap Fix**: Applied `white-space: nowrap` to small action buttons (`.btn-action-sm`) in tables and increased the Action column width from 200px to 280px in the Vaccine Catalog list view to ensure action buttons (such as "Bỏ Nổi Bật") sit on a single line without wrapping.
* **Admin Layout Full-Width Fix**: Changed `.admin-body` maximum width constraint (`max-width`) from `1480px` to `100%` to allow the layout to scale fully across wide screens (e.g. Full HD and higher), eliminating the empty space on the right of the screen.
* **Articles Index Layout Standardization**: Upgraded the News & Articles index page layout by removing the custom `max-width: 1200px` and custom table styles. Converted the layout to utilize `.card-modern`, `.table-responsive-modern`, and `.table-modern` styling classes, enabling full-width scaling and visual parity with other administrative views. Removed article slug URL paths display under article titles, standardized the delete action buttons to use `.btn-action-danger` class, and enlarged article thumbnail images from `60x40px` to `90x60px` with custom shadows and border stylings. Integrated TinyMCE 6 AJAX inline image upload API endpoints (`/admin/articles/upload-image`) to allow authors to drag-and-drop or insert multiple files from their local storage directly inside the editor layout, reflowing seamlessly alongside multiple paragraphs.
* **Website Settings Layout Redesign**: Upgraded the settings index view by removing the layout restriction (`max-width: 700px`), making it scale to full-width. Re-organized input fields into a balanced 2-column grid layout for standard fields (Site name, Email, Hotline, Alternate hotline) while maintaining full-width block formats for long text inputs (Address and Footer copyright text).
* **Responsive Fluid Typography Implementation**: Converted fixed `px` font sizes across all key admin dashboard elements (tables, labels, buttons, badges, titles, sidebars) to relative `rem` units, and declared a responsive fluid font-size equation (`clamp(14px, 0.15vw + 12.5px, 16.5px)`) on the `:root` selector. This allows the layout to dynamically and smoothly scale typographic hierarchy based on the active viewport/screen width.
* **Comprehensive Mobile Responsive Optimization**: Implemented a global `.form-grid-2` CSS class to replace legacy static inline grid definitions across all administrator forms (Vaccine Catalog, Banners, Website settings, and Branch catalogs), automatically reflowing multi-column inputs to a stacked single-column design on mobile screens (<768px). Optimized layout paddings (narrowing main canvas padding to `16px 12px` and card padding to `16px` on mobile), downscaled table column padding (`10px 12px`), corrected detail view split structures down to `minmax(280px, 1fr)` to prevent overflow, and compacted Dashboard card widget gaps to ensure zero-overflow presentation across all common smartphone screen widths (320px to 430px).
* **Table Horizontal Scroll and Action Button Standardization**: Fixed text wrapping issues in layout tables (such as Center and Banner lists) on mobile screens by enforcing a `min-width: 850px` constraint on the `.table-modern` class, prompting standard horizontal scrolling in the responsive wrapper. Overhauled [centers/index.blade.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/resources/views/admin/centers/index.blade.php) and [banners/index.blade.php](file:///home/hongphuoc/Desktop/thue/modules/VaccineRegistration/resources/views/admin/banners/index.blade.php) to use `.card-modern`, `.table-responsive-modern`, and `.table-modern` styling classes. Integrated red-accented `.btn-action-sm.btn-action-danger` classes in CSS variables to format destructive action buttons.

## [v3.5.13] - 2026-07-27

### Dynamic Database Heading Self-Healing Update

* **Dynamic In-Place Database Update**: Added dynamic self-healing code inside the show method of [ArticleController.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/Http/Controllers/ArticleController.php) to automatically replace `<h3>` with `<h2>` in the database dynamically and permanently when a news article page reloads.
* **Standard heading seed format**: Updated [ArticleSeeder.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/Database/Seeders/ArticleSeeder.php) to seed standard `<h2>` tags for main sections instead of `<h3>`.

## [v3.5.12] - 2026-07-27

### TOC Headings & Pagination View Resolution Fix

* **TOC Headings Restoration**: Updated `initDynamicTOC()` in [app.js](file:///d:/Projects/tiemchung/public/js/app.js) to query both `h2` and `h3` headings inside `.article-body-content` while keeping only `h2` for main vaccine sections. This fully restores the table of contents widget on news articles.
* **Pagination View Resolution**: Copied the custom compact pagination template to the root view path [pagination.blade.php](file:///d:/Projects/tiemchung/resources/views/partials/pagination.blade.php) and updated the links call in [articles/show.blade.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/resources/views/articles/show.blade.php) to use `partials.pagination` ensuring it resolves correctly across both namespace paths.

## [v3.5.11] - 2026-07-27

### 4-Card Slider Fit & Border Margin Optimization

* **Card Flex Calculation**: Updated `.related-slider-card` flex width to `calc((100% - 48px) / 4)` and reduced gap to `16px` in [vaccines.css](file:///d:/Projects/tiemchung/public/css/vaccines.css), ensuring all 4 vaccine cards sit comfortably inside the white card container with zero right-border clipping or overflow.

## [v3.5.10] - 2026-07-27

### Complete Visual Parity for White Card Section Container

* **Vaccine Detail Page White Card Container**: Added `.suggested-news-section` and `.suggested-news-header` rules to [vaccines.css](file:///d:/Projects/tiemchung/public/css/vaccines.css), ensuring the Vaccine Detail Page features the exact same white card container (`background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; padding: 32px; box-shadow...`) and red underline header (`border-bottom: 2px solid #c8102e;`) as shown in Image 57.

## [v3.5.9] - 2026-07-27

### Dedicated Isolated White Card Section Restoration

* **White Card Block Container**: Restored `.suggested-news-section` as a dedicated white card block with `background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; padding: 32px; box-shadow: 0 4px 20px rgba(0,0,0,0.03);` in [articles.css](file:///d:/Projects/tiemchung/public/css/articles.css).
* **Clean Isolation**: Placed the white card container below the main 2-column layout in [show.blade.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/resources/views/show.blade.php) and [articles/show.blade.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/resources/views/articles/show.blade.php), ensuring all section titles, subtitles, cards, and pagination links are cleanly enclosed inside a dedicated card block with zero unstyled floating text.

## [v3.5.8] - 2026-07-27

### Pagination Formula & Sticky Sidebar Layout Structure Fix

* **Exact Pagination Formula (`1 2 ... (end-1) end`)**: Updated [pagination.blade.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/resources/views/partials/pagination.blade.php) to strictly output the requested formula (`[<] [1] [2] [...] [16] [17] [>]`).
* **Layout Containment Fix**: Moved `<section class="suggested-news-section">` and `<section class="catalog-advice-banner">` inside `<main class="article-main-content">` in [show.blade.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/resources/views/show.blade.php). This ensures the right sticky sidebar container stretches smoothly alongside the left main content column, completely eliminating floating sidebar overlap over bottom sections.

## [v3.5.7] - 2026-07-27

### Custom Compact Pagination & Section Clearance Fix

* **Custom Compact Pagination (Max 4 Page Buttons + ...)**: Created custom Blade pagination template [pagination.blade.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/resources/views/partials/pagination.blade.php) and updated [articles/show.blade.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/resources/views/articles/show.blade.php), displaying a maximum of 4 page numbers plus dots (`[<] [1] [2] [3] [4] [...] [>]`) with zero double-border bugs or parentheses artifacts.
* **Section Separation Clearance**: Added `clear: both; margin-top: 60px; z-index: 20;` clearance in [show.blade.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/resources/views/show.blade.php) ensuring the sticky right sidebar stops cleanly above the "Vắc Xin Liên Quan" slider section without any overlap.

## [v3.5.6] - 2026-07-27

### Clean Main Section H2-Only TOC Query Fix

* **Single-Line H2 Heading Links**: Updated `initDynamicTOC()` in [app.js](file:///d:/Projects/tiemchung/public/js/app.js) to query ONLY main `<h2>` section headings (`.article-main-content .article-body-content h2, .article-main-content section h2`), excluding lengthy `<h3>` subheadings for a clean, single-line 6-item Table of Contents.
* **Sticky Layout Room**: Added `align-self: stretch` to `.article-sidebar` in [vaccines.css](file:///d:/Projects/tiemchung/public/css/vaccines.css) ensuring sticky scroll room across the entire vaccine product detail page.

## [v3.5.5] - 2026-07-27

### Exact 100% Parity for TOC & Sticky Sidebar Logic

* **Unified HTML & CSS**: Replaced custom layout classes with shared `.vaccine-detail-layout`, `.article-sidebar`, and `.sticky-sidebar-container` markup in [articles/show.blade.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/resources/views/articles/show.blade.php).
* **Targeted Heading Query**: Updated `initDynamicTOC()` in [app.js](file:///d:/Projects/tiemchung/public/js/app.js) to query ONLY `.article-body-content h2, h3` and `.article-main-content section h2, h3`, preventing any non-article headings from being included in the Table of Contents.

## [v3.5.4] - 2026-07-27

### Article Detail Right Sidebar Optimization

* **Concise Sidebar Layout**: Removed "Bài Viết Cùng Chủ Đề" widget from the Right Sidebar in [articles/show.blade.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/resources/views/articles/show.blade.php).
* **Instant Sticky Scroll**: Reduced sidebar height to ~380px so the TOC and Booking CTA widgets stick immediately (`top: 100px`) and scroll smoothly alongside the article text without viewport overflowing.

## [v3.5.3] - 2026-07-27

### TOC Container Target Resolution Fix

* **Header Card Class Rename**: Changed top summary card class in [show.blade.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/resources/views/show.blade.php) to `vaccine-header-card` to eliminate element selection collision with `article-main-content`.
* **Smart Content Container Resolution**: Refined `initDynamicTOC()` in [app.js](file:///d:/Projects/tiemchung/public/js/app.js) to locate `bodyContent.closest(...)` main content containers, ensuring 100% of headings across all sections are queried and rendered into the TOC widget without false hiding.

## [v3.5.2] - 2026-07-27

### Sticky Sidebar Scroll & Complete TOC Scanner Fix

* **Unblocked Sticky Scrolling**: Removed `data-aos="fade-left"` animation attribute from `<aside class="article-sidebar">` in [articles/show.blade.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/resources/views/articles/show.blade.php), eliminating CSS `transform: translate3d()` interference so `position: sticky; top: 100px;` works smoothly when scrolling.
* **Complete TOC Heading Scanner**: Updated `initDynamicTOC()` in [app.js](file:///d:/Projects/tiemchung/public/js/app.js) to query all `h2` and `h3` elements inside `.article-main-content`, ensuring 100% of medical sections and article headings are dynamically listed and highlighted in the Right Sidebar TOC.

## [v3.5.1] - 2026-07-27

### TOC Card Visual Parity Fix

* **Identical Card Container**: Added `.vaccine-toc-widget` and `.toc-link-item` CSS definitions to [articles.css](file:///d:/Projects/tiemchung/public/css/articles.css), ensuring the Article Detail Table of Contents has the exact same white rounded card background (`#ffffff`), 1px border, shadow, and cream active pill highlight (`#fff9e6`) as the Vaccine Detail page.

## [v3.5.0] - 2026-07-27

### Unified Layout (Left-Big Right-Small) & Dynamic Sticky TOC

* **Unified Column Orientation**: Swapped columns in [show.blade.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/resources/views/show.blade.php) to place Main Detailed Content on the **Left (75% Big column)** and Sidebar on the **Right (25% Small column)**, matching [articles/show.blade.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/resources/views/articles/show.blade.php).
* **Automated Dynamic TOC & Scroll Observer**: Created `initDynamicTOC()` in [app.js](file:///d:/Projects/tiemchung/public/js/app.js) to scan `<h2>` and `<h3>` tags in article/vaccine body text, generate dynamic Table of Contents in the Right Sticky Sidebar (`top: 100px;`), and automatically highlight active headings on scroll.

## [v3.4.1] - 2026-07-27

### Sticky Sidebar Layout & Scroll Synchronization

* **Header-Safe Sticky Offset (`top: 100px`)**: Added `.sticky-sidebar-container` with `position: sticky; top: 100px;` to [articles.css](file:///d:/Projects/tiemchung/public/css/articles.css) and wrapped sidebar widgets in [articles/show.blade.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/resources/views/articles/show.blade.php).
* **Unified Scroll Behavior**: Ensured both Article Detail (`/news/{slug}`) and Vaccine Product Detail (`/vaccines/{id}`) share the exact same 2-column layout ratio, padding, shadow, header clearance, and smooth sticky sidebar scroll without header overlap.

## [v3.4.0] - 2026-07-27

### Header Navigation Actions Bar Refinement

* **Simplified Hotline Button**: Removed "Tư vấn:" prefix from `.hotline-btn` in [layouts/app.blade.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/resources/views/layouts/app.blade.php), displaying phone icon and phone number (`0938 60 38 39`).
* **Identical Cart Pill Button (`.header-cart-btn-pill`)**: Redesigned the header cart button using the exact same `.hotline-btn` pill style, background, border, and typography as the phone button, with text "Giỏ hàng" and inline count badge (`.header-cart-badge-inline`).
* **Renamed CTA Button**: Renamed primary red header button from "Đăng ký tiêm" to "Đặt lịch".

## [v3.3.1] - 2026-07-27

### Persistent Header Cart Placement Fix

* **Header Position Insertion**: Fixed markup alignment in [layouts/app.blade.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/resources/views/layouts/app.blade.php) by inserting `.header-cart-wrapper` directly into `.header-actions` right between the Hotline button and the "Đăng ký tiêm" CTA.
* **Persistent Visibility**: Configured `.header-cart-btn` in [app.js](file:///d:/Projects/tiemchung/public/js/app.js) to remain persistently visible on the top header navigation bar at all times (displaying live item count badge `0`, `1`, `2`...).

## [v3.3.0] - 2026-07-27

### Top Header Navigation Bar Cart Integration

* **Header Cart Icon Button (`.header-cart-btn`)**: Integrated the shopping cart button directly into the Top Header Navigation Bar in [layouts/app.blade.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/resources/views/layouts/app.blade.php) right next to the "Đăng ký tiêm" primary button. Styled in [style.css](file:///d:/Projects/tiemchung/public/css/style.css) with a 42px soft cream circle, gold border, red cart icon, and red badge counter (`#cartCount`).
* **Header Cart Dropdown Menu (`#headerCartDropdown`)**: Hovering or clicking the header cart button displays a sleek modal dropdown (width 360px) below the top header listing selected items, unit prices, remove buttons, total price, and "Đăng ký tiêm ngay" CTA.
* **100% Clean Bottom Screen**: Removed all bottom floating cart bars, keeping the bottom right corner clean with only Zalo & Hotline contact buttons.

## [v3.2.1] - 2026-07-26

### Ultra-Premium Bottom-Center Floating Cart Pill Bar (Apple / Shopee Style)

* **Centered Fixed Positioning (`bottom: 24px; left: 50%; transform: translateX(-50%);`)**: Replaced standalone circular button with an ultra-premium horizontal Pill Bar anchored at bottom-center in [layouts/app.blade.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/resources/views/layouts/app.blade.php) and [style.css](file:///d:/Projects/tiemchung/public/css/style.css).
* **Pill Layout Architecture**:
  - **Left**: Shopping cart icon in translucent circle + golden counter badge (`cartCount`).
  - **Middle**: "Danh sách tiêm chủng" header + dynamic total price text (`#cartTotalPrice`).
  - **Right**: "Đăng ký ngay →" CTA button in Medicare Gold.
* **Zero Widget Collision**: Placed cart pill bar horizontally centered, completely separating cart interactions from the vertical Zalo and Hotline stack on the right edge.
* **Centered Modal Popup Drawer**: Clicking cart text/icon opens `#cartDrawerPopup` centered right above the pill bar with full product listing and item removal functionality.

## [v3.2.0] - 2026-07-26

### Circular Top Floating Cart Button & Modal Popup Drawer

* **Top Floating Stack Position**: Converted floating cart into a 48px circular expandable button positioned at the VERY TOP of the floating contact stack in [layouts/app.blade.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/resources/views/layouts/app.blade.php) (Stack order: 1. Cart Icon, 2. Zalo, 3. Hotline).
* **Golden Counter Badge (`cart-badge-count`)**: Rendered a golden Medicare Gold circular counter badge on the top right of the 48px cart button showing real-time item count (`1`, `2`...).
* **Smooth Drawer Popup (`#cartDrawerPopup`)**: Clicking the cart button expands a modal drawer popup (width 360px, rounded corners, box-shadow) displaying selected items, unit prices, remove buttons, total amount, and "Đăng ký tiêm ngay" CTA without obscuring page content.
* **Auto-Close On Outside Click**: Added global event listener to automatically collapse the drawer back to the 48px round button when clicking outside or clicking the close button.

## [v3.1.3] - 2026-07-26

### Floating Cart Collision Prevention & Responsive Layout

* **Desktop Position (`right: 90px; bottom: 28px;`)**: Repositioned `.floating-cart` in [style.css](file:///d:/Projects/tiemchung/public/css/style.css) to `right: 90px; bottom: 28px;` with `border-radius: 14px`, sitting directly to the left of the floating contact widget (Zalo / Hotline) with zero overlap.
* **Mobile Layout (`bottom: 88px;`)**: Configured mobile breakpoint so `.floating-cart` renders as a full-width floating bar elevated above floating action buttons (`bottom: 88px`).

## [v3.1.2] - 2026-07-26

### Unified Cart Toggle API & Multi-Location Synchronization

* **Single API Function (`toggleCart`)**: Standardized all product selection buttons (catalog grid cards, detail page primary button, and related product cards) to call the global `toggleCart(vaccineId)` function in [app.js](file:///d:/Projects/tiemchung/public/js/app.js).
* **Reliable Server-State Inspection**: Refactored `toggleCart` to determine item selection state dynamically from the returned `data.cart[vaccineId]` payload.
* **Synchronized UI Updates**: Clicking any select button automatically updates all matching cards, detail buttons, quick-view modals, floating cart drawer, and toast alerts (`"Đã thêm vắc xin..."` vs `"Đã xóa vắc xin..."`) across the entire SPA.

## [v3.1.1] - 2026-07-26

### Sticky Sidebar Bottom Margin Overlap Fix

* **Bottom Spacing (`margin-bottom: 54px`)**: Added a 54px bottom margin to `.vaccine-detail-layout` in [vaccines.css](file:///d:/Projects/tiemchung/public/css/vaccines.css), ensuring the sticky left sidebar stops cleanly before reaching the related vaccines slider with zero border clipping or overlap.
* **Overflow Masking**: Added `overflow: hidden` to `.sidebar-cta-widget` to preserve rounded corners and clean box shadows during sticky scrolling.

## [v3.1.0] - 2026-07-26

### Dedicated Vaccine Product CSS & Header-Safe Sticky Sidebar

* **Dedicated `vaccines.css`**: Created [vaccines.css](file:///d:/Projects/tiemchung/public/css/vaccines.css) specifically for vaccine product catalog and detail pages ([show.blade.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/resources/views/show.blade.php)), separating vaccine product styles completely from news/articles.
* **Header-Safe Sticky Sidebar (`top: 100px`)**: Fixed sticky sidebar container in [show.blade.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/resources/views/show.blade.php) with `top: 100px` so the Table of Contents header is never covered by the site's top fixed navigation bar when scrolling.
* **Clean HTML/Markdown Parser Support**: Integrated styling rules for headings, paragraphs, lists, and tables inside `.article-body-content`, eliminating artificial callout boxes while preserving 100% justified typography and bottom-right author credit.
* **Related Vaccines Horizontal Slider**: Added `<` and `>` arrow navigation buttons to scroll through 8 related vaccine products dynamically loaded from the database.

## [v3.0.2] - 2026-07-26

### Streamlined 8-Category Prioritized Medical Navigation Bar

* **Prioritized 8 Core Medical Categories**: Streamlined navigation in [ArticleController.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/Http/Controllers/ArticleController.php) by eliminating redundant items and organizing 8 categories in order of medical importance (`Tất cả bài viết`, `Tin Nóng Y Học`, `Khuyến Cáo Y Tế`, `Bệnh Truyền Nhiễm`, `Vắc Xin Mới`, `Chăm Sóc Trẻ Em`, `Tiêm Chủng Người Lớn`, `Tiêm Phòng Mẹ Bầu`).
* **Balanced Spacing (~28px - 30px)**: Achieved clean spacing between category tabs, eliminating squishing while preserving 100% flush left and right margin alignment.

## [v3.0.1] - 2026-07-26

### Standardized Article Detail Page Typography, Bottom-Right Author Credit & Multi-Topic Feed

* **Pure Article Typography (No Colored Callout Boxes)**: Removed artificial colored background callout boxes (`.article-lead-box`) to provide a clean, natural reading flow that is 100% compatible with non-developer WYSIWYG editor input.
* **Bottom-Right Author Credit**: Moved author signature ("Theo Bác sĩ Chuyên khoa Medicare Cờ Đỏ") to the **BOTTOM RIGHT** at the end of the article content (`text-align: right; font-weight: 700; color: #0f172a;`).
* **5 Related Articles in Sidebar**: Updated sidebar widget in [ArticleController.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/Http/Controllers/ArticleController.php) to query and render exactly 5 articles of the same topic category (`take(5)`).
* **Multi-Topic Suggested Articles Feed & Pagination**: Added a multi-topic recommended articles section ("Tin Mới và Đề Xuất Đa Chủ Đề") below the article with centered pill-shaped pagination links (`$suggestedArticles->links()`), allowing readers to navigate through more news directly from the detail page.
* **Strict Justified Typography (`text-align: justify`)**: Enforced `text-align: justify;` across all body paragraphs, headlines, summaries, and sidebar CTA widgets, eliminating `text-align: center`.
* **Updated System Rules ([AGENTS.md](file:///d:/Projects/tiemchung/.agents/AGENTS.md))**: Recorded mandatory Section 10 rules for justified text, bottom-right author placement, and multi-topic suggested article feeds for permanent project-wide compliance.

## [v3.0.0] - 2026-07-26

### Completed Commercial Production Article Detail Page & Layout Perfecting

* **Commercial Production Article Detail Page 3 (`articles/show.blade.php`)**: Built a complete 2-column layout (70% main content / 30% sidebar) featuring standardized breadcrumbs (`Trang chủ › Tin tức › [Title]`), Medicare Gold category badges, justified typography, rich body styling, doctor advisory warning box, related articles list, and hotline CTA booking widget.
* **100% Zero Article Duplication**: Enforced `whereNotIn('id', $hotNewsIds)` filter in [ArticleController.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/Http/Controllers/ArticleController.php) across all pages, ensuring every article card displayed on the page is 100% unique.
* **100% Flush Vertical Edge Alignment**: Measured and aligned `.catalog-hero`, `.news-nav-bar-container`, `.news-hero-section`, and `.news-horizontal-card` flush to exact bounding rect coordinates (`380px` left, `1540px` right).
* **100% Identical Shared Search Bar Component**: Unified `.search-bar-container.catalog-search-box` across `/vaccines` and `/news` pages with identical 50px pill radius, 6px padding, and red Medicare CTA button.
* **Justified Typography Alignment**: Applied `text-align: justify` across all article headlines (`.hero-main-title`, `.hero-sub-title`, `.hot-news-title`, `.news-horizontal-heading`) and summary excerpts (`.hero-main-excerpt`, `.news-horizontal-snippet`, `.catalog-hero p`).
* **Synchronized Metadata Icons & Removed Ranking Numbers**: Standardized all date metadata icons to calendar (📅 `calendar`) across all cards and removed ranking numbers (`01`, `02`, etc.) from the Hot News section.
* **Pure HTML5 + AOS Animation Architecture**: Adopted lightweight pure HTML5 + CSS + AOS animation attributes (`data-aos="fade-up"`), matching the clean architecture of the About page (`/about`).


## [v2.9.9] - 2026-07-26

### Redesigned News Catalog Page, Typography Cloud Syringe Hero & Balanced Báo Mới Layout

* **Removed Obsolete Background Circle Overlay**: Completely removed `.catalog-hero::after` pseudo-element background circle overlay, establishing a distinct visual identity for the news section.
* **Separated Breadcrumb & Eyebrow Badge**: Fixed breadcrumb inline layout in [index.blade.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/resources/views/articles/index.blade.php), placing `Y HỌC VÀ TIÊM CHỦNG CHÍNH THỐNG` on a standalone line above H1 and keeping breadcrumb navigation clean.
* **Centered Search Bar & Typography Syringe Visual**: Integrated a centered search input inside the hero banner alongside a Typography Cloud of 8 categories overlaying an inline Medical Syringe & Shield SVG graphic.
* **Fixed Featured Image Crops & Fallbacks**: Configured 16:9 ratio with `object-fit: cover` and `object-position: center` across all article cards, replacing cropped logo placeholders with sharp medical images (`vaxigrip.jpg`, `hexaxim.jpg`).
* **Optimized Golden 73%/27% Hero Grid**: Balanced dominant left featured story (73% width) with a compact right hot news stream column (27% width, 300px max) featuring ranking numbers (`01`, `02`, `03`, `04`, `05`).
* **Fixed Article Pagination Navigation**: Renamed news pagination wrapper class to `news-pagination` in [index.blade.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/resources/views/articles/index.blade.php) and [articles.css](file:///d:/Projects/tiemchung/public/css/articles.css), decoupling it from the product catalog SPA click handler in `app.js` and enabling 100% smooth page switching navigation.
* **Medicare Gold Accent Category Badges**: Updated `.news-category-badge` styling to use Medicare Gold (`#fff9e6` background, `#d49800` text, `1px solid rgba(234, 170, 0, 0.4)` border) per visual emphasis guidelines.
* **Justified 8-Category Nav Bar**: Added `Góc Chuyên Gia` category in [ArticleSeeder.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/database/seeders/ArticleSeeder.php) and updated `.news-nav-tabs` to justify all 8 categories across 100% of the container width evenly.
* **Implemented Horizontal Rectangular Article Feed**: Replaced vertical card grids with a modern Báo Mới style horizontal card layout featuring 320px × 190px thumbnail images on the left and 18px bold headlines, category tags, summary excerpts, and metadata on the right.
* **Auto-Hiding Hero Banner & Top Alignment**: Configured top Báo Mới Hero banner to render on default view (`/news`) and automatically hide when search (`?search=...`) or category filter (`?category=...`) is active, immediately shifting the search box, nav tabs, and results feed up to the top below breadcrumbs.
* **Removed Obsolete Reset Filter Button**: Completely removed the "Xóa bộ lọc" button from the search bar form for a clean, modern aesthetic.
* **Centered Pill-Shaped Pagination**: Redesigned Laravel pagination controls with centered rounded pill-shaped buttons (`border-radius: 20px`) and Medicare Red active page highlights.
* **Seeded 100 Realistic Medical Articles**: Created and executed [ArticleSeeder.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/database/seeders/ArticleSeeder.php) populating 100 realistic medical articles inspired by VNVC topic structures across 6 primary medical categories (`Bệnh Truyền Nhiễm`, `Khuyến Cáo Y Tế`, `Tin Nóng Y Học`, `Vắc Xin Mới`, `Chăm Sóc Trẻ Em`, `Tiêm Chủng Người Lớn`), generating a dynamic 9-page Laravel pagination (`Showing 1 to 12 of 100 results`).
* **Strict Ampersand (`&`) Character Restriction**: Replaced all occurrences of `&` across article titles, categories, and navigation tabs with the Vietnamese word "và" per user rules.



### Added Disease Details, Empty Cart Options & Admin Weekly Schedule Planner

* **Admin Sidebar Styling Corrections**: Removed the white brightness/invert filter on the admin sidebar logo to restore the original colored Medicare logo design. Fixed the active styling class logic of the "Chỉnh Sửa Trực Quan (Live)" menu item so its background highlights and red border indicator only render when on the active route, preventing dual active state displays.
* **Action Confirmation Dialogs**: Implemented security confirmation prompts (`confirm()`) before executing any database write operations across registration and admin modules. Added prompts on client-side registration submission, disease group consultation submit, empty cart modal consult request, and admin status updates.
* **Brand Color Palette Correction**: Replaced all incorrect teal/cyan accent colors on the vaccine catalog page with the correct Medicare Red (#c8102e) theme color. This covers the sidebar filter checkboxes, active sort-pills ("Được quan tâm", "Giá thấp", "Giá cao"), and the default/hover states of the "+ Chọn vắc xin" buttons to ensure brand identity compliance.
* **Online/Offline Toggle Switch**: Redesigned consultation type selector inside [disease.blade.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/resources/views/disease.blade.php) and [app.js](file:///c:/Users/Admin/Desktop/tiemchung/public/js/app.js) to show a modern, custom 2-sided pill switch (Online via phone / Offline at center) with background color active transitions. Offline choice opens the center selection field while Online hides it.
* **Admin Back Button Enhancements**: Transformed the back link inside [show.blade.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/resources/views/admin/registrations/show.blade.php) to a prominent, bordered action button. Added active hover states changing background to Medicare Red and text to white, paired with a smooth leftward transition on the arrow icon.
* **Pagination Styling Fix**: Redesigned Laravel's default Tailwind pagination component within [index.blade.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/resources/views/index.blade.php) using specialized CSS spacing overrides. Separated pagination button nodes with a 6px gap, applied independent 8px border-radius values, and color-matched the active state background with Medicare Red to eliminate button border overlapping issues.
* **Collapsible Sidebar Filters**: Applied similar collapsible logic on sidebar filter lists (Age groups, Disease prevention, and Country of origin) to show a maximum of 5 options by default, with a toggle link (Xem thêm / Thu gọn) to expand/collapse the full filter option lists.
* **Collapsible Disease Categories & SVG Toggle Fix**: Grouped product categories inside [index.blade.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/resources/views/index.blade.php) to render 2 rows (8 items) by default, showing a modern toggle button (Xem thêm danh mục / Thu gọn). Rewrote JavaScript to replace innerHTML of the button to correctly handle Lucide SVG icon changes upon collapse/expand states (chevron-up for Thu gọn and chevron-down for Xem thêm).
* **Added Disease Details Page**: Built a detailed group disease view [disease.blade.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/resources/views/disease.blade.php) containing medical description of common pathogens (HPV, Flu, Chickenpox, Diphtheria-Pertussis-Tetanus, Pneumococcal) and a side-by-side AJAX consultant form that registers consultation tickets (`MCD-CS-...`) inside `registrations` with 0 price and default configurations.
* **Refactored Category Button Redirects**: Converted category button nodes inside [index.blade.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/resources/views/index.blade.php) to standard semantic links (`<a>`) routing straight to the disease detail template.
* **Enhanced Empty Cart Stepper Actions**: Refactored `openSpaRegisterModal` within [app.js](file:///c:/Users/Admin/Desktop/tiemchung/public/js/app.js) to display dual action pathways (Browse Vaccine Packages / Ask for Professional Consultation) when the client attempts to check out an empty cart. Built direct inline consultation rendering on the Modal drawer.
* **Implemented Admin Weekly Schedule Planner**: Created a weekly planner layout [schedule.blade.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/resources/views/admin/schedule.blade.php) in Admin dashboard mapping daily appointments (Monday to Sunday) using Carbon helper groups. Added relative navigation links (Previous Week, Current Week, Next Week).
* **Extended Consultation Status Flags**: Added `Chờ tư vấn` and `Đã tư vấn` state tags inside [AdminRegistrationController.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/Http/Controllers/Admin/AdminRegistrationController.php) validation filters and colored badge containers within admin views.

## [v2.9.7] - 2026-07-26

### Removed Doctor Fallback Data & Dynamic Confirm Prompts

* **Removed Hardcoded Fallback Team Data**: Removed the static array fallback inside [about.blade.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/resources/views/about.blade.php) and defaulted [SettingSeeder.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/Database/Seeders/SettingSeeder.php) to an empty array (`[]`) to ensure doctor list is 100% dynamic from CSDL.
* **Added Prompt Confirmations**: Integrated custom `confirm` dialogs in [live_editor.blade.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/resources/views/admin/live_editor.blade.php) for doctor actions: confirming before adding a new doctor (`addTeamItem`), confirming on change before modifying a field (`confirmUpdateTeamMemberField` using `onchange` event), and confirming before deleting a doctor (`deleteTeamItem` with unlimited minimum threshold).

## [v2.9.6] - 2026-07-26

### Enlarged Delete Buttons, Flexbox Layout Auto-Aligning & Optimized Confirm Flow

* **Enlarged Doctor Delete Button**: Upgraded the doctor delete button inside [live_editor.blade.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/resources/views/admin/live_editor.blade.php) (width/height: 28px, font-size: 18px, Medicare Red background, white border, soft shadow, hover scale 1.15) making it prominent and easy to click.
* **Auto-aligning Flexbox Layout**: Refactored `.team-grid` and `.team-card` in [style.css](file:///c:/Users/Admin/Desktop/tiemchung/public/css/style.css#L3567) using Flexbox wrapping (`display: flex; justify-content: center; flex-wrap: wrap;`) to ensure the cards dynamically center and balance themselves perfectly on screen based on the actual doctor count (1, 2, 3, 5, etc.) on all viewports.
* **Optimized Confirm Flow**: Refactored the interactive customizer JS functions (`addTeamItem`, `deleteTeamItem`, and `saveSettingsSubmit`) so clicking the "+ Add Doctor" button immediately appends an empty input form without interruption, while clicking the delete "x" button prompts a confirmation message, and clicking "Save Settings" triggers a safe final confirm dialog before posting settings modifications.
* **Cleared Obsolete Caches**: Ran artisan command sets to clear Compiled View, Config, Route, and Application Caches to guarantee the client's browser downloads the clean syntax and updated JS functions immediately.

## [v2.9.5] - 2026-07-26

### Overhauled About Us Page & Dynamic CSDL Integration

* **Implemented Dynamic About Us View**: Overhauled [about.blade.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/resources/views/about.blade.php) using a modern, professional responsive grid design featuring a Hero Banner, Our Story, Mission & Vision, Core Values (6 columns), and Team Members sections.
* **Integrated Dynamic CSDL Settings**: Modified [HomeController.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/Http/Controllers/HomeController.php) to pull website settings dynamically and pass them to the about page view.
* **Added 100% Dynamic & Unlimited Team Administration**: Seeded dynamic JSON-structured doctor profiles (`about_team_members`) via [SettingSeeder.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/Database/Seeders/SettingSeeder.php). Implemented a **Visual Dynamic List Builder** in [live_editor.blade.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/resources/views/admin/live_editor.blade.php) that enables admin users to visually add, delete, and modify doctor profiles (name, role, avatar, Zalo) dynamically, breaking the limit of 4 members.
* **Seeded About Page Defaults**: Updated [SettingSeeder.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/Database/Seeders/SettingSeeder.php) with new settings keys, seeding default texts for Hero, Story, Statistics, Mission, Vision, and the team list JSON.
* **Styled with System CSS Variables**: Structured the about page CSS within `@section('styles')` utilizing existing design system variables (`--primary-color`, `--accent-color`, `--bg-card`, `--text-primary`, `--border-color`) to guarantee seamless integration and automatic light/dark mode support.
* **Integrated AOS Animations**: Tapped into the project's existing AOS (Animate On Scroll) library by adding `data-aos` markup properties to achieve smooth fade-in scrolling transitions without bloating the CSS footprint.

## [v2.9.4] - 2026-07-26

### Complete Admin Interface Redesign & CSS Style Consolidation

* **Consolidated Admin UI Styles**: Integrated a unified set of modern UI classes (`.card-modern`, `.stat-card-modern`, `.table-modern`, `.form-control-modern`, `.btn-modern-*`, `.badge-modern`) into the main layout to replace highly redundant inline CSS across all admin templates.
* **Redesigned Admin Dashboard View**: Completely overhauled [dashboard.blade.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/resources/views/admin/dashboard.blade.php) using modern glassmorphic widgets, structured quick action layout, and responsive recent registrations table without inline styling.
* **Overhauled Vaccines Management views**: Redesigned vaccines list [index.blade.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/resources/views/admin/vaccines/index.blade.php), creation form [create.blade.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/resources/views/admin/vaccines/create.blade.php), update form [edit.blade.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/resources/views/admin/vaccines/edit.blade.php), and [_form.blade.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/resources/views/admin/vaccines/_form.blade.php) using the unified modern forms and tables design.
* **Redesigned Website Settings View**: Upgraded the settings dashboard [settings/index.blade.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/resources/views/admin/settings/index.blade.php) using modern layout wrappers and styling classes.
* **Redesigned Registrations Views**: Refactored registrations list [registrations/index.blade.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/resources/views/admin/registrations/index.blade.php) and registration details [registrations/show.blade.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/resources/views/admin/registrations/show.blade.php) to incorporate unified table models, status badges, and action buttons.

## [v2.9.3] - 2026-07-26

### Admin Interface Color Alignment & Emoji Removal

* **Refined Admin Sidebar Colors**: Changed the admin sidebar background from generic dark gray (`#1e2229`) to brand Medicare Navy sẫm (`#0a192f`). Aligned menu hover/active states to a milder navy (`#132742`) with a Medicare Red (`#c8102e`) left border.
* **Removed Emojis from Admin Dashboard**: Removed emojis (e.g. ⭐, 🖼️) from quick action buttons on the dashboard and replaced them with clean Lucide icons (`star` and `image`).
* **Cleaned Vaccines Management List**: Replaced the star emoji `⭐` on featured items with a professional gold-tinted badge (`#fef3c7`/`#d97706`). Replaced emojis on the toggle buttons with Lucide `star` and `star-off` icons.
* **Eliminated Emojis in Forms & Modals**: Removed checkmark, warning, cross, target, shield and puzzle emojis (`✅`, `⚠️`, `❌`, `🎯`, `🛡️`, `🧩`) from the stock status select dropdown, featured checkbox, and live customizer preview headings and dropdown options.

## [v2.9.2] - 2026-07-26

### Installed AI Agent Skills

* **Added frontend-design skill**: Installed the `frontend-design` skill from `https://github.com/anthropics/skills` into the workspace customization directory `.agents/skills/frontend-design`.

## [v2.9.1] - 2026-07-25

### Unified Hero Banner Layout, CSS Scroll Snapping & Section Dividers

* **Aligned Hero Banner Design**: Replaced the default hero slider layout on `nmkiet` branch with the premium gold-accented style from `hongphuoc` branch, including the signature yellow action buttons (`bg-yellow-400 text-[#6d0515]`) and standard Flowbite carousel navigation controls (previous/next buttons).
* **Implemented CSS Scroll Snapping**: Added scroll snapping behaviors (`scroll-snap-type: y proximity` and `scroll-snap-align: start`) on homepage containers and section wrappers, with custom `scroll-padding-top` to prevent overlapping with the sticky app header.
* **Added Premium Section Separation (VNVC Style)**: Replaced physical line dividers with elegant alternating backgrounds (clean white `#ffffff` and premium medical light blue `#f4f8fa`). Implemented Zebra Contrast Rules (light blue cards on white sections, white cards with soft shadows on light blue sections) to completely prevent card elements from merging with section backgrounds.
* **Aligned Topbar Design (VNVC Light Style)**: Refined the topbar to feature a clean premium medical light blue background (`#f4f8fa`) with navy text (`#004b8f`) and red accent map-pin icons. Upgraded the quick action badge to a solid Medicare Red button (`#c8102e`) with hover transitions, delivering a bright, medical-grade, and brand-consistent header area.
* **Upgraded Floating Contact Widgets**: Redesigned Zalo and Hotline bubbles in [app.blade.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/resources/views/layouts/app.blade.php) as compact, tidy circles (`floating-btn-expandable`) by default. Integrated clean CSS hover transitions that slide out details ("Chat Zalo Bác Sĩ" and phone number) on mouse enter, delivering interactive and elegant float buttons.

## [v2.9.0] - 2026-07-24

### Dynamic 2x2 Featured Vaccines Grid, Hover Transitions & Button Loading Feedback

* **Built Dynamic 2x2 Featured Grid**: Replaced the hardcoded Qdenga promo banner (`qdenga_promo.blade.php`) with a dynamic 2x2 grid displaying 4 featured single vaccines selected via admin toggles, styled as detailed horizontal cards with key information (disease prevention, target, origin, price/sale price, CTA). Added automatic fallback selection if fewer than 4 vaccines are featured.
* **Standardized Admin Layout Editor Labels**: Cleaned up the layout registry display names in [AdminLiveEditorController.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/Http/Controllers/Admin/AdminLiveEditorController.php): renamed `qdenga_promo` to "Vắc-xin nổi bật" and `featured_vaccines` to "Danh mục vắc-xin".
* **Removed Parentheses & Cleaned Labels**: Removed all parenthetical comments from buttons and dropdown selections inside the visual editor layout page (e.g., changed `Khôi Phục (Reset)` to `Khôi Phục`, `Áp Dụng (Xuất bản)` to `Áp Dụng`, `🟢 Hiện (Ghim)` to `🟢 Hiện`, and simplified padding/background option labels).
* **Added Hover & Active Button Styles**: Defined custom classes (`.editor-btn-primary`, `.editor-btn-secondary`, `.editor-btn-secondary-outline`) in [live_editor.blade.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/resources/views/admin/live_editor.blade.php) with CSS hover transitions, dark-red brand states, shadow modifications, and button translation transforms (`transform: translateY(-1px)`).
* **Implemented Spinning Loader & Disabled States**: Added dynamic JavaScript loading feedback on layout action buttons (Disables button, sets opacity, and replaces content with spinning loader icon and loading text like "Đang áp dụng..." or "Đang khôi phục..."). Shows a checkmark icon with success text before reloading the page.

## [v2.8.0] - 2026-07-24

### Dynamic Homepage Layout Customizer, Section Sorting & Safe Preview Simulator

* **Refactored Homepage Content Structure**: Extracted all 11 hardcoded sections of [home.blade.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/resources/views/home.blade.php) into separate, modular blade partial views under [views/partials/home/](file:///d:/Projects/tiemchung/modules/VaccineRegistration/resources/views/partials/home/).
* **Implemented Dynamic Layout Loader**: Rebuilt [home.blade.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/resources/views/home.blade.php) to automatically load, order, and toggle the visibility of the homepage sections based on configuration stored in the database.
* **Added Extensible Section Registry**: Integrated a central registry `$defaultSections` in [AdminLiveEditorController.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/Http/Controllers/Admin/AdminLiveEditorController.php) to map and manage sections. Added automatic initialization to auto-merge new custom sections in future updates.
* **Added Dynamic Background & Padding Controls**: Created wrapper classes in [style.css](file:///d:/Projects/tiemchung/public/css/style.css) (`.section-style-white`, `.section-style-red`, `.section-style-dark`) that enforce strict brand color compliance and contrast. Made spacing configurable using Tailwind padding options (`py-6`, `py-12`, `py-20`).
* **Designed Safe Preview Simulator (Draft Mode)**: Created a draft-based editing system storing layout modifications in a temporary configuration key `homepage_layout_config_draft`. Implemented a secure preview mode accessible via `/?preview=1` only for logged-in administrators, showing a yellow simulator notice bar.
* **Implemented Publishing Controls**: Added a direct "Xuất Bản Chính Thức" capability inside the Live Editor to deploy the draft homepage layout configuration to all public visitors in one click.
* **Enforced Emojis & Icon Constraints**: Integrated a new restriction rule under Section 9 in [AGENTS.md](file:///d:/Projects/tiemchung/.agents/AGENTS.md) to strictly regulate the addition of emojis and custom icons without permission.
* **Cleaned Up Live Editor Toolbar**: Completely removed all Lucide icons and emojis from navigation tab items and header titles in [live_editor.blade.php](file:///d:/Projects/tiemchung/modules/VaccineRegistration/resources/views/admin/live_editor.blade.php) for a clean, brand-consistent design.

## [v2.7.5] - 2026-07-24

### Brand Contrast & Theme Colors Alignment (Red-White-Yellow Style Compliance)

* **Aligned Homepage Elements to Theme Palette**: Replaced all unapproved navy blue (`#004b8f`) classes on text titles and background badges with Medicare Red (`#c8102e`), slate dark tones (`text-slate-800` / `text-slate-600`), and gold/yellow gradients to enforce the target Red-White-Yellow color hierarchy on `home.blade.php`.
* **Standardized Contrast on Red Gradients**: Fixed contrast issue on the Qdenga banner heading by adding the `text-white` class explicitly, ensuring optimal readability on red background gradients.
* **Refactored Footer Secondary Buttons & Badges**: Updated `.footer-branch-btn-secondary` inside `style.css` to feature a gold border, transparent background, and gold background on hover. Replaced the Branch 2 (Thoi Lai) badge inline styles inside `app.blade.php` to use a gold/yellow color scheme instead of blue.
* **Aligned Testimonials Section to Brand Theme**: Rebuilt the Testimonials section background with a vibrant Medicare Red gradient (`bg-gradient-to-br from-red-900 via-[#c8102e] to-red-800`) and unified all client initials avatars to a consistent style (white background with red text: `bg-white text-[#c8102e]`), completely removing yellow background fills on avatars.
* **Removed Yellow Background Fills**: Converted main hero banner action buttons from yellow background fill (`bg-yellow-400`) to white background with red text (`bg-white text-[#c8102e] hover:bg-red-50`). Converted the featured news category pill from yellow background (`bg-[#eaaa00]`) to red background with white text (`bg-[#c8102e] text-white`).
* **Corrected Header Top Bar Branch Icon**: Updated the Branch 2 top bar map-pin icon style to use `var(--secondary-color)` (Medicare Gold) for visual consistency with Branch 1.
* **Separated Guidelines Documentation**: Extracted and refined the custom color rules to a dedicated `COLOR_RULE.md` file under the `.agents/` folder.
* **Integrated TinyMCE 6 for Articles**: Integrated a feature-dense TinyMCE 6 editor with support for colors, tables, custom alignments, HTML source editing, preview, and fullscreen views into article creation and edit forms. Created the missing `edit.blade.php` view for articles. Updated the frontend article detail view `show.blade.php` to render content raw to support HTML WYSIWYG tags.

## [v2.7.4] - 2026-07-24

### Homepage Display Recovery, Lucide & AOS JS Fallbacks, Banner Image Path Fix

* **Fixed Homepage Blank Display (AOS / Lucide JS Crash)**: Wrapped the `lucide.createIcons()` and `AOS.init()` function calls inside safe `typeof` checks and `try...catch` blocks in `app.blade.php` and `app.js`. Added a global fallback object for `window.lucide` to prevent JavaScript ReferenceErrors from halting script execution when the CDN is slow or offline, restoring full homepage section visibility.
* **Resolved Banner Height Collapse**: Defined a custom class `.hero-carousel-wrapper` with media-query-based heights (`460px`, `500px`, `520px`) inside `style.css` and added it to `home.blade.php`. This bypasses any Tailwind JIT compilation omissions and guarantees the Flowbite Carousel always maintains its exact container height.
* **Fixed Blade View Banner Image Attribute**: Changed `$banner->image_path` to `$banner->image_url` on line 82 in `home.blade.php` to match the schema defined in the database migration and Model.
* **Updated Seed Data Image Paths**: Updated `BannerSeeder.php` to reference actual, existing images inside `public/images/banners/` (`banner_family.jpg`, `banner1.jpg`, `banner2.jpg`) and re-seeded the database to eliminate 404 image load errors.

## [v2.7.3] - 2026-07-24

### Homepage Hero Banner Height, Gradient Tone & Image Fit Refinement

* **Fixed Carousel Wrapper Height Collapse**: Replaced `min-h-` with explicit fixed height classes (`h-[460px] sm:h-[500px] lg:h-[520px]`) and `h-full w-full` on inner slides in `home.blade.php`. Prevents Flowbite Carousel absolute positioning from collapsing wrapper container height to 0.
* **Expanded Banner Vertical Space**: Set banner height to `520px` and optimized padding (`py-6`), completely resolving bottom text clipping and overlap with slider indicators.
* **Proportional Right Image Frame**: Set right image frame height to `lg:h-[320px]` with `object-cover object-center` scaling and `border-2 border-white/30`, ensuring perfect visual balance on wide desktop viewports (`1920px`).
* **Vibrant Medicare Red Gradient Background**: Updated homepage hero banner gradient from gloomy dark slate/red to vibrant Medicare Red (`#c8102e`) transitioning smoothly into deep crimson (`#a00d24` to `#6d0515`), maintaining high color richness and warmth without being dark or dull. Added ambient background light glow accents.
* **Flawless Image Frame Fitting**: Removed inner frame padding (`p-1.5`) and switched image scaling from `object-contain` to `object-cover object-center` within a `rounded-2xl overflow-hidden border-2 border-white/30` frame, ensuring banner images fill 100% of their parent container perfectly without gaps or overflowing.

## [v2.7.2] - 2026-07-23

### VNVC-Style Footer Redesign & Branch Contact Address Integration

* **Integrated Footer Contact Section**: Merged the branch contact network and company details into the global footer across all pages (`layouts/app.blade.php`), styled after the VNVC layout template (Image 2).
* **Medicare Navy Theme Footer**: Implemented multi-tiered footer in Medicare Navy (`#00386c`) featuring a top header bar with logo, tagline, and quick location/hotline/opening-hour buttons.
* **Branch Network Grid**: Rendered prominent branch cards for **Chi Nhánh 1 (Cờ Đỏ)** and **Chi Nhánh 2 (Thới Lai)** complete with full physical addresses, phone numbers, operating hours, and map direction / appointment booking buttons.
* **Legal Company Details & Policy Bar**: Added top policy links (*Chính sách bảo mật, Khảo sát tiêm chủng, Chính sách thanh toán, Điều khoản sử dụng*) alongside official company legal information, registration license number, and copyright details.
* **Electronic Vaccination Book QR Code Card**: Added right-column QR Code container with SVG QR graphic for electronic vaccination book lookup and online booking.
* **Enhanced Typography Legibility**: Increased footer font sizes (`0.95rem`–`1.3rem`), line heights (`1.7`–`1.8`), and text contrast (`#e2e8f0` / `#cbd5e1`) for effortless reading by adults without being overly large.
* **Cleaned Up Home View**: Removed redundant standalone section 12 from `home.blade.php` to streamline homepage navigation.



### Medical News & Blog Seed Data Expansion & UI Layout Fixes

* **Rich Article Seed Data & Real Image Mapping**: Updated `ArticleSeeder.php` with 8 comprehensive, real-world medical articles covering key categories ("Tin nóng trong ngày", "Bệnh Truyền Nhiễm", "Vắc Xin Mới", "Khuyến Cáo Y Tế", "Chăm Sóc Bé"). Fixed missing image file paths by mapping clean image filenames (`vaxigrip.jpg`, `qdenga.jpg`, `prevenar13.jpg`, `hexaxim.jpg`, `shingrix.jpg`, `gardasil9.jpg`, `priorix.png`, `varilrix.jpg`).
* **Vaccine Catalog Centered Title & Padded Section Box**: Updated **"DANH MỤC VẮC XIN TẠI MEDICARE"** in `home.blade.php` to a large, bold, centered title formatted in Medicare Red (`#c8102e`), and wrapped all vaccine product cards inside a styled white section box (`bg-white p-8 md:p-12 rounded-2xl border border-red-100 shadow-sm`) with 2-side padding aligned with other contained sections.

## [v2.7.0] - 2026-07-23

### Homepage Comprehensive Redesign & UX Improvements

* **Mobile Hamburger Menu**: Added slide-out drawer menu for mobile/tablet with icon-based nav links, CTA button, and hotline. Hides topbar and desktop nav on small screens.
* **Trust Indicators / Counter Section**: New animated counter section (40+ vaccines, 10,000+ customers, 2 branches, 100% authentic) with IntersectionObserver scroll-triggered animation.
* **Testimonials Section**: New customer review section with 3 glassmorphism cards on dark background, star ratings, and avatar initials.
* **FAQ Accordion**: New collapsible FAQ section with 5 common vaccination questions and smooth toggle animation.
* **Diversified Section Layouts**: Recommendation cards now use horizontal layout (image left, text right). Process steps now use vertical timeline with connector line. Services section uses 2x2 grid with large icons.
* **AOS Scroll Animation**: Integrated AOS library for fade-up animations on all homepage sections with staggered delays.
* **Typography Upgrade**: Added Inter font for all headings, increased section heading sizes to `text-4xl`, improved body text legibility.
* **Footer Expansion**: Redesigned footer from 2-column to 4-column layout (About, Services, Support, Contact) with social icons, operating hours, and proper bottom bar.
* **Container Alignment & Whitespace Optimization**: Fixed excessive left/right whitespace on wide monitors by standardizing topbar, header, footer, and home sections to `max-w-7xl` (1280px). Converted Branch Network into a 2-column center card grid.
* **VNVC-Style Vaccine Catalog**: Redesigned homepage vaccine catalog section to mirror VNVC layout with uppercase navy title header line, top-right "Xem tất cả" link, 4x2 grid of rounded product cards with light grey image frames, red "MỚI" badges, centered navy titles, parenthesized brand/origin subtitles, and price tags.
* **VNVC-Style News & Medical Articles Layout**: Redesigned news section with category pill filter buttons ("Tin nóng trong ngày", "Bệnh Truyền Nhiễm", "Vắc Xin Mới", etc.), 1 large featured article on the left column, and 3 stacked horizontal mini-cards on the right column with dates and "Xem thêm" links.
* **Hero Banner Framing & Height Optimization**: Fixed image clipping by setting image scaling to `object-contain` and balancing container height to fit 100% within the viewport without cutting off images or peeking lower content awkwardly.
* **Trust Counter Animation Fix**: Added initial default fallback values (`40+`, `10.000+`, `2`, `100%`) and updated IntersectionObserver threshold (`0.05`) with immediate bounding rect check so numbers always animate smoothly on page load and scroll.
* **Vaccine Dynamic View Count Tracking**: Added `views` column to `vaccines` table migration, updated `Vaccine` model and `VaccineController::show()` to increment view count on click, and rendered view badges (`X lượt xem`) with real-time DOM increment on vaccine cards and detail modal.
* **SEO Improvements**: Added `meta description` yield support, `loading="lazy"` on all images, proper heading hierarchy.
* **CSS Cleanup**: Replaced btn-primary-header inline styles with proper CSS class. Added `.footer-container-new`, `.mobile-drawer`, `.trust-counter-card` styles.

## [v2.6.0] - 2026-07-23

### Flowbite Integration & Medicare Red Primary Theme Redesign

* **Flowbite UI Package Installation**: Installed `flowbite` npm package and integrated Flowbite plugin into `tailwind.config.js` and `resources/js/app.js`.
* **Medicare Red (`#c8102e`) Dominant Color Palette**: Consolidated Tailwind primary theme scale to Medicare Red (`#c8102e`), minimizing secondary/accent colors across buttons, icons, indicators, price tags, and hover states.
* **Split Hero Banner Panel Layout**: Converted hero banner panel into a 2-column grid (`lg:grid-cols-12`) layout with dedicated right-side image container frame, ensuring banner images fit 100% into the mold without being cropped or obscured by text overlays.
* **Large Screen Image Responsiveness Optimization**: Fixed banner slider and product card image cropping on high-resolution/wide monitors (`1920px+`) using dynamic aspect ratios (`aspect-[16/10]`, `aspect-[4/3]`) and `object-contain` scaling for vaccine boxes.
* **Homepage Redesign (`home.blade.php`)**: Redesigned all home sections with modern Flowbite UI components (Flowbite Hero Carousel, Quick Action Grid, Tabbed Vaccine Catalog, 5-Step Process Cards, Medical Advice Grid, and Multi-Branch CTA) while maintaining 100% dynamic DB rendering and SPA functionality.
* **Vite Asset Compilation**: Updated `@vite(['resources/css/app.css', 'resources/js/app.js'])` layout integration and compiled production bundle.

## [v2.5.0] - 2026-07-23

### 100% Full Client-Side Single-Page Application (SPA) Upgrade

* **AJAX Vaccine Search & Dynamic Filtering**: Instant search and filtering by age group, disease prevention, and vaccine package tabs without full-page reloads on `/vaccines`.
* **Quick View Vaccine Detail Modal**: Pop-up modal overlay for viewing vaccine specs, dosing schedules, and pricing with 1-click cart toggling.
* **SPA Online Registration Drawer**: Instant multi-step registration modal with client-side form validation, AJAX submission, and dynamic appointment ticket preview (MCD-code generation).
* **Toast Notification System**: Floating toast alerts for real-time user feedback on cart updates and registration events.
* **History API Synchronization**: Sync browser address bar URL seamlessly via `pushState` for shareable links.
* **Strict Brand 3-Color Standardization**: Unified UI theme adhering strictly to Primary Medicare Red (`#c8102e`), Secondary Medicare Gold (`#eaaa00`), and Accent Navy (`#004b8f`) across all client and admin views; updated `.agents/AGENTS.md` rules.

## [v2.4.0] - 2026-07-23

### Full Responsive Admin Portal & Mobile Drawer Shell

* **Vaccine Management Table & Filter Grid Upgrade**: Redesigned `/admin/vaccines` table with unified pill badges, vertical-align centering for zero row offset/jaggedness, modern `#f8fafc` header styling, and responsive 2-column filter grid for mobile devices.
* **Dashboard Quick Action Cards Optimization**: Adaptive vertical column layout with 100% full-width touch buttons on mobile screens (`< 576px`) for "Vaccine Catalog" & "Homepage Banner" cards, preventing text squishing and improving mobile touch ergonomics.
* **Off-Canvas Drawer Navigation**: Added mobile hamburger toggle button and blur backdrop overlay for off-canvas drawer navigation on screens `< 1024px`.
* **Adaptive Content Layout**: Responsive padding and zeroed margins for content area on tablet and mobile viewports.
* **Fluid Data Tables**: Implemented `.table-responsive` touch-scroll wrapper across all admin management modules (Vaccines, Registrations, Centers, Banners, Articles, Dashboard).
* **Mobile Login Optimization**: Responsive login card padding and adaptive typography for screens `< 480px`.

## [v2.3.0] - 2026-07-21

### Comprehensive Live Page Customizer Across All Sub-Pages

* **Full Sub-Page Editing Frame Support**:
  - **About Page (`/about`)**: Individual edit frames for Hero Banner, Healthcare Mission Statement, and Cold-Chain GSP Storage Facility.
  - **Services Page (`/services`)**: Dedicated edit frames for Child Vaccination, Adult Vaccination, Package Vaccination, and Pre-vaccination Medical Screening.
  - **Contact Page (`/contact`)**: Edit frames for Branch 1 (Medicare Cờ Đỏ), Branch 2 (Medicare Thới Lai), Working Hours, and Emergency Line.
  - **Vaccine Catalog Page (`/vaccines`)**: Live edit frames for Pricing Catalog Hero & Product Filter Headers.
* **100% Dynamic Database Fallbacks**: All sub-page text components dynamically linked to the MySQL `settings` table.

## [v2.2.0] - 2026-07-21

### 100% Full-Section Dynamic Home & All-Page Live Customization

* **Complete 7-Section Live Editor Coverage on Home**: Interactive live edit frames added to ALL 7 home sections (Hero Banner Slider, 4-Item Quick Action Toolbar, Medical Advice & Recommendations, 5-Step Safe Vaccination Process, Clinic Vaccination Services, Medical News & Knowledge, Multi-Branch Infrastructure).
* **100% Dynamic Database Integration**: All section titles, subtitles, and descriptions dynamically populated from the MySQL `settings` table (except the product catalog loaded from `vaccines`).
* **Zero Hardcoded Content**: Complete visual freedom for administrators to adjust any piece of text across all pages directly from the Admin Live Editor.

## [v2.1.0] - 2026-07-21

### Universal Live Page Customizer & Global Shell Consolidation

* **All-Page Support**: Full live editing coverage for Home (`/`), About (`/about`), Services (`/services`), Contact (`/contact`), Vaccines (`/vaccines`), and Medical News (`/news`).
* **Global Shell Consolidation**: Consolidate repetitive layout elements (Header logo, Topbar multi-branches, Footer text, Zalo chat button) into a single "Global Shell" editor to prevent redundancy across pages.
* **Instant Dynamic Settings**: Sync all visual adjustments directly into the MySQL database `settings` table using real-time AJAX.

## [v2.0.0] - 2026-07-21

### Facebook-Style Live Visual Page Customizer

* **Interactive Live Edit Overlay Frames**: Display glowing interactive frame borders around home banners and featured product grids.
* **Smart Facebook-Style Edit Modal**:
  - **Upload Files from PC**: Direct image preview with JavaScript `FileReader` before uploading.
  - **Choose Existing Library Images**: Quick-pick pre-existing banner and vaccine graphics.
  - **Db-Connected Product Dropdown**: Instant select from 40 real-world vaccine products with live auto-fill price, prevention details, and featured toggles.
* **AJAX Single-Page Experience (SPA)**: Real-time save updates back to MySQL database without page reloads.

## [v1.9.0] - 2026-07-21

### Admin Management for Home Banners & Featured Vaccines

* **Home Banner Management**: Admin CRUD interface to customize Hero Slider Banners (titles, subtitles, image URLs, target links, active toggles, and sorting order).
* **Featured Vaccines Toggle**: Add 1-click `toggleFeatured` action and ⭐ status badges directly inside the Admin Vaccine Management view.
* **Admin Dashboard Integration**: Quick action shortcuts on the Admin Dashboard leading directly to Banner Management & Featured Product Management.

## [v1.8.0] - 2026-07-21

### Multi-Branch Infrastructure & Dynamic Topbar Display

* Replace single headquarter reference with multi-branch system configuration.
* Update Topbar to dynamically display active branch locations:
  - **Chi nhánh 1**: Cờ Đỏ (`Hotline: 0938 60 38 39`)
  - **Chi nhánh 2**: Thới Lai (`Hotline: 0932 477 184`)
* Update `SettingSeeder.php` with multi-branch network details.

## [v1.7.0] - 2026-07-21

### Floating Zalo & Hotline Chat Bubble Widget

* Add a fixed bottom-right floating chat widget available across all pages.
* **Chat Zalo Button**: Direct link to Zalo consultation (`https://zalo.me/0938603839`).
* **Hotline Call Button**: Direct click-to-call link for Hotline `0938 60 38 39`.

## [v1.6.2] - 2026-07-21

### 5-Step Process Section CSS Fix

* Convert 5-step vaccination process cards to 100% inline CSS Grid (`grid-template-columns: repeat(auto-fit, minmax(200px, 1fr))`).
* Render 5 balanced horizontal step cards with badges (1 to 5), icons, and step descriptions.

## [v1.6.1] - 2026-07-21

### Quick Action Toolbar CSS Fix

* Convert Quick Action Toolbar to 100% inline CSS Flex/Grid container (`grid-template-columns: repeat(auto-fit, minmax(240px, 1fr))`).
* Resolve item stacking bug, rendering a sleek 4-column white card widget directly beneath the Hero Slider Banner.

## [v1.6.0] - 2026-07-21

### VNVC Layout Alignment & Medical Standard UI Upgrade

* **Quick Utility Action Toolbar**: Integrate direct navigation widgets ("Đặt Mua Vắc Xin Online", "Đăng Ký Tiêm Chủng", "Bảng Giá Vắc Xin Niêm Yết", "Tìm Chi Nhánh Gần Bạn") floating directly below the main Slider Banner.
* **5-Step Medical Safety Process**: Implement the official 5-step vaccination process (Registration & Screening -> Regimen Consultation -> GSP Vaccination -> 30-min Monitoring -> Post-vaccination Examination).
* **Vaccine Catalog & Recommendations**: Re-align recommended medical vaccine sections and real-world vaccine catalog matching official VNVC layout practices.

## [v1.5.0] - 2026-07-21

### Multi-Branch Registration & Contact Integration

* **Registration Form (`/register`)**: Enforce mandatory branch selection (`center_name`) allowing users to choose between **Medicare Cờ Đỏ (Chi nhánh 1)** and **Medicare Thới Lai (Chi nhánh 2)**.
* **Home Page (`/`)**: Streamline contact section with a high-impact branch network banner leading directly to the dedicated Contact page.
* **Contact Page (`/contact`)**: Move 100% of detailed branch information (Address, Hotline, Google Maps, Business Hours) into structured Cards for both branch locations.

## [v1.4.0] - 2026-07-21

### Pure Real-World Database Refresh Completed

* Reset and refresh MySQL database (`migrate:fresh --force`).
* Remove all auto-generated mock/test data (test registrations, mock packages, fake centers).
* Retain strictly real-world data:
  - Exactly 40 real vaccines from official PDF `DANH MỤC VACCIN MEDICARE CỜ ĐỎ (3.6.2026).pdf`.
  - Exactly 2 active real branches: Medicare Cờ Đỏ & Medicare Thới Lai.
  - Clean state ready for real customer registrations.

## [v1.3.1] - 2026-07-21

### Admin Article Layout Extension Bugfix

* Fix view inheritance in `articles/index.blade.php` and `articles/create.blade.php` to target `@extends('vaccine::layouts.admin')` and `@section('admin_content')`.
* Verify 100% clean runtime error log in `storage/logs/laravel.log`.

## [v1.3.0] - 2026-07-21

### Brand Renaming & Multi-Center System Optimization

* Update brand title to **"Medicare"** (`Hệ Thống Tiêm Chủng Medicare`) across system settings and view templates.
* Update `CenterSeeder.php` to officially manage 2 active branch centers:
  1. **Medicare Cờ Đỏ (Chi nhánh 1)**: Cổng Bệnh viện Quân Dân Y TP Cần Thơ, Ấp Thới Bình, Xã Cờ Đỏ, TP. Cần Thơ.
  2. **Medicare Thới Lai (Chi nhánh 2)**: Thị trấn Thới Lai, Huyện Thới Lai, TP. Cần Thơ.
* Synchronize multi-branch contact cards and registration selection choices.

## [v1.2.0] - 2026-07-21

### Multi-Page Dedicated Route & View Architecture Completed

* Split application into dedicated, modular pages:
  - **Home**: `/` (Main Overview & Featured Showcase)
  - **About Us**: `/about` (Mission, GSP Cold Storage, Medical Standards)
  - **Vaccine Catalog & Pricing**: `/vaccines` (Interactive Filter & SPA Cart)
  - **Services**: `/services` (Children, Adults, Packages, Screening)
  - **News & Knowledge**: `/news` & `/news/{slug}` (Medical Articles)
  - **Contact & Map**: `/contact` (Clinic Address & Google Maps)
  - **Registration**: `/register` (Multi-step Appointment Form)
* Update Header navigation links dynamically to reflect current active routes.

## [v1.1.2] - 2026-07-21

### Admin News & Article Management Completed (Requirement 10 Verification)

* Add `AdminArticleController.php`, `admin.articles` routes, and CRUD views (`index.blade.php`, `create.blade.php`).
* Add "Quản lý Bài Viết" link to Admin Sidebar menu, guaranteeing 100% full content management capability across all 10 minimal homepage sections.

## [v1.1.1] - 2026-07-21

### 100% Dynamic MySQL Real-world Data Architecture

* Migrate all news/knowledge articles to dynamic MySQL `articles` table with `ArticleSeeder.php`.
* Eliminate all mock/hardcoded data across all views, ensuring 100% database-driven content for Banners, Vaccines, Articles, and System Settings.

## [v1.1.0] - 2026-07-21

### Commercial Production 10 Minimal Requirements & SPA Navigation Completed

* Implement complete 10 Minimal Homepage Requirements (Header, Banner Slider, About Us, Vaccine Catalog, Services, News/Knowledge, Testimonials, Contact & Google Maps, Footer, Content Management).
* Build seamless Single-Page Navigation (`smoothScrollTo` SPA anchors) across header links without full page reloads.
* Integrate dynamic database management for Banners (`/admin/banners`), Vaccines (`/admin/vaccines`), and System Settings (`/admin/settings`).

## [v1.0.13] - 2026-07-21

### Real-world PDF Vaccine Catalog Integration

* Import complete 40 real-world vaccines and prices from official PDF `DANH MỤC VACCIN MEDICARE CỜ ĐỎ (3.6.2026).pdf` into `VaccineSeeder.php` and MySQL database.
* Synchronize official clinic address (`Cổng Bệnh viện Quân Dân Y TP Cần Thơ, Ấp Thới Bình, Xã Cờ Đỏ, TP Cần Thơ`) and Hotline (`0938 60 38 39`) in `SettingSeeder.php`.

## [v1.0.12] - 2026-07-21

### Image Alias & Windows Environment Compatibility Fix

* Create short-name file aliases (`vaxigrip.jpg`, `prevenar13.jpg`, `shingrix.jpg`, `qdenga.jpg`, `gardasil9.jpg`) matching database records.
* Optimize asset routing to serve directly from `public/images/` to bypass Windows PHP Built-in Web Server Symbolic Link restrictions, ensuring 100% image rendering across all environments.

## [v1.0.11] - 2026-07-21

### Storage Architecture Optimization (Method 1)

* Migrate asset storage architecture to Laravel Standard Method 1 (`storage/app/public/`).
* Execute `php artisan storage:link` to create a public symbolic link (`public/storage` -> `storage/app/public`).
* Update image references across all views (`home.blade.php`, `index.blade.php`, `show.blade.php`, `app.blade.php`) to use `asset('storage/...')`, ensuring 100% data safety during deployment updates.

## [v1.0.10] - 2026-07-21

### Database Seeding & Clean Up

* Clean up obsolete SQLite database file (`database.sqlite`) and unneeded logo script files from the `database/` directory.
* Run advanced database migrations and populate seed data (`VaccineSeeder`, `CenterSeeder`, `SettingSeeder`, `BannerSeeder`) for full system simulation.

## [v1.0.9] - 2026-07-21

### Commercial Production Standards & Rules Update

* Update `.agents/AGENTS.md` with explicit **Commercial Production Notice**, enforcing strict commercial-grade security, code simplicity, function reusability, database-driven architecture, and zero-defect quality standards for client deployment.

## [v1.0.8] - 2026-07-21

### Commercial Production Standards & Rules Update

* Update `.agents/AGENTS.md` with explicit **Commercial Production Notice**, enforcing strict commercial-grade security, code simplicity, function reusability, database-driven architecture, and zero-defect quality standards for client deployment.

## [v1.0.7] - 2026-07-19

### User Experience Enhancements

* Added custom error pages (403, 404, 419, 429, 500) extending the main application layout to improve user experience during application errors.

## [v1.0.6] - 2026-07-19

### Deployment Credential Handling

* Move FTP credentials from project rules to the Git-ignored `.env` file and add empty FTP variables to `.env.example`.
* Retain the deployment rule that FTP uploads must contain only files changed for the current task.

## [v1.0.5] - 2026-07-19

### Admin Panel Enhancements & Product Management

* Add new fields to vaccines table and model (`sale_price`, `stock_status`, `manufacturer`, `dosage`, `is_featured`, `sort_order`, `category`) to support advanced product tracking.
* Redesign the Vaccine Admin index page with new data columns, filter dropdowns (by type, status, category), and a visual search bar.
* Rebuild the Vaccine Admin create/edit form with a structured layout and inputs for the newly added product fields.
* Implement Homepage Banner Management in the Admin panel (added Banner Controller, views, and sidebar menu).
* Add CSV export functionality for vaccine registration orders.

## [v1.0.4] - 2026-07-19

### MySQL-Only Database Configuration

* Standardize application, cache, queue, session, and test persistence on MySQL; remove SQLite, MariaDB, Redis, Memcached, SQS, Beanstalk, and DynamoDB configuration paths that are not used by the project.
* Remove the SQLite database file and document `medicare_codo` and `medicare_codo_test` as the only supported databases.

## [v1.0.3] - 2026-07-19

### AI Configuration and Workspace Rules Setup & Admin Font Optimization

* Create project-scoped AI rules: Add and refine English instructions in `.agents/AGENTS.md` to define concise workspace-specific instructions for AI agents (focusing on stack versions, changelog/doc maintenance, and communication style).
* Update all customer-facing and admin view templates: Replace inline styling overrides of 'Outfit' font with 'Roboto' font, ensuring font consistency across all layouts.

## [v1.0.2] - 2026-07-19

### Vaccine Registration Project Optimization and Interface Update

* Synchronize font styling with VNVC main site: Update default font to **Roboto** by updating Google Fonts loader in layout files (`app.blade.php`, `admin.blade.php`, `welcome.blade.php`), replacing old font variables in `public/css/style.css`, and updating `tailwind.config.js`.
* Improve local environment database testing: Update table clean & data seeding logic in `SettingSeeder`, `BannerSeeder`, `CenterSeeder`, and `VaccineSeeder` to dynamically support SQLite and MySQL foreign key constraints toggle.
* Version control updates: Append `.DS_Store` and `Thumbs.db` exclusion rules in `.gitignore`.

## [Unreleased](https://github.com/laravel/laravel/compare/v11.6.1...11.x)

## [v11.6.1](https://github.com/laravel/laravel/compare/v11.6.0...v11.6.1) - 2025-01-24

* Update vite dependencies by [@laserhybiz](https://github.com/laserhybiz) in https://github.com/laravel/laravel/pull/6521
* [11.x] Sync `session.lifetime` configuration by [@crynobone](https://github.com/crynobone) in https://github.com/laravel/laravel/pull/6522
* Remove extra hourly() method in console.php by [@scajanus](https://github.com/scajanus) in https://github.com/laravel/laravel/pull/6525

## [v11.6.0](https://github.com/laravel/laravel/compare/v11.5.1...v11.6.0) - 2025-01-21

* Preserve X-Xsrf-Token header from .htaccess by [@thecodeholic](https://github.com/thecodeholic) in https://github.com/laravel/laravel/pull/6520

## [v11.5.1](https://github.com/laravel/laravel/compare/v11.5.0...v11.5.1) - 2025-01-10

* Update .gitignore to not ignore auth.json in the lang directory. by [@Tjoosten](https://github.com/Tjoosten) in https://github.com/laravel/laravel/pull/6515
* Adding PHP 8.4 to the tests matrix by [@mathiasgrimm](https://github.com/mathiasgrimm) in https://github.com/laravel/laravel/pull/6516
* fix css whitespace invalid-calc by [@tvarwig](https://github.com/tvarwig) in https://github.com/laravel/laravel/pull/6517
* [11.x] Fix invalid tailwindcss class by [@Jubeki](https://github.com/Jubeki) in https://github.com/laravel/laravel/pull/6518

## [v11.5.0](https://github.com/laravel/laravel/compare/v11.4.0...v11.5.0) - 2024-12-13

* [11.x] Update `config/mail.php` with supported configuration by [@crynobone](https://github.com/crynobone) in https://github.com/laravel/laravel/pull/6506

## [v11.4.0](https://github.com/laravel/laravel/compare/v11.3.3...v11.4.0) - 2024-12-02

* [11.x] Narrow down array types to lists by [@DvDty](https://github.com/DvDty) in https://github.com/laravel/laravel/pull/6497
* Upgrade to Vite 6 by [@timacdonald](https://github.com/timacdonald) in https://github.com/laravel/laravel/pull/6498

## [v11.3.3](https://github.com/laravel/laravel/compare/v11.3.2...v11.3.3) - 2024-11-18

* Inconsistency in Github action names by [@amirbabaeii](https://github.com/amirbabaeii) in https://github.com/laravel/laravel/pull/6478
* Add schema property to enhance autocompletion for composer.json by [@octoper](https://github.com/octoper) in https://github.com/laravel/laravel/pull/6484
* Update .gitignore by [@EmranMR](https://github.com/EmranMR) in https://github.com/laravel/laravel/pull/6486
* [11.x] Bump framework version by [@PerryvanderMeer](https://github.com/PerryvanderMeer) in https://github.com/laravel/laravel/pull/6490
* [11.x] match `HidesAttributes` docblocks by [@browner12](https://github.com/browner12) in https://github.com/laravel/laravel/pull/6495

## [v11.3.2](https://github.com/laravel/laravel/compare/v11.3.1...v11.3.2) - 2024-10-21

* Fixes pail timing out after an hour by [@nunomaduro](https://github.com/nunomaduro) in https://github.com/laravel/laravel/pull/6473

## [v11.3.1](https://github.com/laravel/laravel/compare/v11.3.0...v11.3.1) - 2024-10-15

**Full Changelog**: https://github.com/laravel/laravel/compare/v11.3.0...v11.3.1

## [v11.3.0](https://github.com/laravel/laravel/compare/v11.2.1...v11.3.0) - 2024-10-14

* Add Tailwind, "composer run dev" by [@taylorotwell](https://github.com/taylorotwell) in https://github.com/laravel/laravel/pull/6463

## [v11.2.1](https://github.com/laravel/laravel/compare/v11.2.0...v11.2.1) - 2024-10-08

* [11.x] Collision Version Upgrade by [@amdad121](https://github.com/amdad121) in https://github.com/laravel/laravel/pull/6454
* [11.x] factory-generics-in-user-model by [@MrPunyapal](https://github.com/MrPunyapal) in https://github.com/laravel/laravel/pull/6453
* Update welcome.blade.php to add missing alt tag by [@mezotv](https://github.com/mezotv) in https://github.com/laravel/laravel/pull/6462

## [v11.2.0](https://github.com/laravel/laravel/compare/v11.1.5...v11.2.0) - 2024-09-11

* Update .gitignore with Zed Editor by [@fahadkhan1740](https://github.com/fahadkhan1740) in https://github.com/laravel/laravel/pull/6449
* Laracon 2024 feature update by [@taylorotwell](https://github.com/taylorotwell) in https://github.com/laravel/laravel/pull/6450

## [v11.1.5](https://github.com/laravel/laravel/compare/v11.1.4...v11.1.5) - 2024-08-14

* Update axios by [@laserhybiz](https://github.com/laserhybiz) in https://github.com/laravel/laravel/pull/6440

## [v11.1.4](https://github.com/laravel/laravel/compare/v11.1.3...v11.1.4) - 2024-07-16

**Full Changelog**: https://github.com/laravel/laravel/compare/v11.1.3...v11.1.4

## [v11.1.3](https://github.com/laravel/laravel/compare/v11.1.2...v11.1.3) - 2024-07-03

* [11.x] Comment maintenance store by [@timacdonald](https://github.com/timacdonald) in https://github.com/laravel/laravel/pull/6429

## [v11.1.2](https://github.com/laravel/laravel/compare/v11.1.1...v11.1.2) - 2024-06-20

* Expose lock table name by [@nhedger](https://github.com/nhedger) in https://github.com/laravel/laravel/pull/6423

## [v11.1.1](https://github.com/laravel/laravel/compare/v11.1.0...v11.1.1) - 2024-06-04

* Format the first letter of `drivers`  to lowercase by [@maru0914](https://github.com/maru0914) in https://github.com/laravel/laravel/pull/6413

## [v11.1.0](https://github.com/laravel/laravel/compare/v11.0.9...v11.1.0) - 2024-05-28

* [11.x] Removes `--dev` dependencies by [@nunomaduro](https://github.com/nunomaduro) in https://github.com/laravel/laravel/pull/6406

## [v11.0.9](https://github.com/laravel/laravel/compare/v11.0.8...v11.0.9) - 2024-05-16

* Updated SMTP mail config to use a valid EHLO domain by [@rcerljenko](https://github.com/rcerljenko) in https://github.com/laravel/laravel/pull/6402

## [v11.0.8](https://github.com/laravel/laravel/compare/v11.0.7...v11.0.8) - 2024-05-13

* Add .phpactor.json to .gitignore by [@princejohnsantillan](https://github.com/princejohnsantillan) in https://github.com/laravel/laravel/pull/6400

## [v11.0.7](https://github.com/laravel/laravel/compare/v11.0.6...v11.0.7) - 2024-05-03

* Remove obsolete driver option by [@u01jmg3](https://github.com/u01jmg3) in https://github.com/laravel/laravel/pull/6395

## [v11.0.6](https://github.com/laravel/laravel/compare/v11.0.5...v11.0.6) - 2024-04-09

* Fix PHPUnit constraint by [@szepeviktor](https://github.com/szepeviktor) in https://github.com/laravel/laravel/pull/6389
* [11.x] Add missing roundrobin transport driver config by [@u01jmg3](https://github.com/u01jmg3) in https://github.com/laravel/laravel/pull/6392

## [v11.0.5](https://github.com/laravel/laravel/compare/v11.0.4...v11.0.5) - 2024-03-26

* [11.x] Use PHPUnit v11 by [@philbates35](https://github.com/philbates35) in https://github.com/laravel/laravel/pull/6385

## [v11.0.4](https://github.com/laravel/laravel/compare/v11.0.3...v11.0.4) - 2024-03-15

* [11.x] Removed useless null parameter for env helper (cache.php) by [@siarheipashkevich](https://github.com/siarheipashkevich) in https://github.com/laravel/laravel/pull/6374
* [11.x] Removed useless null parameter for env helper (queue.php) by [@siarheipashkevich](https://github.com/siarheipashkevich) in https://github.com/laravel/laravel/pull/6373
* [11.x] Fix retry_after to be an integer by [@driesvints](https://github.com/driesvints) in https://github.com/laravel/laravel/pull/6377
* [11.x] Fix on hover animation and ring by [@michaelnabil230](https://github.com/michaelnabil230) in https://github.com/laravel/laravel/pull/6376

## [v11.0.3](https://github.com/laravel/laravel/compare/v11.0.2...v11.0.3) - 2024-03-14

* [11.x] Revert collation change by [@driesvints](https://github.com/driesvints) in https://github.com/laravel/laravel/pull/6372

## [v11.0.2](https://github.com/laravel/laravel/compare/v11.0.1...v11.0.2) - 2024-03-13

* [11.x] Remove branch alias from composer.json by [@zepfietje](https://github.com/zepfietje) in https://github.com/laravel/laravel/pull/6366
* [11.x] Fixes typo in welcome page by [@jrd-lewis](https://github.com/jrd-lewis) in https://github.com/laravel/laravel/pull/6363
* change mariadb default by [@taylorotwell](https://github.com/taylorotwell) in https://github.com/laravel/laravel/commit/79969c99c6456a6d6edfbe78d241575fe1f65594

## [v11.0.1](https://github.com/laravel/laravel/compare/v11.0.0...v11.0.1) - 2024-03-12

* [11.x] Fixes SQLite driver missing by [@nunomaduro](https://github.com/nunomaduro) in https://github.com/laravel/laravel/pull/6361

## [v11.0.0 (2023-02-17)](https://github.com/laravel/laravel/compare/v10.3.2...v11.0.0)

Laravel 11 includes a variety of changes to the application skeleton. Please consult the diff to see what's new.
