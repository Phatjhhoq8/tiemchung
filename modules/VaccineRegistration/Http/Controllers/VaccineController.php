<?php
/**
 * Chức năng: VaccineController xử lý danh mục vắc xin, giỏ hàng và quy trình đăng ký tiêm chủng của khách hàng.
 * Lý do chỉnh sửa: Khôi phục đầy đủ các hàm xử lý giỏ hàng (addToCart, removeFromCart, clearCart) và quy trình đăng ký tiêm.
 */

namespace Modules\VaccineRegistration\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\VaccineRegistration\Models\Vaccine;
use Modules\VaccineRegistration\Models\Registration;
use Modules\VaccineRegistration\Models\Center;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class VaccineController extends Controller
{
    /**
     * Hiển thị danh mục sản phẩm tiêm chủng với các bộ lọc động từ CSDL.
     */
    public function index(Request $request)
    {
        $query = Vaccine::query();
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
            default => $query->orderBy('views', 'desc')->orderBy('id', 'asc'),
        };

        $vaccines = $query->paginate(12)->withQueryString();
        $cart = session()->get('cart', []);

        $allVaccines = Vaccine::all();
        $diseaseOptions = $this->buildDiseaseOptions($allVaccines);
        $diseases = $diseaseOptions;
        
        $ageGroupOptions = $this->buildAgeGroupOptions($allVaccines);
        $ageGroups = $ageGroupOptions;

        $originOptions = $this->buildOriginOptions($allVaccines);
        $origins = $originOptions;

        $doseOptions = [1, 2, 3, 4];
        $doses = $doseOptions;

        $productCategories = $this->buildProductCategories($allVaccines);

        if ($request->ajax() || $request->wantsJson()) {
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
        $vaccine = Vaccine::findOrFail($id);
        
        // Tăng số lượt xem sản phẩm khi xem chi tiết
        $vaccine->increment('views');
        
        $cart = session()->get('cart', []);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'vaccine' => [
                    'id' => $vaccine->id,
                    'name' => $vaccine->name,
                    'price' => $vaccine->price,
                    'formatted_price' => number_format($vaccine->price, 0, ',', '.') . ' đ',
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
        $relatedVaccines = Vaccine::where('id', '!=', $vaccine->id)
            ->where(function ($q) use ($vaccine) {
                $q->where('disease_prevention', 'like', '%' . $vaccine->disease_prevention . '%')
                  ->orWhere('origin', $vaccine->origin);
            })
            ->take(8)
            ->get();

        if ($relatedVaccines->count() < 8) {
            $existingIds = $relatedVaccines->pluck('id')->push($vaccine->id)->toArray();
            $extraVaccines = Vaccine::whereNotIn('id', $existingIds)->take(8 - $relatedVaccines->count())->get();
            $relatedVaccines = $relatedVaccines->concat($extraVaccines);
        }

        return view('vaccine::show', compact('vaccine', 'cart', 'relatedVaccines'));
    }

    /**
     * Thêm vắc xin vào giỏ hàng (session).
     */
    public function addToCart(Request $request)
    {
        $vaccineId = $request->input('vaccine_id');
        $quantity = (int) $request->input('quantity', 1);

        $vaccine = Vaccine::findOrFail($vaccineId);
        $cart = session()->get('cart', []);

        $priceToUse = $vaccine->hasSalePrice() ? $vaccine->sale_price : $vaccine->price;

        if (isset($cart[$vaccineId])) {
            $cart[$vaccineId]['quantity'] = 1;
        } else {
            $cart[$vaccineId] = [
                'name' => $vaccine->name,
                'price' => $priceToUse,
                'image' => $vaccine->image ?: 'hexaxim.jpg',
                'quantity' => 1,
                'type' => $vaccine->type,
                'disease_prevention' => $vaccine->disease_prevention,
            ];
        }

        session()->put('cart', $cart);

        $totalPrice = collect($cart)->sum(function ($item) {
            return ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
        });

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

        $totalPrice = collect($cart)->sum(function ($item) {
            return ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
        });

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
     * Hiển thị trang đăng ký tiêm chủng (hoặc JSON cho SPA Modal).
     */
    public function showRegister(Request $request)
    {
        $cart = session()->get('cart', []);

        if ($request->has('add_vaccine_id')) {
            $vaccineId = $request->input('add_vaccine_id');
            $vaccine = Vaccine::find($vaccineId);
            if ($vaccine) {
                $priceToUse = $vaccine->hasSalePrice() ? $vaccine->sale_price : $vaccine->price;
                $cart[$vaccine->id] = [
                    'name' => $vaccine->name,
                    'price' => $priceToUse,
                    'quantity' => 1,
                    'image' => $vaccine->image ?: 'hexaxim.jpg',
                    'type' => $vaccine->type,
                    'disease_prevention' => $vaccine->disease_prevention,
                ];
                session()->put('cart', $cart);
            }
        }

        if (empty($cart) && ($request->ajax() || $request->wantsJson())) {
            $centers = Center::active()->get();
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng chọn ít nhất một loại vắc xin để đăng ký tiêm.',
                'centers' => $centers
            ], 400);
        }

        if (empty($cart)) {
            return redirect()->route('vaccine.index')->with('warning', 'Vui lòng chọn ít nhất một loại vắc xin để đăng ký.');
        }

        $totalPrice = collect($cart)->sum(function ($item) {
            return ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
        });
        $centers = Center::active()->get();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'cart' => $cart,
                'total_price' => $totalPrice,
                'formatted_total_price' => number_format($totalPrice, 0, ',', '.') . ' đ',
                'centers' => $centers
            ]);
        }

        return view('vaccine::register', compact('cart', 'totalPrice', 'centers'));
    }

    /**
     * Xử lý đăng ký tiêm chủng.
     */
    public function postRegister(Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Giỏ hàng của bạn đang trống.'], 400);
            }
            return redirect()->route('vaccine.index')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        // Validate dữ liệu
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'patients' => 'required|array|min:1',
            'patients.*.name' => 'required|string|max:255',
            'patients.*.dob' => 'required|date|before:today',
            'patients.*.gender' => 'required|string|in:Nam,Nữ,Khác',
            'patients.*.phone' => 'required|string|regex:/^[0-9]{9,11}$/',
            'patients.*.address' => 'required|string|max:500',
            'patients.*.vaccine_ids' => 'required|array|min:1',
            'patients.*.vaccine_ids.*' => 'exists:vaccines,id',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|regex:/^[0-9]{9,11}$/',
            'center_name' => 'required|string',
            'injection_date' => 'required|date|after_or_equal:today',
            'payment_method' => 'required|string|in:Tại trung tâm,QR,Thẻ,Chuyển khoản,Thẻ ATM / QR Code',
        ], [
            'patients.required' => 'Vui lòng cung cấp thông tin người đăng ký tiêm.',
            'patients.array' => 'Dữ liệu người đăng ký tiêm không hợp lệ.',
            'patients.*.name.required' => 'Họ tên người tiêm không được để trống.',
            'patients.*.dob.required' => 'Ngày sinh người tiêm không được để trống.',
            'patients.*.dob.before' => 'Ngày sinh người tiêm phải trước ngày hôm nay.',
            'patients.*.gender.required' => 'Vui lòng chọn giới tính người tiêm.',
            'patients.*.phone.required' => 'Số điện thoại liên hệ không được để trống.',
            'patients.*.phone.regex' => 'Số điện thoại không hợp lệ (9 - 11 chữ số).',
            'patients.*.address.required' => 'Địa chỉ người tiêm không được để trống.',
            'patients.*.vaccine_ids.required' => 'Vui lòng chọn ít nhất một loại vắc xin cho mỗi người.',
            'center_name.required' => 'Vui lòng chọn trung tâm tiêm chủng.',
            'injection_date.required' => 'Vui lòng chọn ngày tiêm dự kiến.',
            'injection_date.after_or_equal' => 'Ngày tiêm dự kiến không được ở quá khứ.',
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán.',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();
        $patientsData = $validated['patients'];
        $successCodes = [];

        DB::beginTransaction();
        try {
            foreach ($patientsData as $index => $patient) {
                // Generate unique registration code
                $registrationCode = 'MCD-' . strtoupper(\Illuminate\Support\Str::random(8)) . '-' . ($index + 1);

                // Load vaccine models to calculate the subtotal price for this patient
                $vaccines = Vaccine::whereIn('id', $patient['vaccine_ids'])->get();
                $subtotalPrice = $vaccines->sum(function ($v) {
                    return $v->hasSalePrice() ? $v->sale_price : $v->price;
                });

                // Create patient registration
                $registration = Registration::create([
                    'registration_code' => $registrationCode,
                    'patient_name' => $patient['name'],
                    'patient_dob' => $patient['dob'],
                    'patient_gender' => $patient['gender'],
                    'patient_phone' => $patient['phone'],
                    'patient_address' => $patient['address'],
                    'guardian_name' => $validated['guardian_name'] ?? null,
                    'guardian_phone' => $validated['guardian_phone'] ?? null,
                    'center_name' => $validated['center_name'],
                    'injection_date' => $validated['injection_date'],
                    'status' => $validated['payment_method'] === 'Tại trung tâm' ? 'Chờ thanh toán' : 'Đã thanh toán',
                    'payment_method' => $validated['payment_method'],
                    'total_price' => $subtotalPrice,
                ]);

                // Attach vaccines
                foreach ($vaccines as $v) {
                    $registration->vaccines()->attach($v->id, [
                        'price' => $v->hasSalePrice() ? $v->sale_price : $v->price,
                        'quantity' => 1,
                    ]);
                }

                $successCodes[] = $registrationCode;
            }

            DB::commit();

            // Clear session cart
            session()->forget('cart');
            session()->put('success_codes', $successCodes);
            if (!empty($successCodes)) {
                session()->put('success_code', $successCodes[0]);
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đăng ký tiêm chủng thành công!',
                    'registration_codes' => $successCodes,
                    'redirect' => route('register.success')
                ]);
            }

            return redirect()->route('register.success');

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Multi-patient registration error: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi lưu đăng ký: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi lưu đăng ký. Vui lòng thử lại.')->withInput();
        }
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

        $registrations = Registration::with('vaccines')->whereIn('registration_code', $codes)->get();
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
        $vaccines = Vaccine::where('category', 'like', '%' . $diseaseDecoded . '%')
            ->orWhere('disease_prevention', 'like', '%' . $diseaseDecoded . '%')
            ->orderBy('sort_order', 'asc')
            ->get();
              
        $cart = session()->get('cart', []);
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
            $info = [
                'title' => 'Vắc xin phòng bệnh ' . $diseaseDecoded,
                'description' => '<h6>Chủ động phòng ngừa bệnh ' . $diseaseDecoded . ' hiệu quả</h6><p>Bệnh <strong>' . $diseaseDecoded . '</strong> là bệnh truyền nhiễm có diễn biến phức tạp và có thể gây ra các biến chứng nguy hiểm đối với sức khỏe. Việc chủ động tiêm ngừa vắc xin là phương pháp phòng bệnh khoa học, an toàn và tiết kiệm nhất cho cả gia đình.</p><p>💉 Hãy liên hệ Medicare để nhận tư vấn chi tiết về phác đồ và lịch tiêm chủng vắc xin phòng bệnh ' . $diseaseDecoded . ' phù hợp nhất với độ tuổi của bạn.</p>'
            ];
        }

        return view('vaccine::disease', compact('diseaseDecoded', 'info', 'vaccines', 'cart', 'centers'));
    }

    /**
     * Xử lý gửi yêu cầu tư vấn nhóm bệnh.
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
        $registrationCode = 'MCD-CS-' . strtoupper(Str::random(6));

        // Lấy danh sách vắc xin của nhóm bệnh này để đính kèm (nếu có)
        $vaccines = Vaccine::where('category', 'like', '%' . $diseaseDecoded . '%')
            ->orWhere('disease_prevention', 'like', '%' . $diseaseDecoded . '%')
            ->get();

        $centerName = $validated['consultType'] === 'online' ? 'Tư vấn Online (Qua điện thoại)' : $validated['centerName'];
        $patientAddress = 'Hình thức: ' . ($validated['consultType'] === 'online' ? 'Online' : 'Trực tiếp tại trung tâm') . ' - Đăng ký tư vấn: ' . $diseaseDecoded . ($validated['customerNote'] ? (' - Ghi chú: ' . $validated['customerNote']) : '');

        DB::beginTransaction();
        try {
            // Tạo bản ghi tư vấn trong registrations
            $registration = Registration::create([
                'registration_code' => $registrationCode,
                'patient_name' => $validated['customerName'],
                'patient_dob' => '2000-01-01',
                'patient_gender' => 'Khác',
                'patient_phone' => $validated['customerPhone'],
                'patient_address' => $validated['consultType'] === 'online' ? $patientAddress : 'Đăng ký tư vấn trực tiếp: ' . $diseaseDecoded . ($validated['customerNote'] ? (' - Ghi chú: ' . $validated['customerNote']) : ''),
                'guardian_name' => null,
                'guardian_phone' => null,
                'center_name' => $centerName,
                'injection_date' => now()->toDateString(),
                'status' => 'Chờ tư vấn',
                'payment_method' => 'Tại trung tâm',
                'total_price' => 0,
            ]);

            // Đính kèm các vắc xin thuộc nhóm bệnh nếu có
            if ($vaccines->isNotEmpty()) {
                foreach ($vaccines as $vac) {
                    $registration->vaccines()->attach($vac->id, ['price' => $vac->price]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Gửi yêu cầu tư vấn thành công! Nhân viên y tế Medicare sẽ liên hệ hỗ trợ bạn qua SĐT ' . $validated['customerPhone'] . ' trong thời gian sớm nhất.',
                'registration_code' => $registrationCode
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lưu yêu cầu tư vấn: ' . $e->getMessage()
            ], 500);
        }
    }
}
