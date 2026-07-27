<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Schema;

class User extends Authenticatable implements CanResetPasswordContract
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, CanResetPassword;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
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

    protected $appends = [
        'role_names',
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
        ];
    }


    /**
     * Obtiene el perfil medico propio del usuario.
     */
    public function profiles(): HasMany
    {
        return $this->hasMany(Profile::class, 'owner_id');
    }

    public function sharedProfiles(): BelongsToMany
    {
        return $this->belongsToMany(Profile::class, 'profile_user')
            ->withPivot('can_edit');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function getRoleNamesAttribute(): array
    {
        if (! Schema::hasTable('role_user') || ! Schema::hasTable('roles')) {
            return array_values(array_filter([$this->attributes['role'] ?? null]));
        }

        if ($this->relationLoaded('roles')) {
            return $this->roles->pluck('name')->values()->toArray();
        }

        return $this->roles()->pluck('name')->values()->toArray();
    }

    public function hasRole(string $role): bool
    {
        if (($this->attributes['role'] ?? null) === $role) {
            return true;
        }

        return in_array($role, $this->role_names, true);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class);
    }

    public function isPremium(): bool
    {
        $subscription = $this->relationLoaded('subscription')
            ? $this->subscription
            : $this->subscription()->with('plan')->first();

        if (! $subscription) {
            return false;
        }

        if ((int) $subscription->plan_id === 2) {
            return true;
        }

        return strtolower((string) ($subscription->plan?->name ?? '')) === 'premium';
    }

    /**
     * Enviar notificación personalizada de reset de contraseña
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
