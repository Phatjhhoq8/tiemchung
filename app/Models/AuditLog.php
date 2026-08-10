<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\VaccineRegistration\Models\Center;

class AuditLog extends Model
{
    use HasFactory;

    protected $table = 'audit_logs';

    protected $fillable = [
        'actor_id',
        'center_id',
        'action',
        'resource_type',
        'resource_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    protected static function booted(): void
    {
        static::updating(fn () => throw new \LogicException('Audit logs cannot be updated.'));
        static::deleting(fn () => throw new \LogicException('Audit logs cannot be deleted.'));
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function center()
    {
        return $this->belongsTo(Center::class, 'center_id');
    }
}
