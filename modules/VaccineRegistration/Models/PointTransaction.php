<?php

namespace Modules\VaccineRegistration\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'expired_at',
    ];

    protected $casts = [
        'points' => 'integer',
        'expired_at' => 'datetime',
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

    public function typeLabel(): string
    {
        return match ($this->type) {
            self::EARN => 'Tích điểm',
            self::REDEEM => 'Sử dụng điểm',
            self::EARN_REVERSAL => 'Đảo điểm tích lũy',
            self::REDEEM_REFUND => 'Hoàn điểm đã sử dụng',
            self::ADJUSTMENT => 'Điều chỉnh',
            default => 'Không xác định',
        };
    }
}
