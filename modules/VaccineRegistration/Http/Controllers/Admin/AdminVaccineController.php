<?php
/**
 * Chức năng: AdminVaccineController xử lý CRUD danh mục vắc xin/gói vắc xin.
 * Lý do chỉnh sửa: Bổ sung validation cho các trường mới (sale_price, stock_status, manufacturer, dosage,
 *                   is_featured, sort_order, category) và thêm chức năng tìm kiếm/lọc theo VNVC.
 */

namespace Modules\VaccineRegistration\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\VaccineRegistration\Models\Vaccine;

class AdminVaccineController extends Controller
{
    /**
     * Danh sách vắc xin (hỗ trợ tìm kiếm & lọc).
     */
    public function index(Request $request)
    {
        $query = Vaccine::query();

        // Tìm kiếm theo tên hoặc bệnh phòng ngừa
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('disease_prevention', 'like', '%' . $search . '%')
                  ->orWhere('category', 'like', '%' . $search . '%')
                  ->orWhere('manufacturer', 'like', '%' . $search . '%');
            });
        }

        // Lọc theo phân loại (lẻ/gói)
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        // Lọc theo tình trạng kho
        if ($request->filled('stock_status')) {
            $query->where('stock_status', $request->input('stock_status'));
        }

        // Lọc theo danh mục bệnh
        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        // Lọc vắc xin nổi bật
        if ($request->filled('featured')) {
            $query->where('is_featured', true);
        }

        $vaccines = $query->orderBy('sort_order', 'asc')
                          ->orderBy('type', 'asc')
                          ->orderBy('name', 'asc')
                          ->paginate(15)
                          ->withQueryString();

        // Lấy danh sách danh mục để hiển thị dropdown lọc
        $categories = Vaccine::whereNotNull('category')
                             ->where('category', '!=', '')
                             ->distinct()
                             ->pluck('category')
                             ->sort()
                             ->values();

        return view('vaccine::admin.vaccines.index', compact('vaccines', 'categories'));
    }

    /**
     * Form thêm mới vắc xin.
     */
    public function create()
    {
        $vaccine = new Vaccine(); // Khởi tạo đối tượng rỗng phục vụ form partial
        $categories = Vaccine::whereNotNull('category')
                             ->where('category', '!=', '')
                             ->distinct()
                             ->pluck('category')
                             ->sort()
                             ->values();

        return view('vaccine::admin.vaccines.create', compact('vaccine', 'categories'));
    }

    /**
     * Lưu vắc xin mới.
     */
    public function store(Request $request)
    {
        $validated = $this->validateVaccine($request);

        // Gán ảnh mặc định nếu không điền
        if (empty($validated['image'])) {
            $validated['image'] = $validated['type'] === 'package' ? 'default_package.jpg' : 'default_vaccine.jpg';
        }

        // Xử lý checkbox is_featured
        $validated['is_featured'] = $request->has('is_featured');

        Vaccine::create($validated);

        return redirect()->route('admin.vaccines.index')->with('success', 'Thêm mới vắc xin thành công.');
    }

    /**
     * Form chỉnh sửa vắc xin.
     */
    public function edit($id)
    {
        $vaccine = Vaccine::findOrFail($id);
        $categories = Vaccine::whereNotNull('category')
                             ->where('category', '!=', '')
                             ->distinct()
                             ->pluck('category')
                             ->sort()
                             ->values();

        return view('vaccine::admin.vaccines.edit', compact('vaccine', 'categories'));
    }

    /**
     * Cập nhật thông tin vắc xin.
     */
    public function update(Request $request, $id)
    {
        $vaccine = Vaccine::findOrFail($id);

        $validated = $this->validateVaccine($request);

        if (empty($validated['image'])) {
            $validated['image'] = $vaccine->image ?: ($validated['type'] === 'package' ? 'default_package.jpg' : 'default_vaccine.jpg');
        }

        // Xử lý checkbox is_featured
        $validated['is_featured'] = $request->has('is_featured');

        $vaccine->update($validated);

        return redirect()->route('admin.vaccines.index')->with('success', 'Cập nhật thông tin vắc xin thành công.');
    }

    /**
     * Bật / Tắt trạng thái Vắc xin Nổi bật hiển thị trên Trang chủ.
     */
    public function toggleFeatured($id)
    {
        $vaccine = Vaccine::findOrFail($id);
        $vaccine->is_featured = !$vaccine->is_featured;
        $vaccine->save();

        $statusMessage = $vaccine->is_featured ? 'Đã bật hiển thị NỔI BẬT trên Trang chủ.' : 'Đã bỏ trạng thái NỔI BẬT.';
        return redirect()->back()->with('success', "Vắc xin '{$vaccine->name}': {$statusMessage}");
    }

    /**
     * Xóa vắc xin.
     */
    public function destroy($id)
    {
        $vaccine = Vaccine::findOrFail($id);
        
        // Hủy liên kết với các đơn tiêm chủng trước khi xóa (hoặc báo lỗi nếu có ràng buộc đơn hàng)
        $vaccine->registrations()->detach();
        $vaccine->delete();

        return redirect()->route('admin.vaccines.index')->with('success', 'Xóa vắc xin khỏi danh mục thành công.');
    }

    /**
     * Validate dữ liệu vắc xin (dùng chung cho store & update).
     */
    private function validateVaccine(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'sale_price' => 'nullable|integer|min:0',
            'type' => 'required|string|in:single,package',
            'doses' => 'required|integer|min:1',
            'stock_status' => 'required|string|in:available,limited,out_of_stock',
            'disease_prevention' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'age_group' => 'required|string|max:255',
            'origin' => 'required|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'dosage' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'image' => 'nullable|string|max:255',
            'is_featured' => 'nullable',
            'sort_order' => 'nullable|integer|min:0',
        ], [
            'name.required' => 'Tên vắc xin không được để trống.',
            'price.required' => 'Giá vắc xin không được để trống.',
            'price.integer' => 'Giá vắc xin phải là số nguyên.',
            'price.min' => 'Giá vắc xin không được nhỏ hơn 0đ.',
            'sale_price.integer' => 'Giá ưu đãi phải là số nguyên.',
            'sale_price.min' => 'Giá ưu đãi không được nhỏ hơn 0đ.',
            'type.required' => 'Vui lòng chọn phân loại vắc xin.',
            'type.in' => 'Phân loại không hợp lệ.',
            'doses.required' => 'Số mũi tiêm không được để trống.',
            'doses.min' => 'Số mũi tiêm phải ít nhất là 1 mũi.',
            'stock_status.required' => 'Vui lòng chọn tình trạng kho.',
            'stock_status.in' => 'Tình trạng kho không hợp lệ.',
            'disease_prevention.required' => 'Công dụng phòng bệnh không được để trống.',
            'age_group.required' => 'Độ tuổi chỉ định không được để trống.',
            'origin.required' => 'Nguồn gốc xuất xứ không được để trống.',
        ]);
    }
}
