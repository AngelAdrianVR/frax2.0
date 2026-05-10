<?php

namespace App\Models\Finances;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Community\User;
use App\Models\Finances\BillingConcept;
use Illuminate\Support\Str;

class Payment extends Model
{
    protected $fillable = [
        'transaction_folio', 
        'amount', 
        'payment_date', 
        'payment_method', 
        'billing_concept_id', 
        'user_id' 
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ==========================================
    // RELACIONES
    // ==========================================
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function billingConcept(): BelongsTo
    {
        return $this->belongsTo(BillingConcept::class, 'billing_concept_id');
    }

    public function reconciliation(): HasOne
    { 
        return $this->hasOne(BankReconciliation::class, 'payment_id'); 
    }

    // ==========================================
    // SCOPES (Filtros Reutilizables)
    // ==========================================

    /**
     * Filtra los pagos para que solo se muestren los del fraccionamiento actual.
     */
    public function scopeForSubdivision(Builder $query, $subdivisionId): Builder
    {
        return $query->whereHas('billingConcept', function (Builder $q) use ($subdivisionId) {
            $q->where('subdivision_id', $subdivisionId);
        });
    }

    /**
     * Búsqueda inteligente por folio o nombre del residente.
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (!$search) return $query;

        return $query->where(function ($q) use ($search) {
            $q->where('transaction_folio', 'like', "%{$search}%")
              ->orWhereHas('user', function ($qUser) use ($search) { 
                  $qUser->where('name', 'like', "%{$search}%"); 
              });
        });
    }

    // ==========================================
    // LÓGICA DE NEGOCIO (Modelo Robusto)
    // ==========================================

    /**
     * Procesa y registra un nuevo pago, generando el folio automáticamente si falta.
     */
    public static function registerPayment(array $data, $subdivisionId): self
    {
        // Generar folio automático si el usuario no lo ingresó
        if (empty($data['transaction_folio'])) {
            $data['transaction_folio'] = 'PAG-' . strtoupper(Str::random(8));
        }

        // Validar que el concepto exista, si no, usar/crear uno genérico
        if (empty($data['billing_concept_id'])) {
            $concept = BillingConcept::firstOrCreate(
                ['subdivision_id' => $subdivisionId, 'name' => 'Abono / Pago General'],
                ['base_amount' => 0, 'description' => 'Concepto general para abonos manuales.']
            );
            $data['billing_concept_id'] = $concept->id;
        }

        return self::create($data);
    }

    /**
     * Actualiza la información de un pago existente.
     */
    public function updatePayment(array $data): bool
    {
        return $this->update($data);
    }
}