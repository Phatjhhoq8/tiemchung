<?php

namespace Modules\VaccineRegistration\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\VaccineRegistration\Models\Vaccine;
use Modules\VaccineRegistration\Models\Registration;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class VaccineController extends Controller
{
    /**
     * Display the vaccine catalog and selection page.
     */
    public function index(Request $request)
    {
        $query = Vaccine::query();

        // Tìm kiếm theo tên hoặc công dụng phòng bệnh
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('disease_prevention', 'like', '%' . $search . '%');
            });
        }

        // Lọc theo nhóm tuổi chỉ định
        if ($request->filled('age_group')) {
            $query->where('age_group', 'like', '%' . $request->input('age_group') . '%');
        }

        $vaccines = $query->get();
        $cart = session()->get('cart', []);

        // Danh sách các bệnh phổ biến để hiển thị bộ lọc nhanh trên giao diện
        $diseases = [
            'Bạch hầu, ho gà, uốn ván',
            'Bại liệt, Hib, viêm gan B',
            'Ung thư cổ tử cung',
            'Viêm phổi, viêm màng não',
            'Cúm mùa',
            'Tiêu chảy cấp Rota',
            'Bệnh thủy đậu'
        ];

        return view('vaccine::index', compact('vaccines', 'cart', 'diseases'));
    }

    /**
     * Add a vaccine to the session-based cart.
     */
    public function addToCart(Request $request)
    {
        $vaccineId = $request->input('vaccine_id');
        $vaccine = Vaccine::find($vaccineId);

        if (!$vaccine) {
            return response()->json(['error' => 'Vắc xin không tồn tại.'], 404);
        }

        $cart = session()->get('cart', []);

        // Nếu vắc xin chưa có trong giỏ hàng thì thêm vào (chỉ cần tiêm 1 mũi đăng ký mỗi loại tại 1 thời điểm)
        if (!isset($cart[$vaccineId])) {
            $cart[$vaccineId] = [
                'name' => $vaccine->name,
                'price' => $vaccine->price,
                'disease_prevention' => $vaccine->disease_prevention,
                'origin' => $vaccine->origin,
                'image' => $vaccine->image,
            ];
            session()->put('cart', $cart);
        }

        return response()->json([
            'success' => true,
            'cart' => $cart,
            'cart_count' => count($cart),
            'total_price' => collect($cart)->sum('price')
        ]);
    }

    /**
     * Remove a vaccine from the session-based cart.
     */
    public function removeFromCart(Request $request)
    {
        $vaccineId = $request->input('vaccine_id');
        $cart = session()->get('cart', []);

        if (isset($cart[$vaccineId])) {
            unset($cart[$vaccineId]);
            session()->put('cart', $cart);
        }

        return response()->json([
            'success' => true,
            'cart' => $cart,
            'cart_count' => count($cart),
            'total_price' => collect($cart)->sum('price')
        ]);
    }

    /**
     * Show the registration form.
     */
    public function showRegister()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('vaccine.index')->with('warning', 'Vui lòng chọn ít nhất một loại vắc xin để đăng ký.');
        }

        $totalPrice = collect($cart)->sum('price');
        
        // Danh sách các trung tâm tiêm chủng VNVC giả lập
        $centers = [
            'VNVC Trường Chinh (Hà Nội)',
            'VNVC Icon 4 Cầu Giấy (Hà Nội)',
            'VNVC Hoàng Văn Thụ (TP.HCM)',
            'VNVC Cantavil Quận 2 (TP.HCM)',
            'VNVC Nguyễn Hữu Thọ (Đà Nẵng)',
            'VNVC Lê Hồng Phong (Nha Trang)',
            'VNVC Mậu Thân (Cần Thơ)'
        ];

        return view('vaccine::register', compact('cart', 'totalPrice', 'centers'));
    }

    /**
     * Process the registration form submission.
     */
    public function postRegister(Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('vaccine.index')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        // Validate dữ liệu đầu vào
        $validated = $request->validate([
            'patient_name' => 'required|string|max:255',
            'patient_dob' => 'required|date|before:today',
            'patient_gender' => 'required|string|in:Nam,Nữ,Khác',
            'patient_phone' => 'required|string|regex:/^[0-9]{9,11}$/',
            'patient_address' => 'required|string|max:500',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|regex:/^[0-9]{9,11}$/',
            'center_name' => 'required|string',
            'injection_date' => 'required|date|after_or_equal:today',
            'payment_method' => 'required|string|in:QR,Thẻ,Tại trung tâm',
        ], [
            'patient_name.required' => 'Họ tên người tiêm không được để trống.',
            'patient_dob.required' => 'Ngày sinh không được để trống.',
            'patient_dob.before' => 'Ngày sinh phải ở trong quá khứ.',
            'patient_gender.required' => 'Vui lòng chọn giới tính.',
            'patient_phone.required' => 'Số điện thoại không được để trống.',
            'patient_phone.regex' => 'Số điện thoại không hợp lệ (9 - 11 chữ số).',
            'patient_address.required' => 'Vui lòng điền địa chỉ liên hệ.',
            'center_name.required' => 'Vui lòng chọn trung tâm tiêm chủng.',
            'injection_date.required' => 'Vui lòng chọn ngày mong muốn tiêm.',
            'injection_date.after_or_equal' => 'Ngày tiêm chủng phải từ ngày hôm nay trở đi.',
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán.'
        ]);

        $totalPrice = collect($cart)->sum('price');
        
        // Sinh mã đăng ký duy nhất
        $registrationCode = 'VNVC-' . strtoupper(Str::random(8));

        DB::beginTransaction();
        try {
            // 1. Tạo bản ghi đăng ký
            $registration = Registration::create([
                'registration_code' => $registrationCode,
                'patient_name' => $validated['patient_name'],
                'patient_dob' => $validated['patient_dob'],
                'patient_gender' => $validated['patient_gender'],
                'patient_phone' => $validated['patient_phone'],
                'patient_address' => $validated['patient_address'],
                'guardian_name' => $validated['guardian_name'],
                'guardian_phone' => $validated['guardian_phone'],
                'center_name' => $validated['center_name'],
                'injection_date' => $validated['injection_date'],
                'status' => $validated['payment_method'] === 'Tại trung tâm' ? 'Chờ thanh toán' : 'Đã thanh toán',
                'payment_method' => $validated['payment_method'],
                'total_price' => $totalPrice,
            ]);

            // 2. Liên kết các vắc xin trong giỏ hàng vào bảng pivot
            foreach ($cart as $id => $item) {
                $registration->vaccines()->attach($id, ['price' => $item['price']]);
            }

            DB::commit();

            // Xóa giỏ hàng
            session()->forget('cart');

            // Lưu mã đăng ký vào session flash để chuyển sang trang hoàn tất
            return redirect()->route('register.success')->with('success_code', $registrationCode);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Có lỗi xảy ra trong quá trình đăng ký: ' . $e->getMessage());
        }
    }

    /**
     * Display the successful registration page.
     */
    public function showSuccess()
    {
        $code = session('success_code');

        if (!$code) {
            return redirect()->route('vaccine.index');
        }

        $registration = Registration::with('vaccines')->where('registration_code', $code)->firstOrFail();

        return view('vaccine::success', compact('registration'));
    }
}
