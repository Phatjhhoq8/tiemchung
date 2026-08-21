## [v6.5.87] - 2026-08-21

### Replace Consultation Form with Direct Zalo Official Flow in SPA Booking Modal

* **Remove Redundant Input Form**:
  - Completely removed the 2nd tab form fields (Consultation Type selector, Contact Name, Contact Phone, Center Selection, and consultation notes text area) from the "Yêu cầu tư vấn Y khoa" view inside `public/js/app.js::renderSpaConsultForm`.
* **Introduce Premium Zalo Connection Card**:
  - Implemented a clean, high-impact y khoa layout displaying a dynamically generated Zalo QR Code matching the branch's Zalo configuration.
  - Added a prominent Zalo Blue (`#0068ff`) call-to-action button ("Mở Zalo Chat Ngay") leading directly to the branch Zalo URL.
  - Simplified the state management by removing temporary data tracking for consultation fields.

## [v6.5.86] - 2026-08-21

### Standardize Front Age Group Filters & Support Overlapping Medical Age Ranges

* **Clean Up Sidebar Age Filters**:
  - Replaced the dynamcially plucked database age strings with 4 clean, standardized medical age groups in `VaccineController.php::buildAgeGroupOptions`:
    1. **Trẻ em (Dưới 2 tuổi)**
    2. **Trẻ tiền học đường (2 - 6 tuổi)**
    3. **Trẻ học đường (7 - 12 tuổi)**
    4. **Tuổi vị thành niên & Người lớn (Trên 12 tuổi)**
* **Medical Range Logic Query Implementation**:
  - Upgraded the query filter inside `VaccineController.php` from a strict `like` match. When filtering by "Tuổi vị thành niên & Người lớn (Trên 12 tuổi)", it now correctly queries all vaccines whose target age constraints allow them to be taken by adolescents/adults (e.g., vaccines starting from "1 tuổi trở lên", "2 tháng trở lên" are no longer filtered out, while baby-only vaccines like 6-in-1 are excluded).

## [v6.5.85] - 2026-08-21

### Reset and Update All Home Slider Banners

* **Database Banner Cleanup & Seeding**:
  - Truncated the `banners` table in the database.
  - Inserted 3 new high-converting y khoa banners with medical details:
    1. **Lá Chắn Vắc Xin Vững Chắc — Bảo Vệ Bé Yêu Toàn Diện** (Targeting infant/children vaccines).
    2. **Chăm Sóc Sức Khỏe Người Cao Tuổi — Sống Vui Khỏe Mỗi Ngày** (Targeting elderly vaccines).
    3. **Gói Vắc Xin Gia Đình — Chăm Sóc Sức Khỏe Cho Cả Nhà** (Targeting family & pregnancy packages).
* **Banner Asset Management**:
  - Uploaded 3 new square medical image assets directly to the public directory.
  - Formatted layout using Flowbite Carousel inside `hero_slider.blade.php`, positioning text titles on the left column and floating the 3D-shadowed medical images on the right column for optimal desktop/mobile presentation.

## [v6.5.84] - 2026-08-21

### Simplify Layout, Limit Categories to SuperAdmin, and Change Cutoff Time to 18:30

* **Live Editor Publish Button Relocation**:
  - Moved the "Xuất Bản Chính Thức" button directly into the main navigation tabs line (right-aligned next to "Cấu Hình Chung") and deleted the redundant "Bảng Điều Khiển Live Editor" bar, optimizing page vertical space.
* **Hide Categories from Branch Admins**:
  - Configured `layouts/admin.blade.php` to conditionally display the "Quản lý Nhóm Bệnh" sub-navigation item only if the active user is a Super Admin (`$isSuperAdmin === true`).
* **Update Vaccine Preparation Cutoff to 18:30**:
  - Changed the daily vaccine inventory check cutoff time globally from `20:30` to `18:30` in both frontend view displays (`dashboard.blade.php`) and backend control logic (`AdminDashboardController.php`, `AdminVaccineController.php`).

## [v6.5.83] - 2026-08-21

### Refactor Live Editor Labels and Action Controls

* **Clean Up Live Editor View & Modal Labels**:
  - Removed technical key placeholders and developer comments in brackets/parentheses (e.g., `(site_logo)`, `[home_safe_process_title]`, `(FAQ)`) from all input field names, options lists, and JSON schemas inside `live_editor.blade.php`.
  - Configured fallback text to display as a clean `'Nội dung cấu hình'` instead of `'Nội dung cấu hình [key]'`.
* **Simplify Dashboard Controls**:
  - Removed "Khôi Phục Nháp" (Restore Draft) and "Xem Thử (Preview)" buttons from the top action toolbar, keeping only the primary "Xuất Bản Chính Thức" action button to simplify the admin UX.

## [v6.5.82] - 2026-08-21

### Prevent Form Data Loss when Switching Tabs in SPA Booking Modal

* **Form State Preservation Logic**:
  - Implemented `saveSpaRegisterFormData()` and `saveSpaConsultFormData()` functions in `app.js` to serialize and capture input values (including dynamic patient blocks, vaccine selection checkboxes, guardian details, and booking times) before tabs are destroyed.
  - Implemented `restoreSpaRegisterFormData()` and `restoreSpaConsultFormData()` to dynamically recreate additional patient blocks and populate input elements after tab redraws.
  - Reset cached data on fresh modal openings or closings to ensure clean session states.

## [v6.5.81] - 2026-08-21

### Exclude Datepicker Selects from Global Custom Dropdown Processing

* **DatePicker Custom Dropdown Exclusions**:
  - Appended `no-custom-select` and `medicare-gdp-select` exclusion classes to the datepicker Month & Year selects in both `app.js` and `layouts/admin.blade.php`.
  - Added layout guards inside `initGlobalMedicareCustomDropdowns` in `app.js` to immediately return and bypass custom styling scripts for elements containing datepicker classes or wrappers.
  - This prevents conflicts with the client-side dropdown rendering script that previously hid the native select elements and replaced them with empty custom wrappers, completely restoring text and options layout.

## [v6.5.80] - 2026-08-21

### Fix Dropdown Styling of Datepicker Header Selects

* **DatePicker Styling Reset**:
  - Implemented `.medicare-gdp-select` class rules in `style.css` to clean up native select rendering in datepicker header.
  - Reset styles:
    - Disabled default browser arrow indicators (`appearance: none`).
    - Configured thin custom dropdown chevron SVG icon, right-positioned.
    - Set font sizing, bold weight, transparent background, and dark slate text color (`#0f172a`).
  - Added the new class to the selects in `app.js` and `layouts/admin.blade.php` to resolve layout issues.

## [v6.5.79] - 2026-08-21

### Replace Static Datepicker Header with Month and Year Select Dropdowns for Rapid Navigation

* **Interactive Datepicker Month & Year Selects**:
  - Replaced static `.gdp-title` span text in datepicker template with `.gdp-month-select` and `.gdp-year-select` dropdown menus in both `app.js` (client) and `layouts/admin.blade.php` (admin).
  - Wired change event listeners to month/year selects to update calendar view parameters and trigger `renderGdp()` immediately.
  - Dynamically populated select options:
    - Month options range from "Tháng 1" to "Tháng 12".
    - Year options range from `1900` up to current year plus 5 years, ordered descending, allowing rapid birthdate selection.

## [v6.5.78] - 2026-08-21

### Bump CSS Asset Versions for Cache Busting and Instant Browser Styles Updates

* **Cache Busting for Client Styles**:
  - Incremented stylesheet query parameter version inside `layouts/app.blade.php` from `1.0.4` to `1.0.12`.
  - Added query parameter version `?v=1.0.6` to `vaccines.css` link tag inside `show.blade.php`.
  - Bumping these versions bypasses heavy browser and CDN caches, forcing clients to instantly download updated style files containing the latest hover animations.

## [v6.5.77] - 2026-08-21

### Add Facebook Link Setting for Team Members and Toggle Social Icons Dynamically

* **Visual Editor updates**:
  - Appended `{ key: 'facebook', label: 'Link Facebook cá nhân', type: 'text', placeholder: 'Ví dụ: https://facebook.com/username' }` to the `about_team_members` section schema in `live_editor.blade.php`.

* **Client Social Icons Dynamic Toggling**:
  - Wrapped Facebook profile anchor tag inside `about.blade.php` in a dynamic `@if(!empty($member['facebook']))` block, ensuring it only appears if configured.
  - Social phone icon already correctly respects the `@if(!empty($member['zalo']))` wrapper to hide empty details.

* **Category Toggle Button Hover transition**:
  - Added `#btnToggleCategories` (Xem thêm danh mục) to the global transition and hover lift lists in `style.css`, and configured a premium color inversion (red background, white text) on hover.

## [v6.5.76] - 2026-08-21

### Extend Premium Hover Lift Transitions to Admin and Consultation Buttons

* **Consultation Banner Button Hover Style**:
  - Appended hover rules to `style.css` for `.catalog-advice-btn` (Bắt đầu ngay button in advice banners) to trigger a `translateY(-3px)` lift and brand red shadow glow on hover.

* **Admin Modal Action Buttons Hover Style**:
  - Added global hover transitions for `.fb-modal-footer button`, `.btn-primary`, `.btn-secondary`, and `.btn-secondary-outline` in `style.css`.
  - Configured hover lift, background transition, and shadow details for admin edit actions:
    - **Xuất Bản Ngay** (Publish)
    - **Lưu Bản Nháp** (Draft)
    - **Hủy** (Cancel)

## [v6.5.75] - 2026-08-21

### Implement Hover Transition Lift Effects Across All Vaccine Action Buttons

* **Unified Action Button Hover Style**:
  - Appended global hover rules to `vaccines.css` and `disease.blade.php` style block.
  - Implemented the `translateY(-3px)` upward lift, shadow expansion, and brand color transition on hover for all major client action buttons:
    - **Quay lại** (Back button on detail page)
    - **Chọn vắc xin** / **Đã chọn vắc xin** (Selection button on detail page)
    - **Xem chi tiết** (Detail page slider cards)
    - **Chọn tiêm** / **Hủy chọn** (Slider cards select trigger)
    - **Chọn tiêm** / **Đã chọn** (Disease catalog list view buttons)

## [v6.5.74] - 2026-08-21

### Implement Red Branding and Lift Hover Animation for Vaccine Detail Booking Button

* **Booking Button Dynamic Display Toggling**:
  - Restored dynamic hiding logic (`display: none` / `display: inline-flex`) for the "Đặt lịch tiêm ngay →" button inside `show.blade.php` and `app.js` setCartSelection logic.

* **Medicare Red Theme & Hover Lift Animation**:
  - Configured default background color to brand red (`#c8102e`), white text, and shadow properties on the booking button.
  - Implemented high-performance CSS transition and inline JS event listeners (`translateY(-3px)` on hover and `translateY(0)` on blur) for the booking button to deliver a premium layout lift and shadow enlargement effect.

## [v6.5.73] - 2026-08-21

### Prevent Currency wrapping and Align Action Buttons in Vaccine Detail

* **Currency Wrapping Fix**:
  - Added `white-space: nowrap` and `flex-shrink: 0` to the price container div inside `show.blade.php`.
  - Replaced regular spaces with non-breaking spaces (`&nbsp;đ`) on both original and sale price elements, ensuring the currency symbol never drops to a new line.

* **Action Button Alignment**:
  - Set `flex-wrap: nowrap` for `.vaccine-action-buttons` flex container to force all three action items (Quay lại, Chọn vắc xin, Đặt lịch tiêm ngay) to stay in a single horizontal row on desktop.
  - Added `flex-shrink: 0` to all action buttons to ensure text stays fully visible without truncation when container sizes change.

## [v6.5.72] - 2026-08-21

### Fix Layout Shifts on Detail Page and Add Select-Cancel Toggle Support

* **Button Label Normalization**:
  - Replaced transactional shopping terms like "Đặt mua ngay" / "Đã chọn đặt" with medical action terms "Chọn tiêm" / "Đã chọn" in the disease catalog view.
  - Replaced "Đăng ký tiêm chủng" on the vaccine detail view with "Chọn vắc xin".

* **Layout Shift Prevention**:
  - Configured the "Đặt lịch tiêm ngay →" button to stay statically rendered with a `0.55 opacity` and `pointer-events: none` when unselected, instead of dynamically applying `display: none` which shifts detail blocks.

* **Selection Cancellation Fix**:
  - Fixed toggle query inside `app.js` to inspect detail page elements with `.btn-select-detail.btn-selected`, allowing users to toggle select and deselect items from the detail page button.

## [v6.5.71] - 2026-08-21

### Implement Category Filtering and Bulk Deletion Across 5 Modules

