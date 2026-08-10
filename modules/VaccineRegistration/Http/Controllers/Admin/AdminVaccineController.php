<?php

/**
 * Chức năng: AdminVaccineController xử lý CRUD danh mục vắc xin.
 * Lý do chỉnh sửa: Bổ sung validation cho các trường mới (sale_price, stock_status, manufacturer, dosage,
 *                   is_featured, sort_order, category) và thêm chức năng tìm kiếm/lọc theo VNVC.
 */

namespace Modules\VaccineRegistration\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Rules\SafeImageFile;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\VaccineRegistration\Models\Center;
use Modules\VaccineRegistration\Models\CenterVaccine;
use Modules\VaccineRegistration\Models\Vaccine;
use Modules\VaccineRegistration\Support\AdminContext;

class AdminVaccineController extends Controller
{
    /**
     * Danh sách vắc xin (hỗ trợ tìm kiếm & lọc).
     */
    public function index(Request $request)
    {
        $filters = $request->validate([
            'center_id' => 'nullable|integer|exists:centers,id',
            'search' => 'nullable|string|max:255',
            'stock_status' => 'nullable|in:available,limited,out_of_stock',
            'category' => 'nullable|string|max:100',
            'featured' => 'nullable|boolean',
            'min_quantity' => 'nullable|integer|min:0',
            'max_quantity' => 'nullable|integer|min:0',

        ], [
            'min_quantity.integer' => 'Số lượng tối thiểu phải là số nguyên.',
            'min_quantity.min' => 'Số lượng tối thiểu không được nhỏ hơn 0.',
            'max_quantity.integer' => 'Số lượng tối đa phải là số nguyên.',
            'max_quantity.min' => 'Số lượng tối đa không được nhỏ hơn 0.',
        ]);
        if (isset($filters['min_quantity'], $filters['max_quantity'])
            && $filters['max_quantity'] < $filters['min_quantity']) {
            throw ValidationException::withMessages([
                'max_quantity' => 'Số lượng tối đa phải lớn hơn hoặc bằng số lượng tối thiểu.',
            ]);
        }

        $centers = Center::active()->orderBy('sort_order')->orderBy('id')->get();
        $selectedCenterId = AdminContext::resolveListCenterId($request);
        $query = Vaccine::forAdminCenters($selectedCenterId);

        // Tìm kiếm theo tên hoặc bệnh phòng ngừa
        if (! empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('vaccines.name', 'like', '%'.$search.'%')
                    ->orWhere('vaccines.disease_prevention', 'like', '%'.$search.'%')
                    ->orWhere('vaccines.category', 'like', '%'.$search.'%')
                    ->orWhere('vaccines.manufacturer', 'like', '%'.$search.'%')
                    ->orWhere('centers.name', 'like', '%'.$search.'%');
            });
        }

        // Lọc theo tình trạng kho
        if (! empty($filters['stock_status'])) {
            $query->where('center_vaccines.stock_status', $filters['stock_status']);
        }

        // Lọc theo danh mục bệnh
        if (! empty($filters['category'])) {
            $query->where('vaccines.category', $filters['category']);
        }

        // Lọc vắc xin nổi bật
        if (! empty($filters['featured'])) {
            $query->where('center_vaccines.is_featured', true);
        }

        if (isset($filters['min_quantity']) && $filters['min_quantity'] !== null) {
            $query->where('center_vaccines.stock_quantity', '>=', $filters['min_quantity']);
        }

        if (isset($filters['max_quantity']) && $filters['max_quantity'] !== null) {
            $query->where('center_vaccines.stock_quantity', '<=', $filters['max_quantity']);
        }

        $vaccines = $query->orderBy('vaccines.id')
            ->orderBy('centers.sort_order')
            ->orderBy('centers.id')
            ->paginate(15)
            ->withQueryString();

        // Lấy danh sách danh mục để hiển thị dropdown lọc
        $categories = Vaccine::whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->pluck('category')
            ->sort()
            ->values();

        $isSuperAdmin = AdminContext::isSuperAdmin();

        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'html' => view('vaccine::admin.vaccines._table', compact('vaccines', 'categories', 'centers', 'selectedCenterId', 'isSuperAdmin'))->render(),
            ]);
        }

        return view('vaccine::admin.vaccines.index', compact('vaccines', 'categories', 'centers', 'selectedCenterId', 'isSuperAdmin'));
    }

    /**
     * Form thêm mới vắc xin.
     */
    public function create()
    {
        abort_unless(AdminContext::isSuperAdmin(), 403, 'Bạn không có quyền tạo vắc xin.');

        $vaccine = new Vaccine; // Khởi tạo đối tượng rỗng phục vụ form partial
        $centers = Center::active()->orderBy('sort_order')->orderBy('id')->get();
        $selectedCenterId = request()->filled('center_id')
            ? AdminContext::selectedCenterId(request()->integer('center_id'))
            : AdminContext::selectedCenterId();
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
        abort_unless(AdminContext::isSuperAdmin(), 403, 'Bạn không có quyền tạo vắc xin.');

        $validated = $this->validateVaccine($request);

        // Xử lý tải lên hình ảnh từ file
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = 'vaccine_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('images/vaccines'), $filename);
            $validated['image'] = $filename;
        }

        // Gán ảnh mặc định nếu không điền
        if (empty($validated['image'])) {
            $validated['image'] = 'default_vaccine.jpg';
        }

        // Xử lý checkbox is_featured
        $validated['is_featured'] = $request->has('is_featured');

        $selectedCenterId = (int) AdminContext::selectedCenterId((int) $validated['center_id']);
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
        AuditLogger::log(
            'vaccine.created',
            'vaccine',
            $vaccine->id,
            newValues: $vaccine->only(['name', 'origin', 'category', 'doses', 'is_active']),
            centerId: $selectedCenterId
        );

        return redirect()->route('admin.vaccines.index', ['center_id' => $selectedCenterId])->with('success', 'Thêm mới vắc xin thành công.');
    }

    /**
     * Form chỉnh sửa vắc xin.
     */
    public function edit($id)
    {
        abort_unless(AdminContext::isBranchAdmin() || AdminContext::isSuperAdmin(), 403, 'Bạn không có quyền chỉnh sửa vắc xin.');

        if (request()->filled('center_id')) {
            AdminContext::assertCanManageCenter(request()->integer('center_id'));
        }

        $vaccine = Vaccine::findOrFail($id);
        $centers = Center::active()->orderBy('sort_order')->orderBy('id')->get();
        $selectedCenterId = request()->filled('center_id')
            ? AdminContext::selectedCenterId(request()->integer('center_id'))
            : AdminContext::selectedCenterId();
        $isSuperAdmin = AdminContext::isSuperAdmin();
        $adminUser = AdminContext::user();
        $centerVaccine = $selectedCenterId
            ? CenterVaccine::where('center_id', $selectedCenterId)->where('vaccine_id', $vaccine->id)->first()
            : null;
        if ($centerVaccine) {
            $vaccine->price = $centerVaccine->price;
            $vaccine->sale_price = $centerVaccine->sale_price;
            $vaccine->stock_quantity = $centerVaccine->stock_quantity;
            $vaccine->stock_status = $centerVaccine->stock_status;
            $vaccine->center_is_active = $centerVaccine->is_active;
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
        abort_unless(AdminContext::isBranchAdmin() || AdminContext::isSuperAdmin(), 403, 'Bạn không có quyền chỉnh sửa vắc xin.');

        $vaccine = Vaccine::findOrFail($id);

        if (! AdminContext::isSuperAdmin()) {
            if ($request->filled('center_id') && (int) $request->input('center_id') !== (int) AdminContext::centerId()) {
                abort(403, 'Bạn không có quyền cập nhật vắc xin của chi nhánh khác.');
            }

            $masterFields = [
                'name', 'origin', 'category', 'description', 'disease_prevention', 'doses', 'age_group',
                'manufacturer', 'dosage', 'administration_route', 'detailed_schedule', 'contraindications',
                'adverse_effects', 'warnings', 'source_reference_url', 'source_review_date',
            ];
            foreach ($masterFields as $field) {
                if ($request->has($field) && (string) $request->input($field) !== (string) $vaccine->$field) {
                    abort(403, 'Quản trị viên chi nhánh không được thay đổi thông tin danh mục vắc xin dùng chung.');
                }
            }
            if ($request->hasFile('image_file')) {
                abort(403, 'Quản trị viên chi nhánh không được thay đổi hình ảnh vắc xin dùng chung.');
            }

            $request->merge([
                'name' => $vaccine->name,
                'disease_prevention' => $vaccine->disease_prevention,
                'age_group' => $vaccine->age_group,
                'origin' => $vaccine->origin,
                'doses' => $vaccine->doses,
                'category' => $vaccine->category,
                'manufacturer' => $vaccine->manufacturer,
                'dosage' => $vaccine->dosage,
                'description' => $vaccine->description,
                'administration_route' => $vaccine->administration_route,
                'detailed_schedule' => $vaccine->detailed_schedule,
                'contraindications' => $vaccine->contraindications,
                'adverse_effects' => $vaccine->adverse_effects,
                'warnings' => $vaccine->warnings,
                'source_reference_url' => $vaccine->source_reference_url,
                'source_review_date' => $vaccine->source_review_date?->toDateString(),
            ]);
        }

        $validated = $this->validateVaccine($request);
        $selectedCenterId = (int) AdminContext::selectedCenterId((int) $validated['center_id']);
        AdminContext::assertCanManageCenter($selectedCenterId);
        unset($validated['center_id']);
        $oldCenterVaccine = CenterVaccine::where('center_id', $selectedCenterId)->where('vaccine_id', $vaccine->id)->first();
        $oldAuditValues = [
            'name' => $vaccine->name,
            'origin' => $vaccine->origin,
            'category' => $vaccine->category,
            'doses' => $vaccine->doses,
            'master_is_active' => $vaccine->is_active,
            'stock_quantity' => $oldCenterVaccine?->stock_quantity,
            'stock_status' => $oldCenterVaccine?->stock_status,
            'center_is_active' => $oldCenterVaccine?->is_active,
            'is_featured' => $oldCenterVaccine?->is_featured,
            'sort_order' => $oldCenterVaccine?->sort_order,
        ];

        // Xử lý tải lên hình ảnh từ file (chỉ cho phép super_admin thay đổi ảnh)
        if (AdminContext::isSuperAdmin() && $request->hasFile('image_file')) {
            // Xóa ảnh cũ nếu không phải ảnh mặc định
            if ($vaccine->image && $vaccine->image !== 'default_vaccine.jpg') {
                $oldPath = public_path('images/vaccines/'.$vaccine->image);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
            $file = $request->file('image_file');
            $filename = 'vaccine_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('images/vaccines'), $filename);
            $validated['image'] = $filename;
        }

        if (empty($validated['image'])) {
            $validated['image'] = $vaccine->image ?: 'default_vaccine.jpg';
        }

        // Xử lý checkbox is_featured
        $validated['is_featured'] = $request->has('is_featured');

        if (AdminContext::isSuperAdmin()) {
            $masterData = $validated;
            unset($masterData['price'], $masterData['sale_price'], $masterData['stock_quantity'], $masterData['stock_status'], $masterData['center_is_active'], $masterData['is_featured'], $masterData['sort_order']);
            $vaccine->update($masterData);
        }
        $this->syncCenterVaccine($vaccine, $selectedCenterId, $validated);
        $freshVaccine = $vaccine->fresh();
        $freshCenterVaccine = CenterVaccine::where('center_id', $selectedCenterId)->where('vaccine_id', $vaccine->id)->first();
        $newAuditValues = [
            'name' => $freshVaccine->name,
            'origin' => $freshVaccine->origin,
            'category' => $freshVaccine->category,
            'doses' => $freshVaccine->doses,
            'master_is_active' => $freshVaccine->is_active,
            'stock_quantity' => $freshCenterVaccine?->stock_quantity,
            'stock_status' => $freshCenterVaccine?->stock_status,
            'center_is_active' => $freshCenterVaccine?->is_active,
            'is_featured' => $freshCenterVaccine?->is_featured,
            'sort_order' => $freshCenterVaccine?->sort_order,
        ];
        if ($oldAuditValues !== $newAuditValues) {
            AuditLogger::log('vaccine.updated', 'vaccine', $vaccine->id, $oldAuditValues, $newAuditValues, $selectedCenterId);
        }

        return redirect()->route('admin.vaccines.index', ['center_id' => $selectedCenterId])->with('success', 'Cập nhật thông tin vắc xin thành công.');
    }

    /**
     * Bật / Tắt trạng thái Vắc xin Nổi bật hiển thị trên Trang chủ.
     */
    public function toggleFeatured($id)
    {
        abort_unless(AdminContext::isSuperAdmin() || AdminContext::isBranchAdmin(), 403, 'Bạn không có quyền thay đổi trạng thái nổi bật của vắc xin.');

        $validated = request()->validate(['center_id' => 'required|integer|exists:centers,id']);
        AdminContext::assertCanManageCenter((int) $validated['center_id']);

        $vaccine = Vaccine::findOrFail($id);
        $selectedCenterId = (int) AdminContext::selectedCenterId((int) $validated['center_id']);
        $centerVaccine = CenterVaccine::firstOrCreate(
            ['center_id' => $selectedCenterId, 'vaccine_id' => $vaccine->id],
            ['price' => $vaccine->price, 'sale_price' => $vaccine->sale_price, 'stock_status' => $vaccine->stock_status ?? 'available']
        );
        $centerVaccine->is_featured = ! $centerVaccine->is_featured;
        $centerVaccine->save();
        AuditLogger::log(
            'vaccine.featured_changed',
            'vaccine',
            $vaccine->id,
            ['is_featured' => ! $centerVaccine->is_featured],
            ['is_featured' => $centerVaccine->is_featured],
            $selectedCenterId
        );

        $statusMessage = $centerVaccine->is_featured ? 'Đã bật hiển thị NỔI BẬT trên Trang chủ.' : 'Đã bỏ trạng thái NỔI BẬT.';

        return redirect()->back()->with('success', "Vắc xin '{$vaccine->name}': {$statusMessage}");
    }

    /**
     * Xóa vắc xin (Soft deactivation).
     */
    public function destroy($id)
    {
        abort_unless(AdminContext::isSuperAdmin(), 403, 'Bạn không có quyền vô hiệu hóa vắc xin.');

        $vaccine = Vaccine::findOrFail($id);
        $vaccine->is_active = false;
        $vaccine->save();
        CenterVaccine::where('vaccine_id', $vaccine->id)->update(['is_active' => false]);
        AuditLogger::log(
            'vaccine.deactivated',
            'vaccine',
            $vaccine->id,
            ['is_active' => true],
            ['is_active' => false],
            resolveCenter: false
        );

        return redirect()->route('admin.vaccines.index')->with('success', 'Vô hiệu hóa vắc xin khỏi danh mục thành công.');
    }

    /**
     * Lấy trạng thái tồn kho vắc-xin tại tất cả các chi nhánh (JSON).
     */
    public function branchesStock($id)
    {
        abort_unless(AdminContext::isSuperAdmin(), 403, 'Bạn không có quyền xem tồn kho vắc xin của tất cả chi nhánh.');

        $vaccine = Vaccine::findOrFail($id);

        $branchesStock = Center::active()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(function ($center) use ($vaccine) {
                $centerVaccine = CenterVaccine::where('center_id', $center->id)
                    ->where('vaccine_id', $vaccine->id)
                    ->first();

                return [
                    'center_name' => $center->name,
                    'price' => $centerVaccine ? $centerVaccine->price : $vaccine->price,
                    'sale_price' => $centerVaccine ? $centerVaccine->sale_price : $vaccine->sale_price,
                    'stock_quantity' => $centerVaccine ? $centerVaccine->stock_quantity : 0,
                    'stock_status' => $centerVaccine ? $centerVaccine->stock_status : 'out_of_stock',
                    'is_active' => $centerVaccine ? (bool) $centerVaccine->is_active : false,
                ];
            });

        return response()->json([
            'vaccine_name' => $vaccine->name,
            'branches' => $branchesStock,
        ]);
    }

    /**
     * Validate dữ liệu vắc xin (dùng chung cho store & update).
     */
    private function validateVaccine(Request $request): array
    {
        if (! $request->has('stock_quantity') && $request->has('stock_status')) {
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
            'doses' => 'required|integer|min:1',
            'stock_quantity' => 'required|integer|min:0',
            'center_is_active' => 'nullable|boolean',
            'disease_prevention' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'age_group' => 'required|string|max:255',
            'origin' => 'required|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'dosage' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'administration_route' => 'nullable|string|max:255',
            'detailed_schedule' => 'nullable|string|max:10000',
            'contraindications' => 'nullable|string|max:10000',
            'adverse_effects' => 'nullable|string|max:10000',
            'warnings' => 'nullable|string|max:10000',
            'source_reference_url' => 'nullable|url:http,https|max:2048',
            'source_review_date' => 'nullable|date|before_or_equal:today',
            'image' => 'nullable|string|max:255',
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048', new SafeImageFile],
            'is_featured' => 'nullable',
            'sort_order' => 'nullable|integer|min:0',
        ], [
            'name.required' => 'Tên vắc xin không được để trống.',
            'price.required' => 'Giá vắc xin không được để trống.',
            'price.integer' => 'Giá vắc xin phải là số nguyên.',
            'price.min' => 'Giá vắc xin không được nhỏ hơn 0đ.',
            'sale_price.integer' => 'Giá ưu đãi phải là số nguyên.',
            'sale_price.min' => 'Giá ưu đãi không được nhỏ hơn 0đ.',
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
        $isActive = array_key_exists('center_is_active', $data)
            ? (bool) $data['center_is_active']
            : ($existing ? (bool) $existing->is_active : true);

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
                'is_active' => $isActive,
                'is_featured' => (bool) ($data['is_featured'] ?? false),
                'sort_order' => (int) ($data['sort_order'] ?? 0),
            ]
        );

        if (! $existing || $oldPrice !== $newPrice || $oldSalePrice !== $newSalePrice) {
            AuditLogger::logPriceUpdate(
                resourceId: $vaccine->id,
                oldValues: ['price' => $oldPrice, 'sale_price' => $oldSalePrice],
                newValues: ['price' => $newPrice, 'sale_price' => $newSalePrice],
                centerId: $centerId
            );
        }
    }
}
