<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
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

    // --- Relaciones ---

    /**
     * Roles asignados al usuario.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->withPivot('primary')->withTimestamps();
    }

    /**
     * Perfil de residente asociado al usuario (si aplica).
     */
    public function resident(): HasOne
    {
        return $this->hasOne(Resident::class, 'user_id');
    }

    /**
     * Patrullajes realizados por el usuario (si es guardia).
     */
    public function patrols(): HasMany
    {
        return $this->hasMany(Patrol::class, 'user_id');
    }

    /**
     * Invitaciones enviadas por este usuario.
     */
    public function sentInvitations(): HasMany
    {
        return $this->hasMany(RegisterInvitation::class, 'invited_by_user_id');
    }

    /**
     * Paquetería gestionada/recibida por este usuario (ej. guardia en caseta).
     */
    public function parcels(): HasMany
    {
        // Ajustado a 'user_id' para coincidir con el diagrama (users_id -> singular)
        return $this->hasMany(ParcelService::class, 'user_id');
    }

    /**
     * Logs de acceso generados por el usuario.
     */
    public function accessLogs(): HasMany
    {
        return $this->hasMany(AccessLog::class, 'user_id');
    }
}
