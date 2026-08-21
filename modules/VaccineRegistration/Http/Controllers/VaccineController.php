<?php

/**
 * Chức năng: VaccineController xử lý danh mục vắc xin, giỏ hàng và quy trình đăng ký tiêm chủng của khách hàng.
 * Lý do chỉnh sửa: Xử lý danh mục, giỏ hàng và quy trình đặt lịch một người theo chi nhánh.
 */

namespace Modules\VaccineRegistration\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\BranchStockService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\VaccineRegistration\Models\Article;
use Modules\VaccineRegistration\Models\Center;
use Modules\VaccineRegistration\Models\CenterVaccine;
use Modules\VaccineRegistration\Models\ConsultationLead;
use Modules\VaccineRegistration\Models\Customer;
use Modules\VaccineRegistration\Models\Registration;
use Modules\VaccineRegistration\Models\Schedule;
use Modules\VaccineRegistration\Models\Slot;
use Modules\VaccineRegistration\Models\Vaccine;
use Modules\VaccineRegistration\Support\CenterContext;
use Modules\VaccineRegistration\Support\PhoneNormalizer;

class VaccineController extends Controller
{
    /**
     * Hiển thị danh mục sản phẩm tiêm chủng với các bộ lọc động từ CSDL.
     */
    public function index(Request $request)
    {
        $currentCenter = CenterContext::current();
        if (!$currentCenter) {
            $vaccines = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 12);
            $cart = [];
            $featuredCount = 0;
            $diseaseOptions = [];
            $diseases = [];
            $ageGroupOptions = [];
            $ageGroups = [];
            $originOptions = [];
            $origins = [];
            $doseOptions = [1, 2, 3, 4];
            $doses = $doseOptions;
            $productCategories = [];

            if ($request->header('X-Vaccine-Filter') || $request->boolean('filter_spa')) {
                return response()->json([
                    'success' => true,
                    'html' => view('vaccine::partials.grid', compact('vaccines', 'cart'))->render(),
                    'count' => 0,
                ]);
            }

            return view('vaccine::index', compact(
                'vaccines',
                'cart',
                'featuredCount',
                'diseaseOptions',
                'diseases',
                'ageGroupOptions',
                'ageGroups',
                'originOptions',
                'origins',
                'doseOptions',
                'doses',
                'productCategories'
            ));
        }

        $query = Vaccine::forCenter($currentCenter->id);

