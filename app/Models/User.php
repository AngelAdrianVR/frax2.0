<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasRoles; // Permite asignar roles y permisos al usuario

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

    // --- Relaciones ------------------------------------
    public function subdivisions()
    {
        // Relación muchos a muchos
        return $this->belongsToMany(Subdivision::class)
                    ->withPivot('role_in_subdivision', 'is_current')
                    ->withTimestamps();
    }

    // Helper para obtener el fraccionamiento actual donde está navegando
    public function currentSubdivision()
    {
        return $this->subdivisions()->wherePivot('is_current', true)->first();
    }

    /**
     * Unidades privadas asociadas al usuario (propiedades donde es dueño, inquilino, etc).
     */
    public function privateUnits(): BelongsToMany
    {
        return $this->belongsToMany(PrivateUnit::class, 'private_unit_user', 'user_id', 'private_unit_id')
                    ->withPivot([
                        'role_in_unit', 
                        'responsible_for_payments', 
                        'start_date', 
                        'end_date', 
                        'is_primary', // Actualizado para coincidir con tu BD
                        'alias'       // permissions_level eliminado
                    ])
                    ->withTimestamps();
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
        return $this->hasMany(ParcelService::class, 'user_id');
    }

    /**
     * Logs de acceso generados por el usuario.
     */
    public function accessLogs(): HasMany
    {
        return $this->hasMany(AccessLog::class, 'user_id');
    }

    public function getCurrentPropertyId()
    {
        // 1. Intentar sacar de sesión
        if (session()->has('current_property_id')) {
            return session('current_property_id');
        }

        // 2. Si no hay sesión, intentar obtener la primera propiedad directamente de la relación
        $firstUnit = $this->privateUnits->first();
        if ($firstUnit) {
            return $firstUnit->id;
        }

        // 3. Si no es residente, buscar si es ADMIN de algún fraccionamiento
        // Buscamos en la tabla de roles si tiene algún rol asociado a un team_id
        $tableNames = config('permission.table_names');
        $firstAdminTeam = DB::table($tableNames['model_has_roles'])
            ->where('model_id', $this->id)
            ->where('model_type', get_class($this))
            ->whereNotNull('team_id')
            ->value('team_id'); // Obtiene el primer team_id

        if ($firstAdminTeam) {
            return 'admin_' . $firstAdminTeam;
        }

        return null;
    }

    /**
     * Helper para obtener el ID del fraccionamiento (Team ID) basado en la propiedad actual.
     * Útil para controllers de Admin.
     */
    public function getCurrentSubdivisionId()
    {
        // Si ya está explícito en sesión
        if (session()->has('current_subdivision_id')) {
            return session('current_subdivision_id');
        }

        $propertyId = $this->getCurrentPropertyId();

        if (!$propertyId) return null;

        // Caso A: Es modo Admin (ej. "admin_2")
        if (is_string($propertyId) && str_starts_with($propertyId, 'admin_')) {
            return (int) str_replace('admin_', '', $propertyId);
        }

        // Caso B: Es una unidad privada (ID numérico)
        // Buscamos a qué fraccionamiento pertenece esa unidad
        $unit = PrivateUnit::find($propertyId);
        return $unit ? $unit->subdivision_id : null;
    }
}