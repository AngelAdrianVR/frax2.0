<?php

namespace App\Models\Community;

use App\Models\Settings\Subdivision;
use App\Models\Security\Patrol;
use App\Models\Community\Vehicle;
use App\Models\Gatehouse\ParcelService;
use App\Models\Gatehouse\AccessLog;
use App\Models\Finances\Payment;
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
    use HasRoles; 
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'alias',
        'phone',
        'show_phone',
        'show_email',
        'accept_messages',
        'committee_role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected $guard_name = 'web';

    // --- Relaciones ------------------------------------
    public function subdivisions()
    {
        return $this->belongsToMany(Subdivision::class)
                    ->withPivot('role_in_subdivision', 'is_current')
                    ->withTimestamps();
    }

    public function currentSubdivision()
    {
        return $this->subdivisions()->wherePivot('is_current', true)->first();
    }

    public function privateUnits(): BelongsToMany
    {
        return $this->belongsToMany(PrivateUnit::class, 'private_unit_user', 'user_id', 'private_unit_id')
                    ->withPivot([
                        'role_in_unit', 
                        'responsible_for_payments', 
                        'start_date', 
                        'end_date', 
                        'is_primary',
                        'alias'
                    ])
                    ->withTimestamps();
    }

    public function patrols(): HasMany
    {
        return $this->hasMany(Patrol::class, 'user_id');
    }

    // public function sentInvitations(): HasMany
    // {
    //     return $this->hasMany(RegisterInvitation::class, 'invited_by_user_id');
    // }

    public function parcels(): HasMany
    {
        return $this->hasMany(ParcelService::class, 'user_id');
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function accessLogs(): HasMany
    {
        return $this->hasMany(AccessLog::class, 'user_id');
    }

    // --- Helpers ------------------------------------

    public function getCurrentPropertyId()
    {
        if (session()->has('current_property_id')) {
            return session('current_property_id');
        }

        $firstUnit = $this->privateUnits->first();
        if ($firstUnit) {
            return $firstUnit->id;
        }

        $tableNames = config('permission.table_names');
        $firstAdminTeam = DB::table($tableNames['model_has_roles'])
            ->where('model_id', $this->id)
            ->where('model_type', get_class($this))
            ->whereNotNull('team_id')
            ->value('team_id');

        if ($firstAdminTeam) {
            return 'admin_' . $firstAdminTeam;
        }

        return null;
    }

    public function getCurrentSubdivisionId()
    {
        if (session()->has('current_subdivision_id')) {
            return session('current_subdivision_id');
        }

        $propertyId = $this->getCurrentPropertyId();

        if (!$propertyId) return null;

        if (is_string($propertyId) && str_starts_with($propertyId, 'admin_')) {
            return (int) str_replace('admin_', '', $propertyId);
        }

        $unit = PrivateUnit::find($propertyId);
        return $unit ? $unit->subdivision_id : null;
    }

    public function posts()
    {
        return $this->hasMany(\App\Models\Community\Post::class);
    }
    
}