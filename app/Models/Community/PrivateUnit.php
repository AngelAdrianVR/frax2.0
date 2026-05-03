<?php

namespace App\Models\Community;

use App\Models\Settings\Subdivision;
use App\Models\Gatehouse\Visit;
use App\Models\Gatehouse\VisitEvent;
use App\Models\Gatehouse\FrequentVisitor;
use App\Models\Gatehouse\AccessLog;
use App\Models\Gatehouse\ParcelService;
use App\Models\Finances\GeneratedFee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class PrivateUnit extends Model
{
    protected $fillable = [
        'lot_number',
        'square_meters', 
        'unit_street', 
        'int_number', 
        'status', // 'Activo', 'Inactivo'
        'access_block', 
        'subdivision_id',
        'is_slow_payer', // Bandera para indicar si la unidad es morosa (3 o más cuotas vencidas)
        'credit_balance' // Saldo a favor de la casa
    ];

    protected $casts = [
        'square_meters' => 'decimal:2',
        'access_block' => 'boolean',
        'is_slow_payer' => 'boolean',
    ];

    /**
     * El fraccionamiento al que pertenece la unidad.
     */
    public function subdivision(): BelongsTo
    {
        return $this->belongsTo(Subdivision::class, 'subdivision_id');
    }

    /**
     * Usuarios asociados a la unidad (Dueños, inquilinos, etc).
     * Relación Muchos a Muchos con tabla intermedia 'private_unit_user'.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'private_unit_user', 'private_unit_id', 'user_id')
                    ->withPivot([
                        'role_in_unit', 'responsible_for_payments', 
                        'start_date', 'end_date', 'is_primary', 'alias'
                    ])
                    ->withTimestamps();
    }

    // --- Activos / Pertenencias ---

    public function vehicles(): HasMany 
    { 
        return $this->hasMany(Vehicle::class, 'private_unit_id'); 
    }

    public function pets(): HasMany 
    {
        return $this->hasMany(Pet::class, 'private_unit_id'); 
    }

    // --- Seguridad y Accesos ---

    public function visits(): HasMany 
    { 
        return $this->hasMany(Visit::class, 'private_unit_id'); 
    }

    public function visitEvents(): HasMany
    {
        return $this->hasMany(VisitEvent::class, 'private_unit_id');
    }

    public function frequentVisitors(): HasMany
    {
        return $this->hasMany(FrequentVisitor::class, 'private_unit_id');
    }

    public function accessLogs(): HasMany
    {
        return $this->hasMany(AccessLog::class, 'private_unit_id');
    }

    // --- Operaciones ---

    // public function reports(): HasMany
    // {
    //     return $this->hasMany(Report::class, 'private_unit_id');
    // }

    public function parcelServices(): HasMany
    {
        return $this->hasMany(ParcelService::class, 'private_unit_id');
    }

    // --- Finanzas ---

    public function generatedFees(): HasMany
    {
        return $this->hasMany(GeneratedFee::class, 'private_unit_id');
    }

    public function scopeWithTotalDebt(Builder $query)
    {
        return $query->addSelect([
            'total_debt' => GeneratedFee::selectRaw('COALESCE(SUM(total_amount - amount_paid), 0)')
                ->whereColumn('generated_fees.private_unit_id', 'private_units.id')
                ->whereIn('status', ['Pendiente', 'Parcial', 'Atrasada'])
        ]);
    }

    public function getIsDebtorAttribute()
    {
        if (isset($this->attributes['total_debt'])) {
            return $this->attributes['total_debt'] > 0;
        }

        return $this->generatedFees()
            ->whereIn('status', ['Pendiente', 'Parcial', 'Atrasada'])
            ->whereRaw('(total_amount - amount_paid) > 0')
            ->exists();
    }

    // =========================================================================
    // LÓGICA DE NEGOCIO
    // =========================================================================

    /**
     * Agrega saldo a favor a esta propiedad.
     */
    public function addCreditBalance(float $amount, string $reference = null)
    {
        $this->credit_balance += $amount;
        $this->save();
    }
}