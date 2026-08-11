<?php

/**
 * Chức năng: AdminCenterController quản trị danh sách các trung tâm tiêm chủng Medicare Cờ Đỏ.
 * Lý do tạo: Cho phép Admin thêm mới chi nhánh, cập nhật địa chỉ hoặc kích hoạt/tạm dừng chi nhánh tiêm chủng.
 */

namespace Modules\VaccineRegistration\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\VaccineRegistration\Models\Center;
use Modules\VaccineRegistration\Models\CenterVaccine;
use Modules\VaccineRegistration\Models\Vaccine;
use Modules\VaccineRegistration\Support\AdminContext;

class AdminCenterController extends Controller
{
    public function __construct()
    {
        // Protected by route middleware 'super.admin' and explicit abort_unless checks
    }

    /**
     * Danh sách trung tâm.
     */
    public function index(Request $request)
    {
        abort_unless(AdminContext::isSuperAdmin(), 403, 'Bạn không có quyền quản lý trung tâm tiêm chủng.');

        $query = Center::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('address', 'like', '%'.$search.'%')
                    ->orWhere('phone', 'like', '%'.$search.'%');
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->filled('from_date')) {
            $query->whereDate('centers.created_at', '>=', $request->input('from_date'));
        }
        if ($request->filled('to_date')) {
            $query->whereDate('centers.created_at', '<=', $request->input('to_date'));
        }

        $day = $request->input('filter_day') ?? $request->input('day');
        $month = $request->input('filter_month') ?? $request->input('month');
        $year = $request->input('filter_year') ?? $request->input('year');

        if ($day !== null && $day !== '') {
            $query->whereDay('centers.created_at', (int) $day);
        }
        if ($month !== null && $month !== '') {
            $query->whereMonth('centers.created_at', (int) $month);
        }
        if ($year !== null && $year !== '') {
            $query->whereYear('centers.created_at', (int) $year);
        }

