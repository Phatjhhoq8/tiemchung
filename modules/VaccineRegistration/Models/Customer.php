<?php

namespace Modules\VaccineRegistration\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'phone',
    ];

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public static function findOrCreateByPhone(string $phone, string $name): self
    {
        $customer = static::where('phone', $phone)->lockForUpdate()->first();
        if ($customer) {
            return $customer;
        }

        DB::table('customers')->insertOrIgnore([
            'name' => trim($name),
            'phone' => $phone,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return static::where('phone', $phone)->lockForUpdate()->firstOrFail();
    }

    public function pointTransactions(): HasMany
    {
        return $this->hasMany(PointTransaction::class);
    }

    public function pointBalance(): int
    {
        return (int) $this->pointTransactions()->sum('points');
    }
}
