<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryAsset extends Model
{
    // Activos fijos del fraccionamiento (Podadoras, Carrito de golf, Herramientas, etc.)
    protected $fillable = [
        'name', 
        'inventory_code', // Código de barras o ID interno
        'description', 
        'purchase_date', 
        'useful_life_in_hours', // Para mantenimiento preventivo
        'current_usage_hours', 
        'subdivision_id'
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'useful_life_in_hours' => 'integer',
        'current_usage_hours' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación: El activo pertenece al inventario de un Fraccionamiento.
     */
    public function subdivision(): BelongsTo
    {
        return $this->belongsTo(Subdivision::class, 'subdivision_id');
    }

    /**
     * (Opcional) Relación con registros de mantenimiento.
     * Si el activo (ej. Podadora) necesita servicio cada 100 horas.
     */
    /*
    public function maintenanceLogs(): HasMany
    {
        return $this->hasMany(MaintenanceLog::class);
    }
    */
}