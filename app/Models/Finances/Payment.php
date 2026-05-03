<?php

namespace App\Models\Finances;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    protected $fillable = [
        'transaction_folio', 
        'amount', 
        'payment_date', 
        'payment_method', // 'Transferencia', 'Efectivo', 'Tarjeta', 'Cheque'
        'billing_concept_id', // FK: A qué concepto se abona
        'user_id' // FK: Quién paga
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
    public function user()
    {
        return $this->belongsTo(User::class);
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