        $centers = $query->orderBy('is_active', 'desc')->orderBy('name', 'asc')->paginate(10)->withQueryString();

        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'html' => view('vaccine::admin.centers._table', compact('centers'))->render(),
            ]);
        }

        return view('vaccine::admin.centers.index', compact('centers'));
    }

    /**
     * Form thêm mới trung tâm.
     */
    public function create()
    {
        $center = new Center;

        return view('vaccine::admin.centers.create', compact('center'));
    }

    /**
     * Lưu trung tâm mới.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:centers,slug',
            'address' => 'required|string|max:500',
            'phone' => 'nullable|string|max:20',
            'zalo_phone' => 'nullable|string|max:20',
            'map_url' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    $lowerVal = strtolower(trim($value));
                    if (preg_match('/^\s*(javascript|data|vbscript)\s*:/i', $value) || str_contains($lowerVal, 'javascript:') || str_contains($lowerVal, 'data:')) {
                        $fail('Bản đồ phải là đường dẫn Google Maps hợp lệ.');

                        return;
                    }
                    if (! str_starts_with($value, 'https://www.google.com/maps/embed') && ! str_starts_with($value, 'https://www.google.com/maps/place') && ! str_starts_with($value, 'https://www.google.com/maps/')) {
                        $fail('Bản đồ phải là đường dẫn Google Maps hợp lệ.');
                    }
                },
            ],
            'working_hours' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'required|boolean',
        ], [
            'name.required' => 'Tên trung tâm không được để trống.',
            'address.required' => 'Địa chỉ không được để trống.',
            'is_active.required' => 'Vui lòng chọn trạng thái hoạt động.',
        ]);

        $validated['slug'] = ($validated['slug'] ?? null) ?: Str::slug($validated['name']);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $center = Center::create($validated);
        Vaccine::active()->each(function ($vaccine) use ($center) {
            CenterVaccine::firstOrCreate(
                ['center_id' => $center->id, 'vaccine_id' => $vaccine->id],
                [
                    'price' => $vaccine->price,
                    'sale_price' => $vaccine->sale_price,
                    'stock_status' => 'out_of_stock',
                    'is_active' => false,
                ]
            );
        });
        AuditLogger::log(
            'center.created',
            'center',
            $center->id,
            newValues: $center->only(['name', 'slug', 'address', 'phone', 'working_hours', 'sort_order', 'is_active']),
            centerId: $center->id
        );

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
            'slug' => 'nullable|string|max:255|unique:centers,slug,'.$center->id,
            'address' => 'required|string|max:500',
            'phone' => 'nullable|string|max:20',
            'zalo_phone' => 'nullable|string|max:20',
            'map_url' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    $lowerVal = strtolower(trim($value));
                    if (preg_match('/^\s*(javascript|data|vbscript)\s*:/i', $value) || str_contains($lowerVal, 'javascript:') || str_contains($lowerVal, 'data:')) {
                        $fail('Bản đồ phải là đường dẫn Google Maps hợp lệ.');

                        return;
                    }
                    if (! str_starts_with($value, 'https://www.google.com/maps/embed') && ! str_starts_with($value, 'https://www.google.com/maps/place') && ! str_starts_with($value, 'https://www.google.com/maps/')) {
                        $fail('Bản đồ phải là đường dẫn Google Maps hợp lệ.');
                    }
                },
            ],
            'working_hours' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'required|boolean',
        ], [
            'name.required' => 'Tên trung tâm không được để trống.',
            'address.required' => 'Địa chỉ không được để trống.',
            'is_active.required' => 'Vui lòng chọn trạng thái hoạt động.',
        ]);

        $validated['slug'] = ($validated['slug'] ?? null) ?: Str::slug($validated['name']);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $oldValues = $center->only(['name', 'slug', 'address', 'phone', 'working_hours', 'sort_order', 'is_active']);
        $center->update($validated);
        AuditLogger::log(
            'center.updated',
            'center',
            $center->id,
            $oldValues,
            $center->fresh()->only(array_keys($oldValues)),
            $center->id
        );

        return redirect()->route('admin.centers.index')->with('success', 'Cập nhật trung tâm tiêm chủng thành công.');
    }

    /**
     * Chuyển đổi trạng thái hoạt động của trung tâm (Tạm dừng / Bật lại).
     */
    public function toggleStatus($id)
    {
        abort_unless(AdminContext::isSuperAdmin(), 403, 'Bạn không có quyền quản lý trung tâm tiêm chủng.');

        $center = Center::findOrFail($id);
        $oldActive = (bool) $center->is_active;
        $newActive = ! $oldActive;
        $center->is_active = $newActive;
        $center->save();

        if (! $newActive) {
            CenterVaccine::where('center_id', $center->id)->update(['is_active' => false]);
        }

        AuditLogger::log(
            $newActive ? 'center.activated' : 'center.deactivated',
            'center',
            $center->id,
            ['is_active' => $oldActive],
            ['is_active' => $newActive],
            $center->id
        );

        $msg = $newActive ? 'Kích hoạt lại trung tâm tiêm chủng thành công.' : 'Tạm dừng trung tâm tiêm chủng thành công.';

        return redirect()->route('admin.centers.index')->with('success', $msg);
    }

    /**
     * Xóa trung tâm (Soft deactivation).
     */
    public function destroy($id)
    {
        $center = Center::findOrFail($id);
        $oldActive = $center->is_active;
        $center->is_active = false;
        $center->save();

        CenterVaccine::where('center_id', $center->id)->update(['is_active' => false]);
        AuditLogger::log(
            'center.deactivated',
            'center',
            $center->id,
            ['is_active' => $oldActive],
            ['is_active' => false],
            $center->id
        );

        return redirect()->route('admin.centers.index')->with('success', 'Tạm dừng trung tâm tiêm chủng thành công.');
    }
}
