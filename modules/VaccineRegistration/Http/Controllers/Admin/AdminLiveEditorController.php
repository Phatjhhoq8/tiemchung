<?php
/**
 * Chức năng: AdminLiveEditorController quản lý trình chỉnh sửa trực quan Live Editor toàn bộ các trang.
 * Lý do chỉnh sửa: Mở rộng Live Editor cho tất cả các trang (Home, About, Services, Contact, Vaccines, News, Global Shell),
 *                   thu gọn các thành phần trùng lặp (Header, Topbar, Footer, Chat Zalo) thành nhóm Cấu hình chung.
 */

namespace Modules\VaccineRegistration\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\VaccineRegistration\Models\Banner;
use Modules\VaccineRegistration\Models\Vaccine;
use Modules\VaccineRegistration\Models\Article;
use Modules\VaccineRegistration\Models\Center;
use Modules\VaccineRegistration\Models\Setting;

class AdminLiveEditorController extends Controller
{
    /**
     * Hiển thị trình chỉnh sửa trực quan toàn hệ thống (Universal Live Page Customizer).
     */
    public function index(Request $request)
    {
        $currentPage = $request->get('page', 'home');

        $banners = Banner::ordered()->get();
        $featuredVaccines = Vaccine::where('is_featured', true)->get();
        $allVaccines = Vaccine::orderBy('name', 'asc')->get();
        $articles = Article::orderBy('created_at', 'desc')->take(6)->get();
        $centers = Center::where('is_active', true)->orderBy('id', 'asc')->get();
        $settings = Setting::all()->pluck('value', 'key')->toArray();

        return view('vaccine::admin.live_editor', compact(
            'currentPage',
            'banners',
            'featuredVaccines',
            'allVaccines',
            'articles',
            'centers',
            'settings'
        ));
    }

    /**
     * Cập nhật Banner từ Live Editor.
     */
    public function updateBanner(Request $request)
    {
        $request->validate([
            'banner_id' => 'required|integer',
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'link_url' => 'nullable|string|max:500',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'image_existing' => 'nullable|string',
        ]);

        $banner = Banner::findOrFail($request->banner_id);
        $imagePath = $banner->image_url;

        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = 'banner_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/banners'), $filename);
            $imagePath = 'images/banners/' . $filename;
        } elseif ($request->filled('image_existing')) {
            $imagePath = $request->image_existing;
        }

        $banner->update([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'link_url' => $request->link_url,
            'image_url' => $imagePath,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật Banner thành công!',
            'banner' => $banner
        ]);
    }

    /**
     * Cập nhật Sản phẩm / Vắc Xin từ Live Editor.
     */
    public function updateVaccine(Request $request)
    {
        $request->validate([
            'vaccine_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'sale_price' => 'nullable|integer|min:0',
            'disease_prevention' => 'required|string|max:255',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'image_existing' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
        ]);

        $vaccine = Vaccine::findOrFail($request->vaccine_id);
        $imageName = $vaccine->image;

        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = 'vac_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/vaccines'), $filename);
            $imageName = $filename;
        } elseif ($request->filled('image_existing')) {
            $imageName = basename($request->image_existing);
        }

        $vaccine->update([
            'name' => $request->name,
            'price' => $request->price,
            'sale_price' => $request->sale_price,
            'disease_prevention' => $request->disease_prevention,
            'image' => $imageName,
            'is_featured' => $request->has('is_featured') ? (bool)$request->is_featured : $vaccine->is_featured,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật Vắc xin thành công!',
            'vaccine' => $vaccine
        ]);
    }

    /**
     * Cập nhật Cài Đặt Động Toàn Hệ Thống (Settings) cho từng Trang & Khung Chung.
     */
    public function updateSettings(Request $request)
    {
        $settingsData = $request->except(['_token', 'page']);

        foreach ($settingsData as $key => $value) {
            if ($value !== null) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Lưu cài đặt trực quan thành công!'
        ]);
    }
}