* **Categories Filtering & Search UI**:
  - Integrated client-side JS filter on Categories index, matching text queries against table rows dynamically.
  - Refactored filter bar to neatly house Center filter select, Search input and Bulk delete action trigger side-by-side.

* **Multi-Select Bulk Deletion**:
  - Integrated checkboxes in headers and rows for 5 modules: Categories, Vaccines, Articles, Banners, and Centers (Branches).
  - Implemented back-end controllers and AJAX bulk deletion endpoints:
    - `AdminVaccineController@bulkDestroyCategories`
    - `AdminVaccineController@bulkDestroy` (Soft Deactivation for Vaccines)
    - `AdminArticleController@bulkDestroy`
    - `AdminBannerController@bulkDestroy`
    - `AdminCenterController@bulkDestroy`
  - Integrated sticky action trigger buttons with validation, confirming popups and responsive toast notifications.

## [v6.5.70] - 2026-08-21

### Expand Width of Category Edit Modal and Editor Height

* **Category Modal UI Expansion**:
  - Resized the Category Edit Modal container in `admin/categories/index.blade.php` to a width of `850px` (previously `520px`).
  - Increased TinyMCE editor viewport height to `420px` (previously `280px`) to provide a spacious writing layout for medical content.

## [v6.5.69] - 2026-08-21

### Integrate TinyMCE Editor to Categories and Vaccine Forms

* **Category Description WYSIWYG integration**:
  - Replaced the raw HTML textarea in `admin/categories/index.blade.php` modal with TinyMCE 6 editor, matching the styling and Vietnamese locale used in the news publishing forms.

* **Vaccine Description WYSIWYG integration**:
  - Integrated TinyMCE on the `description` textarea in the Add/Edit Vaccine form `admin/vaccines/_form.blade.php` to prevent non-tech administrators from having to input raw HTML syntax.

## [v6.5.68] - 2026-08-21

### Display Both Center-Specific and System-Wide Counts Side-by-Side

* **Dual Column Counts on Categories Page**:
  - Refactored `AdminVaccineController@categoriesIndex` and `admin/categories/index.blade.php` to display two separate count columns side-by-side: "Vắc xin tại [Tên chi nhánh]" and "Vắc xin (Toàn hệ thống)".
  - This ensures that Super Admins can verify exact 1-to-1 matching values with the active frontend store counts for any selected branch while still tracking database wide totals.

## [v6.5.67] - 2026-08-21

### Add Center Filter UI to Admin Categories Page

* **Admin Categories Center Filter Selector**:
  - Embedded a branch selection dropdown selector in `admin/categories/index.blade.php`, allowing administrators to switch between centers directly on the Manage Disease Categories page. This updates the displayed vaccine counts per category in real-time, matching what the client-side user views on the frontend for each center.

## [v6.5.66] - 2026-08-21

### Bind Admin Category Product Count to Selected Center

* **Center-based Category Count in Admin**:
  - Updated `AdminVaccineController@categoriesIndex` to count category vaccines using `scopeForCenter` when a specific center is selected on the top header bar, matching the center-filtered display shown to client-side users.

* **Client Detail Page Query Alignment**:
  - Streamlined `VaccineController@diseaseDetail` to strictly retrieve center vaccines matching the category field exactly, eliminating the broad `disease_prevention` fallback query to ensure exact 1-to-1 sync of counts.

## [v6.5.65] - 2026-08-21

### Unify Category Product Counts & Detail Content Synchronization

* **Product Category Unification**:
  - Refactored `VaccineController@buildProductCategories` to group client-side categories strictly by the vaccine's `category` field. This eliminates mismatching virtual categories from the product grid and aligns count results with Admin settings.

* **Modal Content Sync Correction**:
  - Adjusted AJAX creation in `AdminVaccineController@storeCategoryAjax` to insert the initial boilerplate template text directly into the `content` field of the placeholder article.
  - Aligned client-side rendering to parse `content` only, ensuring that any pre-filled or edited category texts show up identically in both the Admin edit modal textareas and the Client detail view.

## [v6.5.64] - 2026-08-21

### Synchronize Category Details and Simplify Admin Layout for Non-Tech Users

* **Client/Admin Synchronized Display**:
  - Rewrote `VaccineController@diseaseDetail` to strictly query linked Category articles without fuzzy text search parameters or hardcoded fallback disease lists.
  - Configured Client view `disease.blade.php` to hide the description panel entirely when the database has empty content, ensuring that what the Admin configures is exactly what is shown to the end-users.

* **Admin Cleanups for Non-Tech Operations**:
  - Removed all complex helper instructions and technical warnings from the Categories index modal (`categoryModal`).
  - Simplified the labels (`Tiêu đề mô tả`, `Nội dung mô tả`) and warning alerts to be friendly and clear for non-technical users.

## [v6.5.63] - 2026-08-21

### Implement Dynamic Category Creation & Details Editor in Admin

* **Vaccine Form Category Dropdown**:
  - Restricted options to selection-only inside the categories dropdown in `admin/vaccines/_form.blade.php` (hidden direct Edit/Delete buttons).
  - Appended a fixed **"+ Thêm nhóm bệnh mới..."** action option at the bottom of the list.
  - Bound an async prompt triggering `admin.categories.store-ajax` to create new category placeholders with empty description content dynamically.

* **Category Management Details Editor**:
  - Unified categories indexing in `AdminVaccineController` by querying distinct values from both `vaccines` and `articles` tables, allowing newly added empty categories to persist.
  - Extended the Categories Index view modal `categoryModal` to include dedicated **Tiêu đề mô tả** and **Nội dung mô tả chi tiết** input/textarea fields.
  - Updated `AdminVaccineController@updateCategory` to save/update linked articles dynamically, syncing description headers and HTML text blocks shown on the client-side disease detail page.

## [v6.5.62] - 2026-08-21

### Restore Traditional Multi-Page Navigation Routing

* **Menu Routing Restoration**:
  - Restored original Laravel route links (`home`, `about`, `vaccine.index`, `news.index`, `booking.lookup`) on both the primary desktop nav menu and mobile drawer.
  - Revoked the smooth scrolling handlers from main menu options to preserve the presence of individual, shareable sub-pages.
  - Ensured the client booking lookup modal logic remains fully active and accessible asynchronously when accessing `/tra-cuu-lich-hen`.

## [v6.5.61] - 2026-08-21

### Implement SPA Navigation Menu & Client Booking Lookup Modal

* **SPA Client Navigation**:
  - Restructured both the main desktop menu and mobile drawer links in `layouts/app.blade.php`.
  - Replaced the direct route href links for "Trang Chủ", "Giới Thiệu", "Danh Mục Sản Phẩm", and "Tin Tức" with custom `smoothScrollTo` handlers referencing section wrappers.
  - Linked the "Tra Cứu Lịch Hẹn" item to open a dynamic booking lookup modal instead of triggering full-page browser reloads.

* **Dynamic Booking Lookup Modal & Backend**:
  - Added a dedicated `#bookingLookupModal` overlay within `modal-detail` layout partial.
  - Implemented async handlers (`openBookingLookupModal`, `submitSpaBookingLookup`) fetching endpoint `/tra-cuu-lich-hen` with JSON headers.
  - Extended `VaccineController@lookupBookingsByPhone` to respond with serialized booking and loyalty point records dynamically.

## [v6.5.60] - 2026-08-21

### Implement SPA Article Details Modal on Client Front-end

* **SPA Article Detail Overlay**:
  - Appended `articleDetailModal` markup to the `modal-detail` partial layout.
  - Formatted layout text blocks inside the modal with justified typography (`text-align: justify;`) and placed author credit signature at the bottom right.
  - Implemented client-side JavaScript handlers (`openArticleDetailModal`, `closeArticleDetailModal`) inside `public/js/app.js` to asynchronously fetch article JSON payloads and render them smoothly without reload.

* **Backend JSON Support & View Integration**:
  - Updated `ArticleController@show` to respond with serialized article JSON structures upon receiving AJAX requests or `Accept: application/json` headers.
  - Replaced native direct route redirection bindings in `partials/home/news.blade.php` with `openArticleDetailModal` triggers.

## [v6.5.59] - 2026-08-21

### Fix Categories Add Button Placement & Admin Active Tab Highlights

* **Categories Header Alignment**:
  - Replaced the Bootstrap flex wrapper with direct inline styles (`display: flex; justify-content: space-between; align-items: center;`) in the category management view header.
  - Resolved the layout overflow/wrapping issue that forced the "Thêm Nhóm Bệnh" button to display below the title.

* **Admin Active Tab Highlights**:
  - Fixed active tab highlight duplication by updating sub-navigation active tab detection in the admin layout file.
  - Excluded the `admin.vaccines.categories` route from triggering active state highlighting on the parent `admin.vaccines.index` tab.

## [v6.5.58] - 2026-08-21

### Fix Admin Category Management Modals & Refine Table Layout

* **Replace Modals with Pure Custom Modals**:
  - Replaced Bootstrap-based `categoryModal` and `deleteConfirmModal` with pure custom display-flex modals using inline overlays (`background: rgba(15, 23, 42, 0.6)`).
  - Bypassed JavaScript dependency on `bootstrap.Modal` to prevent `bootstrap is not defined` runtime reference errors that broke Edit/Delete actions.
  - Implemented clean inline script handlers (`openCreateModal`, `closeCategoryModal`, `openEditModal`, `closeDeleteConfirmModal`, `handleDeleteCategory`, `confirmDeleteCategory`).

* **Refine Table Layout & Aesthetics**:
  - Centered table action buttons and adjusted padding/border configurations to match Medicare Brand Theme colors (`#c8102e`, `#dc2626`).
  - Constrained main table card width to `1000px` for enhanced desktop layout alignment.

## [v6.5.57] - 2026-08-21

### Add Category Management in Admin & Refactor SPA Consultation Form

* **Add Category Management in Admin**:
  - Integrated centralized "Quản lý nhóm bệnh" (Category Management) view in Admin under the Vaccine group.
  - Linked to existing check, update, and delete metadata endpoints (`checkCategoryDelete`, `updateCategory`, `destroyCategory`).
  - Added "Quản lý nhóm bệnh" item to Admin `subNavigation` bar.

* **Refactor SPA Consultation Form**:
  - Replaced the direct Zalo redirection with a lead capture form (collecting Name, Phone, and Note) in the SPA empty-cart/consultation drawer.
  - Form posts data directly to the `/leads` database endpoint to record in CRM (`consultation_leads`).
  - Upon successful submission, displays the success message alongside the branch's specific Zalo QR Code, hotline, and direct chat link for seamless follow-up.

## [v6.5.56] - 2026-08-21

### Migrate Database to the new host

* **Database Migration & Configuration**:
  - Dumped structure and data from remote MySQL source (`yvidlapc_tiemchung`) to `tiemchung_backup.sql` local file.
  - Compressed the local `vendor` library to `vendor.zip` to upload.
  - Configured `index_hook.php` (placed on server `public_html/index.php`) to automatically unzip `vendor.zip` and import database into local database server (`nwyyoafa_tiemchung`) via PHP PDO when triggered by user access.
  - Updated local and remote `.env` configuration to use the new database host (`127.0.0.1`), database name (`nwyyoafa_tiemchung`), and credentials.

## [v6.5.55] - 2026-08-21

### Deploy application to the new FTP host

* **FTP Host Transition**:
  - Updated FTP connection configuration variables in `.env` (`FTP_HOST`, `FTP_PORT`, `FTP_USERNAME`, `FTP_PASSWORD`) to point to the new cPanel host (`hv45-24817.azdigihost.com`).
  - Compiled latest Vite frontend assets into `public/build`.
  - Created `deploy_cpanel.ps1` helper script to scan and upload all code resources.
  - Successfully uploaded all 457 project source and public asset files, routing Laravel core folders (e.g. `app/`, `bootstrap/`, `config/`) outside web root and duplicating/placing public assets directly in `public_html/`.

## [v6.5.54] - 2026-08-15

### Enforce Strict Centering for Single News Article

* **Enforce Single Article Alignment (`news.blade.php`)**:
  - Implemented the `.news-single-centered` CSS class with `justify-self: center !important` and `margin-left/right: auto !important` to override default grid item stretch behaviors on modern browsers, guaranteeing centering of a single article card.

## [v6.5.53] - 2026-08-15

### Restore News Category Pills Layout Alignment

* **News Section Category Pills (`news.blade.php`)**:
  - Restored original left-aligned layout for the category filter bar by removing the `sm:justify-center` desktop center-align configuration.
  - Preserved the dynamic column layout that automatically centers the main article when only 1 article is available.

## [v6.5.52] - 2026-08-15

### Redesign Promo Labels, Relocate Featured Links, Display Top Viewed Vaccines & Adapt News Layout

