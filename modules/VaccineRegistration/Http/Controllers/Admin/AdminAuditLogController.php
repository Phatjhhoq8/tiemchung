<?php

namespace Modules\VaccineRegistration\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Modules\VaccineRegistration\Models\Center;
use Modules\VaccineRegistration\Support\AdminContext;

class AdminAuditLogController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(AdminContext::isSuperAdmin(), 403, 'Bạn không có quyền xem nhật ký hệ thống.');

        $filters = $request->validate([
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date', 'after_or_equal:from_date'],
            'actor_id' => ['nullable', 'integer', 'exists:users,id'],
            'center_id' => ['nullable', 'integer', 'exists:centers,id'],
            'action' => ['nullable', 'string', 'max:50'],
            'resource_type' => ['nullable', 'string', 'max:50'],
            'resource_id' => ['nullable', 'string', 'max:50'],
        ]);

        $query = AuditLog::query()->with(['actor', 'center']);

        if (! empty($filters['from_date'])) {
            $query->where('created_at', '>=', $filters['from_date'].' 00:00:00');
        }
        if (! empty($filters['to_date'])) {
            $query->where('created_at', '<=', $filters['to_date'].' 23:59:59');
        }

        foreach (['actor_id', 'center_id', 'action', 'resource_type', 'resource_id'] as $field) {
            if (isset($filters[$field]) && $filters[$field] !== '') {
                $query->where($field, $filters[$field]);
            }
        }

        $auditLogs = $query->latest('created_at')->latest('id')->paginate(20)->withQueryString();
        $actors = User::query()->orderBy('name')->get(['id', 'name', 'username']);
        $centers = Center::query()->orderBy('sort_order')->orderBy('name')->get(['id', 'name']);
        $actions = AuditLog::query()->whereNotNull('action')->distinct()->orderBy('action')->pluck('action');
        $resourceTypes = AuditLog::query()->whereNotNull('resource_type')->distinct()->orderBy('resource_type')->pluck('resource_type');

        return view('vaccine::admin.audit-logs.index', compact(
            'auditLogs',
            'actors',
            'centers',
            'actions',
            'resourceTypes'
        ));
    }

    public function show(AuditLog $auditLog)
    {
        abort_unless(AdminContext::isSuperAdmin(), 403, 'Bạn không có quyền xem nhật ký hệ thống.');

        $auditLog->load(['actor', 'center']);

        return view('vaccine::admin.audit-logs.show', compact('auditLog'));
    }
}
