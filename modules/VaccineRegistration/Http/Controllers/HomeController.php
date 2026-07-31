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
use Modules\VaccineRegistration\Http\Controllers\Admin\AdminLiveEditorController;
use Illuminate\Http\Request;
use Modules\VaccineRegistration\Support\CenterContext;

class HomeController extends Controller
{
    /**
     * Hiển thị trang chủ với dữ liệu động.
     */
    public function index()
    {
        $currentCenter = CenterContext::current();

        // Lấy danh sách banner đang hoạt động và sắp xếp thứ tự
        $banners = Banner::active()->ordered()->get();

        // Lấy danh sách vắc xin lẻ nổi bật (is_featured = true) được cấu hình trong Admin, tự động điền thêm nếu thiếu
        $featuredVaccines = Vaccine::forCenter($currentCenter?->id)->single()->featured()->orderBy('center_vaccines.sort_order', 'asc')->take(8)->get();
        if ($featuredVaccines->count() < 8) {
            $excludeIds = $featuredVaccines->pluck('id')->toArray();
            $extraVaccines = Vaccine::forCenter($currentCenter?->id)->single()->whereNotIn('vaccines.id', $excludeIds)->orderBy('center_vaccines.sort_order', 'asc')->take(8 - $featuredVaccines->count())->get();
            $featuredVaccines = $featuredVaccines->merge($extraVaccines);
        }

        // Lấy 3 gói vắc xin gia đình phổ biến
        $vaccinePackages = Vaccine::forCenter($currentCenter?->id)->package()->take(3)->get();

        // Lấy 4 vắc xin lẻ nổi bật chiến dịch (để hiển thị lưới 2x2 ở phần qdenga_promo cũ)
        $campaignVaccines = Vaccine::forCenter($currentCenter?->id)->single()->featured()->orderBy('center_vaccines.sort_order', 'asc')->take(4)->get();
        if ($campaignVaccines->count() < 4) {
            $excludeIds = $campaignVaccines->pluck('id')->toArray();
            $extraCampaign = Vaccine::forCenter($currentCenter?->id)->single()->whereNotIn('vaccines.id', $excludeIds)->orderBy('center_vaccines.sort_order', 'asc')->take(4 - $campaignVaccines->count())->get();
            $campaignVaccines = $campaignVaccines->merge($extraCampaign);
        }

        // Lấy 4 bài viết tin tức / kiến thức y tế mới nhất từ CSDL (1 bài lớn + 3 bài nhỏ)
        $articles = Article::where('is_published', true)->latest()->take(4)->get();

        // Kiểm tra xem có đang ở chế độ xem thử giả lập hay không
        $isPreviewMode = request()->has('preview') && session('admin_logged_in') === true;
        $layoutConfig = AdminLiveEditorController::getLayoutConfig($isPreviewMode);

        return view('vaccine::home', compact('banners', 'featuredVaccines', 'campaignVaccines', 'vaccinePackages', 'articles', 'layoutConfig', 'isPreviewMode'));
    }

    public function selectCenter(Request $request)
    {
        $validated = $request->validate([
            'center_id' => 'required|exists:centers,id',
        ]);

        $center = CenterContext::set((int) $validated['center_id']);

        if ($request->ajax() || $request->wantsJson()) {
            $cartState = CenterContext::resolveCart($center?->id);
            return response()->json([
                'success' => true,
                'center' => $center,
                'cart' => $cartState['cart'],
                'cart_count' => count($cartState['cart']),
                'total_price' => $cartState['total_price'],
                'unavailable_count' => $cartState['unavailable_count'],
            ]);
        }

        return redirect()->back()->with('success', 'Đã đổi chi nhánh hiện tại sang ' . $center->name . '.');
    }

    /**
     * Trang Giới Thiệu Phòng Khám riêng biệt.
     */
    public function about()
    {
        $settings = \Modules\VaccineRegistration\Models\Setting::all()->pluck('value', 'key')->toArray();
        return view('vaccine::about', compact('settings'));
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
        $centers = CenterContext::activeCenters();
        $currentCenter = CenterContext::current();
        return view('vaccine::contact', compact('centers', 'currentCenter'));
    }
}