* **Relocate and Adapt Featured Vaccines Link (`qdenga_promo.blade.php`, `featured_vaccines.blade.php`)**:
  - Changed yellow badge "Vắc Xin Tiêu Điểm" to "Vắc Xin Nổi Bật".
  - Moved "Xem tất cả vắc xin nổi bật" link from the catalog header section to the featured vaccine banner, applying dynamic brand coloring matching the background.

* **Top Viewed Vaccines Catalog (`HomeController.php`)**:
  - Reconfigured homepage product catalog query to select the 8 most viewed vaccines based on `views` count descending.

* **News Section Responsive Adaptability (`news.blade.php`)**:
  - Adjusted grid columns to automatically span to 12 columns (centered layout) when there are no side articles available.
  - Centered category filter pills on desktop screen views for cleaner visual harmony.

## [v6.5.51] - 2026-08-15

### Center Align Featured Vaccines & Testimonials Dynamically

* **Dynamic Grid Centering (featured_vaccines.blade.php, testimonials.blade.php)**:
  - Converted rigid grid layouts to flexible, wrap-enabled flexbox containers.
  - Added media-query width calculations inline via `<style>` to prevent dependency on Tailwind JIT compilation in production.
  - Centered items dynamically when the total count is lower than the column limit, eliminating empty space columns on the right.

## [v6.5.50] - 2026-08-14

### Import Real Vaccines & Branch Pricing Catalog

* **Real Vaccines Import Migration (`2026_08_14_000003_import_real_vaccines_catalog.php`)**:
  - Cleared old seeded mock vaccines from the database.
  - Parsed and consolidated 44 real vaccine products from the Cờ Đỏ catalog PDF (for Cờ Đỏ, Phong Điền, and Trà Nóc branches) and Vị Thanh excel sheet.
  - Automatically mapped branch-specific pricing for all 4 centers.
  - Cleaned whitespaces and newlines in text fields, mapped appropriate categories (e.g., HPV, Tiêu chảy Rota, Vắc xin 6 trong 1), and imported correct disease prevention target strings and age groups.

## [v6.5.49] - 2026-08-14

### Re-import Real Medicare Centers Data & Update Site Configuration Settings

* **Real Medicare Centers Seeding & Migration (`2026_08_14_000001_reimport_real_medicare_centers.php`, `CenterSeeder.php`, `test_data.sql`)**:
  - Replaced existing mock/outdated branches with the 4 real branches: Medicare Cờ Đỏ, Medicare Phong Điền, Medicare Trà Nóc, and Medicare Vị Thanh.
  - Configured actual phone numbers, Zalo hotlines, physical addresses, and interactive Google Maps embed URLs for the cards.
  - Programmatically mapped old `center_id` values in `test_data.sql` to match the new branch IDs and prevent foreign key validation failures.

* **Dynamic Footer & About Content Alignment (`2026_08_14_000002_update_medicare_site_settings.php`, `SiteContentRegistry.php`)**:
  - Updated default configurations for branch count (`about_stat_branches`), network addresses (`address`), and description paragraphs (`about_story_desc`, `about_val5_desc`).
  - Executed a DB update migration to automatically replace live site settings matching the active branches.

## [v6.5.48] - 2026-08-14

### Translate System Audit Logs, Fix Dropdown Clipping, Empty DB Crash & Dynamic Logo/Image Uploads

* **Action & Resource Translation (AuditLog.php)**:
  - Translated all remaining code keys / snake_case actions and resource types to user-friendly Vietnamese.
  - Enhanced resource display name logic to query Customer model names, settings names, and render direct strings for dynamic metadata.

* **Fix Dropdown Menu Clipping (`admin.blade.php`)**:
  - Enforced `overflow: visible !important` on table responsive wrappers on desktop screens (min-width: 1024px) to let absolute menus float properly over container boundaries.
  - Upgraded `toggleActionMenu` JS handler to automatically position menus upwards (`dropup`) when rendering near the bottom of the table container (e.g., when the list has only 1-2 records), preventing vertical scrollbars and clipped items.

* **Empty Database Resilience (`HomeController.php`, `VaccineController.php`)**:
  - Prevented SQL error 1054 on homepage queries and vaccine index page when there are no centers (e.g., in a clean/new production state) by safely returning empty collections/paginators without appending center-only table sort orders to non-joined queries.

* **Dynamic Logo Configuration & Visual Image Uploads (`AdminSettingController.php`, `live_editor.blade.php`, `app.blade.php`, `admin.blade.php`, `SiteContentRegistry.php`)**:
  - Replaced all hardcoded website logo assets with a dynamic system setting (`site_logo`), allowing administrators to upload new logo image files directly from either the general configuration dashboard or the "Cấu Hình Chung" (Global Settings) tab in Live Editor by registering `site_logo` in `SiteContentRegistry` fields whitelist.
  - Linked the tab favicon to the dynamic `site_logo` asset, replacing the default browser/host globe icon across both public views and administration portals.
  - Converted avatar inputs from text paths to interactive file pickers with live image previews in Live Editor for both Customer Testimonials (`home_testimonials`) and Team Members (`about_team_members`), utilizing AJAX to upload files on-the-fly and bind URLs to system settings.

## [v6.5.47] - 2026-08-14

### Automatic Customer Lookup & Form Reordering

* **Customer Lookup Route & Action (`web.php`, `AdminCustomerController.php`)**:
  - Implemented a secure AJAX `/admin/customers/lookup` endpoint that accepts a phone number, normalizes it, and returns the customer's name if they already exist in the database.
  
* **Reorder Form & Auto-fill Owner Name (`create.blade.php`)**:
  - Moved the **Số điện thoại tài khoản** (Account phone) field to the left (before **Họ tên chủ tài khoản**).
  - Added a dynamic JS event listener to check the database on-the-fly when a valid phone number is entered.
  - Automatically fetches the existing customer's name, sets the owner name input to read-only, and grays it out if a match is found. Removes read-only constraints if no match is found.

## [v6.5.46] - 2026-08-14

### Google Maps Embed URL Parser & Validation

* **Support Complete Iframe Embed Code (`AdminCenterController.php`)**:
  - Automatically detected if the user pasted a full `<iframe>` tag from Google Maps and extracted the `src` attribute URL.
  
* **Enforce Safe Embed Link Format (`AdminCenterController.php`)**:
  - Changed validation to strictly require a URL starting with `https://www.google.com/maps/embed` to prevent non-iframe URLs (like `google.com/maps/place`) from crashing the frontend maps block.
  - Added a helpful validation message guiding users to copy the share/embed link.

## [v6.5.45] - 2026-08-14

### Fix Branch Loyalty Settings Validation Bug

* **Add Missing Hidden Input for Percentage Redeem (`loyalty.blade.php`)**:
  - Inserted the missing `redeem_percent_bps_per_point` hidden field to satisfy Laravel controller validation rules when submitting the form.
  
* **Preserve Radio Selection on Validation Error (`loyalty.blade.php`)**:
  - Restructured the `use_system_settings` radio inputs and field visibility logic to check `old('use_system_settings')`.
  - Prevented the selection from reverting back to system defaults after validation redirect cycles.

## [v6.5.44] - 2026-08-14

### Admin Quick Registration Date/Time Selector & Bug Fix

* **Fix Undefined Slot Variable Error (`AdminRegistrationController.php`)**:
  - Resolved a fatal PHP error where `$slot` was referenced prior to being retrieved in the transaction block of `store()`. Added the missing `Slot::findOrFail` statement.
  
* **Split Quick Registration Schedule Selectors (`create.blade.php`)**:
  - Replaced the single time-slot selection dropdown with two-step dropdowns: "Ngày tiêm" (Date Select) and "Khung giờ tiêm" (Hour Select), mimicking the client registration form flow.
  - Implemented dynamic slot filtering in JavaScript based on the selected date.
  - Preserved selection values using `old('slot_id')` on validation errors.

## [v6.5.43] - 2026-08-14

### Optional Banner Text Overlays & Image Frame

* **Make Banner Title Optional (`_form.blade.php`, `AdminBannerController.php`)**:
  - Allowed blank Banner Title in the creation/editing form, changing validation to nullable and defaulting to empty string to preserve MySQL integrity constraints.
  
* **Dynamic Banner Rendering & Background Only Layout (`hero_slider.blade.php`)**:
  - Made title, subtitle, and foreground image overlays optional in the hero carousel.
  - Removed the default system image frame completely when no custom banner image is uploaded.
  - Enabled text to stretch to full width (`col-span-12`) if the foreground image is omitted.
  - Wrapped the entire banner area inside a link if it contains only a background image.

## [v6.5.42] - 2026-08-14

### Admin Dashboard Trend Chart Overlapping Fix

* **Optimize X-Axis Label Rendering (`dashboard.blade.php`)**:
  - Dynamically calculated label step spacing based on the count of days in the trend graph.
  - Hidden overlapping day labels when displaying dense ranges (like 30 or 60 days) to prevent text collision.
  
* **Thin Out Node Values & Dynamic Tooltip Areas (`dashboard.blade.php`)**:
  - Restricted drawing of static node value text labels above/below chart points when displaying more than 12 days.
  - Calculated widths of interactive hover slices dynamically to map coordinates accurately without overlaps.

## [v6.5.41] - 2026-08-14

### Regimen Selector Restoration & Administrative Tracking

* **Restore Regimen Selector Conditionally (`register.blade.php`, `app.js`)**:
  - Re-introduced the regimen selector dropdown field to the patient registration vaccine checklist on both the public booking page and the SPA drawer booking modal, displaying it only when a vaccine actually has configured regimens.
  
* **Save Regimen Choice to Pivot (`VaccineController.php`)**:
  - Fixed an issue where the selected regimen was not being saved to the database. Added the `regimen_id` column value to the `registration_vaccines` pivot table attach array when registering bookings.

* **Render Regimen Details in Admin View (`show.blade.php`)**:
  - Updated the admin registration details screen to resolve and display the selected regimen details (age group and doses) under the respective vaccines.

## [v6.5.40] - 2026-08-14

### Regimen Form Price Column & Booking Regimen Selector Removal

* **Remove Price Fields from Regimen Configuration (`_form.blade.php`)**:
  - Removed "Giá gốc riêng" and "Giá ưu đãi riêng" input columns from the age-group regimen configuration table in the admin vaccine form, updating the table widths to fit.
  - Adjusted dynamic JavaScript row template to build rows without price input cells.

* **Remove Regimen Selector from Registration (`register.blade.php`, `app.js`)**:
  - Removed the regimen selector dropdown field from the patient registration vaccine checklist on both the public booking page and the SPA drawer booking modal.
  - Ensured checkboxes carry the base vaccine price so checkout calculations remain correct.

## [v6.5.39] - 2026-08-14

### Banner & Upload Path Production Fix

* **Dynamic Public Path Binding (`app.php`)**:
  - Configured public path dynamically to point to the project root if `index.php` is in the root directory (on production hosting environment). This fixes the issue where uploaded banner and vaccine images go to `/public/images/...` instead of `/images/...`, causing them to return a 404 error on the website.

## [v6.5.38] - 2026-08-13

### Vaccine Management Permission Tuning

* **Conditional SuperAdmin Restrictive Permission (`AdminVaccineController.php`)**:
  - Enhanced permission checks for vaccine master fields (shared details like name, category, origin, regimens, and image).
  - Restricts SuperAdmin from editing shared vaccine fields, uploading vaccine images, creating new vaccines, or deleting vaccines when they are currently operating under a specific branch context (selected center is not null).
  - When a center is selected, SuperAdmin's capability will act exactly like a Branch Admin, allowing them only to update center-specific parameters (price, stock, and featured status).
  - The shared vaccine input fields in the editor view are automatically disabled when a center is selected.

## [v6.5.37] - 2026-08-13

### Admin Dashboard Vaccine Stats Correction

* **Distinct & Active Vaccine Counting (`AdminDashboardController.php`)**:
  - Fixed an issue where the overall vaccine count on the Admin Dashboard was showing duplicate counts across multiple centers (e.g., 220 instead of 40) or including inactive vaccines (e.g., 44 instead of 40).
  - Synchronized the count logic with `AdminVaccineController`: if no center is selected, count active vaccines directly from the `vaccines` table. If a center is selected, count vaccines active in both the center mapping and the main `vaccines` table.

## [v6.5.36] - 2026-08-13

### Header Responsive Layout Optimization

* **Responsive Navigation Header Fix (`app.blade.php`, `style.css`)**:
  - Prevented navigation link texts from wrapping onto multiple lines by adding `white-space: nowrap;` to `.nav-link`.
  - Raised the mobile breakpoint threshold from `768px` to `1200px` to hide the horizontal `.nav-menu` and show the hamburger `.mobile-menu-toggle` on medium/tablet screens.
  - Adjusted the header action pill styles to collapse text and apply compact paddings under `1200px` screen size to fit tablets and prevent header layout distortion.

