# BRIEFING — 2026-08-01T00:45:00Z

## Mission
Implement Milestone M7: Schedules, Slots & Concurrency Control (R3, Ponytail Style) - DB migrations, models, admin management, registration slot reservation with atomic DB transaction & lockForUpdate, feature test suite.

## 🔒 My Identity
- Archetype: implementer
- Roles: implementer, qa, specialist
- Working directory: /home/hongphuoc/Desktop/thue/.agents/worker_m7
- Original parent: f558c12b-57f5-44d7-a344-10f26eb649f3
- Milestone: M7 - Schedules, Slots & Concurrency Control

## 🔒 Key Constraints
- Ponytail style: minimal, native, clean, zero over-engineering.
- Commercial production notice: Strict security, zero defect, database integrity.
- Never use unapproved colors, keep Blade styling consistent with rules.
- Test command: `/opt/lampp/bin/php ./vendor/bin/phpunit tests/Feature/SchedulesSlotsConcurrencyTest.php`.
- Must pass 100% genuine tests.

## Current Parent
- Conversation ID: f558c12b-57f5-44d7-a344-10f26eb649f3
- Updated: 2026-08-01T00:45:00Z

## Task Summary
- **What to build**:
  1. Migrations & Models for `Schedule` (`center_id`, `date`, `is_active`, `note`) and `Slot` (`schedule_id`, `start_at`, `end_at`, `capacity`, `reserved_count`, `is_active`). Add `slot_id` to `registrations`.
  2. Registration slot reservation in `DB::transaction()` using `Slot::where('id', $slotId)->lockForUpdate()->first()`.
  3. Reject reservation if `reserved_count >= capacity` with message "Khung giờ đã đầy công suất" (status 422 or redirect error).
  4. Admin Controller & Routes for schedule & slot capacity management.
  5. Feature tests `tests/Feature/SchedulesSlotsConcurrencyTest.php`.
  6. English notes in `CHANGELOG.md`.
  7. Handoff report in `.agents/worker_m7/handoff.md` and send message to parent.
- **Success criteria**: All tests pass 100%, clean code, minimal diff, documentation updated.

## Key Decisions Made
- Ponytail style: keep models, controllers, and tests lean, direct, using Laravel native features (`DB::transaction`, `lockForUpdate()`).

## Artifact Index
- `.agents/worker_m7/ORIGINAL_REQUEST.md` — Original prompt request
- `.agents/worker_m7/BRIEFING.md` — Active working memory briefing

## Change Tracker
- **Files modified**: TBD
- **Build status**: TBD
- **Pending issues**: None

## Quality Status
- **Build/test result**: TBD
- **Lint status**: OK
- **Tests added/modified**: `tests/Feature/SchedulesSlotsConcurrencyTest.php`

## Loaded Skills
- **Source**: `/home/hongphuoc/.gemini/config/skills/ponytail/SKILL.md`
- **Local copy**: `/home/hongphuoc/Desktop/thue/.agents/worker_m7/skills/ponytail.md`
- **Core methodology**: Laziest solution that actually works, native platform features over dependencies, minimal diff.
