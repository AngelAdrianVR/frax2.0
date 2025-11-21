<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    protected $fillable = [
        'transaction_folio', 
        'amount', 
        'payment_date', 
        'payment_method', // Ej: 'transfer', 'cash', 'credit_card'
        'billing_concept_id', // FK: A qué concepto se abona
        'resident_id' // FK: Quién paga
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    /**
     * Relación: El pago pertenece a un Usuario (Residente).
     */
    public function resident(): BelongsTo
    {
        // Asumiendo que el modelo de usuarios es 'User'
        return $this->belongsTo(User::class, 'resident_id');
    }

    /**
     * Relación: El pago corresponde a un Concepto de Cobro (Ej. Mantenimiento).
     */
    public function billingConcept(): BelongsTo
    {
        return $this->belongsTo(BillingConcept::class, 'billing_concept_id');
    }

    /**
     * Relación existente: Conciliación bancaria.
     */
    public function reconciliation(): HasOne
    { 
        return $this->hasOne(BankReconciliation::class, 'payment_id'); 
    }
}