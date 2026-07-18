<?php
/**
 * Chức năng: HomeController xử lý trang chủ của website Medicare Cờ Đỏ.
 * Lý do tạo: Tải động các banner quảng cáo, vắc xin nổi bật và gói vắc xin từ CSDL.
 */

namespace Modules\VaccineRegistration\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\VaccineRegistration\Models\Banner;
use Modules\VaccineRegistration\Models\Vaccine;

class HomeController extends Controller
{
    /**
     * Hiển thị trang chủ với dữ liệu động.
     */
    public function index()
    {
        // Lấy danh sách banner đang hoạt động và sắp xếp thứ tự
        $banners = Banner::active()->ordered()->get();

        // Lấy 6 vắc xin lẻ nổi bật để quảng bá
        $featuredVaccines = Vaccine::single()->take(6)->get();

        // Lấy 3 gói vắc xin gia đình phổ biến
        $vaccinePackages = Vaccine::package()->take(3)->get();

        return view('vaccine::home', compact('banners', 'featuredVaccines', 'vaccinePackages'));
    }
}
