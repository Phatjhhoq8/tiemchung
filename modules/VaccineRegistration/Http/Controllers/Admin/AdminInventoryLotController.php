<?php

namespace Modules\VaccineRegistration\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\VaccineRegistration\Models\Center;
use Modules\VaccineRegistration\Models\InventoryLot;
use Modules\VaccineRegistration\Models\StockMovement;
use Modules\VaccineRegistration\Models\Vaccine;
use Modules\VaccineRegistration\Support\AdminContext;

class AdminInventoryLotController extends Controller
{
    /**
     * Display a listing of inventory lots.
     */
    public function index(Request $request)
    {
        $selectedCenterId = AdminContext::resolveListCenterId($request);
        $query = InventoryLot::with(['vaccine', 'center']);

        if ($selectedCenterId) {
            $query->where('center_id', $selectedCenterId);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('lot_number', 'like', '%'.$search.'%')
                    ->orWhereHas('vaccine', function ($vq) use ($search) {
                        $vq->where('name', 'like', '%'.$search.'%');
                    });
            });
        }

        $lots = $query->orderBy('expires_at', 'asc')->paginate(15)->withQueryString();
        $centers = Center::active()->get();
        $vaccines = Vaccine::all();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $lots,
            ]);
        }

        return view('vaccine::admin.inventory_lots.index', compact('lots', 'centers', 'vaccines', 'selectedCenterId'));
    }

    /**
     * Store a newly created inventory lot.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vaccine_id' => 'required|exists:vaccines,id',
            'center_id' => 'required|exists:centers,id',
            'lot_number' => 'required|string|max:255',
            'initial_quantity' => 'required|integer|min:1',
            'expires_at' => 'required|date',
            'status' => 'required|in:active,recalled,quarantined',
        ]);

        AdminContext::assertCanManageCenter((int) $validated['center_id']);

        $lot = InventoryLot::create([
            'vaccine_id' => $validated['vaccine_id'],
            'center_id' => $validated['center_id'],
            'lot_number' => $validated['lot_number'],
            'initial_quantity' => $validated['initial_quantity'],
            'available_quantity' => $validated['initial_quantity'],
            'reserved_quantity' => 0,
            'expires_at' => $validated['expires_at'],
            'status' => $validated['status'],
        ]);

        StockMovement::create([
            'inventory_lot_id' => $lot->id,
            'user_id' => AdminContext::user()?->id,
            'type' => 'import',
            'quantity' => $validated['initial_quantity'],
            'note' => 'Khởi tạo lô vắc xin mới '.$validated['lot_number'],
        ]);
        AuditLogger::log(
            'inventory_lot.created',
            'inventory_lot',
            $lot->id,
            newValues: $lot->only(['vaccine_id', 'lot_number', 'initial_quantity', 'available_quantity', 'expires_at', 'status']),
            centerId: $lot->center_id
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tạo lô vắc xin thành công.',
                'lot' => $lot,
            ]);
        }

        return redirect()->back()->with('success', 'Tạo lô vắc xin thành công.');
    }

    /**
     * Update specified inventory lot.
     */
    public function update(Request $request, $id)
    {
        $lot = InventoryLot::findOrFail($id);

        AdminContext::assertCanManageCenter((int) $lot->center_id);

        $validated = $request->validate([
            'expires_at' => 'nullable|date',
            'status' => 'nullable|in:active,recalled,quarantined',
            'available_quantity' => 'nullable|integer|min:0',
        ]);

        DB::transaction(function () use ($lot, $validated) {
            $oldQty = (int) $lot->available_quantity;
            $newQty = isset($validated['available_quantity']) ? (int) $validated['available_quantity'] : $oldQty;

            $updateData = array_filter($validated, fn ($val) => ! is_null($val));
            $oldValues = $lot->only(array_keys($updateData));
            $lot->update($updateData);

            if ($newQty !== $oldQty) {
                StockMovement::create([
                    'inventory_lot_id' => $lot->id,
                    'user_id' => AdminContext::user()?->id,
                    'type' => 'adjustment',
                    'quantity' => $newQty - $oldQty,
                    'note' => 'Điều chỉnh số lượng khả dụng từ '.$oldQty.' sang '.$newQty,
                ]);
            }

            $newValues = $lot->fresh()->only(array_keys($updateData));
            if ($oldValues !== $newValues) {
                AuditLogger::log('inventory_lot.updated', 'inventory_lot', $lot->id, $oldValues, $newValues, $lot->center_id);
            }
        });

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật lô vắc xin thành công.',
                'lot' => $lot->fresh(),
            ]);
        }

        return redirect()->back()->with('success', 'Cập nhật lô vắc xin thành công.');
    }

    /**
     * Update inventory lot status (active, recalled, quarantined).
     */
    public function updateStatus(Request $request, $id)
    {
        $lot = InventoryLot::findOrFail($id);

        AdminContext::assertCanManageCenter((int) $lot->center_id);

        $validated = $request->validate([
            'status' => 'required|in:active,recalled,quarantined',
        ]);

        $oldStatus = $lot->status;
        $lot->update(['status' => $validated['status']]);
        if ($oldStatus !== $lot->status) {
            AuditLogger::log(
                'inventory_lot.status_changed',
                'inventory_lot',
                $lot->id,
                ['status' => $oldStatus],
                ['status' => $lot->status],
                $lot->center_id
            );
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật trạng thái lô vắc xin thành công.',
                'lot' => $lot,
            ]);
        }

        return redirect()->back()->with('success', 'Cập nhật trạng thái lô vắc xin thành công.');
    }
}
