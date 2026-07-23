# Release Notes

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
