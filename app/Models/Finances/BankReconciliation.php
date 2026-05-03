<?php

namespace App\Models\Finances;

use Illuminate\Database\Eloquent\Model;

class BankReconciliation extends Model
{
    protected $fillable = ['bank_reference', 
        'amount', 
        'transaction_date', 
        'status', // 'Pendiente', 'Conciliado', 'Error', 'Manual'
        'error_message', 
        'payment_id'
    ];
    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'datetime',
    ];
    public function payment() 
    { 
        return $this->belongsTo(Payment::class, 'payment_id'); 
    }
}
