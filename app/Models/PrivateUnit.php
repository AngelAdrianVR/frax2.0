<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

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
     * Residentes asociados a la unidad (Dueños, inquilinos, etc).
     * Relación Muchos a Muchos con tabla intermedia personalizada 'residence_units'.
     */
    public function residents(): BelongsToMany
    {
        return $this->belongsToMany(Resident::class, 'residence_units', 'private_unit_id', 'resident_id')
                    ->using(ResidenceUnit::class)
                    ->withPivot([
                        'role_in_unit', 'responsible_for_payments', 
                        'start_date', 'end_date', 'primary', 
                        'is_primary_owner', 'permissions_level', 'alias'
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

    /**
     * Visitas registradas a esta unidad.
     * Tabla: visits
     */
    public function visits(): HasMany 
    { 
        return $this->hasMany(Visit::class, 'private_unit_id'); 
    }

    /**
     * Eventos de visita masivos (ej. fiestas) vinculados a la unidad.
     * Tabla: visit_events
     */
    public function visitEvents(): HasMany
    {
        return $this->hasMany(VisitEvent::class, 'private_unit_id');
    }

    /**
     * Visitantes frecuentes pre-autorizados para esta unidad.
     * Tabla: frequent_visitors
     */
    public function frequentVisitors(): HasMany
    {
        return $this->hasMany(FrequentVisitor::class, 'private_unit_id');
    }

    /**
     * Historial de accesos vinculados a esta unidad.
     * Tabla: access_log
     */
    public function accessLogs(): HasMany
    {
        return $this->hasMany(AccessLog::class, 'private_unit_id');
    }

    // --- Operaciones ---

    /**
     * Reportes (tickets de mantenimiento o quejas) generados por esta unidad.
     * Tabla: reports
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'private_unit_id');
    }

    /**
     * Paquetería destinada a esta unidad.
     * Tabla: parcel_service
     */
    public function parcelServices(): HasMany
    {
        return $this->hasMany(ParcelService::class, 'private_unit_id');
    }

    // --- Finanzas ---

    /**
     * Cuotas generadas (recibos de pago pendientes o pagados).
     * Tabla: generated_fees
     */
    public function generatedFees(): HasMany
    {
        return $this->hasMany(GeneratedFee::class, 'private_unit_id');
    }

    /**
     * SCOPE: Añade una columna 'total_debt' a la consulta.
     * Calcula: Suma de (monto total - monto pagado) de recibos vencidos o pendientes.
     * Usa subquery para máximo rendimiento.
     */
    public function scopeWithTotalDebt(Builder $query)
    {
        return $query->addSelect([
            'total_debt' => GeneratedFee::selectRaw('COALESCE(SUM(total_amount - amount_paid), 0)')
                ->whereColumn('generated_fees.private_unit_id', 'private_units.id')
                ->whereIn('status', ['Pendiente', 'Parcial', 'Atrasada'])
                // Opcional: Si solo quieres contar deuda vencida, descomenta abajo:
                // ->where('expiration_date', '<', now())
        ]);
    }

    /**
     * Helper para saber si la unidad es morosa (útil para bloquear accesos).
     * Puedes llamar a esto $unit->is_debtor
     */
    public function getIsDebtorAttribute()
    {
        // Si ya cargamos el scope, usamos el valor, si no, calculamos.
        if (isset($this->attributes['total_debt'])) {
            return $this->attributes['total_debt'] > 0;
        }

        return $this->generatedFees()
            ->whereIn('status', ['Pendiente', 'Parcial', 'Atrasada'])
            ->whereRaw('(total_amount - amount_paid) > 0')
            ->exists();
    }
}