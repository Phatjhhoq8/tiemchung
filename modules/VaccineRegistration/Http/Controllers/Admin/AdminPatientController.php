<?php

namespace Modules\VaccineRegistration\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Modules\VaccineRegistration\Models\Patient;
use Modules\VaccineRegistration\Support\AdminContext;

class AdminPatientController extends Controller
{
    /**
     * Display a listing of centralized patient profiles.
     */
    public function index(Request $request)
    {
        $query = Patient::query();
        $search = trim((string) $request->input('search'));
        $selectedCenterId = AdminContext::resolveListCenterId($request);

        if ($selectedCenterId) {
            $query->whereHas('registrations', fn ($q) => $q->where('center_id', $selectedCenterId));
        }

        if (AdminContext::isBranchAdmin()) {
            $query->whereHas('registrations', fn ($q) => $q->where('center_id', AdminContext::centerId()));
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('identity_card', 'like', "%{$search}%");
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('patients.created_at', '>=', $request->input('from_date'));
        }
        if ($request->filled('to_date')) {
            $query->whereDate('patients.created_at', '<=', $request->input('to_date'));
        }

        $patients = $query->withCount(['registrations', 'administeredDoses'])
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $isSuperAdmin = AdminContext::isSuperAdmin();
        $adminCenters = $isSuperAdmin
            ? \Modules\VaccineRegistration\Models\Center::active()->orderBy('sort_order')->orderBy('id')->get(['id', 'name'])
            : collect();

        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'html' => view('vaccine::admin.patients._table', compact('patients', 'search', 'selectedCenterId', 'isSuperAdmin', 'adminCenters'))->render(),
            ]);
        }

        return view('vaccine::admin.patients.index', compact('patients', 'search', 'selectedCenterId', 'isSuperAdmin', 'adminCenters'));
    }

    /**
     * Store a newly created patient profile in central management.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'dob' => 'required|date',
            'gender' => 'required|string',
            'phone' => 'required|string|max:20',
            'identity_card' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'medical_history' => 'nullable|string',
        ]);

        $patient = Patient::findOrCreateCentralized($validated);
        if ($patient->wasRecentlyCreated) {
            AuditLogger::log(
                'patient.created',
                'patient',
                $patient->id,
                newValues: $patient->only(['full_name', 'dob', 'gender', 'phone', 'identity_card', 'address', 'medical_history', 'is_active'])
            );
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Hồ sơ bệnh nhân đã được tạo/cập nhật thành công.',
                'data' => $patient,
            ]);
        }

        return redirect()->back()->with('success', 'Hồ sơ bệnh nhân đã được lưu thành công.');
    }

    /**
     * Display the specified patient profile details and vaccination history.
     */
    public function show($id, Request $request)
    {
        $patient = Patient::with(['registrations.vaccines', 'administeredDoses.vaccine', 'administeredDoses.inventoryLot', 'administeredDoses.administrator'])->findOrFail($id);

        if (AdminContext::isBranchAdmin()) {
            $hasRegistration = $patient->registrations()->where('center_id', AdminContext::centerId())->exists();
            if (! $hasRegistration) {
                abort(403, 'Bạn không có quyền xem hồ sơ bệnh nhân thuộc chi nhánh khác.');
            }
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $patient,
            ]);
        }

        if (view()->exists('vaccine::admin.patients.show')) {
            return view('vaccine::admin.patients.show', compact('patient'));
        }

        return response()->json([
            'success' => true,
            'data' => $patient,
        ]);
    }

    /**
     * Update the specified patient profile in storage.
     */
    public function update(Request $request, $id)
    {
        $patient = Patient::findOrFail($id);

        if (AdminContext::isBranchAdmin()) {
            $hasRegistration = $patient->registrations()->where('center_id', AdminContext::centerId())->exists();
            if (! $hasRegistration) {
                abort(403, 'Bạn không có quyền cập nhật hồ sơ bệnh nhân thuộc chi nhánh khác.');
            }
        }

        $validated = $request->validate([
            'full_name' => 'sometimes|required|string|max:255',
            'dob' => 'sometimes|required|date',
            'gender' => 'sometimes|required|string',
            'phone' => 'sometimes|required|string|max:20',
            'identity_card' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        $oldValues = $patient->only(array_keys($validated));
        $patient->update($validated);
        $newValues = $patient->fresh()->only(array_keys($validated));
        if ($oldValues !== $newValues) {
            AuditLogger::log('patient.updated', 'patient', $patient->id, $oldValues, $newValues);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thông tin bệnh nhân thành công.',
                'data' => $patient,
            ]);
        }

        return redirect()->back()->with('success', 'Cập nhật thông tin bệnh nhân thành công.');
    }
}
