<?php
/**
 * Chức năng: VaccineController xử lý danh mục vắc xin, giỏ hàng và quy trình đăng ký tiêm chủng của khách hàng.
 * Lý do chỉnh sửa: Thay thế hoàn toàn dữ liệu tĩnh bằng dữ liệu động từ CSDL (danh sách trung tâm, nhóm bệnh), bổ sung trang chi tiết và hỗ trợ dọn giỏ hàng.
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
     * Hiển thị bảng giá vắc xin (lẻ & gói) với các bộ lọc động.
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

        // Lọc theo loại (single / package)
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        $vaccines = $query->get();
        $cart = session()->get('cart', []);

        // Lấy danh sách các nhóm bệnh phòng ngừa động từ CSDL
        $diseases = Vaccine::select('disease_prevention')
            ->distinct()
            ->get()
            ->pluck('disease_prevention')
            ->map(function($item) {
                // Tách các nhóm bệnh được phân cách bởi dấu phẩy
                return array_map('trim', explode(',', $item));
            })
            ->flatten()
            ->unique()
            ->values()
            ->all();

        return view('vaccine::index', compact('vaccines', 'cart', 'diseases'));
    }

    /**
     * Hiển thị trang chi tiết một loại vắc xin.
     */
    public function show($id)
    {
        $vaccine = Vaccine::findOrFail($id);
        $cart = session()->get('cart', []);

        return view('vaccine::show', compact('vaccine', 'cart'));
    }

    /**
     * Thêm vắc xin vào giỏ hàng (session).
     */
    public function addToCart(Request $request)
    {
        $vaccineId = $request->input('vaccine_id');
        $vaccine = Vaccine::find($vaccineId);

        if (!$vaccine) {
            return response()->json(['error' => 'Vắc xin không tồn tại.'], 404);
        }

        $cart = session()->get('cart', []);

        if (!isset($cart[$vaccineId])) {
            $cart[$vaccineId] = [
                'name' => $vaccine->name,
                'price' => $vaccine->price,
                'type' => $vaccine->type,
                'doses' => $vaccine->doses,
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

        return response()->json([
            'success' => true,
            'cart' => $cart,
            'cart_count' => count($cart),
            'total_price' => collect($cart)->sum('price')
        ]);
    }

    /**
     * Xóa sạch giỏ hàng.
     */
    public function clearCart()
    {
        session()->forget('cart');

        return response()->json([
            'success' => true,
            'cart_count' => 0,
            'total_price' => 0
        ]);
    }

    /**
     * Hiển thị trang đăng ký tiêm chủng.
     */
    public function showRegister()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('vaccine.index')->with('warning', 'Vui lòng chọn ít nhất một loại vắc xin để đăng ký.');
        }

        $totalPrice = collect($cart)->sum('price');
        
        // Lấy danh sách các trung tâm đang hoạt động từ CSDL động
        $centers = Center::active()->get();

        return view('vaccine::register', compact('cart', 'totalPrice', 'centers'));
    }

    /**
     * Xử lý đăng ký tiêm chủng.
     */
    public function postRegister(Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('vaccine.index')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        // Validate dữ liệu
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
        
        // Sinh mã đăng ký duy nhất cho thương hiệu Medicare Cờ Đỏ (MCD-)
        $registrationCode = 'MCD-' . strtoupper(Str::random(8));

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

            // 2. Liên kết các vắc xin trong giỏ vào bảng pivot
            foreach ($cart as $id => $item) {
                $registration->vaccines()->attach($id, ['price' => $item['price']]);
            }

            DB::commit();

            // Xóa giỏ hàng
            session()->forget('cart');

            // Lưu mã đăng ký vào session flash
            return redirect()->route('register.success')->with('success_code', $registrationCode);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Có lỗi xảy ra trong quá trình đăng ký: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị trang đăng ký thành công.
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
