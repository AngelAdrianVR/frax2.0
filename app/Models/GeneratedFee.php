<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneratedFee extends Model
{
    protected $fillable = [
        'payment_reference', 
        'total_amount', 
        'amount_paid', 
        'expiration_date', 
        'start_period', 
        'end_period', 
        'status', // 'Pendiente', 'Parcial', 'Pagado', 'Atrasada', 'Cancelado'
        'private_unit_id', 
        'billing_concept_id'
    ];
    protected $casts = [
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'expiration_date' => 'date',
        'start_period' => 'date',
        'end_period' => 'date',
    ];
    
    public function privateUnit() 
    { 
        return $this->belongsTo(PrivateUnit::class, 'private_unit_id'); 
    }
    
    public function concept() 
    { 
        return $this->belongsTo(BillingConcept::class, 'billing_concept_id'); 
    }
}
