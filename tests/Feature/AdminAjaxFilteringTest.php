<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Modules\VaccineRegistration\Models\Center;
use Modules\VaccineRegistration\Models\CenterVaccine;
use Modules\VaccineRegistration\Models\ConsultationLead;
use Modules\VaccineRegistration\Models\Customer;
use Modules\VaccineRegistration\Models\Registration;
use Modules\VaccineRegistration\Models\Vaccine;
use Modules\VaccineRegistration\Support\PhoneNormalizer;
use Tests\TestCase;

class AdminAjaxFilteringTest extends TestCase
{
    use DatabaseTransactions;

    private User $superAdmin;
    private Center $center;

    protected function setUp(): void
    {
        parent::setUp();

        $suffix = Str::random(8);
        $this->center = Center::create([
            'name' => 'Chi nhánh Test AJAX ' . $suffix,
            'slug' => 'cn-ajax-' . strtolower($suffix),
            'address' => '456 Đường AJAX, Q1',
            'phone' => '0912345678',
            'is_active' => true,
        ]);

        $this->superAdmin = User::create([
            'name' => 'Admin Test AJAX ' . $suffix,
            'username' => 'admin_ajax_' . strtolower($suffix),
            'email' => 'admin_ajax_' . strtolower($suffix) . '@medicare.test',
            'password' => bcrypt('Password123!'),
            'role' => 'super_admin',
            'is_active' => true,
            'status' => 'active',
        ]);
    }

    private function actingAsSuperAdmin()
    {
        return $this->actingAs($this->superAdmin)->withSession([
            'admin_logged_in' => true,
            'admin_user_id' => $this->superAdmin->id,
            'admin_role' => $this->superAdmin->role,
        ]);
    }

    /**
     * Test AdminRegistrationController AJAX filtering and flexible date combinations.
     */
    public function test_registrations_ajax_filtering_and_flexible_date_filters(): void
    {
        $uniqueCode = 'MCD-' . strtoupper(Str::random(6));
        $targetDate = Carbon::create(2026, 5, 15);

        $reg = Registration::create([
            'registration_code' => $uniqueCode,
            'patient_name' => 'Bệnh nhân AJAX May 15',
            'patient_phone' => '+84901112233',
            'center_id' => $this->center->id,
            'center_name' => $this->center->name,
            'injection_date' => $targetDate->toDateString(),
            'status' => 'pending',
            'booking_status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_method' => 'Tại trung tâm',
            'total_price' => 500000,
        ]);

        // 1. Standard AJAX request returns JSON with partial table HTML
        $response = $this->actingAsSuperAdmin()
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get(route('admin.registrations.index', ['search' => $uniqueCode]));

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonStructure(['success', 'html']);
        $this->assertStringContainsString($uniqueCode, $response->json('html'));

        // 2. Flexible Day only filter (day=15)
        $respDay = $this->actingAsSuperAdmin()
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get(route('admin.registrations.index', ['day' => 15, 'search' => $uniqueCode]));
        $respDay->assertOk()->assertJson(['success' => true]);
        $this->assertStringContainsString($uniqueCode, $respDay->json('html'));

        // 3. Flexible Month only filter (month=5)
        $respMonth = $this->actingAsSuperAdmin()
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get(route('admin.registrations.index', ['filter_month' => 5, 'search' => $uniqueCode]));
        $respMonth->assertOk()->assertJson(['success' => true]);
        $this->assertStringContainsString($uniqueCode, $respMonth->json('html'));

        // 4. Flexible Day + Month + Year combination (day=15, month=5, year=2026)
        $respCombo = $this->actingAsSuperAdmin()
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get(route('admin.registrations.index', [
                'filter_day' => 15,
                'filter_month' => 5,
                'filter_year' => 2026,
                'search' => $uniqueCode,
            ]));
        $respCombo->assertOk()->assertJson(['success' => true]);
        $this->assertStringContainsString($uniqueCode, $respCombo->json('html'));

        // 5. Non-matching date combination returns empty view
        $respMismatch = $this->actingAsSuperAdmin()
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get(route('admin.registrations.index', [
                'filter_day' => 20,
                'filter_month' => 5,
                'search' => $uniqueCode,
            ]));
        $respMismatch->assertOk();
        $this->assertStringContainsString('Không có đơn đặt lịch phù hợp', $respMismatch->json('html'));
    }

