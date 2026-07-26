<?php
/**
 * Chức năng: AdminRegistrationController quản lý danh sách đăng ký tiêm chủng của khách hàng.
 * Lý do tạo: Cho phép Quản trị viên duyệt hồ sơ đăng ký tiêm, kiểm tra thông tin bệnh nhân và thay đổi trạng thái đơn tiêm.
 */

namespace Modules\VaccineRegistration\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\VaccineRegistration\Models\Registration;

class AdminRegistrationController extends Controller
{
    /**
     * Danh sách đăng ký tiêm chủng.
     */
    public function index(Request $request)
    {
        $query = Registration::query();

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Tìm kiếm theo mã đăng ký, họ tên hoặc số điện thoại
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('registration_code', 'like', '%' . $search . '%')
                  ->orWhere('patient_name', 'like', '%' . $search . '%')
                  ->orWhere('patient_phone', 'like', '%' . $search . '%');
            });
        }

        $registrations = $query->latest()->paginate(10);
        return view('vaccine::admin.registrations.index', compact('registrations'));
    }

    /**
     * Chi tiết một đơn đăng ký tiêm chủng.
     */
    public function show($id)
    {
        $registration = Registration::with('vaccines')->findOrFail($id);
        
        $statuses = [
            'Chờ thanh toán',
            'Đã thanh toán',
            'Đã tiêm',
            'Đã hủy',
            'Chờ tư vấn',
            'Đã tư vấn'
        ];

        return view('vaccine::admin.registrations.show', compact('registration', 'statuses'));
    }

    /**
     * Cập nhật trạng thái đơn đăng ký tiêm.
     */
    public function updateStatus(Request $request, $id)
    {
        $registration = Registration::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|string|in:Chờ thanh toán,Đã thanh toán,Đã tiêm,Đã hủy,Chờ tư vấn,Đã tư vấn',
        ], [
            'status.required' => 'Vui lòng chọn trạng thái.',
            'status.in' => 'Trạng thái không hợp lệ.'
        ]);

        $registration->update([
            'status' => $validated['status']
        ]);

        return redirect()->route('admin.registrations.show', $id)
            ->with('success', 'Cập nhật trạng thái đơn đăng ký thành công.');
    }

    /**
     * Quản lý lịch hẹn theo ngày trong tuần.
     */
    public function schedule(Request $request)
    {
        $weekParam = $request->input('week');
        
        try {
            $startOfWeek = $weekParam 
                ? \Carbon\Carbon::parse($weekParam)->startOfWeek()
                : \Carbon\Carbon::now()->startOfWeek();
        } catch (\Exception $e) {
            $startOfWeek = \Carbon\Carbon::now()->startOfWeek();
        }

        $endOfWeek = $startOfWeek->copy()->endOfWeek();

        // Lấy toàn bộ đơn đăng ký trong tuần này
        $registrations = Registration::with('vaccines')
            ->whereBetween('injection_date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
            ->get();

        // Nhóm theo ngày trong tuần
        $daysOfWeek = [];
        $currentDay = $startOfWeek->copy();
        for ($i = 0; $i < 7; $i++) {
            $dateString = $currentDay->toDateString();
            $daysOfWeek[$dateString] = [
                'day_name' => $this->getVietnameseDayName($currentDay),
                'date' => $currentDay->format('d/m/Y'),
                'carbon' => $currentDay->copy(),
                'items' => $registrations->filter(fn($reg) => $reg->injection_date == $dateString)->values()
            ];
            $currentDay->addDay();
        }

        return view('vaccine::admin.schedule', compact('daysOfWeek', 'startOfWeek'));
    }

    /**
     * Dịch thứ sang Tiếng Việt.
     */
    private function getVietnameseDayName(\Carbon\Carbon $date)
    {
        return match ($date->dayOfWeek) {
            \Carbon\Carbon::MONDAY => 'Thứ Hai',
            \Carbon\Carbon::TUESDAY => 'Thứ Ba',
            \Carbon\Carbon::WEDNESDAY => 'Thứ Tư',
            \Carbon\Carbon::THURSDAY => 'Thứ Năm',
            \Carbon\Carbon::FRIDAY => 'Thứ Sáu',
            \Carbon\Carbon::SATURDAY => 'Thứ Bảy',
            \Carbon\Carbon::SUNDAY => 'Chủ Nhật',
        };
    }

    /**
     * Xuất danh sách đăng ký ra file CSV.
     */
    public function exportCsv(Request $request)
    {
        $registrations = Registration::with('vaccines')->latest()->get();

        $filename = 'don_dang_ky_tiem_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($registrations) {
            $file = fopen('php://output', 'w');
            // BOM for UTF-8 Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header row
            fputcsv($file, [
                'Mã đơn', 'Họ tên', 'Số điện thoại', 'Email',
                'Năm sinh', 'Giới tính', 'Địa chỉ',
                'Trung tâm tiêm', 'Ngày tiêm', 'Tổng tiền',
                'Trạng thái', 'Vắc xin đã chọn', 'Ngày đăng ký'
            ]);

            foreach ($registrations as $reg) {
                $vaccineNames = $reg->vaccines->pluck('name')->implode(', ');
                fputcsv($file, [
                    $reg->registration_code,
                    $reg->patient_name,
                    $reg->patient_phone,
                    $reg->patient_email ?? '',
                    $reg->patient_birth_year ?? '',
                    $reg->patient_gender ?? '',
                    $reg->patient_address ?? '',
                    $reg->center_name,
                    $reg->injection_date,
                    $reg->total_price,
                    $reg->status,
                    $vaccineNames,
                    $reg->created_at->format('d/m/Y H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
