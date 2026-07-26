# Release Notes

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