## [v6.5.35] - 2026-08-13

### Footer Layout Restoration - Card-based Branches & Legal Panel

* **Rollback to Card-based Footer Layout (`app.blade.php`, `style.css`)**:
  - Restored the footer branch listing to the 4 card-based boxes (`.footer-branch-item` inside `.footer-branch-list`) which show complete address details, hotline, and operating hours.
  - Restored the right column's legal info container back to the background-blur panel card (`.footer-legal-panel`).
  - Restored the top header bar of the footer containing the logo, Medicare tagline, search, and general hotline details.

## [v6.5.34] - 2026-08-13

### Footer Redesign - 2-Column Compact Layout

* **Complete Footer Redesign (`app.blade.php`, `style.css`)**:
  - Removed the top header bar section (logo + tagline + hotline row) from the footer to create a cleaner, more focused layout.
  - Left column now displays the brand logo, tagline, and a compact branch grid (name + hotline only) in a 3-column grid arrangement.
  - Each branch card is a minimal pill-style tile with the branch name and clickable hotline number, with active-branch highlighted in red.
  - Right column retains navigation links and full company legal information.
  - Removed all old flat-row and card-based branch CSS; replaced with new `.footer-branch-compact-grid` and `.footer-branch-compact-item` styles.
  - Footer is now fully responsive: 3-column branch grid collapses to 2 columns on tablet and below.


## [v6.5.33] - 2026-08-13

### Footer Layout Optimization

* **Flat Footer Branches & Info Layout (`app.blade.php`, `style.css`)**:
  - Transformed the footer branches network layout from bulky grid-based cards to a sleek, minimal flat list of rows.
  - Displayed branch names, status tags, contact details, maps, and appointment links on inline text rows separated by clean borders.
  - Removed the solid background card panel (`.footer-legal-panel`) from the company info column on the right, displaying text directly on the dark footer background for a unified, modern, and high-end aesthetic.

## [v6.5.32] - 2026-08-13

### Production Deployment & PHP Platform Compatibility

* **Complete Audit Log Action & Resource Type Localization (`AuditLog.php`, `app.blade.php`, `live_editor.blade.php`, `SiteContentRegistry.php`)**:
  - Added friendly, localized Vietnamese translations for all remaining raw technical identifiers (such as `refund_issued`, `registration.rescheduled`, `order_status_update`, `setting.updated`, `reset_settings`, `save_settings_draft`, `inventory_lot.created/updated/deleted`, `vaccine.deactivated/activated` and resource type `inventory_lot`) so that non-technical administrators can easily understand the logs.
  - Fixed a CSS class helper bug where `deactivated` status badges erroneously matched `activated` condition and displayed in green instead of red.
  - Dynamically linked the footer legal hotline info to the currently selected branch instead of using a hardcoded static configuration string.
  - Fixed a visual scrolling issue in the Live Editor settings modal where the form layout prevented vertical scroll bar activation when fields exceeded viewport height.
  - Adjusted the default loyalty points earned ratio to 0.1% of the paid order value (1,000 VND = 1 point earned) and reinforced the maximum point redemption limit to 50% of the total order value.
  - Simplified the loyalty settings layout by hiding the member tiers, promotional campaigns, and patient birthday multiplier sections to keep a minimal point-based discount workflow.
  - Added real-time member loyalty point display on the client-facing booking lookup page when searching registrations by phone number.
  - Linked the footer opening hours text to a dynamic configuration setting `footer_working_hours` that is fully editable from the Live Editor's Global Configuration tab.
  - Fixed a clinical workflow bug in the `Registration` model where administering a single vaccine immediately marked the entire multi-vaccine order as completed, preventing remaining vaccines from being administered.
  - Added a defensive check `stock_quantity > 0` before decrementing branch vaccine stock in the clinical administration workflow, preventing SQL `Numeric value out of range` exception on `unsignedInteger` columns.
  - Implemented seamless AJAX-based vaccine deletion in the admin catalog (`AdminVaccineController.php`, `index.blade.php`, `_table.blade.php`), allowing rows to fade out and be removed from the DOM dynamically without page reload, preserving the user's active search filters.
  - Added support for collective group payments (`AdminRegistrationController.php`, `web.php`, `show.blade.php`), enabling branch administrators to settle all unpaid registrations within the same booking group (multiple patients registered under one single request) using a single "Thanh toán chung cả nhóm" button, and displaying sibling group members in a structured list.

* **Vaccine Regimens Price & All-Branch Option (`AdminVaccineController.php`, `_form.blade.php`, `show.blade.php`, `VaccineRegimen.php`)**:
  - Added new `price` and `sale_price` columns to `vaccine_regimens` table via migration, allowing each age-group regimen to specify its own pricing.
  - Added "Áp dụng cho tất cả chi nhánh" (All branches) option inside the branch price/stock dropdown selector in the vaccine admin form.
  - Programmed automated creation and synchronization of vaccine price/stock records for all active branches in `AdminVaccineController.php` when the "All branches" option is chosen.
  - Rendered a new "Giá phác đồ" (Regimen price) column inside the vaccine regimens table on the client-facing vaccine details page, automatically multiplying vaccine single-dose price if no regimen-specific price is defined.
  - Adjusted the deferred rescheduling form layout in the admin registrations detail page to split date and time selection into two responsive grid columns with client-side JavaScript filtering.

* **Banner Custom Background & Autoplay, Client-Side Stock Lock Bypassing (`hero_slider.blade.php`, `AdminBannerController.php`, `_form.blade.php`, `Banner.php`, `BranchStockService.php`, `CenterContext.php`, `grid.blade.php`, `show.blade.php`, `VaccineController.php`, `create.blade.php`, `VaccinationWorkflowController.php`, `AdminRegistrationController.php`, `web.php`)**:
  - Implemented dynamic vanilla JavaScript autoplay logic inside `hero_slider.blade.php` to slide automatically every 5s, supporting controls and indicators.
  - Added new `background_url` column to `banners` table via migration, enabling Admin to upload full-width background images instead of default red gradient background.
  - Lifted "Out of Stock" (Hết hàng) block constraints from frontend catalog lists, vaccine detail templates, and admin creation view checkbox, keeping all buttons active.
  - Re-configured `BranchStockService.php` and `VaccineController.php` to bypass stock blocking during shopping cart additions and ticket reservations.
  - Delayed branch stock quantity decrement from booking creation time to actual vaccine administration completion step (`administer` method).
  - Programmed automated refund and order cancellation logic in clinical screening when the evaluation status is contraindicated.
  - Added a rescheduling form inside the clinical screening step for deferred patients to select a new date and time slot, resetting their screening status.
  - Fixed registration details layout CSS spacing between the payment status card and clinical workflow section.
  - Deleted Vite local hot reloading `public/hot` configuration from production FTP host to force loading production build files.
  - Deployed Vite compiled files to both public and root assets folder on FTP to resolve shared hosting assets loading paths mismatch.
  - Executed a temporary database reset script to truncate all bookings, patients, and customers tables while resetting all branches stock quantity back to 100.

* **Deployment Automation Scripts (`deploy_all_v2.ps1`, `upload_untar.ps1`)**:
  - Developed a robust deployment script `deploy_all_v2.ps1` to upload the entire codebase while filtering out VCS and build garbage (e.g. `.git`, `.agents`, `node_modules`).
  - Added support for double-destination uploading for public assets (to both `/public` and `/` directories) to guarantee asset loading compatibility on shared host configurations.
  - Implemented dynamic path rewriting for front controller imports in `/index.php` when copying from `public/index.php` to root level, resolving runtime path errors.
* **PHP Platform Down-Tuning (`composer.json`)**:
  - Re-configured Composer platform emulation settings to target PHP `8.2.0` (matching the remote host version `8.2.26` instead of local development PHP `8.4.x`).
  - Successfully downgraded active dependencies to resolve autoloader check requirements (`platform_check.php`) on older host software environments.
  - Created and executed a web-based extraction script utilizing `PharData` to unzip composer libraries (`vendor.tar`) locally on the server in a matter of seconds.
* **Audit Log Localization, Error Page Branding & Toast Color Adjustments (`AuditLog.php`, `layout.blade.php`, `app-dialog.blade.php`)**:
  - Translated technical action keys (such as `admin_user.created`, `auth.password_changed`, and `publish_settings`) into friendly, localized Vietnamese labels for non-technical users.
  - Custom branded error pages (4xx & 5xx) to match the brand identity using Medicare Red (`#c8102e`) and Medicare Navy (`#004b8f`) as primary/hover states and Medicare Gold (`#eaaa00`) as focus outline.
  - Adjusted alert toast background colors in `app-dialog.blade.php` to use fresh, vibrant palettes (Success `#10b981`, Error `#ef4444`, Info `#3b82f6`).
* **Observation Minutes Removal & Past Slot Selection Blocking (`show.blade.php`, `register.blade.php`, `VaccineController.php`, `AdminRegistrationController.php`, `app.js`, `app.blade.php`)**:
  - Removed "Observation Minutes" (Theo dõi) number input field entirely from the clinical dose administration form in admin `show.blade.php`.
  - Added frontend and backend filters to completely hide and block selection of past time slots of today (e.g. if today is chosen, slots starting before local time are omitted).
  - Applied the same frontend filters to both the standalone `/register` form and the popup SPA Modal form defined inside `app.js` to ensure uniform booking constraints.
  - Replaced undefined `$appJsVersion` with dynamic `time()` timestamp in `app.blade.php` to bypass aggressive client-side Javascript asset caching.
* **Timezone Synchronization, Multi-Patient Checkout, & Lot Selection Removal (`app.php`, `app.blade.php`, `success.blade.php`, `Registration.php`, `VaccinationWorkflowController.php`, `show.blade.php`)**:
  - Shifted application default timezone in `app.php` to `'Asia/Ho_Chi_Minh'` to resolve day-mismatch between server UTC and Vietnam local time (preventing customers from booking on past dates).
  - Removed "Dịch Vụ" menu link from layout header navigation in `app.blade.php` to fit custom business requirements.
  - Refactored `success.blade.php` to render all registered patients tickets (codes, details, and totals) in a multi-patient reservation flow instead of showing only the first person.
  - Modified the clinical workflow to make `inventory_lot_id` nullable (in `VaccinationWorkflowController.php` and `show.blade.php`), removing the select-lot constraint while keeping database constraints intact.
  - Synchronized `booking_status` to `completed` in `Registration.php` when a vaccine dose is successfully administered, updating the admin details page status immediately.

## [v6.5.31] - 2026-08-13

### Mobile Responsiveness & Layout Fixes

* **Mobile Header, Table Alignment & Footer Layouts (`admin.blade.php`, `hero_slider.blade.php`, `style.css`)**:
  - Prevented wrapping of the Admin Panel Page Title on mobile by automatically truncating the text with ellipsis and limiting the width of the branch selector dropdown.
  - Forced table layouts to obey standard browser table-rendering styles using `!important` to keep column headers and body cells strictly aligned, and enabled responsive horizontal scrolling on all viewports (including narrower desktop resolutions) to prevent data tables from overflowing/spilling out of card borders.
  - Stabilized the Homepage Hero Slider on mobile by adding robust responsive height queries, scaling down the image slightly, and hiding navigation arrows on small screens.
  - Enhanced mobile layout for Footer Branch Actions by transforming them into full-width stacked buttons and centered the policy navigation links row on mobile/tablet viewports to eliminate messy wrapping and left-alignments.

## [v6.5.30] - 2026-08-13

### Optional Banner Image Configuration

* **Optional Banner Image (`AdminBannerController.php`, `_form.blade.php`)**:
  - Removed strict requirement for banner images, making it optional to save/update a banner.
  - Automatically falls back to the system's default banner image on the homepage slider when no image is provided.
  - Safely deletes the old banner image file from disk when a user removes it or replaces it (excluding seed/default images).
  - Updated form label and styling in `_form.blade.php` to reflect that the image is optional.

## [v6.5.29] - 2026-08-13

### Dynamic Content, Visual Live Editor & Preview Enforcement

* **Dynamic Content & Live Editor Integration (`SiteContentService.php`, `SiteContentRegistry.php`)**:
  - Created a centralized registry class `SiteContentRegistry` defining all configurable fields for Home, About, Services, and Contact pages, including their validation rules and default data.
  - Implemented `SiteContentService` to manage draft settings, publishing draft configs to production database, and resetting drafts.
