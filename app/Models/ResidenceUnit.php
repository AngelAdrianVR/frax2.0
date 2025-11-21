<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResidenceUnit extends Model
{
    // Al extender de Pivot y usarse con ->using(), habilita características extra
    // NOTA: Si este modelo tiene lógica compleja independiente, es mejor usar Model normal en lugar de Pivot.
    
    protected $table = 'residence_units';
    public $incrementing = true; 

    protected $fillable = [
        'resident_id', 
        'private_unit_id',
        'role_in_unit', // 'Dueño', 'Inquilino'
        'responsible_for_payments', 
        'start_date', 
        'end_date', 
        'primary', 
        'is_primary_owner', 
        'permissions_level', 
        'alias'
    ];

    protected $casts = [
        'responsible_for_payments' => 'boolean',
        'primary' => 'boolean',
        'is_primary_owner' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'permissions_level' => 'array', // JSON para permisos específicos
        'role_in_unit' => 'integer',
    ];

    /**
     * Relación: Esta ocupación pertenece a un Usuario (Residente).
     * Asumiendo que tus residentes están en la tabla 'users'.
     */
    public function resident(): BelongsTo
    {
        // Ajusta 'User::class' si tienes un modelo separado 'Resident::class'
        return $this->belongsTo(User::class, 'resident_id');
    }

    /**
     * Relación: Esta ocupación pertenece a una Unidad Privada.
     */
    public function privateUnit(): BelongsTo
    {
        return $this->belongsTo(PrivateUnit::class, 'private_unit_id');
    }
}