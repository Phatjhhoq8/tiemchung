<?php

namespace Modules\VaccineRegistration\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\VaccineRegistration\Models\ConsultationLead;
use Modules\VaccineRegistration\Models\Center;
use Modules\VaccineRegistration\Support\AdminContext;

class AdminConsultationLeadController extends Controller
{
    /**
     * Display a listing of consultation leads.
     */
    public function index(Request $request)
    {
        $selectedCenterId = AdminContext::isBranchAdmin()
            ? AdminContext::centerId()
            : ($request->filled('center_id') ? (int) $request->input('center_id') : null);

        $query = ConsultationLead::with('center')->latest();

        if ($selectedCenterId) {
            $query->where('center_id', $selectedCenterId);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('source', 'like', "%{$search}%");
            });
        }

        $leads = $query->paginate(20)->withQueryString();
        $centers = Center::active()->get();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $leads,
            ]);
        }

        return view('vaccine::admin.leads.index', compact('leads', 'centers', 'selectedCenterId'));
    }

    /**
     * Display details of a consultation lead.
     */
    public function show(Request $request, $id)
    {
        $lead = ConsultationLead::with('center')->findOrFail($id);
        $this->assertLeadVisible($lead);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'lead' => $lead,
            ]);
        }

        return view('vaccine::admin.leads.show', compact('lead'));
    }

    /**
     * Update status of a consultation lead.
     */
    public function updateStatus(Request $request, $id)
    {
        $lead = ConsultationLead::findOrFail($id);
        $this->assertLeadVisible($lead);

        $validated = $request->validate([
            'status' => 'required|string|max:50',
            'note' => 'nullable|string|max:2000',
        ]);

        $lead->status = $validated['status'];
        if (isset($validated['note'])) {
            $lead->note = $validated['note'];
        }
        $lead->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật trạng thái tư vấn thành công!',
                'lead' => $lead,
            ]);
        }

        return redirect()->back()->with('success', 'Cập nhật trạng thái tư vấn thành công!');
    }

    private function assertLeadVisible(ConsultationLead $lead): void
    {
        if (AdminContext::isBranchAdmin() && (int) $lead->center_id !== (int) AdminContext::centerId()) {
            abort(403, 'Cross-branch access forbidden.');
        }
    }
}
