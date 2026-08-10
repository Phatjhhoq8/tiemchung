<?php

namespace App\Services;

use App\Models\AuditLog;
use Modules\VaccineRegistration\Support\AdminContext;

class AuditLogger
{
    private const EXCLUDED_KEYS = [
        'password',
        'password_confirmation',
        'current_password',
        'remember_token',
        'token',
        'access_token',
        'refresh_token',
        'session',
        'session_id',
        'cookie',
        'admin_password_hash',
    ];

    private const PRIVATE_KEYS = [
        'full_name',
        'patient_name',
        'dob',
        'patient_dob',
        'gender',
        'patient_gender',
        'phone',
        'patient_phone',
        'guardian_phone',
        'email',
        'address',
        'patient_address',
        'identity_card',
        'medical_history',
        'screening_notes',
        'observation_notes',
        'content',
    ];

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
        ?int $actorId = null,
        bool $resolveActor = true,
        bool $resolveCenter = true
    ): AuditLog {
        $resolvedActorId = $actorId ?? ($resolveActor ? auth()->id() ?? AdminContext::user()?->id : null);
        $resolvedCenterId = $centerId ?? ($resolveCenter ? AdminContext::centerId() ?? AdminContext::selectedCenterId() : null);

        $ipAddress = request()?->ip();
        $userAgent = request()?->userAgent();

        [$safeOldValues, $safeNewValues] = static::prepareChanges($oldValues, $newValues);

        return AuditLog::create([
            'actor_id' => $resolvedActorId,
            'center_id' => $resolvedCenterId,
            'action' => $action,
            'resource_type' => $resourceType,
            'resource_id' => (string) $resourceId,
            'old_values' => $safeOldValues,
            'new_values' => $safeNewValues,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);
    }

    /**
     * Keep only useful changed fields and remove secrets or private values.
     */
    private static function prepareChanges(?array $oldValues, ?array $newValues): array
    {
        if ($oldValues !== null && $newValues !== null) {
            $keys = array_unique(array_merge(array_keys($oldValues), array_keys($newValues)));
            $changedKeys = array_filter($keys, fn ($key) => ($oldValues[$key] ?? null) !== ($newValues[$key] ?? null));
            $oldValues = array_intersect_key($oldValues, array_flip($changedKeys));
            $newValues = array_intersect_key($newValues, array_flip($changedKeys));
        }

        $oldValues = static::sanitize($oldValues);
        $newValues = static::sanitize($newValues);

        return [empty($oldValues) ? null : $oldValues, empty($newValues) ? null : $newValues];
    }

    private static function sanitize(?array $values): ?array
    {
        if ($values === null) {
            return null;
        }

        $safe = [];
        foreach ($values as $key => $value) {
            $normalizedKey = strtolower((string) $key);

            if (in_array($normalizedKey, self::EXCLUDED_KEYS, true)) {
                continue;
            }

            if (in_array($normalizedKey, self::PRIVATE_KEYS, true)) {
                $safe[$normalizedKey.'_changed'] = true;

                continue;
            }

            $safe[$key] = is_array($value) ? static::sanitize($value) : $value;
        }

        return $safe;
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