* **Preview Mode & Booking Security Enforcement (`app.blade.php`, `web.php`, `BlockInPreviewMode.php`, `HomeController.php`)**:
  - Implemented a sticky yellow warning banner at the top of all public pages when in Preview mode (`?preview=1` checked against active SuperAdmin session), featuring a dedicated "Quay về Admin" button in Medicare Red for easy navigation back to the Live Editor.
  - Added the missing "Dịch Vụ" link to the public header navigation menu in `app.blade.php`, enabling users and admins to naturally access and preview the services page.
  - Created `BlockInPreviewMode` middleware to prevent registration, cart additions, and consultation lead submission at the backend level when previewing.
  - Injected client-side JavaScript to intercept and block all booking/purchase interactions when previewing, showing custom warning alerts.
* **Enhanced JSON Array Fields inside Live Editor (`live_editor.blade.php`, `AdminLiveEditorController.php`)**:
  - Redesigned the Live Editor form panel to visually render complex JSON lists (FAQs, Testimonials, Team Members, and Services Commitments) with dynamic "Add" and "Delete" actions.
  - Refactored `AdminLiveEditorController` to integrate schema validation, transaction-safe publishing, and system audit logging.
* **Brand Color Harmonization & Natural Filter UI (`style.css`, `index.blade.php`, `app.js`, `show.blade.php`)**:
  - Cleaned up the "Vắc xin nổi bật" filter styling by removing machine-translated/AI-like titles ("Phân loại đặc biệt" renamed to "Loại sản phẩm"), removing excessive gold stars, and styling the count badge to use a clean Medicare Red color palette instead of a bright yellow.
  - Removed inline style overrides from the toolbar filter buttons, letting the "Nổi bật" pill active states blend seamlessly using the official Medicare Red primary color. Added high-priority CSS overrides with `!important` for `#btnFeaturedFilterPill` to completely suppress legacy cached JS color injections, locking the pill to the brand identity.
  - Removed the redundant "Loại sản phẩm" (Featured Filter) group block from the sidebar to prevent visual clutter and duplication with the toolbar pill button.
  - Refactored selected product card state (`.catalog-product-card.selected`) and checkout selection buttons (`.btn-select-vaccine.btn-selected`, `btn-select-detail`) to shift from the previous inconsistent gold/brown colors to the official Medicare Red primary tone and soft red background, resolving UX visual disharmony. Simplified admin password policy to only enforce a minimum length of 8 characters (removing letters, uppercase, number, and symbol complex restrictions), updating corresponding validator rules and view forms. Created automatic database exporter tool `session_data/dump_db.php` to export all records into `database/seeders/test_data.sql` and updated `DatabaseSeeder.php` to seed this customized test data if present.
* **Visual Live Editor & Layout Sorting Restoration (`live_editor.blade.php`, `live_editor_layout_tab.blade.php`)**:
  - Recreated the missing `live_editor_layout_tab.blade.php` view, restoring the visual layout customizer for the homepage sections (using JavaScript-powered row swapping and automatic order recalculation).
  - Integrated the layout save, publish, and reset forms inside the live editor with proper action dispatching, resolving the 500 error on page swap. Replaced default browser `alert()` and `confirm()` prompts with `window.AppDialog` custom modals for all actions. Styled the "Lưu Sắp Xếp Nháp" button to use Medicare Red instead of blue.
  - Cleaned up tab aesthetics by removing the bright border outline styles and focus outline rings from the navigation tab buttons.
  - Simplified visual edit badges by removing unnecessary Lucide icons, shortening labels, and styling with clean brand colors (Medicare Red background shifting to deep red on hover) to avoid an AI-generated look. Removed inline blue styles from the global configuration edit badge. Added visual grid preview for the 6 golden core values inside the Giới Thiệu tab.
  - Removed all artificial emojis (e.g. `⭐`, `📖`, `🎯`, `👁️`, `👨‍⚕️`, `📋`) from the live editor preview section titles.
* **Dual Branch Actions for Centers (Hard Delete & Soft Pause) (`AdminCenterController.php`, `Center.php`, `_table.blade.php`)**:
  - Refactored the `destroy` method of `AdminCenterController` and removed the deleting event interceptor in `Center.php` to execute a true database hard delete, leveraging SQL constraints (`cascadeOnDelete`, `nullOnDelete`) to handle related slots, stocks, and schedules safely.
  - Added a dedicated "Xóa" button next to "Ngừng" / "Bật lại" in the centers datatable.
  - Color-coded action buttons to match their risk levels: "Ngừng" styled with Medicare Gold (`#eaaa00`), and "Xóa" styled with Medicare Red (`#c8102e`).
  - Harmonized the 4 quick action toolbar title colors on the homepage tab to only use the official Medicare Red and Medicare Navy, replacing the unharmonized bright green and orange titles.
  - Simplified the live-edit-frame border style by changing it from a thick, blue dashed outline to a thin, subtle light gray dashed border, which gracefully lights up in Medicare Red on hover.
  - Cleaned up tab navigation text by removing the numbers like "(7 Khung)", "(5 Khung)", and "(2 Khung)" and renaming "Khung Chung System Shell" to "Cấu Hinh Chung". Removed the blue border from the global configuration navigation tab button.
  - Completely removed all AI-generated placeholder messages in square brackets (e.g. `[Khung: ...]`, `[Bao gồm mảng...]`) to deliver a fully clean production visual preview.

## [v6.5.28] - 2026-08-12

### Admin Menu Regrouping & Horizontal Sub-Navigation

* **Sidebar Regrouping (`admin.blade.php`)**:
  - Grouped 15 raw admin menu items into 5 major business categories: "Bảng điều khiển" (Dashboard), "Tiêm Chủng & Lịch Trình" (Vaccine & Schedules), "Đăng Ký & Khách Hàng" (Registrations & Customers), "Nội Dung Website" (Website Contents - SuperAdmin only), and "Hệ Thống & Thiết Lập" (System & Settings).
  - Maintained consistent Lucide brand icons and preserved role-based access permissions.
* **Horizontal Sub-Navigation (`admin.blade.php`)**:
  - Implemented an elegant, responsive horizontal sub-navigation tab system (Modern Underline Tabs style) at the top of the admin page content area.
  - Automatically detects the active category group based on the current Laravel route name and displays the corresponding sub-menu links dynamically (e.g. switching between Vaccines, Time Slots, and Weekly Calendars).
  - Cleanly handled route prefix collision cases (such as between `admin.settings.index` and `admin.settings.loyalty`).
  - Added smooth CSS transitions, hover states, and overflow scroll support using brand colors (Medicare Red `#c8102e` and Medicare Navy `#004b8f`).
* **UX Improvement for Out-of-Stock Status (`_table.blade.php`, `index.blade.php`)**:
  - Replaced the confusing "Thiếu (0)" badge status with "Hết hàng" (Out of stock) when the today's scheduled dose demand is zero but the branch vaccine warehouse stock is also zero.
* **Scrollable Daily Schedule Blocks (`schedule.blade.php`)**:
  - Added a `max-height: 480px` constraint and custom slim scrollbar style for each daily block on the Weekly Calendar dashboard, preventing the page from expanding indefinitely when handling hundreds of daily injections.
* **Client-Side Filters for Patient Details (`patients/show.blade.php`)**:
  - Added real-time client-side search inputs and dropdown filters for both "Lịch sử tiêm chủng thực tế" (Administered Doses) and "Lịch sử đăng ký tiêm" (Registrations) tables.
  - Users can now search by vaccine/lot/code and filter dynamically by branches, booking status, and payment status without reloading.
* **Clinical Workflow Security & Payment Enforcement (`VaccinationWorkflowController.php`, `registrations/show.blade.php`)**:
  - Blocked access to the clinical workflow steps (Check-in, Clinical Screening, and Dose Administration) at both the controller layer and view layer for any unpaid registrations.
  - Added a red warning banner on the registration detail page if the bill is unpaid, ensuring financial compliance before medical operations.
* **Warehouse Stock & Batch Lot Synchronization**:
  - Identified 142 mismatched data records between branch warehouse totals (`center_vaccines`) and individual batch lots (`inventory_lots`) caused by raw database seeders.
  - Created and executed a stock repair script to dynamically auto-generate missing batch lots, achieving 100% synchronization and preventing clinical "out of stock lots" errors when the overall stock shows positive values.
* **Thoi Lai Branch Vaccine Activation**:
  - Identified that 44 vaccines at the Thoi Lai branch (`center_id = 2`) had their active status disabled (`is_active = false`) in the `center_vaccines` table, blocking registrations.
  - Created and executed an activation script to re-enable all 44 vaccines for the Thoi Lai branch, allowing normal booking operations.
* **Consultation Leads Menu Removal (`admin.blade.php`)**:
  - Removed the "Yêu Cầu Tư Vấn" (Leads) link from the main layout sub-navigation bar under the "Đăng Ký & Khách Hàng" category as requested.
* **Smart Branch Navigation from Stock Shortage Alerts (`dashboard.blade.php`)**:
  - Updated both the general "Quản lý vắc xin" button and individual vaccine shortage alert cards on the Dashboard to pass the exact `center_id` of the branch with shortages.
  - Clicking a shortage alert now navigates directly to the Vaccine Management screen filtered specifically to that branch and searched by that vaccine name.
* **Table Row Sequence Numbering (STT) Across Admin Tables**:
  - Added standard STT (Sequence Number 1, 2, 3...) columns across all main admin management tables: Recent Registrations on Dashboard, Registrations Index, Patients Index, Customers & Loyalty Points Index, and Patient Detail history tables.
  - Correctly calculated pagination offsets (`$paginator->firstItem() + $loop->index`) across paginated views.
* **Center Vaccine Synchronization on Status Toggle (`AdminCenterController.php`)**:
  - Resolved an issue where activating a center did not propagate `is_active = true` to its associated `center_vaccines` records, leaving all vaccines in "Tạm ngưng" status.
  - Updated `store`, `update`, and `toggleStatus` to automatically sync branch vaccine statuses with the center's active state.
  - Activated all vaccines across all active branches including Medicare Vị Thanh.
* **Vaccine Featured Status (`is_featured`) Synchronization & Catalog Filter (`VaccineController.php`, `AdminVaccineController.php`, `HomeController.php`, `index.blade.php`, `app.js`)**:
  - Resolved an issue where unchecking the "Nổi bật" checkbox on the vaccine edit form failed to persist because `is_featured` was unset before saving to the master `vaccines` table.
  - Fixed rapid toggle action in table dropdown menu (`admin.vaccines.toggle-featured`): now properly supports toggling in all views, including global "Toàn hệ thống" mode without requiring `center_id`.
  - Removed unwanted automatic extra non-featured vaccine injection fallback in `HomeController.php`: only specifically marked featured vaccines now render on the homepage.
  - Added dedicated interactive Featured Products filter (`?featured=1`) on the public product catalog page (`/vaccines`) across sidebar, sort pill toolbar, and mobile bottom sheet.
  - Added direct link "Xem tất cả vắc xin nổi bật ⭐" on the homepage catalog section.
* **Vaccine Edit & Update Bug Fix (`AdminVaccineController.php`)**:
  - Resolved a validation failure on the vaccine edit form where `sale_price` being equal to `price`, empty, or zero triggered the `lt:price` validation error ("Giá ưu đãi phải nhỏ hơn giá gốc vắc xin").
  - Normalized `sale_price` so that empty/zero values or values $\ge$ `price` automatically convert to `null` (representing non-discounted pricing).
  - Fixed an issue where editing a vaccine from the global "Toàn hệ thống" mode resulted in missing `center_id`: now automatically preselects and defaults to the first active branch.
* **Homepage Section Order & Layout Customizer in Live Editor (`AdminLiveEditorController.php`, `live_editor.blade.php`, `web.php`, `HomeController.php`)**:
  - Restored and integrated the dedicated "Sắp Xếp & Thứ Tự Khung" tab (`?page=layout`) inside Visual Live Editor (`/admin/live-editor`).
  - Added intuitive Up / Down arrow buttons alongside HTML5 Drag & Drop sorting to reorder homepage blocks effortlessly.
  - Provided section customization for visibility (Hiển thị / Tạm ẩn), background theme (Nền trắng, Nền đỏ thương hiệu, Nền tối), and padding spacing.
  - Implemented 3 key workflow actions: "Khôi Phục Gốc", "Xem Thử Giả Lập" (`/?preview=1`), and "Xuất Bản Áp Dụng".
* **Audit Log UI & Non-Tech Readability Redesign (`AuditLog.php`, `audit-logs/index.blade.php`, `audit-logs/show.blade.php`)**:
  - Replaced ambiguous, technical target IDs (such as `Ngữ cảnh chi nhánh #current`, `Hồ sơ bệnh nhân #49`, `Chi nhánh #1`) with fully resolved, human-readable entity names (e.g. `Chi nhánh: MEDICARE CỜ ĐỎ`, `Bệnh nhân: Trần Thị Mỹ Duyên (#49)`, `Vắc xin: Hexaxim`, `Xem: Toàn hệ thống`, `Xuất 12 lịch tiêm`).
  - Completely stripped all internal technical function call signatures and English code identifiers from both dropdowns and table badges.
  - Reorganized filter bar into an aligned, responsive 2-tier search panel with friendly dropdown options and a reset button.
  - Added sequence numbers (STT), timestamp styling, user icons, and dedicated detail view panels.
