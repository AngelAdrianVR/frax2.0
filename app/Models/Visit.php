<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Visit extends Model
{

    protected $fillable = [
        'name',             
        'reason',          
        'qr_code',          
        'expiration_date',  
        'date_of_use',      
        'access_type', // 'Peatonal', 'Vehicular'     
        'status',        // 'Pendiente','Ingresado','Expirado','Cancelado'    
        'private_unit_id', 
        'visit_event_id',   
    ];

    protected $casts = [
        'expiration_date' => 'datetime',
        'date_of_use'     => 'datetime',
        // Los enums generalmente se manejan como string, a menos que uses PHP Enums
    ];

    /**
     * Relación: Una visita pertenece a una Unidad Privada (Casa/Depa).
     */
    public function privateUnit(): BelongsTo
    {
        return $this->belongsTo(PrivateUnit::class, 'private_unit_id');
    }

    /**
     * Relación: Una visita puede pertenecer a un Evento de Visita padre.
     * (Basado en el campo visit_event_id de tu migración).
     */
    public function visitEvent(): BelongsTo
    {
        return $this->belongsTo(VisitEvent::class, 'visit_event_id');
    }

    /**
     * Relación: Logs de acceso.
     * Mantenemos esta relación asumiendo que tienes una tabla para registrar 
     * las entradas y salidas físicas de esta visita.
     */
    public function accessLogs(): HasMany
    {
        return $this->hasMany(AccessLog::class);
    }
}