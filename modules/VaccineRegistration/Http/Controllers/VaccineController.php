<?php
/**
 * Chức năng: VaccineController xử lý danh mục vắc xin, giỏ hàng và quy trình đăng ký tiêm chủng của khách hàng.
 * Lý do chỉnh sửa: Xử lý danh mục, giỏ hàng và quy trình đặt lịch một người theo chi nhánh.
 */

namespace Modules\VaccineRegistration\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\VaccineRegistration\Models\Vaccine;
use Modules\VaccineRegistration\Models\Registration;
use Modules\VaccineRegistration\Models\Center;
use Modules\VaccineRegistration\Models\CenterVaccine;
use Modules\VaccineRegistration\Models\Schedule;
use Modules\VaccineRegistration\Models\Slot;
use Modules\VaccineRegistration\Models\Customer;
use Modules\VaccineRegistration\Support\CenterContext;
use Modules\VaccineRegistration\Support\PhoneNormalizer;
use Modules\VaccineRegistration\Models\ConsultationLead;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class VaccineController extends Controller
{
    /**
     * Hiển thị danh mục sản phẩm tiêm chủng với các bộ lọc động từ CSDL.
     */
    public function index(Request $request)
    {
        $currentCenter = CenterContext::current();
        $query = Vaccine::forCenter($currentCenter?->id);
        $type = $request->input('type');

        // Tìm kiếm theo tên sản phẩm. Lọc theo bệnh dùng tham số disease riêng.
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', '%' . $search . '%');
        }

        if ($request->filled('disease')) {
            $disease = $request->input('disease');
            $query->where(function ($q) use ($disease) {
                $q->where('disease_prevention', 'like', '%' . $disease . '%')
                    ->orWhere('category', 'like', '%' . $disease . '%');
            });
        }

        // Lọc theo nhóm tuổi chỉ định
        if ($request->filled('age_group')) {
            $query->where('age_group', 'like', '%' . $request->input('age_group') . '%');
        }

        if ($request->filled('origin')) {
            $query->where('origin', $request->input('origin'));
        }

        if ($request->filled('doses')) {
            $query->where('doses', (int) $request->input('doses'));
        }

        if (in_array($type, ['single', 'package'], true)) {
            $query->where('type', $type);
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
                if (!empty($vaccine->category)) {
                    $items[] = trim($vaccine->category);
                }

                if (!empty($vaccine->disease_prevention)) {
                    $parts = preg_split('/[,;\-\/]+/', $vaccine->disease_prevention);
                    foreach ($parts as $part) {
                        $cleaned = trim($part);
                        if (!empty($cleaned)) {
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
            ->filter(fn ($vaccine) => !empty($vaccine->category) || !empty($vaccine->disease_prevention))
            ->groupBy(fn ($vaccine) => $vaccine->category ?: $this->buildDiseaseOptions(collect([$vaccine]))->first())
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

        if ($request->header('X-Vaccine-Detail-Json') || ($request->wantsJson() && !$request->header('X-SPA-Request') && !$request->ajax())) {
            return response()->json([
                'success' => true,
                'vaccine' => [
                    'id' => $vaccine->id,
                    'name' => $vaccine->name,
                    'price' => $vaccine->hasSalePrice() ? $vaccine->sale_price : $vaccine->price,
                    'formatted_price' => number_format($vaccine->hasSalePrice() ? $vaccine->sale_price : $vaccine->price, 0, ',', '.') . ' đ',
                    'type' => $vaccine->type,
                    'type_label' => $vaccine->type === 'package' ? 'Gói vắc xin' : 'Vắc xin lẻ',
                    'doses' => $vaccine->doses,
                    'disease_prevention' => $vaccine->disease_prevention,
                    'age_group' => $vaccine->age_group,
                    'origin' => $vaccine->origin,
                    'manufacturer' => $vaccine->manufacturer,
                    'dosage' => $vaccine->dosage,
                    'description' => $vaccine->description,
                    'image' => asset('images/vaccines/' . ($vaccine->image ?: 'hexaxim.jpg')),
                    'is_in_cart' => isset($cart[$vaccine->id]),
                    'views' => $vaccine->views,
                    'formatted_views' => number_format($vaccine->views, 0, ',', '.') . ' lượt xem',
                ]
            ]);
        }

        // Lấy 8 vắc xin liên quan cùng phòng bệnh hoặc cùng xuất xứ
        $relatedVaccines = Vaccine::forCenter($currentCenter?->id)->where('vaccines.id', '!=', $vaccine->id)
            ->where(function ($q) use ($vaccine) {
                $q->where('disease_prevention', 'like', '%' . $vaccine->disease_prevention . '%')
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
        $isAvailable = $currentCenter && CenterVaccine::where('center_id', $currentCenter->id)
            ->where('vaccine_id', $vaccine->id)
            ->where('is_active', true)
            ->where('stock_status', '!=', 'out_of_stock')
            ->exists();

        if (!$isAvailable) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Sản phẩm không được bán tại chi nhánh đang chọn.'], 422);
            }

            return back()->with('error', 'Sản phẩm không được bán tại chi nhánh đang chọn.');
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$vaccineId])) {
            $cart[$vaccineId]['quantity'] = 1;
        } else {
            $cart[$vaccineId] = [
                'name' => $vaccine->name,
                'price' => 0,
                'image' => $vaccine->image ?: 'hexaxim.jpg',
                'quantity' => 1,
                'type' => $vaccine->type,
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
                'formatted_total_price' => number_format($totalPrice, 0, ',', '.') . ' đ',
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
                'formatted_total_price' => number_format($totalPrice, 0, ',', '.') . ' đ',
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
                ->where('stock_status', '!=', 'out_of_stock')
                ->exists();

            if ($isAvailable) {
                $cart = session()->get('cart', []);
                $cart[$vaccine->id] = [
                    'name' => $vaccine->name,
                    'price' => 0,
                    'quantity' => 1,
                    'image' => $vaccine->image ?: 'hexaxim.jpg',
                    'type' => $vaccine->type,
                    'disease_prevention' => $vaccine->disease_prevention,
                ];
                session()->put('cart', $cart);
            }
        }

        $cartState = CenterContext::resolveCart($currentCenter->id);
        $isEmptyCart = empty($cartState['cart']);
        $centers = CenterContext::activeCenters();

        // Tự động sinh lịch 30 ngày từ cấu hình mặc định
        Schedule::generateFromDefaults($currentCenter->id, today(), today()->addDays(30));

        $schedules = Schedule::query()
            ->where('center_id', $currentCenter->id)
            ->where('is_active', true)
            ->whereDate('date', '>=', today())
            ->with(['slots' => function ($query) {
                $query->where('is_active', true)
                    ->whereColumn('reserved_count', '<', 'capacity')
                    ->orderBy('start_at');
            }])
            ->orderBy('date')
            ->get()
            ->filter(fn (Schedule $schedule) => $schedule->slots->isNotEmpty())
            ->values();

        if ($request->ajax() || $request->wantsJson()) {
            if ($isEmptyCart) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn chưa chọn vắc xin nào',
                    'centers' => $centers,
                    'current_center' => $currentCenter
                ]);
            }

            return response()->json([
                'success' => true,
                'cart' => $cartState['cart'],
                'total_price' => $cartState['total_price'],
                'unavailable_count' => $cartState['unavailable_count'],
                'schedules' => $schedules,
                'centers' => $centers,
                'current_center' => $currentCenter
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
        if (!$phone) {
            return back()->withErrors(['phone' => 'Số điện thoại di động Việt Nam không hợp lệ.'])->withInput();
        }

        $code = trim((string)$request->input('registration_code'));

        $registrations = Registration::query()
            ->with(['vaccines:id,name', 'slot.schedule'])
            ->where(function ($query) use ($phone) {
                $query->where('patient_phone', $phone)
                    ->orWhereHas('customer', fn ($customerQuery) => $customerQuery->where('phone', $phone));
            })
            ->orderByDesc('injection_date')
            ->orderByDesc('id')
            ->get();

        $registrations->each(function ($reg) use ($code) {
            if (!empty($code) && strtolower($reg->registration_code) === strtolower($code)) {
                $reg->is_masked = false;
                $reg->display_name = $reg->patient_name;
                $reg->display_code = $reg->registration_code;
                $reg->display_price = number_format($reg->netPaidAmount(), 0, ',', '.') . ' đ';
                $reg->display_vaccines = $reg->vaccines->map(fn ($vaccine) => $vaccine->name . (($vaccine->pivot->quantity ?? 1) > 1 ? ' x' . $vaccine->pivot->quantity : ''))->implode(', ');
            } else {
                $reg->is_masked = true;
                $reg->display_name = self::maskName($reg->patient_name);
                $reg->display_code = self::maskCode($reg->registration_code);
                $reg->display_price = '*** đ';
                $reg->display_vaccines = $reg->vaccines->count() . ' loại vắc xin (Nhập Mã đặt lịch để xem chi tiết)';
            }
        });

        return view('vaccine::booking_lookup', [
            'lookedUp' => true,
            'registrations' => $registrations,
            'phone' => $phone,
            'registration_code' => $code,
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
            return mb_substr($name, 0, 1, 'UTF-8') . '*' . mb_substr($name, -1, 1, 'UTF-8');
        }
        $first = $parts[0];
        $last = $parts[count($parts) - 1];
        return $first . ' * ' . $last;
    }

    public static function maskCode(string $code): string
    {
        $parts = explode('-', $code);
        if (count($parts) < 3) {
            return substr($code, 0, 3) . '-***';
        }
        return $parts[0] . '-***-' . $parts[2];
    }

    /**
     * Create one booking. Prices and branch membership are always resolved server-side.
     */
    public function postRegister(Request $request)
    {
        $hasPatientsArrayInRequest = $request->has('patients');

        // 1. Pack single patient fields into patients array if client sends legacy format
        if (!$hasPatientsArrayInRequest && $request->filled('patient_name')) {
            $request->merge([
                'patients' => [
                    [
                        'name' => $request->input('patient_name'),
                        'phone' => $request->input('patient_phone'),
                        'dob' => $request->input('patient_dob', '2000-01-01'),
                        'gender' => $request->input('patient_gender', 'Khác'),
                        'address' => $request->input('patient_address', 'Tại trung tâm'),
                        'vaccine_ids' => $request->input('vaccine_ids', []),
                    ]
                ]
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
            'patients.*.vaccine_ids.*' => 'required|integer|distinct|exists:vaccines,id',
            'slot_id' => 'required|integer|exists:slots,id',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:30',
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

        $idempotencyKey = $validated['idempotency_key'] ?? null;
        if ($idempotencyKey) {
            $existing = Registration::where('idempotency_key', $idempotencyKey)
                ->orWhere('idempotency_key', $idempotencyKey . '_0')
                ->first();
            if ($existing) {
                return $this->completePublicBooking($existing);
            }
        }

        $successCodes = [];

        try {
            DB::transaction(function () use ($validated, $currentCenter, &$successCodes, $idempotencyKey, $request, $hasPatientsArrayInRequest) {
                // Lock slot
                $slot = Slot::with('schedule')->whereKey($validated['slot_id'])->lockForUpdate()->firstOrFail();
                if (!$slot->is_active || !$slot->schedule || !$slot->schedule->is_active
                    || (int) $slot->schedule->center_id !== (int) $currentCenter->id
                    || $slot->schedule->date->isBefore(today())) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'slot_id' => 'Khung giờ không khả dụng hoặc không thuộc chi nhánh đang chọn.',
                    ]);
                }

                $patientCount = count($validated['patients']);
                if ($slot->reserved_count + $patientCount > $slot->capacity) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'slot_id' => 'Khung giờ này chỉ còn lại ' . ($slot->capacity - $slot->reserved_count) . ' chỗ trống.',
                    ]);
                }

                // Check stock for all unique vaccines selected
                $allVaccineIds = collect($validated['patients'])->flatMap(fn($p) => $p['vaccine_ids'])->unique()->toArray();
                $centerVaccines = CenterVaccine::with('vaccine')
                    ->where('center_id', $currentCenter->id)
                    ->whereIn('vaccine_id', $allVaccineIds)
                    ->where('is_active', true)
                    ->where('stock_status', '!=', 'out_of_stock')
                    ->get()
                    ->keyBy('vaccine_id');

                if ($centerVaccines->count() !== count($allVaccineIds)) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'vaccine_ids' => 'Một hoặc nhiều vắc xin đã hết hàng hoặc không được bán tại chi nhánh này.',
                    ]);
                }

                // Create registrations for each profile
                foreach ($validated['patients'] as $index => $patient) {
                    $phone = PhoneNormalizer::normalize($patient['phone']);
                    if (!$phone) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            "patients.{$index}.phone" => "Số điện thoại của người tiêm #" . ($index + 1) . " không hợp lệ.",
                        ]);
                    }

                    $customer = Customer::where('phone', $phone)->lockForUpdate()->first();
                    if (!$customer) {
                        DB::table('customers')->insertOrIgnore([
                            'name' => trim($patient['name']),
                            'phone' => $phone,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $customer = Customer::where('phone', $phone)->lockForUpdate()->firstOrFail();
                    }

                    $items = [];
                    $total = 0;
                    foreach ($patient['vaccine_ids'] as $vaccineId) {
                        $centerVaccine = $centerVaccines->get($vaccineId);
                        $price = $centerVaccine->hasSalePrice() ? $centerVaccine->sale_price : $centerVaccine->price;
                        $total += $price;
                        $items[$vaccineId] = [
                            'price' => $price,
                            'sale_price' => null,
                            'quantity' => 1,
                        ];
                    }

                    $regCode = 'MCD-' . strtoupper(\Illuminate\Support\Str::random(8)) . '-' . ($index + 1);
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
                        'idempotency_key' => $idempotencyKey ? ($hasPatientsArrayInRequest ? ($idempotencyKey . '_' . $index) : $idempotencyKey) : null,
                        'total_price' => $total,
                    ]);

                    $registration->vaccines()->attach($items);
                    $slot->increment('reserved_count');
                    $successCodes[] = $regCode;
                }
            });
        } catch (\Illuminate\Database\QueryException $exception) {
            if ($idempotencyKey) {
                $existing = Registration::where('idempotency_key', $idempotencyKey)
                    ->orWhere('idempotency_key', $idempotencyKey . '_0')
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
        if (!empty($successCodes)) {
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
                'redirect_url' => route('register.success')
            ]);
        }

        return redirect()->route('register.success');
    }

    private function newRegistrationCode(): string
    {
        do {
            $code = 'MCD-' . strtoupper(Str::random(8));
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

        if (!$codes && $singleCode) {
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
        $diseaseDecoded = urldecode($disease);
        
        // Lọc danh sách vắc xin thuộc nhóm bệnh này
        $currentCenter = CenterContext::current();
        $vaccines = Vaccine::forCenter($currentCenter?->id)
            ->where(function ($q) use ($diseaseDecoded) {
                $q->where('category', 'like', '%' . $diseaseDecoded . '%')
                    ->orWhere('disease_prevention', 'like', '%' . $diseaseDecoded . '%');
            })
            ->orderBy('center_vaccines.sort_order', 'asc')
            ->get();
               
        $cart = CenterContext::resolveCart($currentCenter?->id)['cart'];
        $centers = Center::active()->get();

        // Mảng dữ liệu tĩnh chứa thông tin sơ bộ chuyên môn cho các bệnh phổ biến
        $diseaseData = [
            'hpv' => [
                'title' => 'Vắc xin phòng Ung thư do HPV',
                'description' => '<h6>Phòng ngừa ung thư do HPV thế nào cho hiệu quả?</h6><p><strong>HPV (Human Papillomavirus)</strong> là nguyên nhân chính gây ung thư cổ tử cung và các bệnh lý nguy hiểm khác như mụn cóc sinh dục, ung thư hậu môn, dương vật, hầu họng… Bệnh thường lây nhiễm rất nhanh qua đường quan hệ tình dục, diễn tiến âm thầm và cực kỳ khó phát hiện sớm.</p><p>💉 <strong>Tiêm vắc xin đúng lịch, đủ liều</strong> là biện pháp phòng ngừa hiệu quả chủ động các bệnh lý liên quan đến HPV, đặc biệt là ung thư cổ tử cung ở nữ giới.</p><p>🛡️ <strong>Gardasil (Mỹ)</strong>: Bảo vệ cơ thể khỏi 4 chủng virus HPV phổ biến (6, 11, 16, 18), khuyến cáo tiêm cho nữ từ 9 đến 26 tuổi.</p><p>🛡️ <strong>Gardasil 9 (Mỹ)</strong>: Bảo vệ cơ thể khỏi 9 chủng virus HPV phổ biến (6, 11, 16, 18, 31, 33, 45, 52, 58), mở rộng chỉ định tiêm phòng cho cả nam và nữ giới từ 9 đến 45 tuổi.</p>'
            ],
            'cúm' => [
                'title' => 'Vắc xin phòng bệnh Cúm Mùa',
                'description' => '<h6>Bảo vệ lá phổi khỏe mạnh trước đại dịch Cúm Mùa</h6><p><strong>Cúm mùa</strong> là bệnh nhiễm trùng đường hô hấp cấp tính do virus cúm (Influenza) gây ra. Bệnh lây lan rất nhanh qua giọt bắn và có thể dẫn đến các biến chứng nguy hiểm như viêm phổi nặng, suy hô hấp, viêm cơ tim, thậm chí tử vong ở người cao tuổi và trẻ nhỏ.</p><p>💉 <strong>Tiêm vắc xin cúm hằng năm</strong> giúp giảm đến 90% nguy cơ mắc bệnh và 80% nguy cơ tử vong do các biến chứng nguy hiểm của cúm.</p>'
            ],
            'thủy đậu' => [
                'title' => 'Vắc xin phòng bệnh Thủy Đậu',
                'description' => '<h6>Chủ động phòng ngừa biến chứng nguy hiểm của Thủy đậu</h6><p><strong>Bệnh thủy đậu</strong> do virus Varicella-Zoster gây ra. Bệnh lây qua đường hô hấp hoặc tiếp xúc trực tiếp với dịch bóng nước. Mặc dù là bệnh lành tính trong nhiều trường hợp, thủy đậu có thể dẫn đến các biến chứng nặng nề như viêm màng não, viêm phổi, nhiễm trùng huyết và để lại sẹo vĩnh viễn trên cơ thể.</p><p>💉 <strong>Tiêm vắc xin thủy đậu</strong> giúp bảo vệ cơ thể khỏi nguy cơ lây nhiễm lên đến 95%. Khuyến cáo tiêm phòng cho trẻ em từ 9 tháng tuổi trở lên và người trưởng thành chưa có kháng thể.</p>'
            ],
            'ho gà' => [
                'title' => 'Vắc xin phòng Bạch hầu - Ho gà - Uốn ván',
                'description' => '<h6>Bảo vệ gia đình khỏi Bạch hầu - Ho gà - Uốn ván</h6><p><strong>Bạch hầu, Ho gà và Uốn ván</strong> là những bệnh truyền nhiễm nguy hiểm gây ra bởi vi khuẩn, có tỷ lệ tử vong cao đặc biệt ở trẻ sơ sinh dưới 1 tuổi. Bệnh ho gà có thể gây ra những cơn ho rũ rượi kéo dài dẫn đến ngừng thở; bạch hầu gây giả mạc làm tắc đường thở; uốn ván gây co cứng cơ cực kỳ đau đớn.</p><p>💉 <strong>Tiêm vắc xin kết hợp (như 6 trong 1, 5 trong 1, hoặc vắc xin Adacel/Boostrix)</strong> là giải pháp toàn diện để tạo lá chắn vững chắc bảo vệ trẻ nhỏ và người lớn khỏi 3 căn bệnh nguy hiểm này.</p>'
            ],
            'phế cầu' => [
                'title' => 'Vắc xin phòng các bệnh do Phế Cầu Khuẩn',
                'description' => '<h6>Phòng ngừa Viêm phổi, Viêm màng não do Phế cầu khuẩn</h6><p><strong>Phế cầu khuẩn (Streptococcus pneumoniae)</strong> là tác nhân hàng đầu gây ra các bệnh lý nghiêm trọng đe dọa tính mạng như viêm phổi, viêm màng não, nhiễm trùng huyết và viêm tai giữa cấp tính, đặc biệt ở trẻ nhỏ dưới 5 tuổi và người già trên 65 tuổi hoặc người suy giảm miễn dịch.</p><p>💉 <strong>Vắc xin phế cầu (như Synflorix hoặc Prevenar 13)</strong> giúp bảo vệ cơ thể chủ động chống lại các chủng phế cầu khuẩn phổ biến, làm giảm gánh nặng bệnh tật và ngăn ngừa các di chứng thần kinh vĩnh viễn.</p>'
            ]
        ];

        // So khớp thông minh không phân biệt hoa thường
        $info = null;
        $searchKey = mb_strtolower($diseaseDecoded, 'UTF-8');
        foreach ($diseaseData as $key => $data) {
            if (mb_strpos($searchKey, $key, 0, 'UTF-8') !== false || mb_strpos($key, $searchKey, 0, 'UTF-8') !== false) {
                $info = $data;
                break;
            }
        }

        // Fallback mặc định nếu không khớp bệnh phổ biến
        if (!$info) {
            $safeDisease = htmlspecialchars($diseaseDecoded, ENT_QUOTES, 'UTF-8');
            $info = [
                'title' => 'Vắc xin phòng bệnh ' . $safeDisease,
                'description' => '<h6>Chủ động phòng ngừa bệnh ' . $safeDisease . ' hiệu quả</h6><p>Bệnh <strong>' . $safeDisease . '</strong> là bệnh truyền nhiễm có diễn biến phức tạp và có thể gây ra các biến chứng nguy hiểm đối với sức khỏe. Việc chủ động tiêm ngừa vắc xin là phương pháp phòng bệnh khoa học, an toàn và tiết kiệm nhất cho cả gia đình.</p><p>💉 Hãy liên hệ Medicare để nhận tư vấn chi tiết về phác đồ và lịch tiêm chủng vắc xin phòng bệnh ' . $safeDisease . ' phù hợp nhất với độ tuổi của bạn.</p>'
            ];
        }

        return view('vaccine::disease', compact('diseaseDecoded', 'info', 'vaccines', 'cart', 'centers'));
    }

    /**
     * Xử lý gửi yêu cầu tư vấn nhóm bệnh (Lưu duy nhất vào consultation_leads).
     */
    public function postDiseaseConsult(Request $request, $disease)
    {
        $diseaseDecoded = urldecode($disease);

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
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
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();
        $currentCenter = CenterContext::current();

        $selectedCenter = null;
        if ($validated['consultType'] === 'offline') {
            $selectedCenter = Center::active()->where('name', $validated['centerName'])->first();
        }
        $selectedCenter = $selectedCenter ?: $currentCenter;

        $note = 'Hình thức: ' . ($validated['consultType'] === 'online' ? 'Online' : 'Trực tiếp tại trung tâm') . ' - Đăng ký tư vấn: ' . $diseaseDecoded . ($validated['customerNote'] ? (' - Ghi chú: ' . $validated['customerNote']) : '');

        DB::beginTransaction();
        try {
            // Lưu duy nhất vào consultation_leads, KHÔNG tạo dummy registration
            $lead = ConsultationLead::create([
                'name' => $validated['customerName'],
                'phone' => $validated['customerPhone'],
                'source' => 'Nhóm bệnh: ' . $diseaseDecoded,
                'status' => 'new',
                'note' => $note,
                'center_id' => $selectedCenter?->id,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Gửi yêu cầu tư vấn thành công! Nhân viên y tế Medicare sẽ liên hệ hỗ trợ bạn qua SĐT ' . $validated['customerPhone'] . ' trong thời gian sớm nhất.',
                'lead_id' => $lead->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Consultation lead creation error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lưu yêu cầu tư vấn. Vui lòng thử lại.'
            ], 500);
        }
    }

}
