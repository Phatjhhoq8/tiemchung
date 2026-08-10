<?php

namespace App\Services;

use App\Models\AuditLog;
use Modules\VaccineRegistration\Support\AdminContext;

class AuditLogger
{
    /**
     * Log a general audit event.
     */
    public static function log(
        string $action,
        string $resourceType,
        string|int $resourceId,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?int $centerId = null,
        ?int $actorId = null
    ): AuditLog {
        $resolvedActorId = $actorId ?? auth()->id() ?? AdminContext::user()?->id;
        $resolvedCenterId = $centerId ?? AdminContext::centerId() ?? AdminContext::selectedCenterId();

        $ipAddress = request()?->ip();
        $userAgent = request()?->userAgent();

        return AuditLog::create([
            'actor_id' => $resolvedActorId,
            'center_id' => $resolvedCenterId,
            'action' => $action,
            'resource_type' => $resourceType,
            'resource_id' => (string) $resourceId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);
    }

    /**
     * Helper for vaccine price updates.
     */
    public static function logPriceUpdate(
        string|int $resourceId,
        ?array $oldValues,
        ?array $newValues,
        ?int $centerId = null,
        string $resourceType = 'vaccine'
    ): AuditLog {
        return static::log(
            action: 'price_update',
            resourceType: $resourceType,
            resourceId: $resourceId,
            oldValues: $oldValues,
            newValues: $newValues,
            centerId: $centerId
        );
    }

    /**
     * Helper for order status changes.
     */
    public static function logOrderStatusUpdate(
        string|int $resourceId,
        ?array $oldValues,
        ?array $newValues,
        ?int $centerId = null,
        string $resourceType = 'registration'
    ): AuditLog {
        return static::log(
            action: 'order_status_update',
            resourceType: $resourceType,
            resourceId: $resourceId,
            oldValues: $oldValues,
            newValues: $newValues,
            centerId: $centerId
        );
    }

    /**
     * Helper for refunds issued.
     */
    public static function logRefund(
        string|int $resourceId,
        ?array $oldValues,
        ?array $newValues,
        ?int $centerId = null,
        string $resourceType = 'registration'
    ): AuditLog {
        return static::log(
            action: 'refund_issued',
            resourceType: $resourceType,
            resourceId: $resourceId,
            oldValues: $oldValues,
            newValues: $newValues,
            centerId: $centerId
        );
    }
}
