# Milestone 5 Empirical Stress Testing Handoff Report

## 1. Observation

### Test Execution Commands & Outputs
Executed test commands in root directory `/home/hongphuoc/Desktop/thue`:

Command 1:
```bash
export PATH=/opt/lampp/bin:$PATH; php artisan test --filter=AdminAjaxFilteringTest
```
Output:
```text
PASS  Tests\Feature\AdminAjaxFilteringTest
✓ registrations ajax filtering and flexible date filters               0.14s  
✓ customers ajax filtering and date filters                            0.03s  
✓ consultation leads ajax filtering and date filters                   0.02s  
✓ vaccines ajax filtering and date filters                             0.04s  
✓ centers ajax filtering and date filters                              0.02s  
✓ ajax pagination link and query string preservation                   0.03s  
✓ out of range and invalid date inputs                                 0.08s  
✓ special sql wildcard characters and empty search                     0.21s  
✓ combined filters matching and mismatching                            0.02s  
✓ ajax vs standard http request response structure                     0.09s  

Tests:    10 passed (296 assertions)
Duration: 0.73s
```

Command 2:
```bash
export PATH=/opt/lampp/bin:$PATH; php artisan test
```
Output:
```text
Tests:    132 passed (1066 assertions)
Duration: 7.50s
```

### Inspected Code Locations
1. `modules/VaccineRegistration/Http/Controllers/Admin/AdminRegistrationController.php` (lines 56–68)
2. `modules/VaccineRegistration/Http/Controllers/Admin/AdminCustomerController.php` (lines 42–54)
3. `modules/VaccineRegistration/Http/Controllers/Admin/AdminConsultationLeadController.php` (lines 39–51)
4. `modules/VaccineRegistration/Http/Controllers/Admin/AdminVaccineController.php` (lines 36–41, 94–106)
5. `modules/VaccineRegistration/Http/Controllers/Admin/AdminCenterController.php` (lines 47–59)
6. `tests/Feature/AdminAjaxFilteringTest.php`

### Empirical Test Scenarios Verified
- **Out-of-range / Invalid Dates**: `filter_day=99`, `filter_month=13`, `filter_year=-1`, `filter_day='abc'`.
  - In `AdminRegistrationController`, `AdminCustomerController`, `AdminConsultationLeadController`, and `AdminCenterController`, inputs are cast to `(int)` and processed safely by `whereDay`, `whereMonth`, `whereYear` without throwing database syntax errors or 500 exceptions, returning `200 OK` JSON with `"success": true` and empty table HTML.
  - In `AdminVaccineController`, validation rules (`filter_day => between:1,31`) trigger a `422 Unprocessable Content` JSON validation error on AJAX requests, properly rejecting invalid date parameters.
- **SQL Wildcard Characters & Injection Payloads**: Tested `search` values `""`, `"   "`, `"%"`, `"_"`, `"%'_"`, `"' OR '1'='1"`, `"\\"`, and `"\"><script>alert(1)</script>"`. All 5 controllers safely handle search payloads using Eloquent parameter binding without SQL injection or syntax error vulnerabilities.
- **Combined Filters Chaining**: Tested multi-parameter queries (`search` + `filter_day` + `filter_month` + `filter_year` + `center_id` + `booking_status` + `payment_status`). Matching criteria successfully return expected records; mismatching single parameters gracefully return an empty table notice (e.g. `"Không có đơn đặt lịch phù hợp"`).
- **Pagination Query String Preservation**: Verified that paginated AJAX requests (`page=2`) maintain all query parameters (`search`, `filter_day`, `filter_month`, `filter_year`, etc.) via `->withQueryString()` in pagination link URLs.
- **AJAX vs Standard Request Response Format**:
  - `X-Requested-With: XMLHttpRequest` header produces HTTP 200 JSON `{ "success": true, "html": "<table...>...</table>" }`.
  - Standard GET request without AJAX headers produces standard HTTP 200 HTML page containing full layout structure (`<!DOCTYPE html>`).

## 2. Logic Chain

1. **Observation**: `AdminAjaxFilteringTest` was expanded to cover 10 feature test cases with 296 assertions testing out-of-range dates, SQL wildcards, combined filter chains, pagination query preservation, and response headers across all 5 admin endpoints (`registrations`, `customers`, `leads`, `vaccines`, `centers`).
2. **Step 1 Reasoning**: Out-of-range date inputs (`day=99`, `month=13`, `year=-1`) cast safely to `(int)` in controllers (or trigger `422` validation errors in `AdminVaccineController`), preventing SQL exception crashes or 500 errors.
3. **Step 2 Reasoning**: Parameterized Eloquent queries (`where('col', 'like', '%'.$search.'%')`) treat SQL wildcards (`%`, `_`) and SQL quotes as literal string bindings, preventing SQL injection and unhandled query failures.
4. **Step 3 Reasoning**: Chained Eloquent scopes construct valid SQL compound `WHERE` clauses. If all conditions match, target records are returned; if any condition mismatches, an empty table HTML snippet is returned with HTTP status 200.
5. **Step 4 Reasoning**: `paginate(N)->withQueryString()` generates pagination links that retain all active request query parameters.
6. **Step 5 Reasoning**: Header inspection in controllers (`$request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest'`) accurately differentiates AJAX requests (returning JSON partial HTML) from full-page GET requests (returning Blade layout HTML).
7. **Conclusion**: The implementation is empirically robust, secure, and fully compliant with Milestone 5 requirements.

## 3. Caveats

No caveats.

## 4. Conclusion

**Verdict: PASS**

The Real-Time AJAX Filtering & Flexible Date Filter implementation across all admin endpoints (`registrations`, `customers`, `leads`, `vaccines`, `centers`) passes all unit, feature, and empirical stress tests. Edge cases involving invalid dates, SQL wildcard characters, multi-filter combinations, pagination parameter retention, and AJAX response structures are handled safely and predictably without errors or security flaws.

## 5. Verification Method

To independently verify this result, execute the following commands in the workspace root directory (`/home/hongphuoc/Desktop/thue`):

```bash
export PATH=/opt/lampp/bin:$PATH; php artisan test --filter=AdminAjaxFilteringTest
export PATH=/opt/lampp/bin:$PATH; php artisan test
```

### Invalidation Conditions
- Any test failure in `AdminAjaxFilteringTest`.
- Any failure in the full `php artisan test` suite.
- SQL syntax error or 500 server error when passing invalid date parameters or wildcard search strings.
- Omission of active query parameters in AJAX pagination URLs.
