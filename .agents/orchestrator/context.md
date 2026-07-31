# Project Context & Requirements Mapping - Medicare Vaccination System

## Overview
This document maintains the technical context, architecture understanding, and requirement mappings for Phase 1 Refactoring of the Medicare Vaccination System (`Tiemchung`).

## Requirements vs. Component Mapping

| Req ID | Description | Target Codebase Components / Files | Acceptance Criteria |
|--------|-------------|------------------------------------|---------------------|
| **R1** | Admin Account Normalization & Security | `app/Console/Commands/CreateAdminCommand.php`, `database/migrations/*_users_table.php`, `app/Models/User.php`, `app/Http/Controllers/Auth/LoginController.php` (or similar), `app/Services/Auth/LoginService.php`, Security Logs | No auto admin/admin123, artisan command created, account locking (>5 fails), user status fields present |
| **R2** | RBAC & Multi-branch Isolation | `app/Policies/VaccinePolicy.php`, `app/Policies/CenterVaccinePolicy.php`, `app/Policies/RegistrationPolicy.php`, `app/Http/Controllers/Admin/VaccineController.php`, `app/Http/Controllers/Admin/CenterVaccineController.php`, `app/Http/Middleware/*` | `super_admin` vs `branch_admin` permissions enforced, anti-IDOR 403 on cross-branch access, master catalog protected |
| **R3** | Consultation Leads & Registration Schema Normalization | `database/migrations/*_create_consultation_leads_table.php`, `app/Models/ConsultationLead.php`, `app/Http/Controllers/ConsultationController.php`, `app/Models/Registration.php`, `app/Models/Vaccine.php`, pivot `registration_vaccines` (`quantity`) | `consultation_leads` table created, no fake registration records, pivot quantity working |
| **R4** | Content Security, Upload Hardening, URL Filtering & CSV Sanitization | `app/Services/Security/HtmlSanitizer.php`, `app/Http/Requests/UploadImageRequest.php` (or validation rules across controllers), `app/Http/Controllers/Admin/ArticleController.php`, `BannerController`, `BranchController`, `ExportController` | Stored XSS stripped from `{!! $article->content !!}`, SVG uploads rejected, URL schemes (`javascript:`, `data:`) filtered, CSV injection (`=,+,--,@`) sanitized |

## Project Rules & Constraints
- **Brand Colors**: Medicare Red (`#c8102e`), Medicare Gold (`#eaaa00`), Medicare Navy (`#004b8f`).
- **Typography**: Article details must use `text-align: justify;`, no colored callout boxes, author credit at bottom right ("Theo Bác sĩ Chuyên khoa Medicare Cờ Đỏ").
- **Icons/Emojis**: No arbitrary addition/modification of icons or emojis in UI unless requested.
- **Temporary Files**: Save under `session_data/`.
- **Database**: MySQL only. `php artisan migrate:fresh --seed` must run cleanly.
- **Changelog**: Must update `CHANGELOG.md` upon modification.
