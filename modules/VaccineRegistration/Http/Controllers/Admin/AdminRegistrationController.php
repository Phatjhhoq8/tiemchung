<?php
/**
 * Chức năng: AdminRegistrationController quản lý danh sách đăng ký tiêm chủng của khách hàng.
 * Lý do tạo: Cho phép Quản trị viên duyệt hồ sơ đăng ký tiêm, kiểm tra thông tin bệnh nhân và thay đổi trạng thái đơn tiêm.
 */

namespace Modules\VaccineRegistration\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\VaccineRegistration\Models\Registration;
use Modules\VaccineRegistration\Models\CenterVaccine;
use Modules\VaccineRegistration\Models\VaccineStockMovement;
use Modules\VaccineRegistration\Support\AdminContext;

class AdminRegistrationController extends Controller
{
    /**
     * Danh sách đăng ký tiêm chủng.
     */
    public function index(Request $request)
    {
        if (AdminContext::isBranchAdmin() && $request->filled('center_id') && (int)$request->input('center_id') !== (int)AdminContext::centerId()) {
            abort(403, 'Cross-branch access forbidden.');
        }

        $query = AdminContext::applyCenterScope(Registration::query());

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
        if (AdminContext::isBranchAdmin() && (int) $registration->center_id !== (int) AdminContext::centerId()) {
            abort(403, 'Cross-branch access forbidden.');
        }
        
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
        $registration = Registration::with('vaccines')->findOrFail($id);
        if (AdminContext::isBranchAdmin() && (int) $registration->center_id !== (int) AdminContext::centerId()) {
            abort(403, 'Cross-branch access forbidden.');
        }
        $oldStatus = $registration->status;

        $validated = $request->validate([
            'status' => 'required|string|in:Chờ thanh toán,Đã thanh toán,Đã tiêm,Đã hủy,Chờ tư vấn,Đã tư vấn',
        ], [
            'status.required' => 'Vui lòng chọn trạng thái.',
            'status.in' => 'Trạng thái không hợp lệ.'
        ]);

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($registration, $validated, $oldStatus) {
                $registration->update([
                    'status' => $validated['status']
                ]);

                if ($oldStatus !== $validated['status']) {
                    \App\Services\AuditLogger::logOrderStatusUpdate(
                        resourceId: $registration->id,
                        oldValues: ['status' => $oldStatus],
                        newValues: ['status' => $validated['status']],
                        centerId: $registration->center_id
                    );
                }

                $wasPaidOrInjected = in_array($oldStatus, ['Đã thanh toán', 'Đã tiêm'], true);
                $isPaidOrInjected = in_array($validated['status'], ['Đã thanh toán', 'Đã tiêm'], true);

                if ($wasPaidOrInjected && !$isPaidOrInjected) {
                    \App\Services\AuditLogger::logRefund(
                        resourceId: $registration->id,
                        oldValues: ['status' => $oldStatus, 'total_price' => $registration->total_price],
                        newValues: ['status' => $validated['status']],
                        centerId: $registration->center_id
                    );
                }

                if (!$wasPaidOrInjected && $isPaidOrInjected) {
                    // Trừ tồn kho
                    foreach ($registration->vaccines as $vaccine) {
                        $centerVaccine = CenterVaccine::where('center_id', $registration->center_id)
                            ->where('vaccine_id', $vaccine->id)
                            ->lockForUpdate()
                            ->first();

                        if ($centerVaccine) {
                            if ($centerVaccine->stock_quantity < 1) {
                                throw \Illuminate\Validation\ValidationException::withMessages([
                                    'status' => "Vắc xin '{$vaccine->name}' tại chi nhánh hiện tại đã hết hàng."
                                ]);
                            }

                            $oldStock = (int) $centerVaccine->stock_quantity;
                            $centerVaccine->stock_quantity = (int) $centerVaccine->stock_quantity - 1;
                            $centerVaccine->stock_status = $centerVaccine->stock_quantity <= 0 ? 'out_of_stock' : ($centerVaccine->stock_quantity <= 5 ? 'limited' : 'available');
                            $centerVaccine->save();

                            \App\Services\AuditLogger::logStockUpdate(
                                resourceId: $centerVaccine->id,
                                oldValues: ['stock_quantity' => $oldStock],
                                newValues: ['stock_quantity' => $centerVaccine->stock_quantity, 'stock_status' => $centerVaccine->stock_status],
                                centerId: $registration->center_id
                            );

                            VaccineStockMovement::create([
                                'center_id' => $registration->center_id,
                                'vaccine_id' => $vaccine->id,
                                'type' => 'sale',
                                'quantity' => 1,
                                'unit_price' => (int) ($vaccine->pivot->price ?? 0),
                                'note' => 'Ghi nhận bán từ đơn ' . $registration->registration_code,
                                'created_by' => AdminContext::user()?->id,
                            ]);
                        }
                    }
                } elseif ($wasPaidOrInjected && !$isPaidOrInjected) {
                    // Hoàn lại tồn kho
                    foreach ($registration->vaccines as $vaccine) {
                        $centerVaccine = CenterVaccine::where('center_id', $registration->center_id)
                            ->where('vaccine_id', $vaccine->id)
                            ->lockForUpdate()
                            ->first();

                        if ($centerVaccine) {
                            $oldStock = (int) $centerVaccine->stock_quantity;
                            $centerVaccine->stock_quantity = (int) $centerVaccine->stock_quantity + 1;
                            $centerVaccine->stock_status = $centerVaccine->stock_quantity <= 0 ? 'out_of_stock' : ($centerVaccine->stock_quantity <= 5 ? 'limited' : 'available');
                            $centerVaccine->save();

                            \App\Services\AuditLogger::logStockUpdate(
                                resourceId: $centerVaccine->id,
                                oldValues: ['stock_quantity' => $oldStock],
                                newValues: ['stock_quantity' => $centerVaccine->stock_quantity, 'stock_status' => $centerVaccine->stock_status],
                                centerId: $registration->center_id
                            );

                            VaccineStockMovement::create([
                                'center_id' => $registration->center_id,
                                'vaccine_id' => $vaccine->id,
                                'type' => 'import',
                                'quantity' => 1,
                                'unit_price' => (int) ($vaccine->pivot->price ?? 0),
                                'note' => 'Hoàn tồn do thay đổi trạng thái từ đơn ' . $registration->registration_code,
                                'created_by' => AdminContext::user()?->id,
                            ]);
                        }
                    }
                }
            });
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to update registration status: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi cập nhật trạng thái đơn đăng ký.');
        }

        return redirect()->route('admin.registrations.show', $id)
            ->with('success', 'Cập nhật trạng thái đơn đăng ký thành công.');
    }

    /**
     * Quản lý lịch hẹn theo ngày trong tuần.
     */
    public function schedule(Request $request)
    {
        if (AdminContext::isBranchAdmin() && $request->filled('center_id') && (int)$request->input('center_id') !== (int)AdminContext::centerId()) {
            abort(403, 'Cross-branch access forbidden.');
        }

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
        $registrations = AdminContext::applyCenterScope(Registration::with('vaccines'))
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
        if (AdminContext::isBranchAdmin() && $request->filled('center_id') && (int)$request->input('center_id') !== (int)AdminContext::centerId()) {
            abort(403, 'Cross-branch access forbidden.');
        }

        $registrations = AdminContext::applyCenterScope(Registration::with('vaccines'))->latest()->get();

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
                    $this->safeCsvCell($reg->registration_code),
                    $this->safeCsvCell($reg->patient_name),
                    $this->safeCsvCell($reg->patient_phone),
                    $this->safeCsvCell($reg->patient_email ?? ''),
                    $this->safeCsvCell($reg->patient_birth_year ?? ''),
                    $this->safeCsvCell($reg->patient_gender ?? ''),
                    $this->safeCsvCell($reg->patient_address ?? ''),
                    $this->safeCsvCell($reg->center_name),
                    $this->safeCsvCell($reg->injection_date),
                    $this->safeCsvCell((string) $reg->total_price),
                    $this->safeCsvCell($reg->status),
                    $this->safeCsvCell($vaccineNames),
                    $this->safeCsvCell($reg->created_at->format('d/m/Y H:i')),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function safeCsvCell(?string $value): string
    {
        return \App\Services\Security\CsvSanitizer::sanitizeCell($value);
    }
}
