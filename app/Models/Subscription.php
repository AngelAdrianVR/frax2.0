<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $fillable = [
        'service_plan', 
        'cost', 
        'start_date', 
        'current_expiration_date', 
        'status', // Ej: 'active', 'expired', 'trial'
        'payment_details', // JSON con info de pasarela (Stripe/PayPal)
        'subdivision_id' // FK corregida a singular
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'start_date' => 'date',
        'current_expiration_date' => 'date',
        'payment_details' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación: Esta suscripción pertenece a un Fraccionamiento específico.
     */
    public function subdivision(): BelongsTo
    {
        return $this->belongsTo(Subdivision::class, 'subdivision_id');
    }

    /**
     * Scope opcional para verificar si la suscripción está activa.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                     ->where('current_expiration_date', '>=', now());
    }
}