* **Typo Fix in Center Management Forms (`centers/create.blade.php`, `centers/edit.blade.php`)**:
  - Corrected spelling typo on the cancel button from "Hủy bộ" to "Hủy bỏ".
* **Interactive Image Cropper Modal Integration (`_image_cropper_modal.blade.php`, `admin.blade.php`, `banners/_form.blade.php`, `articles/create.blade.php`, `articles/edit.blade.php`, `vaccines/_form.blade.php`)**:
  - Implemented an interactive Image Cropper modal powered by Cropper.js across all admin image upload interfaces (Banners, Article Thumbnails & Content Blocks, Vaccine Products).
  - Provided custom preset aspect ratios tailored to each content type (16:9, 21:9 ultra-wide for Banners; 16:9 / 4:3 for Articles; 1:1 square for Vaccine products; and Freeform crop).
  - Added transformation tools: zoom in/out, 90-degree left/right rotations, horizontal flipping, and reset.
  - Added dynamic re-crop button ("✂️ Cắt lại hình ảnh") directly below preview containers so admins can fine-tune crop boundaries without re-selecting files from their computer.
  - Preserved standard HTML5 form submission flow via `DataTransfer` file encapsulation without requiring backend endpoint modifications.
* **Banner Hard Deletion & Soft Toggle Implementation (`Banner.php`, `AdminBannerController.php`, `index.blade.php`, `web.php`)**:
  - Separated Banner soft visibility toggle ("Ẩn mềm" / "Hiện") from permanent deletion ("Xóa cứng").
  - Removed internal `Banner::deleting` event blocker to allow SuperAdmin to permanently delete unwanted banners from the database alongside their physical uploaded image files.
  - Added dedicated route `admin.banners.toggle-status` with quick toggle actions ("Ẩn" / "Hiện") directly from the table.
  - Added standard STT column, right-aligned pagination, and clear 3-action buttons (Ẩn/Hiện, Sửa, Xóa) with irreversible deletion warning confirmations.
* **Live Editor Banner Deduplication (`live_editor.blade.php`, `live_editor_modals.blade.php`)**:
  - Removed duplicated "Hero Banner Slider" frame and its single-banner popup modal from the Live Editor tool.
  - Centralized banner slider management exclusively into the dedicated, full-featured Banner Management section (`/admin/banners`).
  - Streamlined Home tab sections in Live Editor starting directly from Quick Action Toolbar.
* **Article Hard Deletion & Soft Toggle Implementation (`Article.php`, `AdminArticleController.php`, `index.blade.php`, `web.php`)**:
  - Separated Article soft visibility toggle ("Ẩn mềm" / "Hiện") from permanent deletion ("Xóa cứng").
  - Removed internal `Article::deleting` event blocker to allow SuperAdmin to permanently delete unwanted articles from the database alongside their physical uploaded image files.
  - Added dedicated route `admin.articles.toggle-status` with quick toggle actions ("Ẩn" / "Hiện") directly from the table.
  - Added STT column, view count indicator, and clear 3-action buttons (Ẩn/Hiện, Sửa, Xóa) with irreversible deletion warning confirmations.
* **Registration CSV Export Filter Synchronization (`AdminRegistrationController.php`)**:
  - Unified registration query filtering into `buildFilteredRegistrationQuery` across both list pagination (`index`) and CSV export (`exportCsv`).
  - Corrected CSV export to accurately respect all active query filters: search keyword, branch ID, appointment status (`booking_status`), payment status (`payment_status`), appointment date ranges (`injection_date_from` and `injection_date_to`), and specific day/month/year selections.
  - Added missing `injection_date_to` date range filter parsing to ensure precise historical reporting.
* **Footer Deduplication & Modern CSS Redesign (`app.blade.php`, `style.css`)**:
  - Removed redundant duplicated branch addresses from the right-hand legal column in the website footer.
  - Redesigned company legal panel with structured iconography (business certificate, headquarters, email, hotline).
  - Fixed uneven branch card stretched span on odd-number branches, establishing clean balanced grid alignments and smooth micro-interaction hover elevations.
  - Implemented 4-card maximum grid logic for footer branches: displays up to 4 branches if total <= 4; if > 4, displays the top 3 branches and a dedicated interactive 4th card linking to the full centers map page (`route('contact')`).
  - Synchronized exact color hierarchy, font styling (Medicare Gold `#eaaa00` title), solid borders, and iconography of the 4th "HỆ THỐNG TẤT CẢ CHI NHÁNH" card to perfectly match the other 3 branch cards.
  - Aligned top headers (`.footer-branches-title` and `.footer-policy-links`) to exactly match top borders and ensure 100% horizontal alignment between the branch cards and the legal information panel.

## [v6.5.27] - 2026-08-12

### Automated Stock Shortage Alerts & 20:30 Evening Next-Day Cutoff Analysis

* **Vaccine Shortage Analysis & 20:30 Cutoff Rule (`AdminDashboardController.php`, `dashboard.blade.php`)**:
  - Implemented automated stock shortage detection that reconciles upcoming injection appointment demands against available active inventory lots (`inventory_lots.available_quantity`) per branch.
  - Added an intelligent time rule: Before 20:30, the system tracks today's injection appointments; **from 20:30 onward**, it automatically shifts to analyzing next-day (tomorrow's) injection demands so medical staff can proactively identify stock shortages and arrange replenishments before morning clinics.
  - Rendered prominent real-time shortage alert banners highlighting the specific vaccine name, branch, required doses, currently available doses, and exact shortage quantity needed.
  - Refined CSS, removed redundant date/label repetitions, and added instant action buttons for immediate replenishment.
  - Added direct quick links to the Vaccine Management panel (`admin.vaccines.index`) with quick vaccine searching and date-filtered registration lists.
  - Fixed single-day / 1-point SVG trend chart calculation: centered single node at `x=375`, replaced skewed triangle with a balanced centered pillar and horizontal guideline, and optimized node label vertical offsets to eliminate text collisions.
  - Dynamically reconciled vaccine inventory status against daily scheduled injection demands (`AdminVaccineController.php`, `_table.blade.php`, `index.blade.php`): displayed "Đầy đủ" when inventory >= scheduled demand, and "Thiếu" when inventory < scheduled demand (including exact shortage count e.g., "Thiếu (-X)", and "Thiếu (0)" when available stock is zero and there is no current-day demand, completely replacing the "Hết hàng" badge for clean UX consistency).
  - Implemented real-time two-way synchronization between `InventoryLot` and `CenterVaccine` (`InventoryLot.php` model booted hook), ensuring stock quantities displayed across all admin management screens and shortage alerts are 100% consistent.
  - Redesigned and aligned the Admin Dashboard filter form layout with custom responsive CSS (`dashboard.blade.php`): set balanced fixed widths for Center and Date inputs, pushed search/clear action buttons cleanly to the right, and optimized wrap behavior on mobile screens.
  - Simplified stock shortage logic: reverted the complex multi-batch lot system in favor of direct vaccine inventory checks (`center_vaccines.stock_quantity`), ensuring the Admin Dashboard and Vaccine Management screens display identical simple warehouse stock counts.
  - Fixed double select dropdown duplication on the Add/Edit Vaccine form (`_form.blade.php`) by adding the `no-custom-select` class to the select inputs, completely bypassing conflict with the global layout custom select initializer.
  - Resolved action dropdown menu clipping/cutting-off issue and vertical scrollbar in tables (`admin.blade.php` layout): added CSS styles for `.action-dropdown-menu.dropup`, updated the toggle JS handler to position menus upwards (`dropup`) dynamically when rendering near bottom viewport, and set `overflow: visible !important;` on the table responsive wrapper at desktop screens (`@media (min-width: 1024px)`) to keep absolute menus floating over the card without triggering any browser vertical/horizontal scrollbars, while preserving full touch-scrollability on mobile.
  - Fixed form deletion submission error on the Vaccine list (`_table.blade.php`): resolved a Chrome/Edge browser quirk where disabling submit buttons synchronously cancelled form navigation by wrapping it in `setTimeout(..., 0)` asynchronously, and resolved a race condition where the double-submit protection listener incorrectly intercepted and blocked the subsequent submission triggered after clicking "Xác nhận" on the custom `AppDialog.confirm` modal.
  - Reconciled registration and customer data consistency: ran a database repair migration script to backfill `customer_id` associations for 57 orphaned bookings using normalized patient phone numbers, and updated demo seeders (`seed_shortage_demo.php`, `seed_50_shortage_demo.php`) to automatically generate and link corresponding Customer records for all test registrations.
  - Implemented automatic database-level customer synchronization in the `Registration` model (`Registration.php` booted saving hook): automatically normalizes patient phone numbers and finds or creates associated `Customer` records during any create or update event.
  - Resolved patient identification duplication for shared family phone numbers (`Patient.php` model): updated `findOrCreateCentralized` to identify unique individuals using the combination of phone number, full name, and birth date, preventing different family members who share a single phone number from being merged into one record.
  - Reset and re-seeded the database: cleaned up all test registrations, patients, and customers, and re-ran the updated 50 shortage registrations seeder to populate clean, fully synchronized, and differentiated data.
  - Integrated Clinical Workflow Control Center on registration detail page (`show.blade.php`): added step-by-step interactive panels for Patient Check-in, Clinical Screening (eligible/deferred/contraindicated status and notes), and Vaccine Administration execution (lot-selection, vaccinator assignment, observation timer) to ensure clinical history (`administered_doses`) and stock movements are properly captured and synchronized.
  - Enhanced Patient Profile index page with advanced filters and AJAX pagination (`AdminPatientController.php`, `index.blade.php`, `_table.blade.php`): integrated branch selection (for Super Admins) and registration date range filters (`from_date` / `to_date`), implemented `withQueryString()` to preserve search criteria across pages, and integrated AJAX table reloading for a fast SPA-like user experience.

## [v6.5.26] - 2026-08-12

### Custom Date Range Filtering & Enhanced Admin Analytics

* **Custom Date Range Filter (`AdminDashboardController.php`, `dashboard.blade.php`)**:
  - Added "Từ ngày" (`from_date`) and "Đến ngày" (`to_date`) date range selectors to the admin dashboard filter form.
  - Added one-click quick preset buttons: "Hôm nay", "7 ngày qua", "30 ngày qua", "Tháng này", and "Tháng trước".
  - Dynamically recalculates all summary statistics (Total Registrations, Paid Revenue, Pending, Completed, Consultations) and plots daily trend curves for the selected custom date interval.
* **Enhanced Dual-Line Trend Chart (`dashboard.blade.php`)**:
  - Rendered **Đường Doanh Thu (Revenue Line)** in bold Medicare Red (`#c8102e`) with a soft glow area fill and white/red node indicators.
  - Rendered **Đường Lượt Đăng Ký (Registration Line)** in Medicare Navy (`#004b8f`) dashed style with gold node indicators (`#eaaa00`).
  - Added an interactive floating tooltip card displaying exact revenue (VND) and registration counts per day/month on hover.
  - Added dynamic summary total metrics (Total Revenue & Total Registrations) directly above the chart.
* **Dashboard Stat Cards (`dashboard.blade.php`)**:
  - Removed redundant "Yêu Cầu Tư Vấn" and "Tồn Kho Vắc Xin" stat cards from the admin dashboard grid.

## [v6.5.25] - 2026-08-12

### Refined Loyalty Service, FIFO Ledger Allocations & Strict Settings Validation

* **Loyalty Service Integration (`RegistrationPaymentService.php`, `AdminCustomerController.php`, `Customer.php`, `_table.blade.php`)**:
  - Refactored payment settling, refunding, and quoting to delegate completely to `LoyaltyService`.
  - Replaced raw point sums with allocation-aware balance calculation to hide expired points and prevent invalid usage.
* **Atomic Settings Transactions (`AdminLoyaltySettingController.php`)**:
  - Wrapped settings updates, resets, and synchronization logic inside database transactions to ensure database consistency.
* **BPS Redemption Schema (`loyalty.blade.php`, `registrations/show.blade.php`)**:
  - Implemented separate inputs for VND and percent-based BPS (Basis Points) point redemption settings.
  - Dynamically calculated maximum percent/amount discount limitations for branch checkouts.
* **Comprehensive Testing (`LoyaltyPointsDynamicConfigTest.php`)**:
  - Added new integration test cases for FIFO lot consumption, refunds restoring original point lots, earn reversals on spent points, and BPS rounding.

## [v6.5.24] - 2026-08-12

