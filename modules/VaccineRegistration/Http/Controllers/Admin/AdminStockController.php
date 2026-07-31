<?php

namespace Modules\VaccineRegistration\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
        $selectedCenterId = AdminContext::selectedCenterId($request->filled('center_id') ? (int) $request->input('center_id') : null);

        $movements = VaccineStockMovement::with(['center', 'vaccine', 'creator'])
            ->when($selectedCenterId, fn ($q) => $q->where('center_id', $selectedCenterId))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('vaccine::admin.stock.index', compact('movements', 'centers', 'selectedCenterId'));
    }

    public function create(Request $request)
    {
        abort_unless(AdminContext::isBranchAdmin(), 403);

        $centers = Center::active()->orderBy('sort_order')->orderBy('id')->get();
        $selectedCenterId = AdminContext::selectedCenterId($request->filled('center_id') ? (int) $request->input('center_id') : null);
        $vaccines = Vaccine::forCenter($selectedCenterId)->orderBy('vaccines.name')->get();

        return view('vaccine::admin.stock.create', compact('centers', 'selectedCenterId', 'vaccines'));
    }

    public function store(Request $request)
    {
        abort_unless(AdminContext::isBranchAdmin(), 403);

        $validated = $request->validate([
            'center_id' => 'required|exists:centers,id',
            'vaccine_id' => 'required|exists:vaccines,id',
            'type' => 'required|in:import,adjustment',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'nullable|integer|min:0',
            'note' => 'nullable|string|max:1000',
        ]);

        $centerId = AdminContext::selectedCenterId((int) $validated['center_id']);
        if ((int) $validated['center_id'] !== (int) $centerId) {
            abort(403);
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

        $centerVaccine = CenterVaccine::firstOrCreate(
            ['center_id' => $centerId, 'vaccine_id' => $validated['vaccine_id']],
            ['price' => Vaccine::findOrFail($validated['vaccine_id'])->price, 'stock_status' => 'available']
        );

        $centerVaccine->stock_quantity = max(0, (int) $centerVaccine->stock_quantity + (int) $movement->quantity);
        $centerVaccine->stock_status = $centerVaccine->stock_quantity <= 0 ? 'out_of_stock' : ($centerVaccine->stock_quantity <= 5 ? 'limited' : 'available');
        $centerVaccine->save();

        return redirect()->route('admin.stock.index', ['center_id' => $centerId])->with('success', 'Đã ghi nhận nhập/điều chỉnh kho.');
    }
}
