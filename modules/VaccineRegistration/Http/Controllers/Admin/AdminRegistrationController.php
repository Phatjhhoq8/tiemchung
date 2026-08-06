<?php

namespace Modules\VaccineRegistration\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditLogger;
use App\Services\RegistrationPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\VaccineRegistration\Models\Center;
use Modules\VaccineRegistration\Models\CenterVaccine;
use Modules\VaccineRegistration\Models\Customer;
use Modules\VaccineRegistration\Models\Registration;
use Modules\VaccineRegistration\Models\Slot;
use Modules\VaccineRegistration\Support\AdminContext;
use Modules\VaccineRegistration\Support\PhoneNormalizer;
use Illuminate\Support\Str;

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

    public function create(Request $request)
    {
        $selectedCenterId = AdminContext::selectedCenterId($request->integer('center_id'));
        $center = Center::active()->findOrFail($selectedCenterId);

        $centers = AdminContext::isSuperAdmin()
            ? Center::active()->orderBy('sort_order')->orderBy('id')->get(['id', 'name'])
            : collect();
        $slots = Slot::query()
            ->with('schedule:id,center_id,date')
            ->whereHas('schedule', fn ($query) => $query->where('center_id', $center->id)->whereDate('date', '>=', today()))
            ->where('is_active', true)
            ->whereColumn('reserved_count', '<', 'capacity')
            ->orderBy('id')
            ->get();
        $vaccines = CenterVaccine::query()
            ->with('vaccine:id,name,origin,is_active,type,category,age_group')
            ->where('center_id', $center->id)
            ->where('is_active', true)
            ->where('stock_status', '!=', 'out_of_stock')
            ->orderBy('sort_order')
            ->get()
            ->filter(fn (CenterVaccine $centerVaccine) => $centerVaccine->vaccine?->is_active)
            ->values();

        return view('vaccine::admin.registrations.create', compact('center', 'centers', 'slots', 'vaccines'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'center_id' => 'required|integer|exists:centers,id',
            'patient_name' => 'required|string|max:255',
            'patient_phone' => 'required|string|max:30',
            'slot_id' => 'required|integer|exists:slots,id',
            'vaccine_ids' => 'required|array|min:1',
            'vaccine_ids.*' => 'required|integer|distinct|exists:vaccines,id',
            'booking_status' => 'required|in:pending,confirmed',
        ]);

        if (AdminContext::isBranchAdmin() && $validated['center_id'] !== AdminContext::centerId()) {
            abort(403, 'Cross-branch access forbidden.');
        }

        $phone = PhoneNormalizer::normalize($validated['patient_phone']);
        if (!$phone) {
            return back()->withErrors(['patient_phone' => 'Số điện thoại di động Việt Nam không hợp lệ.'])->withInput();
        }

        $center = Center::active()->findOrFail($validated['center_id']);
        $registration = DB::transaction(function () use ($validated, $phone, $center, $request) {
            $slot = Slot::with('schedule')->whereKey($validated['slot_id'])->lockForUpdate()->firstOrFail();
            if (!$slot->is_active || !$slot->schedule || !$slot->schedule->is_active
                || (int) $slot->schedule->center_id !== (int) $center->id
                || $slot->schedule->date->isBefore(today()) || $slot->reserved_count >= $slot->capacity) {
                throw ValidationException::withMessages(['slot_id' => 'Khung giờ không còn chỗ hoặc không thuộc chi nhánh đã chọn.']);
            }

            $vaccineIds = array_map('intval', $validated['vaccine_ids']);
            $centerVaccines = CenterVaccine::with('vaccine')
                ->where('center_id', $center->id)
                ->whereIn('vaccine_id', $vaccineIds)
                ->where('is_active', true)
                ->where('stock_status', '!=', 'out_of_stock')
                ->lockForUpdate()
                ->get()
                ->keyBy('vaccine_id');

            if ($centerVaccines->count() !== count($vaccineIds)
                || $centerVaccines->contains(fn (CenterVaccine $centerVaccine) => !$centerVaccine->vaccine || !$centerVaccine->vaccine->is_active)) {
                throw ValidationException::withMessages(['vaccine_ids' => 'Một hoặc nhiều vắc xin không còn bán tại chi nhánh này.']);
            }

            $customer = Customer::where('phone', $phone)->lockForUpdate()->first();
            if (!$customer) {
                $customer = Customer::create([
                    'name' => trim($validated['patient_name']),
                    'phone' => $phone,
                ]);
            }

            $items = [];
            $total = 0;
            $quantities = $request->input('quantities', []);
            foreach ($vaccineIds as $vaccineId) {
                $centerVaccine = $centerVaccines->get($vaccineId);
                $qty = (int) ($quantities[$vaccineId] ?? 1);
                if ($qty < 1) {
                    $qty = 1;
                }
                if ($qty > $centerVaccine->stock_quantity) {
                    throw ValidationException::withMessages([
                        'vaccine_ids' => "Số lượng đăng ký vắc xin {$centerVaccine->vaccine->name} vượt quá tồn kho hiện tại ({$centerVaccine->stock_quantity})."
                    ]);
                }
                $price = $centerVaccine->hasSalePrice() ? $centerVaccine->sale_price : $centerVaccine->price;
                $total += $price * $qty;
                $items[$vaccineId] = ['price' => $price, 'sale_price' => null, 'quantity' => $qty];
            }

            $registration = Registration::create([
                'registration_code' => $this->newRegistrationCode(),
                'customer_id' => $customer->id,
                'patient_name' => trim($validated['patient_name']),
                'patient_phone' => $phone,
                'center_id' => $center->id,
                'center_name' => $center->name,
                'injection_date' => $slot->schedule->date->toDateString(),
                'slot_id' => $slot->id,
                'status' => $validated['booking_status'],
                'booking_status' => $validated['booking_status'],
                'payment_status' => Registration::PAYMENT_UNPAID,
                'payment_method' => 'Tại trung tâm',
                'total_price' => $total,
            ]);
            $registration->vaccines()->attach($items);
            $slot->increment('reserved_count');

            AuditLogger::log(
                action: 'counter_booking_created',
                resourceType: 'registration',
                resourceId: $registration->id,
                newValues: ['registration_code' => $registration->registration_code, 'booking_status' => $registration->booking_status, 'total_price' => $total],
                centerId: $center->id,
            );

            return $registration;
        });

        return redirect()->route('admin.registrations.show', $registration)->with('success', 'Đã tạo phiếu đăng ký tại quầy.');
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

    private function newRegistrationCode(): string
    {
        do {
            $code = 'MCD-' . strtoupper(Str::random(8));
        } while (Registration::where('registration_code', $code)->exists());

        return $code;
    }
}
