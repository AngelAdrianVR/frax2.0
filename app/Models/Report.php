<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Report extends Model
{
    // Reportes de incidencias (Lámpara rota, Vecino ruidoso, Bache, etc.)
    protected $fillable = [
        'title', 
        'description', 
        'priority', // Ej: 'low', 'medium', 'high', 'urgent'
        'status', // Ej: 'open', 'in_progress', 'resolved', 'closed'
        'exact_location', // Coordenadas o descripción de dónde es el problema
        'private_unit_id', // (Opcional) Si el reporte es sobre una casa específica
        'resident_id' // Quién reportó
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación: El reporte fue creado por un Residente (Usuario).
     */
    public function resident(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resident_id');
    }

    /**
     * Relación: El reporte está asociado a una Unidad Privada (opcional).
     * Útil si reportan "Perro ladrando en la casa 42".
     */
    public function privateUnit(): BelongsTo
    {
        return $this->belongsTo(PrivateUnit::class, 'private_unit_id');
    }
    
    // Polimorfismo para el hilo de conversación del reporte (Admin <-> Residente)
    public function comments(): MorphMany 
    { 
        return $this->morphMany(Comment::class, 'commentable'); 
    }

    // Sería útil agregar MorphMany para evidencias (fotos del problema)
    /*
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'model');
    }
    */
}