<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    // Proveedores del Fraccionamiento (Jardinería, Seguridad, Plomería, etc.)
    protected $fillable = [
        'name', 
        'principal_contact', 
        'phone', 
        'email', 
        'category', // Ej: 'maintenance', 'security', 'services'
        'subdivision_id' // FK corregida a singular
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación: El proveedor está registrado en un Fraccionamiento específico.
     */
    public function subdivision(): BelongsTo
    {
        return $this->belongsTo(Subdivision::class, 'subdivision_id');
    }
    
    /**
     * Relación: Un proveedor tiene muchas calificaciones de los residentes.
     */
    public function ratings(): HasMany 
    {
         return $this->hasMany(SupplierRating::class, 'supplier_id'); 
    }

    /**
     * Helper para obtener el promedio de estrellas.
     */
    public function getAverageRatingAttribute()
    {
        return $this->ratings()->avg('punctuation');
    }
}