    /**
     * Test AdminCustomerController AJAX filtering and flexible date combinations.
     */
    public function test_customers_ajax_filtering_and_date_filters(): void
    {
        $rawPhone = '09' . rand(10000000, 99999999);
        $normalizedPhone = PhoneNormalizer::normalize($rawPhone);

        $customer = Customer::create([
            'name' => 'Khách Hàng AJAX Test',
            'phone' => $normalizedPhone,
        ]);
        $customer->created_at = Carbon::create(2026, 6, 20);
        $customer->save();

        // 1. AJAX search request
        $response = $this->actingAsSuperAdmin()
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get(route('admin.customers.index', ['search' => $rawPhone]));

        $response->assertOk()
            ->assertJson(['success' => true]);
        $this->assertStringContainsString('Khách Hàng AJAX Test', $response->json('html'));

        // 2. Day + Month + Year filter combination
        $respCombo = $this->actingAsSuperAdmin()
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get(route('admin.customers.index', [
                'search' => $rawPhone,
                'filter_day' => 20,
                'filter_month' => 6,
                'filter_year' => 2026,
            ]));
        $respCombo->assertOk()->assertJson(['success' => true]);
        $this->assertStringContainsString('Khách Hàng AJAX Test', $respCombo->json('html'));

        // 3. Mismatching month filter returns empty result
        $respMismatch = $this->actingAsSuperAdmin()
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get(route('admin.customers.index', [
                'search' => $rawPhone,
                'filter_month' => 12,
            ]));
        $respMismatch->assertOk();
        $this->assertStringContainsString('Không tìm thấy khách hàng phù hợp', $respMismatch->json('html'));
    }

    /**
     * Test AdminConsultationLeadController AJAX filtering and flexible date combinations.
     */
    public function test_consultation_leads_ajax_filtering_and_date_filters(): void
    {
        $uniqueName = 'Leads Test ' . Str::random(6);
        $lead = ConsultationLead::create([
            'name' => $uniqueName,
            'phone' => '0987654321',
            'source' => 'Website',
            'status' => 'new',
            'center_id' => $this->center->id,
        ]);
        $lead->created_at = Carbon::create(2026, 7, 10);
        $lead->save();

        // 1. AJAX search request
        $response = $this->actingAsSuperAdmin()
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get(route('admin.leads.index', ['search' => $uniqueName]));

        $response->assertOk()->assertJson(['success' => true]);
        $this->assertStringContainsString($uniqueName, $response->json('html'));

        // 2. Day & Year filter combination
        $respDate = $this->actingAsSuperAdmin()
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get(route('admin.leads.index', [
                'search' => $uniqueName,
                'day' => 10,
                'year' => 2026,
            ]));
        $respDate->assertOk()->assertJson(['success' => true]);
        $this->assertStringContainsString($uniqueName, $respDate->json('html'));
    }

    /**
     * Test AdminVaccineController AJAX filtering and date combinations.
     */
    public function test_vaccines_ajax_filtering_and_date_filters(): void
    {
        $uniqueVacName = 'Vắc xin AJAX ' . Str::random(6);
        $vaccine = Vaccine::create([
            'name' => $uniqueVacName,
            'disease_prevention' => 'Phòng cúm AJAX',
            'age_group' => 'Trẻ em',
            'origin' => 'Pháp',
            'doses' => 1,
            'price' => 350000,
            'stock_status' => 'available',
            'is_active' => true,
        ]);
        $vaccine->created_at = Carbon::create(2026, 8, 5);
        $vaccine->save();

        CenterVaccine::create([
            'center_id' => $this->center->id,
            'vaccine_id' => $vaccine->id,
            'price' => 350000,
            'stock_quantity' => 20,
            'stock_status' => 'available',
            'is_active' => true,
        ]);

        // 1. AJAX filter by search
        $response = $this->actingAsSuperAdmin()
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get(route('admin.vaccines.index', ['search' => $uniqueVacName]));

        $response->assertOk()->assertJson(['success' => true]);
        $this->assertStringContainsString($uniqueVacName, $response->json('html'));

        // 2. Month + Year filter combination
        $respMonthYear = $this->actingAsSuperAdmin()
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get(route('admin.vaccines.index', [
                'search' => $uniqueVacName,
                'filter_month' => 8,
                'filter_year' => 2026,
            ]));
        $respMonthYear->assertOk()->assertJson(['success' => true]);
        $this->assertStringContainsString($uniqueVacName, $respMonthYear->json('html'));
    }

