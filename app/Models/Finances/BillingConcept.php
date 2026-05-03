<?php

namespace App\Models\Finances;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BillingConcept extends Model
{
    protected $fillable = [
        'name', 
        'base_amount', 
        'recurrence_type', // 'Semanal', 'Quincenal', 'Mensual', 'Bimestral', 'Anual', 'Pago unico'
        'slow_payers_apply', // Si aplica recargos a morosos
        'subdivision_id'
    ];

    protected $casts = [
        'base_amount' => 'decimal:2',
        'slow_payers_apply' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación: Un concepto de facturación pertenece a un Fraccionamiento (Subdivision).
     */
    public function subdivision(): BelongsTo
    {
        return $this->belongsTo(Subdivision::class, 'subdivision_id');
    }

    /**
     * Relación: Un concepto genera muchas cuotas/recibos específicos (generate_fees).
     * Ejemplo: El concepto "Mantenimiento Enero" genera 100 recibos (uno por casa).
     */
    public function generatedFees(): HasMany
    {
        return $this->hasMany(GeneratedFee::class);
    }

    /**
     * Relación: Un concepto tiene muchos pagos asociados.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * (Opcional) Relación con las deudas generadas.
     * Asumiendo que tienes un modelo 'Debt' o 'Bill'.
     */
    /*
    public function debts(): HasMany
    {
        return $this->hasMany(Debt::class);
    }
    */
}