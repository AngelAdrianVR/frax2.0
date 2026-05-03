<?php

namespace App\Models\Finances;

use Illuminate\Database\Eloquent\Model;
use App\Models\Community\PrivateUnit;

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
    
    // --- Relaciones ---
    
    public function privateUnit() 
    { 
        return $this->belongsTo(PrivateUnit::class, 'private_unit_id'); 
    }
    
    public function billingConcept() 
    { 
        return $this->belongsTo(BillingConcept::class, 'billing_concept_id'); 
    }

    // =========================================================================
    // LÓGICA DE NEGOCIO (Fat Model)
    // =========================================================================

    /**
     * Crea un cargo manual o multa y lo asocia a la propiedad.
     */
    public static function createManualCharge(PrivateUnit $unit, array $data)
    {
        // Formateamos la referencia para que incluya las notas si existen
        $reference = $data['concept'];
        if (!empty($data['notes'])) {
            $reference .= ' (' . $data['notes'] . ')';
        }

        return self::create([
            'private_unit_id' => $unit->id,
            'payment_reference' => $reference,
            'total_amount' => $data['amount'],
            'amount_paid' => 0,
            'expiration_date' => now(), // El cargo manual vence el mismo día (o puedes sumarle 30 días)
            'start_period' => now(),
            'end_period' => now(),
            'status' => 'Pendiente',
            // 'billing_concept_id' => null // Asumimos que los cargos manuales no requieren un concepto fijo
        ]);
    }
}