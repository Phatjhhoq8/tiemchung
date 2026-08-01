<?php

namespace Modules\VaccineRegistration\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\VaccineRegistration\Models\Patient;

class AdminPatientController extends Controller
{
    /**
     * Display a listing of centralized patient profiles.
     */
    public function index(Request $request)
    {
        $query = Patient::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('identity_card', 'like', "%{$search}%");
            });
        }

        $patients = $query->withCount(['registrations', 'administeredDoses'])->latest()->paginate(15);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $patients,
            ]);
        }

        if (view()->exists('vaccine::admin.patients.index')) {
            return view('vaccine::admin.patients.index', compact('patients'));
        }

        return response()->json([
            'success' => true,
            'data' => $patients,
        ]);
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

        $patient->update($validated);

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