        // Tìm kiếm theo tên sản phẩm. Lọc theo bệnh dùng tham số disease riêng.
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', '%'.$search.'%');
        }

        if ($request->filled('disease')) {
            $disease = $request->input('disease');
            $query->where(function ($q) use ($disease) {
                $q->where('disease_prevention', 'like', '%'.$disease.'%')
                    ->orWhere('category', 'like', '%'.$disease.'%');
            });
        }

        // Lọc theo nhóm tuổi chỉ định
        if ($request->filled('age_group')) {
            $query->where('age_group', 'like', '%'.$request->input('age_group').'%');
        }

        if ($request->filled('origin')) {
            $query->where('origin', $request->input('origin'));
        }

        if ($request->filled('doses')) {
            $query->where('doses', (int) $request->input('doses'));
        }

        if ($request->boolean('featured') || $request->input('featured') === '1') {
            $query->featured();
        }

        $sort = $request->input('sort', 'popular');
        match ($sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name_asc' => $query->orderBy('name', 'asc'),
            default => $query->orderBy('views', 'desc')->orderBy('vaccines.id', 'asc'),
        };

        $vaccines = $query->paginate(12)->withQueryString();
        $cartState = CenterContext::resolveCart($currentCenter?->id);
        $cart = $cartState['cart'];

        $allVaccines = Vaccine::forCenter($currentCenter?->id)->get();
        $featuredCount = $allVaccines->where('is_featured', true)->count();
        $diseaseOptions = $this->buildDiseaseOptions($allVaccines);
        $diseases = $diseaseOptions;

        $ageGroupOptions = $this->buildAgeGroupOptions($allVaccines);
        $ageGroups = $ageGroupOptions;

        $originOptions = $this->buildOriginOptions($allVaccines);
        $origins = $originOptions;

        $doseOptions = [1, 2, 3, 4];
        $doses = $doseOptions;

        $productCategories = $this->buildProductCategories($allVaccines);

        if ($request->header('X-Vaccine-Filter') || $request->boolean('filter_spa')) {
            return response()->json([
                'success' => true,
                'html' => view('vaccine::partials.grid', compact('vaccines', 'cart'))->render(),
                'count' => $vaccines->total(),
            ]);
        }

        return view('vaccine::index', compact(
            'vaccines',
            'cart',
            'featuredCount',
            'diseaseOptions',
            'diseases',
            'ageGroupOptions',
            'ageGroups',
            'originOptions',
            'origins',
            'doseOptions',
            'doses',
            'productCategories'
        ));
    }

    private function buildDiseaseOptions($vaccines)
    {
        return $vaccines
            ->flatMap(function ($vaccine) {
                $items = [];
                if (! empty($vaccine->category)) {
                    $items[] = trim($vaccine->category);
                }

                if (! empty($vaccine->disease_prevention)) {
                    $parts = preg_split('/[,;\-\/]+/', $vaccine->disease_prevention);
                    foreach ($parts as $part) {
                        $cleaned = trim($part);
                        if (! empty($cleaned)) {
                            $items[] = $cleaned;
                        }
                    }
                }

                return $items;
            })
            ->filter()
            ->unique()
            ->sort()
            ->values();
    }

    private function buildAgeGroupOptions($vaccines)
    {
        return $vaccines
            ->pluck('age_group')
            ->filter()
            ->map(fn ($item) => trim($item))
            ->unique()
            ->sort()
            ->values();
    }

    private function buildOriginOptions($vaccines)
    {
        return $vaccines
            ->pluck('origin')
            ->filter()
            ->map(fn ($item) => trim($item))
            ->unique()
            ->sort()
            ->values();
    }

    private function buildProductCategories($vaccines)
    {
        return $vaccines
            ->filter(fn ($vaccine) => ! empty($vaccine->category))
            ->groupBy('category')
            ->map(function ($items, $name) {
                $first = $items->first();

                return [
                    'name' => $name,
                    'count' => $items->count(),
                    'image' => $first?->image,
                ];
            })
            ->sortBy('name')
            ->values();
    }

    /**
     * Hiển thị trang chi tiết một loại vắc xin.
     */
    public function show(Request $request, $id)
    {
        $currentCenter = CenterContext::current();
        $vaccine = Vaccine::forCenter($currentCenter?->id)->where('vaccines.id', $id)->firstOrFail();

        // Tăng số lượt xem sản phẩm khi xem chi tiết
        $vaccine->increment('views');

        $cart = CenterContext::resolveCart($currentCenter?->id)['cart'];

        if ($request->header('X-Vaccine-Detail-Json') || ($request->wantsJson() && ! $request->header('X-SPA-Request') && ! $request->ajax())) {
            return response()->json([
                'success' => true,
                'vaccine' => [
                    'id' => $vaccine->id,
                    'name' => $vaccine->name,
                    'price' => $vaccine->hasSalePrice() ? $vaccine->sale_price : $vaccine->price,
                    'formatted_price' => number_format($vaccine->hasSalePrice() ? $vaccine->sale_price : $vaccine->price, 0, ',', '.').' đ',
                    'doses' => $vaccine->doses,
                    'disease_prevention' => $vaccine->disease_prevention,
                    'age_group' => $vaccine->age_group,
                    'origin' => $vaccine->origin,
                    'manufacturer' => $vaccine->manufacturer,
                    'dosage' => $vaccine->dosage,
                    'description' => $vaccine->description,
                    'administration_route' => $vaccine->administration_route,
                    'detailed_schedule' => $vaccine->detailed_schedule,
                    'contraindications' => $vaccine->contraindications,
                    'adverse_effects' => $vaccine->adverse_effects,
                    'warnings' => $vaccine->warnings,
                    'source_reference_url' => $vaccine->source_reference_url,
                    'source_review_date' => $vaccine->source_review_date?->toDateString(),
                    'image' => asset('images/vaccines/'.($vaccine->image ?: 'hexaxim.jpg')),
                    'is_in_cart' => isset($cart[$vaccine->id]),
                    'views' => $vaccine->views,
                    'formatted_views' => number_format($vaccine->views, 0, ',', '.').' lượt xem',
                ],
            ]);
        }

        // Lấy 8 vắc xin liên quan cùng phòng bệnh hoặc cùng xuất xứ
        $relatedVaccines = Vaccine::forCenter($currentCenter?->id)->where('vaccines.id', '!=', $vaccine->id)
            ->where(function ($q) use ($vaccine) {
                $q->where('disease_prevention', 'like', '%'.$vaccine->disease_prevention.'%')
                    ->orWhere('origin', $vaccine->origin);
            })
            ->take(8)
            ->get();

        if ($relatedVaccines->count() < 8) {
            $existingIds = $relatedVaccines->pluck('id')->push($vaccine->id)->toArray();
            $extraVaccines = Vaccine::forCenter($currentCenter?->id)->whereNotIn('vaccines.id', $existingIds)->take(8 - $relatedVaccines->count())->get();
            $relatedVaccines = $relatedVaccines->concat($extraVaccines);
        }

        return view('vaccine::show', compact('vaccine', 'cart', 'relatedVaccines'));
    }

    /**
     * Thêm vắc xin vào giỏ hàng (session).
     */
    public function addToCart(Request $request)
    {
        $vaccineId = $request->integer('vaccine_id');
        $vaccine = Vaccine::active()->findOrFail($vaccineId);
        $currentCenter = CenterContext::current();
        $isAvailable = true;

        $cart = session()->get('cart', []);

        if (isset($cart[$vaccineId])) {
            $cart[$vaccineId]['quantity'] = 1;
        } else {
            $cart[$vaccineId] = [
                'name' => $vaccine->name,
                'price' => 0,
                'image' => $vaccine->image ?: 'hexaxim.jpg',
                'quantity' => 1,
                'disease_prevention' => $vaccine->disease_prevention,
            ];
        }

        session()->put('cart', $cart);
        $cartState = CenterContext::resolveCart();
        $cart = $cartState['cart'];
        $totalPrice = $cartState['total_price'];

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã thêm vắc xin vào danh sách tiêm.',
                'cart' => $cart,
                'cart_count' => count($cart),
                'total_price' => $totalPrice,
                'formatted_total_price' => number_format($totalPrice, 0, ',', '.').' đ',
            ]);
        }

        return redirect()->back()->with('success', 'Đã thêm vắc xin vào danh sách đăng ký.');
    }

    /**
     * Xóa vắc xin khỏi giỏ hàng.
     */
    public function removeFromCart(Request $request)
    {
        $vaccineId = $request->input('vaccine_id');
        $cart = session()->get('cart', []);

        if (isset($cart[$vaccineId])) {
            unset($cart[$vaccineId]);
            session()->put('cart', $cart);
        }

        $cartState = CenterContext::resolveCart();
        $cart = $cartState['cart'];
        $totalPrice = $cartState['total_price'];

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa vắc xin khỏi danh sách tiêm.',
                'cart' => $cart,
                'cart_count' => count($cart),
                'total_price' => $totalPrice,
                'formatted_total_price' => number_format($totalPrice, 0, ',', '.').' đ',
            ]);
        }

        return redirect()->back()->with('success', 'Đã xóa vắc xin khỏi danh sách đăng ký.');
    }

    /**
     * Xóa sạch giỏ hàng.
     */
    public function clearCart(Request $request)
    {
        session()->forget('cart');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa sạch danh sách tiêm.',
                'cart' => [],
                'cart_count' => 0,
                'total_price' => 0,
                'formatted_total_price' => '0 đ',
            ]);
        }

        return redirect()->back()->with('success', 'Đã xóa sạch giỏ hàng.');
    }

    /**
     * Render the single-person booking form for the currently selected branch.
     */
    public function showRegister(Request $request)
    {
        $currentCenter = CenterContext::current();
        abort_unless($currentCenter, 404, 'Không tìm thấy chi nhánh đang hoạt động.');

        if ($request->filled('add_vaccine_id')) {
            $vaccine = Vaccine::active()->find($request->integer('add_vaccine_id'));
            $isAvailable = $vaccine && CenterVaccine::where('center_id', $currentCenter->id)
                ->where('vaccine_id', $vaccine->id)
                ->where('is_active', true)
                ->where('stock_quantity', '>', 0)
                ->exists();

            if ($isAvailable) {
                $cart = session()->get('cart', []);
                $cart[$vaccine->id] = [
                    'name' => $vaccine->name,
                    'price' => 0,
                    'quantity' => 1,
                    'image' => $vaccine->image ?: 'hexaxim.jpg',
                    'disease_prevention' => $vaccine->disease_prevention,
                ];
                session()->put('cart', $cart);
            }
        }

        $cartState = CenterContext::resolveCart($currentCenter->id);
        $isEmptyCart = empty($cartState['cart']);
        $centers = CenterContext::activeCenters();

        $nowVn = \Carbon\Carbon::now('Asia/Ho_Chi_Minh');
        $nowTime = $nowVn->format('H:i');
        $today = $nowVn->toDateString();

        // Tự động sinh lịch 30 ngày từ cấu hình mặc định
        Schedule::generateFromDefaults($currentCenter->id, $today, $nowVn->copy()->addDays(30)->toDateString());

        $schedules = Schedule::query()
            ->where('center_id', $currentCenter->id)
            ->where('is_active', true)
            ->whereDate('date', '>=', $today)
            ->with(['slots' => function ($query) {
                $query->where('is_active', true)
                    ->whereColumn('reserved_count', '<', 'capacity')
                    ->orderBy('start_at');
            }])
            ->orderBy('date')
            ->get()
            ->map(function (Schedule $schedule) use ($today, $nowTime) {
                if ($schedule->date->toDateString() === $today) {
                    $schedule->setRelation('slots', $schedule->slots->filter(function ($slot) use ($nowTime) {
                        return $slot->start_at > $nowTime;
                    }));
                }
                return $schedule;
            })
            ->filter(fn (Schedule $schedule) => $schedule->slots->isNotEmpty())
            ->values();

        if ($request->ajax() || $request->wantsJson()) {
            if ($isEmptyCart) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn chưa chọn vắc xin nào',
                    'centers' => $centers,
                    'current_center' => $currentCenter,
                ]);
            }

            return response()->json([
                'success' => true,
                'cart' => $cartState['cart'],
                'total_price' => $cartState['total_price'],
                'unavailable_count' => $cartState['unavailable_count'],
                'schedules' => $schedules,
                'centers' => $centers,
                'current_center' => $currentCenter,
            ]);
        }

        return view('vaccine::register', [
            'cart' => $cartState['cart'],
            'totalPrice' => $cartState['total_price'],
            'unavailableCount' => $cartState['unavailable_count'],
            'schedules' => $schedules,
            'currentCenter' => $currentCenter,
            'activeCenters' => CenterContext::activeCenters(),
            'isEmptyCart' => $isEmptyCart,
        ]);
    }

    public function showBookingLookup()
    {
        return view('vaccine::booking_lookup', [
            'lookedUp' => false,
            'registrations' => collect(),
            'phone' => null,
        ]);
    }

    public function lookupBookingsByPhone(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string|max:30',
            'registration_code' => 'nullable|string|max:100',
        ], [
            'phone.required' => 'Vui lòng nhập số điện thoại đã dùng để đặt lịch.',
        ]);

        $phone = PhoneNormalizer::normalize($validated['phone']);
        if (! $phone) {
            return back()->withErrors(['phone' => 'Số điện thoại di động Việt Nam không hợp lệ.'])->withInput();
        }

        $code = trim((string) $request->input('registration_code'));

        $query = Registration::query()
            ->with(['vaccines:id,name', 'slot.schedule'])
            ->where(function ($query) use ($phone) {
                $query->where('patient_phone', $phone)
                    ->orWhereHas('customer', fn ($customerQuery) => $customerQuery->where('phone', $phone));
            });

        if (! empty($code)) {
            $query->where(function ($q) use ($code) {
                $q->where('registration_code', $code)
                    ->orWhere('registration_code', 'like', '%'.$code.'%');
            });
        }

        $registrations = $query->orderByDesc('injection_date')
            ->orderByDesc('id')
            ->get();

        $registrations->each(function ($reg) use ($code) {
            if (! empty($code) && (strtolower($reg->registration_code) === strtolower($code) || str_contains(strtolower($reg->registration_code), strtolower($code)))) {
                $reg->is_masked = false;
                $reg->display_name = $reg->patient_name;
                $reg->display_code = $reg->registration_code;
                $reg->display_price = number_format($reg->netPaidAmount(), 0, ',', '.').' đ';
                $reg->display_vaccines = $reg->vaccines->map(fn ($vaccine) => $vaccine->name.(($vaccine->pivot->quantity ?? 1) > 1 ? ' x'.$vaccine->pivot->quantity : ''))->implode(', ');
            } else {
                $reg->is_masked = true;
                $reg->display_name = self::maskName($reg->patient_name);
                $reg->display_code = self::maskCode($reg->registration_code);
                $reg->display_price = '*** đ';
                $reg->display_vaccines = $reg->vaccines->count().' loại vắc xin (Nhập Mã đặt lịch để xem chi tiết)';
            }
        });

        $customer = Customer::where('phone', $phone)->first();
        $points = 0;
        if ($customer) {
            $loyaltyService = app(\App\Services\LoyaltyService::class);
            $points = $loyaltyService->calculateAvailablePoints($customer);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'points' => $points,
                'phone' => $phone,
                'registrations' => $registrations->map(function ($reg) {
                    return [
                        'registration_code' => $reg->registration_code,
                        'is_masked' => $reg->is_masked,
                        'display_code' => $reg->display_code,
                        'display_name' => $reg->display_name,
                        'display_price' => $reg->display_price,
                        'display_vaccines' => $reg->display_vaccines,
                        'injection_date' => $reg->injection_date ? \Carbon\Carbon::parse($reg->injection_date)->format('d/m/Y') : '',
                        'slot_time' => $reg->slot && $reg->slot->schedule ? $reg->slot->schedule->start_time . ' - ' . $reg->slot->schedule->end_time : '',
                        'status' => $reg->status === 'completed' ? 'Đã hoàn thành' : ($reg->status === 'cancelled' ? 'Đã hủy' : ($reg->status === 'confirmed' ? 'Đã xác nhận' : 'Chờ xác nhận')),
                        'status_color' => $reg->status === 'completed' ? '#166534' : ($reg->status === 'cancelled' ? '#991b1b' : '#854d0e'),
                    ];
                })
            ]);
        }

        return view('vaccine::booking_lookup', [
            'lookedUp' => true,
            'registrations' => $registrations,
            'phone' => $phone,
            'registration_code' => $code,
            'points' => $points,
        ]);
    }

    public static function maskName(string $name): string
    {
        $parts = explode(' ', trim($name));
        if (count($parts) <= 1) {
            $len = mb_strlen($name, 'UTF-8');
            if ($len <= 2) {
                return $name;
            }

            return mb_substr($name, 0, 1, 'UTF-8').'*'.mb_substr($name, -1, 1, 'UTF-8');
        }
        $first = $parts[0];
        $last = $parts[count($parts) - 1];

        return $first.' * '.$last;
    }

    public static function maskCode(string $code): string
    {
        $parts = explode('-', $code);
        if (count($parts) < 3) {
            return substr($code, 0, 3).'-***';
        }

        return $parts[0].'-***-'.$parts[2];
    }

    /**
     * Create one booking. Prices and branch membership are always resolved server-side.
     */
    public function postRegister(Request $request, BranchStockService $stockService)
    {
        $hasPatientsArrayInRequest = $request->has('patients');

        // 1. Pack single patient fields into patients array if client sends legacy format
        if (! $hasPatientsArrayInRequest && $request->filled('patient_name')) {
            $request->merge([
                'patients' => [
                    [
                        'name' => $request->input('patient_name'),
                        'phone' => $request->input('patient_phone'),
                        'dob' => $request->input('patient_dob', '2000-01-01'),
                        'gender' => $request->input('patient_gender', 'Khác'),
                        'address' => $request->input('patient_address', 'Tại trung tâm'),
                        'vaccine_ids' => $request->input('vaccine_ids', []),
                    ],
                ],
            ]);
        }

        $validated = $request->validate([
            'patients' => 'required|array|min:1|max:5',
            'patients.*.name' => 'required|string|max:255',
            'patients.*.phone' => 'required|string|max:30',
            'patients.*.dob' => 'nullable|date|before:today',
            'patients.*.gender' => 'nullable|string|in:Nam,Nữ,Khác',
            'patients.*.address' => 'nullable|string|max:500',
            'patients.*.vaccine_ids' => 'required|array|min:1',
            'patients.*.vaccine_ids.*' => 'required|integer|exists:vaccines,id',
            'patients.*.regimen_choices' => 'nullable|array',
            'slot_id' => 'required|integer|exists:slots,id',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:30',
            'account_name' => 'nullable|string|max:255',
            'account_phone' => 'nullable|string|max:30',
            'idempotency_key' => 'nullable|string|max:100',
        ], [
            'patients.required' => 'Vui lòng cung cấp thông tin người đăng ký tiêm.',
            'patients.max' => 'Mỗi lượt đăng ký chỉ được phép tối đa 5 người.',
            'patients.*.name.required' => 'Họ tên người tiêm không được để trống.',
            'patients.*.phone.required' => 'Số điện thoại liên hệ không được để trống.',
            'patients.*.vaccine_ids.required' => 'Vui lòng chọn ít nhất một loại vắc xin cho mỗi người.',
            'slot_id.required' => 'Vui lòng chọn khung giờ tiêm.',
        ]);

        $currentCenter = CenterContext::current();
        abort_unless($currentCenter, 404, 'Không tìm thấy chi nhánh đang hoạt động.');

        foreach ($validated['patients'] as $index => $patient) {
            if (count($patient['vaccine_ids']) !== count(array_unique($patient['vaccine_ids']))) {
                throw ValidationException::withMessages([
                    "patients.{$index}.vaccine_ids" => 'Mỗi loại vắc xin chỉ được chọn một lần cho một người tiêm.',
                ]);
            }
        }

        $accountPhoneInput = $validated['account_phone'] ?? $validated['guardian_phone'] ?? $validated['patients'][0]['phone'];
        $accountPhone = PhoneNormalizer::normalize($accountPhoneInput);
        if (! $accountPhone) {
            return back()->withErrors(['account_phone' => 'Số điện thoại tài khoản tích điểm không hợp lệ.'])->withInput();
        }
        $accountName = trim($validated['account_name']
            ?? (isset($validated['guardian_phone']) ? ($validated['guardian_name'] ?? '') : '')
            ?: $validated['patients'][0]['name']);

        $idempotencyKey = $validated['idempotency_key'] ?? null;
        if ($idempotencyKey) {
            $existing = Registration::where('idempotency_key', $idempotencyKey)
                ->orWhere('idempotency_key', $idempotencyKey.'_0')
                ->first();
            if ($existing) {
                return $this->completePublicBooking($existing);
            }
        }

        $successCodes = [];

        try {
            DB::transaction(function () use ($validated, $currentCenter, &$successCodes, $idempotencyKey, $request, $hasPatientsArrayInRequest, $stockService, $accountPhone, $accountName) {
                // Lock slot
                $slot = Slot::with('schedule')->whereKey($validated['slot_id'])->lockForUpdate()->firstOrFail();
                $nowVn = \Carbon\Carbon::now('Asia/Ho_Chi_Minh');
                $todayDate = $nowVn->toDateString();
                $isPastSlot = $slot->schedule->date->toDateString() === $todayDate 
                    && $slot->start_at <= $nowVn->format('H:i');

                if (! $slot->is_active || ! $slot->schedule || ! $slot->schedule->is_active
                    || (int) $slot->schedule->center_id !== (int) $currentCenter->id
                    || $slot->schedule->date->isBefore($todayDate)
                    || $isPastSlot) {
                    throw ValidationException::withMessages([
                        'slot_id' => 'Khung giờ này đã trôi qua hoặc không khả dụng.',
                    ]);
                }

                $patientCount = count($validated['patients']);
                if ($slot->reserved_count + $patientCount > $slot->capacity) {
                    throw ValidationException::withMessages([
                        'slot_id' => 'Khung giờ này chỉ còn lại '.($slot->capacity - $slot->reserved_count).' chỗ trống.',
                    ]);
                }

                $demand = collect($validated['patients'])
                    ->flatMap(fn ($patient) => $patient['vaccine_ids'])
                    ->countBy()
                    ->all();
                $centerVaccines = $stockService->commit($currentCenter->id, $demand);
                $customer = Customer::findOrCreateByPhone($accountPhone, $accountName);

                // Create registrations for each profile
                foreach ($validated['patients'] as $index => $patient) {
                    $phone = PhoneNormalizer::normalize($patient['phone']);
                    if (! $phone) {
                        throw ValidationException::withMessages([
                            "patients.{$index}.phone" => 'Số điện thoại của người tiêm #'.($index + 1).' không hợp lệ.',
                        ]);
                    }

                    $items = [];
                    $total = 0;
                    foreach ($patient['vaccine_ids'] as $vaccineId) {
                        $centerVaccine = $centerVaccines->get($vaccineId);
                        
                        $regimenId = $patient['regimen_choices'][$vaccineId] ?? null;
                        $price = $centerVaccine->hasSalePrice() ? $centerVaccine->sale_price : $centerVaccine->price;
                        
                        if (!empty($regimenId)) {
                            $regimen = \Modules\VaccineRegistration\Models\VaccineRegimen::find((int)$regimenId);
                            if ($regimen && (int)$regimen->vaccine_id === (int)$vaccineId) {
                                if ($regimen->price !== null) {
                                    $price = $regimen->sale_price !== null ? $regimen->sale_price : $regimen->price;
                                } else {
                                    $price = $price * $regimen->doses;
                                }
                            } else {
                                $regimenId = null;
                            }
                        } else {
                            $regimenId = null;
                        }

                        $total += $price;
                        $items[$vaccineId] = [
                            'price' => $price,
                            'sale_price' => null,
                            'quantity' => 1,
                            'stock_committed_quantity' => 1,
                            'regimen_id' => $regimenId,
                        ];
                    }

                    $regCode = 'MCD-'.strtoupper(Str::random(8)).'-'.($index + 1);
                    $registration = Registration::create([
                        'registration_code' => $regCode,
                        'customer_id' => $customer->id,
                        'patient_name' => trim($patient['name']),
                        'patient_phone' => $phone,
                        'patient_dob' => $patient['dob'] ?? '2000-01-01',
                        'patient_gender' => $patient['gender'] ?? 'Khác',
                        'patient_address' => $patient['address'] ?? 'Tại trung tâm',
                        'guardian_name' => $validated['guardian_name'] ?? null,
                        'guardian_phone' => $validated['guardian_phone'] ?? null,
                        'center_id' => $currentCenter->id,
                        'center_name' => $currentCenter->name,
                        'injection_date' => $slot->schedule->date->toDateString(),
                        'slot_id' => $slot->id,
                        'status' => Registration::BOOKING_PENDING,
                        'booking_status' => Registration::BOOKING_PENDING,
                        'payment_status' => Registration::PAYMENT_UNPAID,
                        'payment_method' => $request->input('payment_method', 'Tại trung tâm'),
                        'idempotency_key' => $idempotencyKey ? ($hasPatientsArrayInRequest ? ($idempotencyKey.'_'.$index) : $idempotencyKey) : null,
                        'total_price' => $total,
                    ]);

                    $registration->vaccines()->attach($items);
                    $slot->increment('reserved_count');
                    $successCodes[] = $regCode;
                }
            });
        } catch (QueryException $exception) {
            if ($idempotencyKey) {
                $existing = Registration::where('idempotency_key', $idempotencyKey)
                    ->orWhere('idempotency_key', $idempotencyKey.'_0')
                    ->first();
                if ($existing) {
                    return $this->completePublicBooking($existing);
                }
            }
            throw $exception;
        }

        // Store result registration codes in session
        session()->forget('cart');
        session()->put('success_codes', $successCodes);
        if (! empty($successCodes)) {
            session()->put('success_code', $successCodes[0]);
        }

        $redirectUrl = route('register.success');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đăng ký tiêm chủng thành công!',
                'registration_codes' => $successCodes,
                'redirect_url' => $redirectUrl,
            ]);
        }

        return redirect()->to($redirectUrl);
    }

    private function completePublicBooking(Registration $registration)
    {
        session()->forget('cart');
        session()->put('success_code', $registration->registration_code);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đặt lịch tiêm chủng thành công!',
                'redirect_url' => route('register.success'),
            ]);
        }

        return redirect()->route('register.success');
    }

    private function newRegistrationCode(): string
    {
        do {
            $code = 'MCD-'.strtoupper(Str::random(8));
        } while (Registration::where('registration_code', $code)->exists());

        return $code;
    }

    /**
     * Hiển thị trang kết quả đăng ký tiêm thành công.
     */
    public function showSuccess()
    {
        $codes = session('success_codes');
        $singleCode = session('success_code');

        if (! $codes && $singleCode) {
            $codes = [$singleCode];
        }

        if (empty($codes)) {
            return redirect()->route('vaccine.index');
        }

        $registrations = Registration::with(['vaccines', 'slot'])->whereIn('registration_code', $codes)->get();
        $grandTotal = $registrations->sum('total_price');

        return view('vaccine::success', compact('registrations', 'grandTotal'));
    }

    /**
     * Hiển thị trang chi tiết nhóm bệnh phòng ngừa và form tư vấn.
     */
    public function diseaseDetail(Request $request, $disease)
    {
        $diseaseDecoded = trim(urldecode($disease));

        // Lọc danh sách vắc xin thuộc nhóm bệnh này
        $currentCenter = CenterContext::current();
        $vaccines = Vaccine::forCenter($currentCenter?->id)
            ->where(function ($q) use ($diseaseDecoded) {
                $q->where('category', $diseaseDecoded)
                    ->orWhere('disease_prevention', 'like', '%'.$diseaseDecoded.'%');
            })
            ->orderBy('center_vaccines.sort_order', 'asc')
            ->get();

        $cart = CenterContext::resolveCart($currentCenter?->id)['cart'];
        $centers = Center::active()->get();

        // Tìm bài viết mô tả chính xác của nhóm bệnh này
        $dbArticle = Article::where('category', $diseaseDecoded)->first();

        // "Admin sao thì client vậy": lấy trực tiếp từ bài viết, để trống nếu không có hoặc rỗng.
        $info = [
            'title' => ($dbArticle && trim($dbArticle->title) !== '') ? $dbArticle->title : $diseaseDecoded,
            'description' => $dbArticle ? (string) $dbArticle->content : '',
        ];

        return view('vaccine::disease', compact('diseaseDecoded', 'info', 'vaccines', 'cart', 'centers'));
    }

    /**
     * Xử lý gửi yêu cầu tư vấn nhóm bệnh (Lưu duy nhất vào consultation_leads).
     */
    public function postDiseaseConsult(Request $request, $disease)
    {
        $diseaseDecoded = urldecode($disease);

        $validator = Validator::make($request->all(), [
            'consultType' => 'required|string|in:online,offline',
            'customerName' => 'required|string|max:255',
            'customerPhone' => 'required|string|regex:/^[0-9]{9,11}$/',
            'centerName' => 'required_if:consultType,offline|nullable|string|max:255',
            'customerNote' => 'nullable|string|max:1000',
        ], [
            'customerName.required' => 'Vui lòng điền Họ tên người liên hệ.',
            'customerPhone.required' => 'Vui lòng điền Số điện thoại liên hệ.',
            'customerPhone.regex' => 'Số điện thoại liên hệ không hợp lệ (9 - 11 chữ số).',
            'centerName.required_if' => 'Vui lòng chọn trung tâm mong muốn tư vấn.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $currentCenter = CenterContext::current();

        $selectedCenter = null;
        if ($validated['consultType'] === 'offline') {
            $selectedCenter = Center::active()->where('name', $validated['centerName'])->first();
        }
        $selectedCenter = $selectedCenter ?: $currentCenter;

        $note = 'Hình thức: '.($validated['consultType'] === 'online' ? 'Trực tuyến' : 'Trực tiếp tại trung tâm').' - Đăng ký tư vấn: '.$diseaseDecoded.($validated['customerNote'] ? (' - Ghi chú: '.$validated['customerNote']) : '');

        DB::beginTransaction();
        try {
            // Lưu duy nhất vào consultation_leads, KHÔNG tạo dummy registration
            $lead = ConsultationLead::create([
                'name' => $validated['customerName'],
                'phone' => $validated['customerPhone'],
                'source' => 'Nhóm bệnh: '.$diseaseDecoded,
                'status' => 'new',
                'note' => $note,
                'center_id' => $selectedCenter?->id,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Gửi yêu cầu tư vấn thành công! Nhân viên y tế Medicare sẽ liên hệ hỗ trợ bạn qua số điện thoại '.$validated['customerPhone'].' trong thời gian sớm nhất.',
                'lead_id' => $lead->id,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Consultation lead creation error: '.$e->getMessage()."\n".$e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lưu yêu cầu tư vấn. Vui lòng thử lại.',
            ], 500);
        }
    }
}
