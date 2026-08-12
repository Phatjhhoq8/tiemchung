<?php

/**
 * Chức năng: AdminBannerController quản trị banner/slider trang chủ Medicare Cờ Đỏ.
 * Lý do tạo: Cho phép Admin thêm/sửa/xóa banner quảng bá trên slider trang chủ.
 */

namespace Modules\VaccineRegistration\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Rules\SafeImageFile;
use Illuminate\Http\Request;
use Modules\VaccineRegistration\Models\Banner;
use Modules\VaccineRegistration\Support\AdminContext;

class AdminBannerController extends Controller
{
    public function __construct()
    {
        // Protected by route middleware 'super.admin' and explicit abort_unless checks
    }

    /**
     * Danh sách banner.
     */
    public function index(Request $request)
    {
        abort_unless(AdminContext::isSuperAdmin(), 403, 'Bạn không có quyền quản lý biểu ngữ.');

        $query = Banner::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%'.$search.'%')
                    ->orWhere('subtitle', 'like', '%'.$search.'%');
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $banners = $query->ordered()->paginate(10)->withQueryString();

        return view('vaccine::admin.banners.index', compact('banners'));
    }

    /**
     * Form thêm banner mới.
     */
    public function create()
    {
        abort_unless(AdminContext::isSuperAdmin(), 403, 'Bạn không có quyền tạo biểu ngữ.');
        $banner = new Banner;

        return view('vaccine::admin.banners.create', compact('banner'));
    }

    /**
     * Lưu banner mới.
     */
    public function store(Request $request)
    {
        abort_unless(AdminContext::isSuperAdmin(), 403, 'Bạn không có quyền tạo biểu ngữ.');
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'image_url' => 'nullable|string|max:500',
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048', new SafeImageFile],
            'link_url' => [
                'nullable',
                'string',
                'max:500',
                function ($attribute, $value, $fail) {
                    $lowerVal = strtolower(trim($value));
                    if (preg_match('/^\s*(javascript|data|vbscript)\s*:/i', $value) || str_contains($lowerVal, 'javascript:') || str_contains($lowerVal, 'data:')) {
                        $fail('Đường dẫn liên kết không hợp lệ.');
                    }
                },
            ],
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable',
        ], [
            'title.required' => 'Tiêu đề biểu ngữ không được để trống.',
        ]);

        // Xử lý tải lên hình ảnh từ file
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = 'banner_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('images/banners'), $filename);
            $validated['image_url'] = '/images/banners/'.$filename;
        }

        if (empty($validated['image_url'])) {
            return redirect()->back()->withInput()->withErrors(['image_file' => 'Vui lòng chọn hình ảnh biểu ngữ để tải lên.']);
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        Banner::create($validated);

        return redirect()->route('admin.banners.index')->with('success', 'Thêm biểu ngữ mới thành công.');
    }

    /**
     * Form sửa banner.
     */
    public function edit($id)
    {
        abort_unless(AdminContext::isSuperAdmin(), 403, 'Bạn không có quyền chỉnh sửa biểu ngữ.');
        $banner = Banner::findOrFail($id);

        return view('vaccine::admin.banners.edit', compact('banner'));
    }

    /**
     * Cập nhật banner.
     */
    public function update(Request $request, $id)
    {
        abort_unless(AdminContext::isSuperAdmin(), 403, 'Bạn không có quyền chỉnh sửa biểu ngữ.');
        $banner = Banner::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'image_url' => 'nullable|string|max:500',
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048', new SafeImageFile],
            'link_url' => [
                'nullable',
                'string',
                'max:500',
                function ($attribute, $value, $fail) {
                    $lowerVal = strtolower(trim($value));
                    if (preg_match('/^\s*(javascript|data|vbscript)\s*:/i', $value) || str_contains($lowerVal, 'javascript:') || str_contains($lowerVal, 'data:')) {
                        $fail('Đường dẫn liên kết không hợp lệ.');
                    }
                },
            ],
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable',
        ], [
            'title.required' => 'Tiêu đề biểu ngữ không được để trống.',
        ]);

        // Xử lý tải lên hình ảnh từ file
        if ($request->hasFile('image_file')) {
            // Xóa ảnh cũ nếu có
            if ($banner->image_url) {
                $oldFilename = basename($banner->image_url);
                $oldPath = public_path('images/banners/'.$oldFilename);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
            $file = $request->file('image_file');
            $filename = 'banner_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('images/banners'), $filename);
            $validated['image_url'] = '/images/banners/'.$filename;
        }

        if (empty($validated['image_url'])) {
            $validated['image_url'] = $banner->image_url;
        }

        $validated['is_active'] = $request->has('is_active');

        $banner->update($validated);

        return redirect()->route('admin.banners.index')->with('success', 'Cập nhật biểu ngữ thành công.');
    }

    /**
     * Bật / Tắt trạng thái hiển thị (Ẩn mềm) biểu ngữ.
     */
    public function toggleStatus($id)
    {
        abort_unless(AdminContext::isSuperAdmin(), 403, 'Bạn không có quyền thay đổi trạng thái biểu ngữ.');
        $banner = Banner::findOrFail($id);
        $banner->is_active = ! $banner->is_active;
        $banner->save();

        $statusText = $banner->is_active ? 'Hiển thị' : 'Đã ẩn';

        return redirect()->back()->with('success', "Đã chuyển biểu ngữ \"{$banner->title}\" sang trạng thái: {$statusText}!");
    }

    /**
     * Xóa cứng (Hard Delete) banner vĩnh viễn khỏi CSDL và xóa file ảnh.
     */
    public function destroy($id)
    {
        abort_unless(AdminContext::isSuperAdmin(), 403, 'Bạn không có quyền xóa biểu ngữ.');
        $banner = Banner::findOrFail($id);

        // Xóa file ảnh đính kèm nếu không phải ảnh mặc định hệ thống
        if ($banner->image_url && ! in_array($banner->image_url, ['images/banners/banner_family.jpg', 'images/banners/banner2.jpg', '/images/banners/banner_family.jpg', '/images/banners/banner2.jpg'])) {
            $oldFilename = basename($banner->image_url);
            $oldPath = public_path('images/banners/'.$oldFilename);
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }

        $title = $banner->title;
        $banner->delete();

        return redirect()->route('admin.banners.index')->with('success', "Đã xóa vĩnh viễn biểu ngữ \"{$title}\" thành công!");
    }
}
