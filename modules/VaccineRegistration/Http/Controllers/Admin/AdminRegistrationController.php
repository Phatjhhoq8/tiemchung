<?php

namespace Modules\VaccineRegistration\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditLogger;
use App\Services\RegistrationPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\VaccineRegistration\Models\Center;
use Modules\VaccineRegistration\Models\Registration;
use Modules\VaccineRegistration\Support\AdminContext;

class AdminRegistrationController extends Controller
{
    public function index(Request $request)
    {
        $this->assertRequestedCenter($request);

        $query = $this->registrationQuery($request)
            ->with('customer:id,name,phone');

        if ($request->filled('booking_status')) {
            $query->where('booking_status', $request->input('booking_status'));
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->input('payment_status'));
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($builder) use ($search) {
                $builder->where('registration_code', 'like', $search . '%')
                    ->orWhere('patient_phone', 'like', '%' . $search . '%')
                    ->orWhere('patient_name', 'like', '%' . $search . '%');
            });
        }

        $registrations = $query->latest('id')->paginate(20)->withQueryString();
        $centers = AdminContext::isSuperAdmin()
            ? Center::active()->orderBy('sort_order')->orderBy('id')->get(['id', 'name'])
            : collect();

        return view('vaccine::admin.registrations.index', compact('registrations', 'centers'));
    }

    public function show(int $id, RegistrationPaymentService $paymentService)
    {
        $registration = $this->visibleRegistration($id, ['vaccines', 'customer', 'slot.schedule']);
        $pointQuote = $registration->customer ? $paymentService->quote($registration->customer, $registration) : null;

        return view('vaccine::admin.registrations.show', compact('registration', 'pointQuote'));
    }

    public function updateStatus(Request $request, int $id, RegistrationPaymentService $paymentService)
    {
        $registration = $this->visibleRegistration($id);
        $validated = $request->validate([
            'booking_status' => 'required|in:pending,confirmed,completed,cancelled,no_show',
        ]);

        if ($validated['booking_status'] === Registration::BOOKING_CANCELLED) {
            try {
                $paymentService->cancelUnpaid($registration->id, AdminContext::user());
            } catch (ValidationException $exception) {
                return back()->withErrors($exception->errors());
            }

            return back()->with('success', 'Đã hủy lịch hẹn và giải phóng khung giờ.');
        }

        try {
            DB::transaction(function () use ($registration, $validated) {
                $locked = Registration::lockForUpdate()->findOrFail($registration->id);
                $this->assertRegistrationVisible($locked);

                if ($locked->booking_status === Registration::BOOKING_CANCELLED) {
                    throw ValidationException::withMessages([
                        'booking_status' => 'Lịch hẹn đã hủy và không thể cập nhật tiếp.',
                    ]);
                }

                if ($validated['booking_status'] === Registration::BOOKING_COMPLETED
                    && $locked->payment_status !== Registration::PAYMENT_PAID) {
                    throw ValidationException::withMessages([
                        'booking_status' => 'Chỉ có thể hoàn tất lịch hẹn sau khi đã xác nhận thanh toán.',
                    ]);
                }

                $oldStatus = $locked->booking_status;
                $locked->update([
                    'booking_status' => $validated['booking_status'],
                    'status' => $validated['booking_status'],
                ]);

                if ($oldStatus !== $validated['booking_status']) {
                    AuditLogger::logOrderStatusUpdate(
                        resourceId: $locked->id,
                        oldValues: ['booking_status' => $oldStatus],
                        newValues: ['booking_status' => $validated['booking_status']],
                        centerId: $locked->center_id,
                    );
                }
            });
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors());
        }

        return back()->with('success', 'Đã cập nhật trạng thái lịch hẹn.');
    }

    public function settle(Request $request, int $id, RegistrationPaymentService $paymentService)
    {
        $registration = $this->visibleRegistration($id);
        $validated = $request->validate([
            'redeem_points' => 'nullable|integer|min:0',
        ]);

        try {
            $paymentService->settle($registration->id, (int) ($validated['redeem_points'] ?? 0), AdminContext::user());
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors())->withInput();
        }

        return back()->with('success', 'Đã xác nhận thanh toán và cập nhật điểm khách hàng.');
    }

    public function refund(int $id, RegistrationPaymentService $paymentService)
    {
        $registration = $this->visibleRegistration($id);

        try {
            $paymentService->refund($registration->id, AdminContext::user());
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors());
        }

        return back()->with('success', 'Đã hoàn tiền, đảo điểm và hủy lịch hẹn.');
    }

    public function schedule(Request $request)
    {
        $this->assertRequestedCenter($request);
        $validated = $request->validate([
            'week' => 'nullable|date_format:Y-m-d',
        ]);
        $startOfWeek = !empty($validated['week'])
            ? \Carbon\Carbon::createFromFormat('Y-m-d', $validated['week'])->startOfWeek()
            : now()->startOfWeek();
        $endOfWeek = $startOfWeek->copy()->endOfWeek();

        $registrations = $this->registrationQuery($request)
            ->with(['vaccines', 'slot'])
            ->whereBetween('injection_date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
            ->get();

        $daysOfWeek = collect(range(0, 6))->mapWithKeys(function (int $offset) use ($startOfWeek, $registrations) {
            $date = $startOfWeek->copy()->addDays($offset);

            return [$date->toDateString() => [
                'day_name' => $this->vietnameseDayName($date),
                'date' => $date->format('d/m/Y'),
                'carbon' => $date,
                'items' => $registrations
                    ->filter(fn (Registration $registration) => $registration->injection_date?->toDateString() === $date->toDateString())
                    ->values(),
            ]];
        })->all();

        return view('vaccine::admin.schedule', compact('daysOfWeek', 'startOfWeek'));
    }

    public function exportCsv(Request $request)
    {
        $this->assertRequestedCenter($request);
        $filename = 'don_dang_ky_tiem_' . now()->format('Y-m-d_His') . '.csv';
        $query = $this->registrationQuery($request)->with('vaccines:id,name')->orderBy('id');

        return response()->stream(function () use ($query) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, [
                'Mã đơn', 'Họ tên', 'Số điện thoại', 'Trung tâm', 'Ngày tiêm',
                'Tổng tiền', 'Trạng thái lịch hẹn', 'Trạng thái thanh toán', 'Vắc xin', 'Ngày đăng ký',
            ]);

            $query->lazyById(500)->each(function (Registration $registration) use ($file) {
                fputcsv($file, [
                    $this->safeCsvCell($registration->registration_code),
                    $this->safeCsvCell($registration->patient_name),
                    $this->safeCsvCell($registration->patient_phone),
                    $this->safeCsvCell($registration->center_name),
                    $this->safeCsvCell($registration->injection_date?->format('Y-m-d')),
                    $registration->total_price,
                    $this->safeCsvCell($registration->bookingStatusLabel()),
                    $this->safeCsvCell($registration->paymentStatusLabel()),
                    $this->safeCsvCell($registration->vaccines->pluck('name')->implode(', ')),
                    $registration->created_at->format('d/m/Y H:i'),
                ]);
            });

            fclose($file);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function registrationQuery(Request $request)
    {
        $query = Registration::query();

        if (AdminContext::isBranchAdmin()) {
            return $query->where('center_id', AdminContext::centerId());
        }

        if ($request->filled('center_id')) {
            $query->where('center_id', $request->integer('center_id'));
        }

        return $query;
    }

    private function visibleRegistration(int $id, array $with = []): Registration
    {
        $registration = Registration::with($with)->findOrFail($id);
        $this->assertRegistrationVisible($registration);

        return $registration;
    }

    private function assertRegistrationVisible(Registration $registration): void
    {
        if (AdminContext::isBranchAdmin() && (int) $registration->center_id !== (int) AdminContext::centerId()) {
            abort(403, 'Cross-branch access forbidden.');
        }
    }

    private function assertRequestedCenter(Request $request): void
    {
        if (AdminContext::isBranchAdmin() && $request->filled('center_id')
            && $request->integer('center_id') !== (int) AdminContext::centerId()) {
            abort(403, 'Cross-branch access forbidden.');
        }
    }

    private function vietnameseDayName(\Carbon\Carbon $date): string
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

    private function safeCsvCell(?string $value): string
    {
        return \App\Services\Security\CsvSanitizer::sanitizeCell($value);
    }
}