    /**
     * Test AdminCenterController AJAX filtering and date combinations.
     */
    public function test_centers_ajax_filtering_and_date_filters(): void
    {
        $uniqueCenterName = 'Trung tâm AJAX ' . Str::random(6);
        $newCenter = Center::create([
            'name' => $uniqueCenterName,
            'slug' => 'tt-ajax-' . strtolower(Str::random(6)),
            'address' => '789 Đường AJAX Center',
            'phone' => '0933445566',
            'is_active' => true,
        ]);
        $newCenter->created_at = Carbon::create(2026, 4, 25);
        $newCenter->save();

        // 1. AJAX search request
        $response = $this->actingAsSuperAdmin()
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get(route('admin.centers.index', ['search' => $uniqueCenterName]));

        $response->assertOk()->assertJson(['success' => true]);
        $this->assertStringContainsString($uniqueCenterName, $response->json('html'));

        // 2. Day + Month + Year combination filter
        $respCombo = $this->actingAsSuperAdmin()
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get(route('admin.centers.index', [
                'search' => $uniqueCenterName,
                'filter_day' => 25,
                'filter_month' => 4,
                'filter_year' => 2026,
            ]));
        $respCombo->assertOk()->assertJson(['success' => true]);
        $this->assertStringContainsString($uniqueCenterName, $respCombo->json('html'));
    }

    /**
     * Test AJAX pagination link response and query string preservation.
     */
    public function test_ajax_pagination_link_and_query_string_preservation(): void
    {
        for ($i = 1; $i <= 25; $i++) {
            Center::create([
                'name' => 'Chi nhánh PhanTrangAJAX ' . sprintf('%02d', $i),
                'slug' => 'cn-pt-' . $i . '-' . Str::random(4),
                'address' => 'Địa chỉ test ' . $i,
                'phone' => '09000000' . sprintf('%02d', $i),
                'is_active' => true,
            ]);
        }

        $response = $this->actingAsSuperAdmin()
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get(route('admin.centers.index', [
                'search' => 'PhanTrangAJAX',
                'is_active' => '1',
                'page' => 2,
            ]));

        $response->assertOk()->assertJson(['success' => true]);
        $html = $response->json('html');

        $this->assertStringContainsString('table-modern', $html);
        $this->assertStringContainsString('page=1', $html);
        $this->assertStringContainsString('search=PhanTrangAJAX', $html);
    }

    /**
     * Test out-of-range and invalid date inputs (e.g. day=99, month=13, year=-1, day=abc).
     */
    public function test_out_of_range_and_invalid_date_inputs(): void
    {
        $uniqueCode = 'MCD-ERR-' . strtoupper(Str::random(6));
        Registration::create([
            'registration_code' => $uniqueCode,
            'patient_name' => 'Bệnh nhân Edge Case Date',
            'patient_phone' => '0901234999',
            'center_id' => $this->center->id,
            'center_name' => $this->center->name,
            'injection_date' => '2026-05-15',
            'status' => 'pending',
            'booking_status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_method' => 'Tại trung tâm',
            'total_price' => 500000,
        ]);

        $invalidParamsList = [
            ['filter_day' => 99, 'search' => $uniqueCode],
            ['filter_month' => 13, 'search' => $uniqueCode],
            ['filter_year' => -1, 'search' => $uniqueCode],
            ['filter_day' => 'abc', 'search' => $uniqueCode],
            ['filter_day' => 99, 'filter_month' => 13, 'filter_year' => -1],
        ];

        // 1. Registrations index handles out-of-range date inputs gracefully
        foreach ($invalidParamsList as $params) {
            $resp = $this->actingAsSuperAdmin()
                ->withHeaders(['X-Requested-With' => 'XMLHttpRequest', 'Accept' => 'application/json'])
                ->get(route('admin.registrations.index', $params));
            $resp->assertOk()->assertJson(['success' => true]);
            $this->assertStringContainsString('Không có đơn đặt lịch phù hợp', $resp->json('html'));
        }

        // 2. Customers index handles out-of-range date inputs gracefully
        foreach ($invalidParamsList as $params) {
            $resp = $this->actingAsSuperAdmin()
                ->withHeaders(['X-Requested-With' => 'XMLHttpRequest', 'Accept' => 'application/json'])
                ->get(route('admin.customers.index', $params));
            $resp->assertOk()->assertJson(['success' => true]);
        }

        // 3. Leads index handles out-of-range date inputs gracefully
        foreach ($invalidParamsList as $params) {
            $resp = $this->actingAsSuperAdmin()
                ->withHeaders(['X-Requested-With' => 'XMLHttpRequest', 'Accept' => 'application/json'])
                ->get(route('admin.leads.index', $params));
            $resp->assertOk()->assertJson(['success' => true]);
        }

        // 4. Centers index handles out-of-range date inputs gracefully
        foreach ($invalidParamsList as $params) {
            $resp = $this->actingAsSuperAdmin()
                ->withHeaders(['X-Requested-With' => 'XMLHttpRequest', 'Accept' => 'application/json'])
                ->get(route('admin.centers.index', $params));
            $resp->assertOk()->assertJson(['success' => true]);
        }

        // 5. Vaccines index enforces validation rules on date range (returns 422 JSON on invalid dates)
        $respVacErr = $this->actingAsSuperAdmin()
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest', 'Accept' => 'application/json'])
            ->get(route('admin.vaccines.index', ['filter_day' => 99]));
        $respVacErr->assertStatus(422)->assertJsonValidationErrors(['filter_day']);
    }

