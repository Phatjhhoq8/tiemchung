<?php

namespace Modules\VaccineRegistration\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\VaccineRegistration\Models\ConsultationLead;
use Modules\VaccineRegistration\Models\Center;
use Modules\VaccineRegistration\Support\CenterContext;

class ConsultationLeadController extends Controller
{
    /**
     * Store a new consultation lead from public submissions.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required_without:customerName|nullable|string|max:255',
            'customerName' => 'required_without:name|nullable|string|max:255',
            'phone' => 'required_without:customerPhone|nullable|string|regex:/^[0-9]{9,11}$/',
            'customerPhone' => 'required_without:phone|nullable|string|regex:/^[0-9]{9,11}$/',
            'source' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:2000',
            'customerNote' => 'nullable|string|max:2000',
            'center_id' => 'nullable|exists:centers,id',
        ], [
            'name.required_without' => 'Vui lòng điền Họ và tên.',
            'customerName.required_without' => 'Vui lòng điền Họ và tên.',
            'phone.required_without' => 'Vui lòng điền Số điện thoại liên hệ.',
            'customerPhone.required_without' => 'Vui lòng điền Số điện thoại liên hệ.',
            'phone.regex' => 'Số điện thoại không hợp lệ (9 - 11 chữ số).',
            'customerPhone.regex' => 'Số điện thoại không hợp lệ (9 - 11 chữ số).',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        $name = $validated['name'] ?? $validated['customerName'];
        $phone = $validated['phone'] ?? $validated['customerPhone'];
        $source = $validated['source'] ?? 'Website Public Form';
        $note = $validated['note'] ?? $validated['customerNote'] ?? null;
        $centerId = $validated['center_id'] ?? CenterContext::current()?->id;

        $lead = ConsultationLead::create([
            'name' => $name,
            'phone' => $phone,
            'source' => $source,
            'status' => 'new',
            'note' => $note,
            'center_id' => $centerId,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Yêu cầu tư vấn đã được gửi thành công!',
            'lead' => $lead,
        ], 201);
    }
}
