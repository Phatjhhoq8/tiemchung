# AI Agent Rules - Vaccine Registration Project (Tiemchung)

> **Commercial Production Notice**: This is a real-world commercial project built for client deployment and sale. Code quality, strict security, seamless SPA experience, and zero-defect data safety are top priorities.

## 1. Changelog & Documentation Updates
- **Update CHANGELOG.md**: When modifying code (adding features, fixing bugs, or configurations), immediately update the top of CHANGELOG.md (e.g., `## [v1.0.3] - YYYY-MM-DD`), detailing the changes made. **Entries must be written concisely in English.**
- **Synchronize Documentation**: Update related documentation (such as README.md) if changes affect installation or operations.

## 2. Project Technology Stack
- **Backend**: Laravel 11.x (PHP >= 8.2).
- **Dependency**: Composer (composer.json).
- **Frontend**: Vite 6.x, Tailwind CSS 3.x, Axios.
- **Database**: MySQL only, including the isolated test database.

## 3. Communication & Responses
- Keep responses concise, direct, and in Vietnamese.
- Use clickable Markdown links (`file://`, without backticks) for all file paths and code symbols.

## 4. Deployment
- Read FTP and database credentials from `.env`; never store them in project documentation or rules.
- When deploying through FTP, upload only files changed for the current task.

## 5. Coding & Architecture Principles
- **Simplicity & Reusability**: Write simple, clean, and easily understandable code. Ensure components and functions are modular and reusable.
- **Leverage Existing Capabilities**: Always prioritize using built-in framework helpers (Laravel/Blade/JS), existing models, and pre-built functions over writing custom logic from scratch. Do not reinvent the wheel.
- **Dynamic & Database-driven**: All content, settings, sections, and configuration data must be dynamically managed via the database (MySQL) instead of hardcoding in template views.
- **Single-Page Application (SPA) Experience**: Implement smooth, dynamic client-side interactions (using Fetch/AJAX, dynamic DOM updates, drawers, and modals) to deliver a fast and seamless Single-Page user experience without unnecessary full-page reloads.

## 6. Security & Commercial Production Standards
- **Strict Input Validation**: Always validate and sanitize all client input parameters using Laravel Form Requests or Validator. Never trust raw user inputs.
- **Vulnerability Prevention**: Strictly prevent SQL Injection (use Eloquent ORM / parameterized queries), XSS (escape output with Blade `{{ }}`), CSRF (include CSRF tokens in all POST/PUT/DELETE requests), and Mass Assignment ($fillable protection).
- **Authentication & Rate Limiting**: Secure all Admin endpoints with strict authentication middleware (`admin.auth`) and rate-limiting (`throttle`). Encrypt sensitive credentials using Bcrypt/Argon2.
- **Production Error Masking**: Mask raw database exceptions and sensitive stack traces from end-users in production. Log technical errors safely to system logs.


