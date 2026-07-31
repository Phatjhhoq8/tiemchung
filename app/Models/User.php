<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Modules\VaccineRegistration\Models\Center;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'center_id',
        'is_active',
        'status',
        'must_change_password',
        'password_changed_at',
        'last_login_at',
        'locked_until',
        'failed_login_count',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'must_change_password' => 'boolean',
            'password_changed_at' => 'datetime',
            'last_login_at' => 'datetime',
            'locked_until' => 'datetime',
            'failed_login_count' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (User $user) {
            $user->is_active = false;
            $user->status = 'inactive';
            $user->save();
            return false; // Prevent hard deletion
        });
    }

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isBranchAdmin(): bool
    {
        return $this->role === 'branch_admin';
    }

    /**
     * Check if user account is locked.
     */
    public function isLocked(): bool
    {
        if ($this->status === 'locked') {
            return true;
        }

        if ($this->locked_until !== null && $this->locked_until->isFuture()) {
            return true;
        }

        return false;
    }

    /**
     * Record a successful login event for the user.
     */
    public function recordSuccessfulLogin(): void
    {
        $this->forceFill([
            'failed_login_count' => 0,
            'locked_until' => null,
            'last_login_at' => now(),
        ])->save();
    }

    /**
     * Record a failed login attempt for the user and lock account if limit reached.
     */
    public function recordFailedLogin(int $maxAttempts = 5, int $lockoutMinutes = 15): void
    {
        $this->failed_login_count = ($this->failed_login_count ?? 0) + 1;

        if ($this->failed_login_count >= $maxAttempts) {
            $this->locked_until = now()->addMinutes($lockoutMinutes);
        }

        $this->save();
    }
}
