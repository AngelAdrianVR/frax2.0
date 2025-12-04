<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import necesario para las relaciones

class Reservation extends Model
{
    protected $fillable = [
        'start_date_time', 
        'end_date_time', 
        'total_cost', 
        'status', // 'Pendiente', 'Aprobada', 'Rechazada', 'Cancelada', 'Completada'
        'amenity_id', 
        'resident_id',
        // --- Nuevos campos agregados ---
        'private_unit_id',   // Para vincular la reserva a una casa específica
        'attendees_amount',  // Para controlar el aforo
        'admin_notes',       // Notas internas del administrador
        'generated_fee_id'   // Para vincular con el cobro generado
    ];

    protected $casts = [
        'start_date_time' => 'datetime',
        'end_date_time' => 'datetime',
        'total_cost' => 'decimal:2',
    ];
    
    // --- Relaciones Originales (Intactas) ---

    public function amenity(): BelongsTo
    { 
        return $this->belongsTo(Amenity::class, 'amenity_id'); 
    }

    public function resident(): BelongsTo
    { 
        return $this->belongsTo(Resident::class, 'resident_id'); 
    }

    // --- Nuevas Relaciones Agregadas ---

    /**
     * Relación: La casa a la que se le asigna esta reservación.
     * Crucial para saber a qué unidad cobrarle o validar adeudos.
     */
    public function privateUnit(): BelongsTo
    {
        return $this->belongsTo(PrivateUnit::class, 'private_unit_id');
    }

    /**
     * Relación: El cargo/cuota generado por esta reservación en el módulo de finanzas.
     */
    public function fee(): BelongsTo
    {
        return $this->belongsTo(GeneratedFee::class, 'generated_fee_id');
    }
}