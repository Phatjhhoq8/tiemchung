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
            'Đã hủy'
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
            'status' => 'required|string|in:Chờ thanh toán,Đã thanh toán,Đã tiêm,Đã hủy',
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
}
