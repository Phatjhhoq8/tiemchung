<?php
/**
 * Chức năng: AdminVaccineController xử lý CRUD danh mục vắc xin/gói vắc xin.
 * Lý do tạo: Cho phép Quản trị viên thêm mới, cập nhật hoặc xóa vắc xin lẻ và gói vắc xin.
 */

namespace Modules\VaccineRegistration\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\VaccineRegistration\Models\Vaccine;

class AdminVaccineController extends Controller
{
    /**
     * Danh sách vắc xin.
     */
    public function index()
    {
        $vaccines = Vaccine::orderBy('type', 'asc')->orderBy('name', 'asc')->paginate(10);
        return view('vaccine::admin.vaccines.index', compact('vaccines'));
    }

    /**
     * Form thêm mới vắc xin.
     */
    public function create()
    {
        $vaccine = new Vaccine(); // Khởi tạo đối tượng rỗng phục vụ form partial
        return view('vaccine::admin.vaccines.create', compact('vaccine'));
    }

    /**
     * Lưu vắc xin mới.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'type' => 'required|string|in:single,package',
            'doses' => 'required|integer|min:1',
            'disease_prevention' => 'required|string|max:255',
            'age_group' => 'required|string|max:255',
            'origin' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Tên vắc xin không được để trống.',
            'price.required' => 'Giá vắc xin không được để trống.',
            'price.integer' => 'Giá vắc xin phải là số nguyên.',
            'price.min' => 'Giá vắc xin không được nhỏ hơn 0đ.',
            'type.required' => 'Vui lòng chọn phân loại vắc xin.',
            'type.in' => 'Phân loại không hợp lệ.',
            'doses.required' => 'Số mũi tiêm không được để trống.',
            'doses.min' => 'Số mũi tiêm phải ít nhất là 1 mũi.',
            'disease_prevention.required' => 'Công dụng phòng bệnh không được để trống.',
            'age_group.required' => 'Độ tuổi chỉ định không được để trống.',
            'origin.required' => 'Nguồn gốc xuất xứ không được để trống.',
        ]);

        // Gán ảnh mặc định nếu không điền
        if (empty($validated['image'])) {
            $validated['image'] = $validated['type'] === 'package' ? 'default_package.jpg' : 'default_vaccine.jpg';
        }

        Vaccine::create($validated);

        return redirect()->route('admin.vaccines.index')->with('success', 'Thêm mới vắc xin thành công.');
    }

    /**
     * Form chỉnh sửa vắc xin.
     */
    public function edit($id)
    {
        $vaccine = Vaccine::findOrFail($id);
        return view('vaccine::admin.vaccines.edit', compact('vaccine'));
    }

    /**
     * Cập nhật thông tin vắc xin.
     */
    public function update(Request $request, $id)
    {
        $vaccine = Vaccine::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'type' => 'required|string|in:single,package',
            'doses' => 'required|integer|min:1',
            'disease_prevention' => 'required|string|max:255',
            'age_group' => 'required|string|max:255',
            'origin' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Tên vắc xin không được để trống.',
            'price.required' => 'Giá vắc xin không được để trống.',
            'price.integer' => 'Giá vắc xin phải là số nguyên.',
            'price.min' => 'Giá vắc xin không được nhỏ hơn 0đ.',
            'type.required' => 'Vui lòng chọn phân loại vắc xin.',
            'doses.required' => 'Số mũi tiêm không được để trống.',
            'doses.min' => 'Số mũi tiêm phải ít nhất là 1 mũi.',
            'disease_prevention.required' => 'Công dụng phòng bệnh không được để trống.',
            'age_group.required' => 'Độ tuổi chỉ định không được để trống.',
            'origin.required' => 'Nguồn gốc xuất xứ không được để trống.',
        ]);

        if (empty($validated['image'])) {
            $validated['image'] = $vaccine->image ?: ($validated['type'] === 'package' ? 'default_package.jpg' : 'default_vaccine.jpg');
        }

        $vaccine->update($validated);

        return redirect()->route('admin.vaccines.index')->with('success', 'Cập nhật thông tin vắc xin thành công.');
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
}
