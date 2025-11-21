<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class VisitEvent extends Model
{
    protected $fillable = [
        'name', 
        'qr_code', 
        'date_time_start', 
        'date_time_end', 
        'guest_amount', 
        'max_qr_uses', 
        'current_use_count', 
        'status',
        'description', 
        'private_unit_id'
    ];

    protected $casts = [
        'date_time_start' => 'datetime',
        'date_time_end' => 'datetime',
        'guest_amount' => 'integer',
        'max_qr_uses' => 'integer',
        'current_use_count' => 'integer',
    ];

    /**
     * Relación: Una visita pertenece a una Unidad Privada (Casa/Depa).
     */
    public function privateUnit(): BelongsTo
    {
        return $this->belongsTo(PrivateUnit::class, 'private_unit_id');
    }

     /**
     * Relación: Esta autorización de visita tiene múltiples registros de acceso (entradas/salidas).
     * Referencia a la tabla 'visit_events' o 'visit_entries' que mencionas.
     */
    public function accessLogs(): HasMany
    {
        // Asumiendo que el modelo para la tabla de bitácora se llama VisitEntry o AccessLog
        // Si tu tabla de logs se llama literalmente 'visit_events' y este modelo es el padre,
        // tendrás un conflicto de nombres. Asumiré que el hijo es 'VisitEntry'.
        return $this->hasMany(AccessLog::class);
    }
    
}