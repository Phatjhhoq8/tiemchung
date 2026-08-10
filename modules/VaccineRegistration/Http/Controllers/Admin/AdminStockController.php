<?php

namespace Modules\VaccineRegistration\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\VaccineRegistration\Models\Center;
use Modules\VaccineRegistration\Models\CenterVaccine;
use Modules\VaccineRegistration\Models\Vaccine;
use Modules\VaccineRegistration\Models\VaccineStockMovement;
use Modules\VaccineRegistration\Support\AdminContext;

class AdminStockController extends Controller
{
    public function index(Request $request)
    {
        $centers = Center::active()->orderBy('sort_order')->orderBy('id')->get();
        $selectedCenterId = AdminContext::resolveListCenterId($request);

        $query = VaccineStockMovement::with(['center', 'vaccine', 'creator'])
            ->when($selectedCenterId, fn ($q) => $q->where('center_id', $selectedCenterId));

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('note', 'like', '%' . $search . '%')
                  ->orWhereHas('vaccine', fn($v) => $v->where('name', 'like', '%' . $search . '%'));
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        $movements = $query->latest()
            ->paginate(20)
            ->withQueryString();

        return view('vaccine::admin.stock.index', compact('movements', 'centers', 'selectedCenterId'));
    }

    public function create(Request $request)
    {
        abort_unless(AdminContext::isBranchAdmin() || AdminContext::isSuperAdmin(), 403);

        if ($request->filled('center_id')) {
            AdminContext::assertCanManageCenter($request->integer('center_id'));
        }

        $centers = Center::active()->orderBy('sort_order')->orderBy('id')->get();
        $selectedCenterId = $request->filled('center_id')
            ? AdminContext::selectedCenterId($request->integer('center_id'))
            : AdminContext::selectedCenterId();
        $vaccines = $selectedCenterId
            ? Vaccine::forCenter($selectedCenterId)->orderBy('vaccines.name')->get()
            : collect();

        return view('vaccine::admin.stock.create', compact('centers', 'selectedCenterId', 'vaccines'));
    }

    public function store(Request $request)
    {
        abort_unless(AdminContext::isBranchAdmin() || AdminContext::isSuperAdmin(), 403);

        $validated = $request->validate([
            'center_id' => 'required|exists:centers,id',
            'vaccine_id' => 'required|exists:vaccines,id',
            'type' => 'required|in:import,adjustment',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'nullable|integer|min:0',
            'note' => 'nullable|string|max:1000',
        ]);

        $centerId = (int) Center::active()->findOrFail($validated['center_id'])->id;
        AdminContext::assertCanManageCenter($centerId);

        DB::transaction(function () use ($centerId, $validated) {
            $centerVaccine = CenterVaccine::query()
                ->where('center_id', $centerId)
                ->where('vaccine_id', $validated['vaccine_id'])
                ->where('is_active', true)
                ->whereHas('vaccine', fn ($query) => $query->where('is_active', true))
                ->lockForUpdate()
                ->first();

            if (!$centerVaccine) {
                throw ValidationException::withMessages([
                    'vaccine_id' => 'Vắc xin chưa được kích hoạt tại chi nhánh đã chọn.',
                ]);
            }

            $movement = VaccineStockMovement::create([
                'center_id' => $centerId,
                'vaccine_id' => $validated['vaccine_id'],
                'type' => $validated['type'],
                'quantity' => $validated['quantity'],
                'unit_price' => $validated['unit_price'] ?? 0,
                'note' => $validated['note'] ?? null,
                'created_by' => AdminContext::user()?->id,
            ]);

            $oldQuantity = (int) $centerVaccine->stock_quantity;
            $centerVaccine->stock_quantity = max(0, $oldQuantity + (int) $movement->quantity);
            $centerVaccine->stock_status = $centerVaccine->stock_quantity <= 0 ? 'out_of_stock' : ($centerVaccine->stock_quantity <= 5 ? 'limited' : 'available');
            $centerVaccine->save();

            \App\Services\AuditLogger::logStockUpdate(
                resourceId: $centerVaccine->id,
                oldValues: ['stock_quantity' => $oldQuantity],
                newValues: ['stock_quantity' => $centerVaccine->stock_quantity, 'stock_status' => $centerVaccine->stock_status],
                centerId: $centerId
            );
        });

        return redirect()->route('admin.stock.index', ['center_id' => $centerId])->with('success', 'Đã ghi nhận nhập/điều chỉnh kho.');
    }
}
