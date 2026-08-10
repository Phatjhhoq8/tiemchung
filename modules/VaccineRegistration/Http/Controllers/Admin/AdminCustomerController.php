<?php

namespace Modules\VaccineRegistration\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\VaccineRegistration\Models\Customer;
use Modules\VaccineRegistration\Models\PointTransaction;
use Modules\VaccineRegistration\Support\AdminContext;
use Modules\VaccineRegistration\Support\PhoneNormalizer;

class AdminCustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query()->withSum('pointTransactions', 'points');
        $search = trim((string) $request->input('search'));
        $selectedCenterId = AdminContext::resolveListCenterId($request);

        if ($selectedCenterId) {
            $query->whereHas('registrations', fn ($registrations) => $registrations->where('center_id', $selectedCenterId));
        }

        if (AdminContext::isBranchAdmin()) {
            if ($search === '') {
                $query->whereRaw('1 = 0');
            } else {
                $phone = PhoneNormalizer::normalize($search);
                $query->where('phone', $phone ?: $search);
            }
        } elseif ($search !== '') {
            $phone = PhoneNormalizer::normalize($search);
            $query->where(function ($builder) use ($search, $phone) {
                $builder->where('phone', $phone ?: $search)
                    ->orWhere('name', 'like', '%' . $search . '%');
            });
        }

        $customers = $query->latest('id')->paginate(20)->withQueryString();

        return view('vaccine::admin.customers.index', compact('customers', 'search', 'selectedCenterId'));
    }

    public function show(int $id)
    {
        $customer = Customer::findOrFail($id);
        $registrations = $customer->registrations()->with('vaccines')->latest('id');
        $selectedCenterId = AdminContext::selectedCenterId();

        if ($selectedCenterId) {
            $registrations->where('center_id', $selectedCenterId);
            if (!(clone $registrations)->exists()) {
                abort(403, 'Khách hàng không thuộc phạm vi chi nhánh đang chọn.');
            }
        }

        $registrations = $registrations->paginate(20, ['*'], 'registrations_page');
        $transactions = $customer->pointTransactions()
            ->with('center:id,name')
            ->when($selectedCenterId, fn ($query) => $query->where('center_id', $selectedCenterId))
            ->latest('id')
            ->paginate(20, ['*'], 'points_page');
        $pointBalance = (int) $customer->pointTransactions()->sum('points');

        return view('vaccine::admin.customers.show', compact('customer', 'registrations', 'transactions', 'pointBalance', 'selectedCenterId'));
    }

    public function adjustPoints(Request $request, int $id)
    {
        abort_unless(AdminContext::isSuperAdmin(), 403);

        $validated = $request->validate([
            'points' => 'required|integer|not_in:0',
            'note' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($id, $validated) {
            $customer = Customer::lockForUpdate()->findOrFail($id);
            $transaction = PointTransaction::create([
                'customer_id' => $customer->id,
                'created_by' => AdminContext::user()?->id,
                'type' => PointTransaction::ADJUSTMENT,
                'points' => (int) $validated['points'],
                'source_key' => 'adjustment:' . $customer->id . ':' . Str::uuid(),
                'note' => $validated['note'],
            ]);

            AuditLogger::log(
                action: 'points_adjusted',
                resourceType: 'customer',
                resourceId: $customer->id,
                newValues: ['points' => $transaction->points, 'note' => $transaction->note],
                actorId: AdminContext::user()?->id,
            );
        });

        return back()->with('success', 'Đã điều chỉnh điểm khách hàng.');
    }
}
