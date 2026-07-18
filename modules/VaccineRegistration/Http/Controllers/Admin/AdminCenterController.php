<?php
/**
 * Chức năng: AdminCenterController quản trị danh sách các trung tâm tiêm chủng Medicare Cờ Đỏ.
 * Lý do tạo: Cho phép Admin thêm mới chi nhánh, cập nhật địa chỉ hoặc kích hoạt/tạm dừng chi nhánh tiêm chủng.
 */

namespace Modules\VaccineRegistration\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\VaccineRegistration\Models\Center;

class AdminCenterController extends Controller
{
    /**
     * Danh sách trung tâm.
     */
    public function index()
    {
        $centers = Center::orderBy('is_active', 'desc')->orderBy('name', 'asc')->paginate(10);
        return view('vaccine::admin.centers.index', compact('centers'));
    }

    /**
     * Form thêm mới trung tâm.
     */
    public function create()
    {
        $center = new Center();
        return view('vaccine::admin.centers.create', compact('center'));
    }

    /**
     * Lưu trung tâm mới.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'required|boolean',
        ], [
            'name.required' => 'Tên trung tâm không được để trống.',
            'address.required' => 'Địa chỉ không được để trống.',
            'is_active.required' => 'Vui lòng chọn trạng thái hoạt động.',
        ]);

        Center::create($validated);

        return redirect()->route('admin.centers.index')->with('success', 'Thêm mới trung tâm tiêm chủng thành công.');
    }

    /**
     * Form sửa thông tin trung tâm.
     */
    public function edit($id)
    {
        $center = Center::findOrFail($id);
        return view('vaccine::admin.centers.edit', compact('center'));
    }

    /**
     * Cập nhật thông tin trung tâm.
     */
    public function update(Request $request, $id)
    {
        $center = Center::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'required|boolean',
        ], [
            'name.required' => 'Tên trung tâm không được để trống.',
            'address.required' => 'Địa chỉ không được để trống.',
            'is_active.required' => 'Vui lòng chọn trạng thái hoạt động.',
        ]);

        $center->update($validated);

        return redirect()->route('admin.centers.index')->with('success', 'Cập nhật trung tâm tiêm chủng thành công.');
    }

    /**
     * Xóa trung tâm.
     */
    public function destroy($id)
    {
        $center = Center::findOrFail($id);
        $center->delete();

        return redirect()->route('admin.centers.index')->with('success', 'Xóa trung tâm tiêm chủng thành công.');
    }
}