    /**
     * Test empty search inputs and special SQL wildcard characters (%, _, quotes, slashes).
     */
    public function test_special_sql_wildcard_characters_and_empty_search(): void
    {
        $specialSearches = [
            '',
            '   ',
            '%',
            '_',
            '%\'_',
            '\' OR \'1\'=\'1',
            '\\',
            '"><script>alert(1)</script>',
        ];

        $routes = [
            'admin.registrations.index',
            'admin.customers.index',
            'admin.leads.index',
            'admin.vaccines.index',
            'admin.centers.index',
        ];

        foreach ($routes as $routeName) {
            foreach ($specialSearches as $searchPayload) {
                $response = $this->actingAsSuperAdmin()
                    ->withHeaders(['X-Requested-With' => 'XMLHttpRequest', 'Accept' => 'application/json'])
                    ->get(route($routeName, ['search' => $searchPayload]));

                $response->assertOk()
                    ->assertJson(['success' => true])
                    ->assertJsonStructure(['success', 'html']);
            }
        }
    }

    /**
     * Test combined filters (search + Day + Month + Year + center_id + status).
     */
    public function test_combined_filters_matching_and_mismatching(): void
    {
        $code = 'MCD-COMBO-' . strtoupper(Str::random(6));
        $reg = Registration::create([
            'registration_code' => $code,
            'patient_name' => 'Bệnh nhân Combo Test',
            'patient_phone' => '0988776655',
            'center_id' => $this->center->id,
            'center_name' => $this->center->name,
            'injection_date' => '2026-10-25',
            'status' => 'confirmed',
            'booking_status' => 'confirmed',
            'payment_status' => 'paid',
            'payment_method' => 'Thẻ ATM / Banking',
            'total_price' => 750000,
        ]);

        // 1. All filters match
        $respMatch = $this->actingAsSuperAdmin()
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest', 'Accept' => 'application/json'])
            ->get(route('admin.registrations.index', [
                'search' => $code,
                'filter_day' => 25,
                'filter_month' => 10,
                'filter_year' => 2026,
                'center_id' => $this->center->id,
                'booking_status' => 'confirmed',
                'payment_status' => 'paid',
            ]));

        $respMatch->assertOk()->assertJson(['success' => true]);
        $this->assertStringContainsString($code, $respMatch->json('html'));

        // 2. Mismatch on status filter -> returns empty html without crash
        $respMismatch = $this->actingAsSuperAdmin()
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest', 'Accept' => 'application/json'])
            ->get(route('admin.registrations.index', [
                'search' => $code,
                'filter_day' => 25,
                'filter_month' => 10,
                'filter_year' => 2026,
                'center_id' => $this->center->id,
                'booking_status' => 'cancelled',
            ]));

        $respMismatch->assertOk()->assertJson(['success' => true]);
        $this->assertStringContainsString('Không có đơn đặt lịch phù hợp', $respMismatch->json('html'));
    }

    /**
     * Test AJAX vs standard HTTP request response structure.
     */
    public function test_ajax_vs_standard_http_request_response_structure(): void
    {
        $routes = [
            'admin.registrations.index',
            'admin.customers.index',
            'admin.leads.index',
            'admin.vaccines.index',
            'admin.centers.index',
        ];

        foreach ($routes as $routeName) {
            // 1. AJAX request returns JSON with success and html keys
            $ajaxResp = $this->actingAsSuperAdmin()
                ->withHeaders(['X-Requested-With' => 'XMLHttpRequest', 'Accept' => 'application/json'])
                ->get(route($routeName));

            $ajaxResp->assertOk()
                ->assertJson(['success' => true])
                ->assertJsonStructure(['success', 'html']);

            // 2. Standard GET request (no AJAX headers) returns HTML view
            $standardResp = $this->actingAsSuperAdmin()
                ->flushHeaders()
                ->get(route($routeName));

            $standardResp->assertOk();
            $contentType = (string) $standardResp->headers->get('Content-Type');
            $this->assertTrue(
                str_contains($contentType, 'text/html'),
                "Route {$routeName} expected text/html content type but got {$contentType}"
            );
            $this->assertStringContainsString('<!DOCTYPE html>', $standardResp->getContent());
        }
    }
}


