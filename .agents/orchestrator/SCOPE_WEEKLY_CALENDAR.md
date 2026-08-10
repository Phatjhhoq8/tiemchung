# Scope: Weekly Calendar Grid Implementation

## Architecture
- **Framework**: Laravel 11.x, Blade, Tailwind CSS 3.x, Axios / SPA interactions.
- **Module**: `VaccineRegistration` (`modules/VaccineRegistration/`)
- **Key View Target**: `modules/VaccineRegistration/resources/views/admin/schedules/index.blade.php`
- **Key Controller Target**: `AdminScheduleController.php`
- **Key Models**: `Schedule`, `Slot`, `DefaultSlot`, `Center`, `Registration`
- **Key Test**: `tests/Feature/WeeklyCalendarDashboardTest.php`

---

## Milestones
| # | Name | Scope | Dependencies | Status |
|---|------|-------|-------------|--------|
| 1 | M1: Codebase Exploration | Audit current schedule controllers, routes, views, models, auto-generation commands | None | DONE |
| 2 | M2: Backend API & Business Logic | Weekly calendar data feed, slot CRUD, day open/close toggle, delete day schedule, copy schedule logic with `reserved_count > 0` validation, multi-branch RBAC | M1 | DONE |
| 3 | M3: Frontend Weekly Calendar Grid UI | 7 parallel columns (Mon-Sun), week navigation bar, add/edit/delete slot modals, copy schedule modal & AJAX integration | M2 | DONE |
| 4 | M4: Automated Test Suite & CHANGELOG | Create `tests/Feature/WeeklyCalendarDashboardTest.php` (100% pass), update `CHANGELOG.md` | M3 | DONE |
| 5 | M5: Review & Forensic Audit | Code review (APPROVE), adversarial stress testing (PASS), forensic integrity audit verification (CLEAN) | M4 | DONE |

---

## Interface Contracts
### Weekly Calendar Grid Data Feed & Operations
- `GET /admin/schedules`: Renders index view with weekly navigation (selected `week` / `date`, defaults to current week).
- `POST /admin/schedules/slots`: Create time slot for a specific day/schedule.
- `PUT/PATCH /admin/schedules/slots/{slot}`: Update time slot capacity/time range.
- `DELETE /admin/schedules/slots/{slot}`: Delete time slot.
- `POST /admin/schedules/toggle-day`: Toggle open/close day status.
- `DELETE /admin/schedules/day`: Delete all schedule/slots for a specific day.
- `POST /admin/schedules/copy`: Copy schedule from source day to target days. Validate target days: if target day has `reserved_count > 0`, return 422 error response.

### Security & Access Control Policies
- `super_admin`: Can manage schedules for all branches (dropdown selector available).
- `branch_admin`: Restricted strictly to assigned `center_id`. Cross-branch schedule access returns `403 Forbidden`.
