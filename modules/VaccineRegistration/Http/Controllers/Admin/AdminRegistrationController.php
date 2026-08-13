<?php

namespace Modules\VaccineRegistration\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditLogger;
use App\Services\BranchStockService;
use App\Services\RegistrationPaymentService;
use App\Services\Security\CsvSanitizer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\VaccineRegistration\Models\Center;
use Modules\VaccineRegistration\Models\CenterVaccine;
use Modules\VaccineRegistration\Models\Customer;
use Modules\VaccineRegistration\Models\Registration;
use Modules\VaccineRegistration\Models\Schedule;
use Modules\VaccineRegistration\Models\Slot;
use Modules\VaccineRegistration\Support\AdminContext;
use Modules\VaccineRegistration\Support\PhoneNormalizer;

class AdminRegistrationController extends Controller
{
    public function index(Request $request)
    {
        $selectedCenterId = AdminContext::resolveListCenterId($request);

        $query = $this->buildFilteredRegistrationQuery($request, $selectedCenterId)
            ->with('customer:id,name,phone');

        $registrations = $query->latest('id')->paginate(20)->withQueryString();
        $isSuperAdmin = AdminContext::isSuperAdmin();
        $centers = $isSuperAdmin
            ? Center::active()->orderBy('sort_order')->orderBy('id')->get(['id', 'name'])
            : collect();

        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'html' => view('vaccine::admin.registrations._table', compact('registrations', 'centers', 'selectedCenterId', 'isSuperAdmin'))->render(),
            ]);
        }

        return view('vaccine::admin.registrations.index', compact('registrations', 'centers', 'selectedCenterId', 'isSuperAdmin'));
    }

    public function show(int $id, RegistrationPaymentService $paymentService)
    {
        $registration = $this->visibleRegistration($id, ['vaccines', 'customer', 'slot.schedule']);
        $pointQuote = $registration->customer ? $paymentService->quote($registration->customer, $registration) : null;
        $loyaltySettings = $paymentService->getLoyaltySettings($registration->center_id);

        $groupRegistrations = collect();
        $otherUnpaidCount = 0;
        if ($registration->idempotency_key) {
            $parts = explode('_', $registration->idempotency_key);
            $prefix = $parts[0];
            if ($prefix) {
                $groupRegistrations = Registration::where('idempotency_key', 'like', $prefix . '%')
                    ->where('id', '!=', $registration->id)
                    ->with('vaccines')
                    ->get();
                $otherUnpaidCount = $groupRegistrations->where('payment_status', '!=', Registration::PAYMENT_PAID)->count();
            }
        }

        // Lấy danh sách khung giờ trống khả dụng để đổi lịch hẹn (nếu cần hoãn lịch)
        $nowVn = \Carbon\Carbon::now('Asia/Ho_Chi_Minh');
        $today = $nowVn->toDateString();
        $availableSlots = Slot::query()
            ->with('schedule')
            ->whereHas('schedule', fn ($query) => $query->where('center_id', $registration->center_id)->whereDate('date', '>=', $today))
            ->where('is_active', true)
            ->whereColumn('reserved_count', '<', 'capacity')
            ->orderBy('id')
            ->get()
            ->filter(function ($slot) use ($today, $nowVn) {
                $nowTime = $nowVn->format('H:i');
                if ($slot->schedule->date->toDateString() === $today) {
                    return $slot->start_at > $nowTime;
                }
                return true;
            })
            ->values();

        return view('vaccine::admin.registrations.show', compact('registration', 'pointQuote', 'loyaltySettings', 'availableSlots', 'groupRegistrations', 'otherUnpaidCount'));
    }

    public function create(Request $request)
    {
        if ($request->filled('center_id')) {
            AdminContext::assertCanManageCenter($request->integer('center_id'));
        }

        $selectedCenterId = $request->filled('center_id')
            ? AdminContext::selectedCenterId($request->integer('center_id'))
            : AdminContext::selectedCenterId();
        $center = $selectedCenterId ? Center::active()->findOrFail($selectedCenterId) : null;

        $nowVn = \Carbon\Carbon::now('Asia/Ho_Chi_Minh');
        $today = $nowVn->toDateString();

        if ($center) {
            Schedule::generateFromDefaults($center->id, $today, $nowVn->copy()->addDays(30)->toDateString());
        }

        $centers = AdminContext::isSuperAdmin()
            ? Center::active()->orderBy('sort_order')->orderBy('id')->get(['id', 'name'])
            : collect();
        $slots = $center
            ? Slot::query()
                ->with('schedule:id,center_id,date')
                ->whereHas('schedule', fn ($query) => $query->where('center_id', $center->id)->whereDate('date', '>=', $today))
                ->where('is_active', true)
                ->whereColumn('reserved_count', '<', 'capacity')
                ->orderBy('id')
                ->get()
                ->filter(function ($slot) use ($today, $nowVn) {
                    $nowTime = $nowVn->format('H:i');
                    if ($slot->schedule->date->toDateString() === $today) {
                        return $slot->start_at > $nowTime;
                    }
                    return true;
                })
                ->values()
            : collect();
        $vaccines = $center
            ? CenterVaccine::query()
                ->with('vaccine:id,name,origin,is_active,category,age_group')
                ->where('center_id', $center->id)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->filter(fn (CenterVaccine $centerVaccine) => $centerVaccine->vaccine?->is_active)
                ->values()
            : collect();

        return view('vaccine::admin.registrations.create', compact('center', 'centers', 'slots', 'vaccines'));
    }

    public function store(Request $request, BranchStockService $stockService)
    {
        $validated = $request->validate([
            'center_id' => 'required|integer|exists:centers,id',
            'patient_name' => 'required|string|max:255',
            'patient_phone' => 'required|string|max:30',
            'account_name' => 'nullable|string|max:255',
            'account_phone' => 'nullable|string|max:30',
            'slot_id' => 'required|integer|exists:slots,id',
            'vaccine_ids' => 'required|array|min:1',
            'vaccine_ids.*' => 'required|integer|distinct|exists:vaccines,id',
            'booking_status' => 'required|in:pending,confirmed',
            'quantities' => 'nullable|array',
            'quantities.*' => 'nullable|integer|min:1',
            'idempotency_key' => 'nullable|string|max:100',
        ]);

        AdminContext::assertCanManageCenter((int) $validated['center_id']);

        $recipientPhone = PhoneNormalizer::normalize($validated['patient_phone']);
        if (! $recipientPhone) {
            return back()->withErrors(['patient_phone' => 'Số điện thoại di động Việt Nam không hợp lệ.'])->withInput();
        }
        $accountPhone = PhoneNormalizer::normalize($validated['account_phone'] ?? $validated['patient_phone']);
        if (! $accountPhone) {
            return back()->withErrors(['account_phone' => 'Số điện thoại tài khoản tích điểm không hợp lệ.'])->withInput();
        }
        $idempotencyKey = $validated['idempotency_key'] ?? null;
        if ($idempotencyKey && ($existing = Registration::where('idempotency_key', $idempotencyKey)->first())) {
            $this->assertRegistrationVisible($existing);

            return redirect()->route('admin.registrations.show', $existing);
        }

        $center = Center::active()->findOrFail($validated['center_id']);
        $registration = DB::transaction(function () use ($validated, $recipientPhone, $accountPhone, $center, $stockService, $idempotencyKey) {
            $nowVn = \Carbon\Carbon::now('Asia/Ho_Chi_Minh');
            $todayDate = $nowVn->toDateString();
            $isPastSlot = $slot->schedule->date->toDateString() === $todayDate 
                && $slot->start_at <= $nowVn->format('H:i');

            if (! $slot->is_active || ! $slot->schedule || ! $slot->schedule->is_active
                || (int) $slot->schedule->center_id !== (int) $center->id
                || $slot->schedule->date->isBefore($todayDate) 
                || $isPastSlot || $slot->reserved_count >= $slot->capacity) {
                throw ValidationException::withMessages(['slot_id' => 'Khung giờ không còn chỗ, đã trôi qua hoặc không thuộc chi nhánh đã chọn.']);
            }

            $vaccineIds = array_map('intval', $validated['vaccine_ids']);
            $items = [];
            $total = 0;
            $quantities = $validated['quantities'] ?? [];
            $demand = [];
            foreach ($vaccineIds as $vaccineId) {
                $demand[$vaccineId] = (int) ($quantities[$vaccineId] ?? 1);
            }
            $centerVaccines = $stockService->commit($center->id, $demand);
            $customer = Customer::findOrCreateByPhone($accountPhone, $validated['account_name'] ?? $validated['patient_name']);

            foreach ($vaccineIds as $vaccineId) {
                $centerVaccine = $centerVaccines->get($vaccineId);
                $qty = $demand[$vaccineId];
                $price = $centerVaccine->hasSalePrice() ? $centerVaccine->sale_price : $centerVaccine->price;
                $total += $price * $qty;
                $items[$vaccineId] = ['price' => $price, 'sale_price' => null, 'quantity' => $qty, 'stock_committed_quantity' => $qty];
            }

            $registration = Registration::create([
                'registration_code' => $this->newRegistrationCode(),
                'customer_id' => $customer->id,
                'patient_name' => trim($validated['patient_name']),
                'patient_phone' => $recipientPhone,
                'center_id' => $center->id,
                'center_name' => $center->name,
                'injection_date' => $slot->schedule->date->toDateString(),
                'slot_id' => $slot->id,
                'status' => $validated['booking_status'],
                'booking_status' => $validated['booking_status'],
                'payment_status' => Registration::PAYMENT_UNPAID,
                'payment_method' => 'Tại trung tâm',
                'idempotency_key' => $idempotencyKey,
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

        if ($validated['booking_status'] === Registration::BOOKING_NO_SHOW) {
            $paymentService->markNoShow($registration->id, AdminContext::user());

            return back()->with('success', 'Đã ghi nhận khách không đến và hoàn tồn kho đã giữ.');
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

    public function settleGroup(Request $request, int $id, RegistrationPaymentService $paymentService)
    {
        $registration = $this->visibleRegistration($id);

        if (! $registration->idempotency_key) {
            return back()->with('error', 'Đơn hàng này không thuộc nhóm đặt lịch chung nào.');
        }

        $parts = explode('_', $registration->idempotency_key);
        $prefix = $parts[0];

        if (! $prefix) {
            return back()->with('error', 'Khóa nhóm không hợp lệ.');
        }

        // Lấy toàn bộ các đơn chưa thanh toán của nhóm, bao gồm cả đơn hiện tại
        $unpaidGroupRegistrations = Registration::where('idempotency_key', 'like', $prefix . '%')
            ->where('payment_status', '!=', Registration::PAYMENT_PAID)
            ->get();

        if ($unpaidGroupRegistrations->isEmpty()) {
            return back()->with('info', 'Tất cả các đơn trong nhóm đã được thanh toán.');
        }

        $successCount = 0;
        $errors = [];

        foreach ($unpaidGroupRegistrations as $reg) {
            try {
                $paymentService->settle($reg->id, 0, AdminContext::user());
                $successCount++;
            } catch (\Exception $e) {
                $errors[] = "Đơn {$reg->registration_code} ({$reg->patient_name}): " . $e->getMessage();
            }
        }

        if (count($errors) > 0) {
            return back()->with('success', "Đã thanh toán thành công {$successCount} đơn. Gặp lỗi tại: " . implode(', ', $errors));
        }

        return back()->with('success', "Đã xác nhận thanh toán chung thành công cho toàn bộ {$successCount} đơn hàng trong nhóm!");
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
        $selectedCenterId = AdminContext::resolveListCenterId($request);
        $validated = $request->validate([
            'week' => 'nullable|date_format:Y-m-d',
            'center_id' => 'nullable|integer|exists:centers,id',
        ]);
        $startOfWeek = ! empty($validated['week'])
            ? Carbon::createFromFormat('Y-m-d', $validated['week'])->startOfWeek()
            : now()->startOfWeek();
        $endOfWeek = $startOfWeek->copy()->endOfWeek();

        $registrations = $this->registrationQuery($selectedCenterId)
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

        $centers = AdminContext::isSuperAdmin()
            ? Center::active()->orderBy('sort_order')->orderBy('id')->get(['id', 'name'])
            : collect();

        return view('vaccine::admin.schedule', compact('daysOfWeek', 'startOfWeek', 'centers', 'selectedCenterId'));
    }

    public function exportCsv(Request $request)
    {
        $selectedCenterId = AdminContext::resolveListCenterId($request);
        $filename = 'don_dang_ky_tiem_'.now()->format('Y-m-d_His').'.csv';
        $query = $this->buildFilteredRegistrationQuery($request, $selectedCenterId)
            ->with('vaccines:id,name')
            ->latest('id');

        AuditLogger::log(
            'registration.exported',
            'registration_export',
            $selectedCenterId ?? 'all',
            newValues: [
                'center_id' => $selectedCenterId,
                'record_count' => (clone $query)->count(),
            ],
            centerId: $selectedCenterId,
            resolveCenter: false
        );

        return response()->stream(function () use ($query) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
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
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    private function buildFilteredRegistrationQuery(Request $request, ?int $selectedCenterId)
    {
        $query = $this->registrationQuery($selectedCenterId);

        if ($request->filled('booking_status')) {
            $query->where('booking_status', $request->input('booking_status'));
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->input('payment_status'));
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($builder) use ($search) {
                $builder->where('registration_code', 'like', $search.'%')
                    ->orWhere('patient_phone', 'like', '%'.$search.'%')
                    ->orWhere('patient_name', 'like', '%'.$search.'%');
            });
        }

        if ($request->filled('injection_date_from')) {
            $query->whereDate('injection_date', '>=', $request->input('injection_date_from'));
        }

        if ($request->filled('injection_date_to')) {
            $query->whereDate('injection_date', '<=', $request->input('injection_date_to'));
        }

        $day = $request->input('filter_day') ?? $request->input('day');
        $month = $request->input('filter_month') ?? $request->input('month');
        $year = $request->input('filter_year') ?? $request->input('year');

        if ($day !== null && $day !== '') {
            $query->whereDay('injection_date', (int) $day);
        }
        if ($month !== null && $month !== '') {
            $query->whereMonth('injection_date', (int) $month);
        }
        if ($year !== null && $year !== '') {
            $query->whereYear('injection_date', (int) $year);
        }

        return $query;
    }

    private function registrationQuery(?int $selectedCenterId)
    {
        $query = Registration::query();

        if ($selectedCenterId) {
            $query->where('center_id', $selectedCenterId);
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
        AdminContext::assertCanManageCenter((int) $registration->center_id);
    }

    private function vietnameseDayName(Carbon $date): string
    {
        return match ($date->dayOfWeek) {
            Carbon::MONDAY => 'Thứ Hai',
            Carbon::TUESDAY => 'Thứ Ba',
            Carbon::WEDNESDAY => 'Thứ Tư',
            Carbon::THURSDAY => 'Thứ Năm',
            Carbon::FRIDAY => 'Thứ Sáu',
            Carbon::SATURDAY => 'Thứ Bảy',
            Carbon::SUNDAY => 'Chủ Nhật',
        };
    }

    private function safeCsvCell(?string $value): string
    {
        return CsvSanitizer::sanitizeCell($value);
    }

    public function reschedule(Request $request, int $id)
    {
        $registration = $this->visibleRegistration($id);

        $validated = $request->validate([
            'slot_id' => 'required|integer|exists:slots,id',
        ]);

        $slot = Slot::with('schedule')->findOrFail($validated['slot_id']);

        if (!$slot->is_active || !$slot->schedule || !$slot->schedule->is_active
            || (int) $slot->schedule->center_id !== (int) $registration->center_id
            || $slot->reserved_count >= $slot->capacity) {
            return back()->withErrors(['slot_id' => 'Khung giờ được chọn không hợp lệ hoặc đã hết chỗ.']);
        }

        DB::transaction(function () use ($registration, $slot) {
            // Giải phóng slot cũ
            if ($registration->slot_id) {
                Slot::where('id', $registration->slot_id)->decrement('reserved_count');
            }

            // Cập nhật sang slot mới
            $registration->update([
                'slot_id' => $slot->id,
                'injection_date' => $slot->schedule->date->toDateString(),
                'screening_status' => null,
                'screening_notes' => null,
                'status' => 'confirmed',
                'booking_status' => 'confirmed',
            ]);

            $slot->increment('reserved_count');

            AuditLogger::log(
                'registration.rescheduled',
                'registration',
                $registration->id,
                ['slot_id' => $registration->slot_id, 'injection_date' => $registration->injection_date],
                ['slot_id' => $slot->id, 'injection_date' => $slot->schedule->date->toDateString()],
                $registration->center_id
            );
        });

        return back()->with('success', 'Đã thay đổi lịch hẹn tiêm chủng thành công sang ngày ' . $slot->schedule->date->format('d/m/Y') . ' (' . $slot->start_at . ' - ' . $slot->end_at . ').');
    }

    private function newRegistrationCode(): string
    {
        do {
            $code = 'MCD-'.strtoupper(Str::random(8));
        } while (Registration::where('registration_code', $code)->exists());

        return $code;
    }
}
