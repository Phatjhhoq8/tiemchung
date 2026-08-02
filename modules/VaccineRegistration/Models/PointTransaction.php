<?php

namespace Modules\VaccineRegistration\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class PointTransaction extends Model
{
    public const EARN = 'earn';
    public const REDEEM = 'redeem';
    public const EARN_REVERSAL = 'earn_reversal';
    public const REDEEM_REFUND = 'redeem_refund';
    public const ADJUSTMENT = 'adjustment';

    protected $fillable = [
        'customer_id',
        'registration_id',
        'center_id',
        'created_by',
        'type',
        'points',
        'source_key',
        'note',
    ];

    protected $casts = [
        'points' => 'integer',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function center(): BelongsTo
    {
        return $this->belongsTo(Center::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
