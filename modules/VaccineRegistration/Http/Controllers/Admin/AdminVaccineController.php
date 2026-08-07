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
use Modules\VaccineRegistration\Models\Center;
use Modules\VaccineRegistration\Models\CenterVaccine;
use Modules\VaccineRegistration\Support\AdminContext;

class AdminVaccineController extends Controller
{
    /**
     * Danh sách vắc xin (hỗ trợ tìm kiếm & lọc).
     */
    public function index(Request $request)
    {
        if (AdminContext::isBranchAdmin() && $request->filled('center_id') && (int)$request->input('center_id') !== (int)AdminContext::centerId()) {
            abort(403, 'Cross-branch access forbidden.');
        }

        $centers = Center::active()->orderBy('sort_order')->orderBy('id')->get();
        $selectedCenterId = (int) AdminContext::selectedCenterId((int) $request->input('center_id'));
        $query = Vaccine::forCenter($selectedCenterId);

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
            $query->where('center_vaccines.stock_status', $request->input('stock_status'));
        }

        // Lọc theo danh mục bệnh
        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        // Lọc vắc xin nổi bật
        if ($request->filled('featured')) {
            $query->where('is_featured', true);
        }

        $vaccines = $query->orderBy('vaccines.id', 'asc')
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

        return view('vaccine::admin.vaccines.index', compact('vaccines', 'categories', 'centers', 'selectedCenterId'));
    }

    /**
     * Form thêm mới vắc xin.
     */
    public function create()
    {
        abort_unless(AdminContext::isSuperAdmin(), 403);

        $vaccine = new Vaccine(); // Khởi tạo đối tượng rỗng phục vụ form partial
        $centers = Center::active()->orderBy('sort_order')->orderBy('id')->get();
        $selectedCenterId = (int) AdminContext::selectedCenterId((int) request('center_id'));
        $isSuperAdmin = AdminContext::isSuperAdmin();
        $adminUser = AdminContext::user();
        $categories = Vaccine::whereNotNull('category')
                             ->where('category', '!=', '')
                             ->distinct()
                             ->pluck('category')
                             ->sort()
                             ->values();

        return view('vaccine::admin.vaccines.create', compact('vaccine', 'categories', 'centers', 'selectedCenterId', 'isSuperAdmin', 'adminUser'));
    }

    /**
     * Lưu vắc xin mới.
     */
    public function store(Request $request)
    {
        abort_unless(AdminContext::isSuperAdmin(), 403);

        $validated = $this->validateVaccine($request);

        // Xử lý tải lên hình ảnh từ file
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = 'vaccine_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/vaccines'), $filename);
            $validated['image'] = $filename;
        }

        // Gán ảnh mặc định nếu không điền
        if (empty($validated['image'])) {
            $validated['image'] = $validated['type'] === 'package' ? 'default_package.jpg' : 'default_vaccine.jpg';
        }

        // Xử lý checkbox is_featured
        $validated['is_featured'] = $request->has('is_featured');

        $selectedCenterId = (int) AdminContext::selectedCenterId((int) ($validated['center_id'] ?? 0));
        unset($validated['center_id']);

        $vaccine = Vaccine::create($validated);
        Center::active()->each(function (Center $center) use ($vaccine, $validated, $selectedCenterId) {
            $qty = (int) ($validated['stock_quantity'] ?? 0);
            $stockStatus = 'available';
            if ($qty === 0) {
                $stockStatus = 'out_of_stock';
            } elseif ($qty <= 5) {
                $stockStatus = 'limited';
            }
            
            CenterVaccine::firstOrCreate(
                ['center_id' => $center->id, 'vaccine_id' => $vaccine->id],
                [
                    'price' => (int) $validated['price'],
                    'sale_price' => $validated['sale_price'] ?? null,
                    'stock_quantity' => $qty,
                    'stock_status' => $stockStatus,
                    'is_active' => $center->id === $selectedCenterId,
                ]
            );
        });
        $this->syncCenterVaccine($vaccine, $selectedCenterId, $validated);

        return redirect()->route('admin.vaccines.index', ['center_id' => $selectedCenterId])->with('success', 'Thêm mới vắc xin thành công.');
    }

    /**
     * Form chỉnh sửa vắc xin.
     */
    public function edit($id)
    {
        abort_unless(AdminContext::isBranchAdmin() || AdminContext::isSuperAdmin(), 403);

        if (AdminContext::isBranchAdmin() && request()->filled('center_id') && (int)request('center_id') !== (int)AdminContext::centerId()) {
            abort(403, 'Cross-branch access forbidden.');
        }

        $vaccine = Vaccine::findOrFail($id);
        $centers = Center::active()->orderBy('sort_order')->orderBy('id')->get();
        $selectedCenterId = (int) AdminContext::selectedCenterId((int) request('center_id'));
        $isSuperAdmin = AdminContext::isSuperAdmin();
        $adminUser = AdminContext::user();
        $centerVaccine = CenterVaccine::where('center_id', $selectedCenterId)->where('vaccine_id', $vaccine->id)->first();
        if ($centerVaccine) {
            $vaccine->price = $centerVaccine->price;
            $vaccine->sale_price = $centerVaccine->sale_price;
            $vaccine->stock_quantity = $centerVaccine->stock_quantity;
            $vaccine->stock_status = $centerVaccine->stock_status;
            $vaccine->is_featured = $centerVaccine->is_featured;
            $vaccine->sort_order = $centerVaccine->sort_order;
        }
        $categories = Vaccine::whereNotNull('category')
                             ->where('category', '!=', '')
                             ->distinct()
                             ->pluck('category')
                             ->sort()
                             ->values();

        return view('vaccine::admin.vaccines.edit', compact('vaccine', 'categories', 'centers', 'selectedCenterId', 'isSuperAdmin', 'adminUser'));
    }

    /**
     * Cập nhật thông tin vắc xin.
     */
    public function update(Request $request, $id)
    {
        abort_unless(AdminContext::isBranchAdmin() || AdminContext::isSuperAdmin(), 403);

        $vaccine = Vaccine::findOrFail($id);

        if (!AdminContext::isSuperAdmin()) {
            if ($request->filled('center_id') && (int)$request->input('center_id') !== (int)AdminContext::centerId()) {
                abort(403, 'Cross-branch access forbidden.');
            }

            $masterFields = ['name', 'origin', 'category', 'description', 'disease_prevention', 'type', 'doses', 'age_group', 'manufacturer', 'dosage'];
            foreach ($masterFields as $field) {
                if ($request->has($field) && (string)$request->input($field) !== (string)$vaccine->$field) {
                    abort(403, 'Branch admin cannot modify master vaccine catalog fields.');
                }
            }
            if ($request->hasFile('image_file')) {
                abort(403, 'Branch admin cannot modify master vaccine catalog fields.');
            }

            $request->merge([
                'name' => $vaccine->name,
                'type' => $vaccine->type,
                'disease_prevention' => $vaccine->disease_prevention,
                'age_group' => $vaccine->age_group,
                'origin' => $vaccine->origin,
                'doses' => $vaccine->doses,
                'category' => $vaccine->category,
                'manufacturer' => $vaccine->manufacturer,
                'dosage' => $vaccine->dosage,
                'description' => $vaccine->description,
            ]);
        }

        $validated = $this->validateVaccine($request);
        $selectedCenterId = (int) AdminContext::selectedCenterId((int) ($validated['center_id'] ?? 0));
        unset($validated['center_id']);

        // Xử lý tải lên hình ảnh từ file (chỉ cho phép super_admin thay đổi ảnh)
        if (AdminContext::isSuperAdmin() && $request->hasFile('image_file')) {
            // Xóa ảnh cũ nếu không phải ảnh mặc định
            if ($vaccine->image && !in_array($vaccine->image, ['default_package.jpg', 'default_vaccine.jpg'])) {
                $oldPath = public_path('images/vaccines/' . $vaccine->image);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
            $file = $request->file('image_file');
            $filename = 'vaccine_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/vaccines'), $filename);
            $validated['image'] = $filename;
        }

        if (empty($validated['image'])) {
            $validated['image'] = $vaccine->image ?: ($validated['type'] === 'package' ? 'default_package.jpg' : 'default_vaccine.jpg');
        }

        // Xử lý checkbox is_featured
        $validated['is_featured'] = $request->has('is_featured');

        if (AdminContext::isSuperAdmin()) {
            $masterData = $validated;
            unset($masterData['price'], $masterData['sale_price'], $masterData['stock_quantity'], $masterData['is_featured'], $masterData['sort_order']);
            $vaccine->update($masterData);
        }
        $this->syncCenterVaccine($vaccine, $selectedCenterId, $validated);

        return redirect()->route('admin.vaccines.index', ['center_id' => $selectedCenterId])->with('success', 'Cập nhật thông tin vắc xin thành công.');
    }

    /**
     * Bật / Tắt trạng thái Vắc xin Nổi bật hiển thị trên Trang chủ.
     */
    public function toggleFeatured($id)
    {
        abort_unless(AdminContext::isSuperAdmin() || AdminContext::isBranchAdmin(), 403);

        if (AdminContext::isBranchAdmin() && request()->filled('center_id') && (int)request('center_id') !== (int)AdminContext::centerId()) {
            abort(403, 'Cross-branch access forbidden.');
        }

        $vaccine = Vaccine::findOrFail($id);
        $selectedCenterId = (int) AdminContext::selectedCenterId((int) request('center_id'));
        $centerVaccine = CenterVaccine::firstOrCreate(
            ['center_id' => $selectedCenterId, 'vaccine_id' => $vaccine->id],
            ['price' => $vaccine->price, 'sale_price' => $vaccine->sale_price, 'stock_status' => $vaccine->stock_status ?? 'available']
        );
        $centerVaccine->is_featured = !$centerVaccine->is_featured;
        $centerVaccine->save();

        $statusMessage = $centerVaccine->is_featured ? 'Đã bật hiển thị NỔI BẬT trên Trang chủ.' : 'Đã bỏ trạng thái NỔI BẬT.';
        return redirect()->back()->with('success', "Vắc xin '{$vaccine->name}': {$statusMessage}");
    }

    /**
     * Xóa vắc xin (Soft deactivation).
     */
    public function destroy($id)
    {
        abort_unless(AdminContext::isSuperAdmin(), 403);

        $vaccine = Vaccine::findOrFail($id);
        $vaccine->is_active = false;
        $vaccine->save();
        CenterVaccine::where('vaccine_id', $vaccine->id)->update(['is_active' => false]);

        return redirect()->route('admin.vaccines.index')->with('success', 'Vô hiệu hóa vắc xin khỏi danh mục thành công.');
    }

    /**
     * Validate dữ liệu vắc xin (dùng chung cho store & update).
     */
    private function validateVaccine(Request $request): array
    {
        if (!$request->has('stock_quantity') && $request->has('stock_status')) {
            $status = $request->input('stock_status');
            $qty = 10;
            if ($status === 'out_of_stock') {
                $qty = 0;
            } elseif ($status === 'limited') {
                $qty = 3;
            }
            $request->merge(['stock_quantity' => $qty]);
        }

        return $request->validate([
            'name' => 'required|string|max:255',
            'center_id' => 'required|exists:centers,id',
            'price' => 'required|integer|min:0',
            'sale_price' => 'nullable|integer|min:0|lt:price',
            'type' => 'required|string|in:single,package',
            'doses' => 'required|integer|min:1',
            'stock_quantity' => 'required|integer|min:0',
            'disease_prevention' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'age_group' => 'required|string|max:255',
            'origin' => 'required|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'dosage' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'image' => 'nullable|string|max:255',
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048', new \App\Rules\SafeImageFile()],
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
            'stock_quantity.required' => 'Vui lòng nhập số lượng tồn kho.',
            'stock_quantity.integer' => 'Số lượng tồn kho phải là số nguyên.',
            'stock_quantity.min' => 'Số lượng tồn kho không được nhỏ hơn 0.',
            'disease_prevention.required' => 'Công dụng phòng bệnh không được để trống.',
            'age_group.required' => 'Độ tuổi chỉ định không được để trống.',
            'origin.required' => 'Nguồn gốc xuất xứ không được để trống.',
        ]);
    }

    private function syncCenterVaccine(Vaccine $vaccine, int $centerId, array $data): void
    {
        $existing = CenterVaccine::where('center_id', $centerId)->where('vaccine_id', $vaccine->id)->first();
        $oldPrice = $existing ? $existing->price : null;
        $oldSalePrice = $existing ? $existing->sale_price : null;

        $newPrice = (int) $data['price'];
        $newSalePrice = isset($data['sale_price']) && $data['sale_price'] !== null && $data['sale_price'] !== '' ? (int) $data['sale_price'] : null;

        $qty = (int) ($data['stock_quantity'] ?? 0);
        $stockStatus = 'available';
        if ($qty === 0) {
            $stockStatus = 'out_of_stock';
        } elseif ($qty <= 5) {
            $stockStatus = 'limited';
        }

        CenterVaccine::updateOrCreate(
            ['center_id' => $centerId, 'vaccine_id' => $vaccine->id],
            [
                'price' => $newPrice,
                'sale_price' => $newSalePrice,
                'stock_quantity' => $qty,
                'stock_status' => $stockStatus,
                'is_active' => true,
                'is_featured' => (bool) ($data['is_featured'] ?? false),
                'sort_order' => (int) ($data['sort_order'] ?? 0),
            ]
        );

        if (!$existing || $oldPrice !== $newPrice || $oldSalePrice !== $newSalePrice) {
            \App\Services\AuditLogger::logPriceUpdate(
                resourceId: $vaccine->id,
                oldValues: ['price' => $oldPrice, 'sale_price' => $oldSalePrice],
                newValues: ['price' => $newPrice, 'sale_price' => $newSalePrice],
                centerId: $centerId
            );
        }
    }
}