### Dynamic Loyalty Point Configurations & FIFO Expiration

* **Dynamic Configurations (`RegistrationPaymentService.php`, `AdminLoyaltySettingController.php`, `loyalty.blade.php`)**:
  - Replaced hardcoded point rules with dynamic DB configurations stored as a JSON object under the `loyalty_settings` key.
  - Added options for minimum order amounts to earn/redeem points, adjustable redemption types (fixed VND value or invoice percentage), and custom expiry months.
  - Implemented Shopee-style membership tiers (Bronze, Silver, Gold, Diamond) based on historic point balance and active promotional campaigns.
  - Implemented birthday multiplier for patients matching their birth date with the injection date.
  - Applied additive calculation logic for active multipliers: `1.0 + SUM(Multiplier_i - 1.0)`.
* **Hierarchical Configurations & Sync Mechanism (`RegistrationPaymentService.php`, `AdminLoyaltySettingController.php`, `loyalty.blade.php`, `routes/web.php`, `layouts/admin.blade.php`)**:
  - Allowed branch-specific configurations stored as `loyalty_settings_center_{id}` with system-wide settings fallback.
  - Exposed the Loyalty configuration panel to Branch Administrators.
  - Implemented a sync warning banner notifying branch admins of global configuration updates.
  - Designed a synchronization modal allowing partial (basic settings, redemption, tiers, campaigns, or birthday) or full sync from the system settings.
* **FIFO Expiration Queue (`RegistrationPaymentService.php`, `PointTransaction.php`)**:
  - Added an `expired_at` column to the `point_transactions` table.
  - Developed a FIFO point allocation algorithm that consumes oldest points first and excludes expired points when calculating available point balance.
* **Point Adjustments with Expiry Dates (`AdminCustomerController.php`, `admin/customers/show.blade.php`)**:
  - Enhanced manual point adjustment form to dynamically display an "Expiry Date" field when adding points, and hide it when deducting points.
  - Added the Expiry Date column to the admin client points history list.

## [v6.5.23] - 2026-08-12

### Visual Live Editor Integration & Web Configuration Synchronization

* **Visual Live Page Customizer (`AdminLiveEditorController.php`, `admin/live_editor.blade.php`, `admin/live_editor_modals.blade.php`, `routes/web.php`, `layouts/admin.blade.php`)**:
  - Ported the Live Editor feature from the `hongphuoc` branch to `master` under secure `super.admin` access.
  - Added a responsive, modern sidebar link for the Visual Live Editor.
* **Synchronized Web Settings & Headquarters Address (`AdminSettingController.php`, `admin/settings/index.blade.php`)**:
  - Integrated the "Headquarters Address" (`address`) configuration field into the admin settings view and controller.
  - Kept settings routes protected under `super.admin` middleware and controller-level check blocks to maintain security compliance.

## [v6.5.22] - 2026-08-12

### Master All-Branches Aggregated Vaccine View & Pricing Segregation

* **Consolidated Multi-Branch View (`AdminVaccineController.php`, `admin/vaccines/_table.blade.php`, `admin/vaccines/index.blade.php`)**:
  - **Single Master Vaccine Row**: When selecting "Tất cả chi nhánh" in admin vaccine management, each vaccine is presented as exactly 1 row (instead of repeating across all branches).
  - **Aggregated Total Inventory**: Calculates and displays the total system-wide stock (`SUM(stock_quantity)`) across all active branches.
  - **Dynamic Price Segregation**: Replaces single price with "Theo từng chi nhánh" badge when viewing across all branches, while displaying branch-specific prices and promo rates when filtering by a specific branch.

## [v6.5.21] - 2026-08-12

### Refined Quick Navigation Branch Card Subtitle

* **Refined Homepage Action Toolbar (`partials/home/quick_booking.blade.php`)**:
  - Replaced long verbose concatenation of all branch names with a concise, professional subtitle ("Xem địa chỉ & bản đồ chỉ đường") consistent with other quick navigation cards.

## [v6.5.20] - 2026-08-12

### Exact Booking Code Lookup & Multi-Record Filter Toolbar

* **Exact Code Matching (`VaccineController.php`)**:
  - When a `registration_code` is entered along with the phone number, the query strictly targets and displays only that exact booking record, unmasked with full appointment details.
* **Interactive Multi-Booking Filter & Search Bar (`booking_lookup.blade.php`)**:
  - When querying by phone number alone, added a real-time filter bar with an instant text search box (patient name, center, code) and status pills (`Tất cả`, `Chờ xác nhận`, `Đã xác nhận`, `Đã hoàn tất`, `Đã hủy`), allowing users to easily narrow down results without clutter.

## [v6.5.19] - 2026-08-12

### System-Wide Multi-Click Prevention & Idempotent Transaction Guards

* **Global Double-Submit Prevention (`layouts/admin.blade.php`, `public/js/app.js`)**:
  - **Form Submission Lock**: Intercepts all administrative and public forms; immediately locks the form (`data-submitting="true"`), disables submit buttons, and prevents rapid consecutive clicks from sending duplicate requests.
  - **SPA Booking & Consultation Guards**: Added `isSpaBookingSubmitting` and `isSpaConsultSubmitting` state locks preventing multiple submissions during active API requests.
* **Backend Idempotency for Points & Payments (`AdminCustomerController.php`, `RegistrationPaymentService.php`)**:
  - **Idempotent Points Adjustment**: Implemented `idempotency_key` and unique `source_key` enforcement via `PointTransaction::firstOrCreate()` to guarantee that rapid repeat requests for the same adjustment only execute once.
  - **Cleaned Test Data**: Removed duplicate adjustment records from rapid clicks.

## [v6.5.18] - 2026-08-12

### Form State Preservation When Switching Branches in SPA Booking Modal

* **Seamless Field Retention on Center Change (`public/js/app.js`)**:
  - **Full Patient & Guardian State Persistence**: Captures all entered patient records (Name, Phone, DOB, Gender, Address, Vaccine selections) and Guardian info before updating the center session.
  - **Automatic Form Restoration**: Re-applies all preserved values into their respective form fields, custom datepickers, custom dropdowns, and vaccine checkboxes after re-rendering the new branch's schedule and pricing.

## [v6.5.17] - 2026-08-12

### Reactive Time-Slot Dropdown Synchronization & Booking Submission Flow Guard

* **Reactive Dropdown Re-rendering (`public/js/app.js`)**:
  - **Slot Dropdown Update Sync**: Hooked `slotSelect.updateMedicareCustomSelect()` into `changeSpaDateFilter` so selecting a vaccination date immediately unlocks and populates the custom Time-Slot dropdown with available hourly slots.
  - **Robust Pre-submission Validation**: Added strict client-side guards for slot selection, patient information (name, phone, dob, address), minor guardian requirements, and phone format checks before sending the booking payload to `/register`.

## [v6.5.16] - 2026-08-11

### Smart Direction-Aware Auto-Flipping for Dropdowns & DatePickers

* **Adaptive Dropdown Positioning (`public/js/app.js`, `layouts/admin.blade.php`, `partials/modal-register.blade.php`)**:
  - **Dynamic Direction Auto-Flipping**: Automatically detects available container/viewport height (`spaceBelow < 220px`); opens popups upwards (`bottom: calc(100% + 4px)`) when near the bottom of the modal, completely preventing dropdown menus from being clipped or obscured by modal boundaries.
  - **Modal Content Bottom Padding**: Increased bottom padding to `80px` in `#spaRegisterBody` ensuring lower form inputs have ample breathing room.

## [v6.5.15] - 2026-08-11

### Refined Custom Dropdown Alignment, Arrow Sizing & Popup Scrollbars

* **Standardized Custom Dropdown Styling (`public/js/app.js`, `public/css/style.css`, `layouts/admin.blade.php`)**:
  - **Fixed Chevron Arrow Distortion**: Added `flex-shrink: 0; min-width: 18px;` and `min-width: 0;` on the text label to prevent long option text from crushing the dropdown arrow icon.
  - **Suppressed Native Popup Scrollbars**: Applied `scrollbar-width: none;` and `::-webkit-scrollbar { display: none; }` on `.medicare-select-popup` for a clean, modern scroll experience.
  - **Refined Selection Highlighting**: Styled active items with soft brand tint (`rgba(200, 16, 46, 0.08)`) and Medicare Red text (`#c8102e`), while aligning text cleanly with `justify-content: space-between`.

## [v6.5.14] - 2026-08-11

### Removed Default Browser Scrollbars from SPA Registration Modal

* **Hidden Native Scrollbars (`public/css/style.css`, `partials/modal-register.blade.php`)**:
  - **Applied Seamless Scrollbar Suppression**: Added `scrollbar-width: none;` and `::-webkit-scrollbar { display: none; }` across `.spa-modal-overlay`, `.spa-modal-card`, and `#spaRegisterBody`.
  - **Maintained Fluid Scrolling**: Preserved smooth mouse wheel, trackpad, and touch scrolling while eliminating the unsightly browser scrollbar thumb and track.

## [v6.5.13] - 2026-08-11

### Simplified Phone Validation to Strict 10-Digit Starting with 0 Rule

* **Simplified Vietnamese Phone Format Rule (`public/js/app.js`)**:
  - **10-Digit Zero-Prefix Requirement (`/^0[0-9]{9}$/`)**: Refactored `validatePhoneNumberField` to strictly validate that the input contains exactly 10 numeric digits starting with `0`.
  - **Immediate Real-Time Feedback**: Automatically warns if the phone does not meet the 10-digit starting with 0 requirement on blur or submit.

## [v6.5.12] - 2026-08-11

### Definitive JavaScript Syntax Repair & Unconditional Script Initialization

* **Complete Script Syntax Closure & Top-Level Execution (`public/js/app.js`)**:
  - **Closed Nested Loop Block**: Added the missing closing brace in `submitSpaRegistrationForm` patient validation loop that previously broke JavaScript parsing at page load.
  - **Removed Conditional Guard Blockers**: Removed outer script wrapper to allow all functions (`toggleCart`, `toggleCartDrawer`, `initGlobalMedicareDatePickers`, `validatePhoneNumberField`) to initialize unconditionally at top-level scope.
  - **Verified Function Availability**: Confirmed via standalone node validation that `window.toggleCart` and `window.toggleCartDrawer` are 100% executable and reactive.

## [v6.5.11] - 2026-08-11

### Global Scope Export Consolidation for Cart & Vaccine Selection Handlers

* **Comprehensive Window Scope Exports (`public/js/app.js`)**:
  - **Exposed Core User Action Handlers**: Fully registered `toggleCart`, `toggleCartDrawer`, `toggleHeaderCartDropdown`, `clearCartUI`, `setCartSelection`, and modal controllers directly onto `window`.
  - **Guaranteed Inline HTML Event Binding**: Ensures all `onclick="toggleCart(id)"` handlers in vaccine cards, catalog grid, and detail views execute immediately without scope isolation issues.

## [v6.5.10] - 2026-08-11

### Resolved Script Syntax Closure to Restore Cart & Vaccine Selection Handlers

* **Restored JavaScript Execution (`public/js/app.js`)**:
  - **Fixed Unclosed Block Closure**: Resolved missing block terminator in `public/js/app.js`, restoring complete JavaScript execution across the frontend.
  - **Verified Cart & Booking Interactions**: Re-enabled instant cart drawer toggling, vaccine checkbox selection, and SPA registration modal flows.

## [v6.5.9] - 2026-08-11

### Instant Real-Time Phone Validation on Blur (`focusout`) & Input

* **Live Client-Side Vietnamese Phone Number Validation (`public/js/app.js`, `register.blade.php`)**:
  - **Instant Blur Validation (`validatePhoneNumberField`)**: Added global event listener to automatically validate phone format (`10 digits`, starting with `03, 05, 07, 08, 09, 02` or `+84/84`) as soon as the user finishes typing and clicks outside (`blur` / `focusout`).
  - **Visual Error Indicators & Live Recovery**: Highlights invalid phone inputs with a red border (`#ef4444`) and inline warning message; automatically restores neutral/green states on user input or valid entry.
  - **Comprehensive Form Guard**: Embedded phone format pre-checks before submission in both public registration form and SPA drawer modal.

## [v6.5.8] - 2026-08-11

### Fixed Tab Navigation Layout Shift in SPA Registration Modal

* **Standardized Tab Container & Label Alignment (`public/js/app.js`)**:
  - **Identical Centered Container**: Unified tab bar header layout with `justify-content: center;` across both registration (`renderSpaRegisterForm`) and consultation views (`renderSpaConsultForm`), eliminating horizontal position jumping.
  - **Consistent Tab Naming & Fixed Whitespace**: Standardized Tab 2 label to `"2. Tư vấn Y khoa qua Zalo Official"` and applied `white-space: nowrap;` across all tab buttons to ensure zero layout shifting during tab switching.

