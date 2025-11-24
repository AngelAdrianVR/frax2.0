<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot; // CAMBIO: Usar Pivot en vez de Model
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResidenceUnit extends Pivot // CAMBIO: Extiende de Pivot
{
    // Al tener un ID autoincremental en la tabla pivote, debemos indicarlo.
    // Por defecto, Pivot asume que no hay ID autoincremental.
    public $incrementing = true; 

    protected $table = 'residence_units';

    protected $fillable = [
        'resident_id', 
        'private_unit_id',
        'role_in_unit', 
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
        'permissions_level' => 'array',
        'role_in_unit' => 'string', // Ojo: En tu migración es un Enum/String ('Dueño', 'Inquilino'), no integer.
    ];

    /**
     * Relación: Esta ocupación pertenece a un Residente.
     */
    public function resident(): BelongsTo
    {
        // NOTA: Según tus migraciones, la tabla es 'residents', por lo que deberías
        // apuntar al modelo Resident::class si existe, no a User::class.
        // Si no tienes modelo Resident, déjalo como User, pero verifica tus llaves foráneas.
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