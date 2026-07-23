<?php
/**
 * Chức năng: HomeController xử lý trang chủ của website Medicare Cờ Đỏ.
 * Lý do tạo: Tải động các banner quảng cáo, vắc xin nổi bật và gói vắc xin từ CSDL.
 */

namespace Modules\VaccineRegistration\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\VaccineRegistration\Models\Banner;
use Modules\VaccineRegistration\Models\Vaccine;
use Modules\VaccineRegistration\Models\Article;

class HomeController extends Controller
{
    /**
     * Hiển thị trang chủ với dữ liệu động.
     */
    public function index()
    {
        // Lấy danh sách banner đang hoạt động và sắp xếp thứ tự
        $banners = Banner::active()->ordered()->get();

        // Lấy 8 vắc xin lẻ nổi bật để quảng bá (khung 2 hàng 4 cột)
        $featuredVaccines = Vaccine::single()->take(8)->get();

        // Lấy 3 gói vắc xin gia đình phổ biến
        $vaccinePackages = Vaccine::package()->take(3)->get();

        // Lấy 4 bài viết tin tức / kiến thức y tế mới nhất từ CSDL (1 bài lớn + 3 bài nhỏ)
        $articles = Article::where('is_published', true)->latest()->take(4)->get();

        return view('vaccine::home', compact('banners', 'featuredVaccines', 'vaccinePackages', 'articles'));
    }

    /**
     * Trang Giới Thiệu Phòng Khám riêng biệt.
     */
    public function about()
    {
        return view('vaccine::about');
    }

    /**
     * Trang Dịch Vụ Tiêm Chủng riêng biệt.
     */
    public function services()
    {
        return view('vaccine::services');
    }

    /**
     * Trang Liên Hệ & Bản Đồ riêng biệt.
     */
    public function contact()
    {
        return view('vaccine::contact');
    }
}