## [v6.5.7] - 2026-08-11

### Direct Keyboard Input & Auto-Masking Support for DatePicker Component

* **Keyboard Typing & Visual Calendar Picker Hybrid (`public/js/app.js`, `layouts/admin.blade.php`, `register.blade.php`)**:
  - **Direct Keyboard Input (`medicare-datepicker-input`)**: Replaced non-editable trigger span with an interactive text input (`dd/mm/yyyy`), allowing users to type dates directly using keyboard or numbers.
  - **Smart Input Masking & Auto-Formatting**: Automatically inserts slashes (`/`) as users type digits, parses valid `DD/MM/YYYY` dates, and synchronizes with underlying hidden ISO date inputs (`YYYY-MM-DD`).
  - **Seamless Popup Calendar Synchronization**: Clicking the calendar icon or trigger still displays the popup calendar, and choosing dates updates the text box and triggers input/change events for real-time age verification and form submission.

## [v6.5.6] - 2026-08-11

### Center Management Active Status Toggle & Reactivation Feature

* **Reactivation & Status Toggling for Centers (`admin/centers`)**:
  - **Center Toggle Status Action & Endpoint**: Added `POST /admin/centers/{id}/toggle-status` route and `AdminCenterController::toggleStatus` method to support both pausing (`Tạm dừng` / `Ngừng`) and reactivating (`Bật lại`) branches.
  - **Dynamic Action Button Display in Center Table**: Updated [admin/centers/_table.blade.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/resources/views/admin/centers/_table.blade.php) to display the red `Ngừng` action button for active centers and the green `Bật lại` action button (`.btn-action-sm.btn-action-success`) for suspended/inactive centers.
  - **CSS Styling & Automated Feature Tests**: Added `.btn-action-sm.btn-action-success` styling in [layouts/admin.blade.php](file:///c:/Users/Admin/Desktop/tiemchung/modules/VaccineRegistration/resources/views/layouts/admin.blade.php) and verified complete deactivation and reactivation flow via automated feature test `test_toggle_status_can_deactivate_and_reactivate_center`.

## [v6.5.5] - 2026-08-11

### Robust Safeguards for Cart Drawer and SPA Register Modal Event Handlers

* **Fixed JavaScript Initialization and Event Binding**:
  - **Resolved Unclosed Block & Missing Export Reference (`public/js/app.js`)**: Fixed block closure in global script scope and removed discontinued `setSpaConsultType` reference preventing script execution.
  - **Safeguarded Empty `<select>` Dropdowns (`public/js/app.js`, `layouts/admin.blade.php`)**: Added safe optional chaining in `initGlobalMedicareCustomDropdowns()` to gracefully handle empty `<select>` option lists without throwing `TypeError`.
  - **Verified Smooth Cart Drawer & SPA Modal Interactions**: Confirmed zero console errors, instant header cart drawer toggling, and seamless SPA register modal opening.

## [v6.5.4] - 2026-08-11

### System-Wide Custom Select & Datepicker Standardization Across Client & Admin Pages

* **Comprehensive Dropdown & Datepicker Component Refactoring**:
  - **Dynamic Re-rendering for Client & Admin Dropdowns (`public/js/app.js`, `layouts/admin.blade.php`)**: Enhanced `initGlobalMedicareCustomDropdowns()` and `initGlobalMedicareDatePickers()` to automatically convert all native `<select>` dropdowns and `<input type="date">` fields into floating Medicare custom card components with 8px rounded option cards, soft Medicare red highlight for active options, animated arrow indicators, and dynamic re-rendering capability when option lists update asynchronously.
  - **Zero Browser Native Popups**: Replaced all native browser select popups and `mm/dd/yyyy` datepickers across both public client forms (`/register`, SPA Modal, `/vaccines`, `/disease`) and admin control panels (`/admin/registrations`, `/admin/centers`, `/admin/leads`, `/admin/users`, `/admin/audit-logs`).

## [v6.5.3] - 2026-08-11

### Precise Button & Tab Centering Across SPA Modal & Public Lookup Forms

* **Centered All Form Action Buttons & SPA Navigation Tabs**:
  - **SPA Register Modal Alignment (`public/js/app.js`, `register.blade.php`)**: Center-aligned modal navigation tabs (`1. Đăng ký tiêm chủng...` / `2. Gửi yêu cầu...`), the `+ Thêm người tiêm khác` action button, and the `Hoàn tất đặt lịch` checkout submit button.
  - **Public Booking Lookup Form (`booking_lookup.blade.php`, `public/css/style.css`)**: Centered icon and label inside `.btn-primary-header` button with `justify-content: center;`.

## [v6.5.2] - 2026-08-11

### Full Expansion of Button & Form Element Standardization to Public User Pages

* **Standardized All Public Client Forms & Buttons**:
  - **Checkout & Consultation Forms (`register.blade.php`, `contact.blade.php`)**: Unified all form submit buttons, action toggles, and consultation form elements to clean `8px` border-radius, `42px-46px` heights, and brand-approved Medicare Red `#c8102e` & Navy `#004b8f`.
  - **Booking Lookup (`booking_lookup.blade.php`) & Modal Forms (`modal-register.blade.php`)**: Harmonized all search/lookup inputs, submit buttons, and status badges to `8px`/`6px` border-radius, eliminating outdated pill shapes and irregular offsets.

## [v6.5.1] - 2026-08-11

### System-wide Admin Action Button Clusters Standardization

* **Harmonized Action Columns Across All Admin Tables**:
  - **Standardized Right-Aligned Table Action Columns**: Re-aligned `Thao tác` action columns across all admin tables (`admin/registrations/_table`, `admin/leads/_table`, `admin/customers/_table`, `admin/centers/_table`, `admin/banners/index`, `admin/articles/index`, `admin/users/index`, `admin/audit-logs/index`) strictly to the right boundary with `text-align: right; white-space: nowrap;`.
  - **Consistent Button Sizing & Palette**: Unified all row action buttons (`Chi tiết`, `Sửa`, `Xóa`) to 30px height, 6px border-radius, clean 12px font size, and brand-approved Medicare colors (Medicare Navy `#004b8f` for primary actions, Medicare Red `#c8102e` for delete/danger actions).

## [v6.5.0] - 2026-08-11

### Comprehensive UI/UX Standardization Across All Schedule Views

* **System-wide Button Cluster & Toolbar Standardization**:
  - **Unified `/admin/schedule` Toolbar**: Redesigned top navigation bar on Weekly Appointment Schedule to match `/admin/schedules` with left-aligned week info display badge + branch selector, and right-aligned segmented navigation controls (`Tuần trước`, `Tuần hiện tại`, `Tuần sau`) + DatePicker.
  - **Standardized Day Cards & Action Toolbars**: Harmonized all sub-buttons (`Mở cửa`/`Đóng cửa`, `+ Thêm giờ`, `Sao chép`, `Xóa lịch ngày`) across weekly calendar cards with unified `6px` border-radius, 30px height, clean typography, and Medicare brand palette.

## [v6.4.9] - 2026-08-11

### Refined Filter Bar UX & Removal of Cluttered Dropdowns

* **Replaced Cluttered 3-Select Dropdowns (`NGÀY`, `THÁNG`, `NĂM`) with Medicare DatePickers**:
  - **Removed Redundant Select Elements**: Eliminated the outdated 3-dropdown selects (`filter_day`, `filter_month`, `filter_year`) across Admin Registrations, Customers, Consultation Leads, and Centers views.
  - **Streamlined Date Range Filtering**: Implemented intuitive `Từ ngày` (`from_date`) and `Đến ngày` (`to_date`) Medicare DatePickers, eliminating invalid calendar combinations (e.g. Feb 31st) while retaining full backward compatibility in all Admin Controllers.

## [v6.4.8] - 2026-08-11

### UX & Layout Separation Upgrade

* **Complete Separation of Schedule Information Display & Navigation Filter Controls (`/admin/schedules`)**:
  - **Left-aligned Info Display Section**: Positioned `Tuần từ 10/08/2026 - 16/08/2026` title badge prominently on the left alongside the branch selector and settings button, clearly indicating the active viewing context.
  - **Right-aligned Navigation Control Group**: Grouped navigation action controls (`Tuần trước`, `Tuần hiện tại`, `Tuần sau`) into a seamless segmented button bar pinned to the right edge alongside the date jump picker, creating an intuitive 2-column flex layout.

## [v6.4.7] - 2026-08-11

### UI Redesign & Standardization

* **Schedule Navigation Control Group Redesign (`/admin/schedules`)**:
  - **Unified Height & Border Radius**: Standardized all week navigation buttons (`Tuần trước`, `Tuần hiện tại`, `Tuần sau`), week range badge (`10/08/2026 – 16/08/2026`), and DatePicker control to a clean `38px` height and `8px` rounded corners.
  - **Harmonious Palette & Iconography**: Formatted active `Tuần hiện tại` button with Medicare Navy tint (`#eff6ff`, border `#93c5fd`, text `#004b8f`), replaced raw text arrows with SVG Lucide `chevron-left` and `chevron-right` icons, and centered elements along a single horizontal axis.

## [v6.4.6] - 2026-08-11

### Refined Alignment & UX

* **Smart Auto-Alignment for Custom Select Triggers**:
  - **Centered Default for Short Labels**: Configured `initGlobalMedicareCustomDropdowns` in `layouts/admin.blade.php` to center-align text and chevron icons for short options (such as "Tất cả", "Ngày 1", "Tháng 8", "Mới"), providing a clean, balanced pill button appearance.
  - **Automatic Dynamic Left-Alignment for Long Labels**: Automatically detects long text strings (or constrained containers) and transitions to left-aligned text with ellipsis truncation (`text-overflow: ellipsis`) and right-pinned arrow (`margin-left: auto`), preventing text overlap or layout overflow.

## [v6.4.5] - 2026-08-11

### Feature & Component Upgrade

* **Global Medicare Custom Select Dropdown Component (`initGlobalMedicareCustomDropdowns`)**:
  - **Eliminated OS Native Select Menus**: Built a global auto-initializer in `layouts/admin.blade.php` that transforms all native `<select>` tags across every admin view into custom Medicare floating dropdown components.
  - **Identical Brand Design Alignment**: Formatted expanded option popups with white background `#ffffff`, `12px` rounded corners, soft shadow (`0 15px 30px -5px rgba(0,0,0,0.15)`), Medicare Red (`#c8102e`) active state highlighting on `#fee2e2`, smooth Chevron rotation, and custom scrollbar.
  - **Full AJAX & Form Filter Integration**: Bound click events to seamlessly update hidden select element values and trigger native `change` events for 100% compatibility with AJAX filter tables.

## [v6.4.4] - 2026-08-11

### Improved & Standardized

* **System-wide Dropdown & DatePicker Standardization**:
  - **Global Select Arrow Restoration**: Removed legacy inline `style="background-image: none;"` overrides across all admin views (`vaccines`, `leads`, `registrations`, `customers`, `centers`, `banners`, `articles`, `users`, `dashboard`), restoring sleek custom Medicare Chevron Down SVG indicators.
  - **Auto-Reinit DatePickers on AJAX Updates**: Added `initGlobalMedicareDatePickers()` call inside `_ajax_filter_js.blade.php` to instantly process any dynamically updated filter controls without full page reloads.
  - **Zero Emoji Compliance**: Ensured 100% adherence to Rule 9 with clean, professional, non-cluttered admin UI styling.

## [v6.4.3] - 2026-08-11

### Improved & Fixed

* **Public Booking & Cart Auto-Open UI UX Upgrade**:
  - **Stock Inventory Initialization**: Updated default stock quantity seeding to 100 for all branch vaccines in migration, preventing out-of-stock lockouts on booking checkout (`/register`).
  - **Cart Dropdown Auto-Open Interaction**: Added auto-triggering logic in `public/js/app.js` and `disease.blade.php` to immediately open the header cart dropdown whenever a user clicks "Chọn tiêm", giving users instant visual confirmation of items added to their cart.
  - **Robust Public Checkout Submission**: Optimized `handlePublicRegisterSubmit` in `register.blade.php` to ensure reliable form submission regardless of browser dialog settings.

## [v6.4.2] - 2026-08-11

### Fixed

* **Admin UI - Smart Boundary Collision Detection for DatePicker Popups**:
  - **Dynamic Right Edge Alignment**: Added real-time viewport boundary detection (`triggerRect.left + 290 > viewportWidth`) in `layouts/admin.blade.php`. When a date picker input is situated on the far right edge of the screen (such as `/admin/schedules`), the DatePicker popup automatically aligns right (`right: 0; left: auto`), preventing popup clipping and ensuring 100% full visibility of month navigation arrows, calendar grids, and action buttons (*Xóa*, *Hôm nay*).

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
