<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParcelService extends Model
{
    // Gestión de Paquetería (Amazon, MercadoLibre, etc. dejados en caseta)
    protected $fillable = [
        'name', // Ej: "Paquete Amazon" o "Sobre DHL"
        'tracking_number', 
        'receipt_date', // Cuando llega a caseta
        'delivery_date', // Cuando el residente lo recoge
        'imagen_etiqueta_url', // Foto de la etiqueta para evidencia
        'status', // 'Recibido', 'Entregado', 'Regresado'
        'private_unit_id', // A qué casa va dirigido
        'user_id' // El guardia/empleado que recibió el paquete en caseta
    ];

    protected $casts = [
        'receipt_date' => 'datetime',
        'delivery_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    /**
     * Relación: El paquete va dirigido a una Unidad Privada.
     */
    public function privateUnit(): BelongsTo
    { 
        return $this->belongsTo(PrivateUnit::class, 'private_unit_id'); 
    }

    /**
     * Relación: El paquete fue registrado/recibido por un Empleado (Guardia/Admin).
     */
    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}