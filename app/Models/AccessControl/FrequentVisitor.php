<?php

namespace App\Models\AccessControl;

use App\Models\Community\PrivateUnit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * Visitante Frecuente.
 * 
 * Catálogo de visitantes recurrentes de una unidad privada.
 * Ejemplos: jardinero, repartidor, familiar que visita seguido, etc.
 * Permite pre-registrar datos para acelerar el acceso en caseta.
 */
class FrequentVisitor extends Model
{
    protected $fillable = [
        'alias',
        'name',
        'identification',
        'default_reason',
        'default_access_type',   // 'Peatonal' | 'Vehicular'
        'default_plate',
        'private_unit_id',
    ];

    // ─── Constantes ──────────────────────────────────────────────────
    public const ACCESS_PEATONAL  = 'Peatonal';
    public const ACCESS_VEHICULAR = 'Vehicular';

    // ─── Relaciones ──────────────────────────────────────────────────
    public function privateUnit(): BelongsTo
    {
        return $this->belongsTo(PrivateUnit::class, 'private_unit_id');
    }

    // ─── Lógica de Negocio ──────────────────────────────────────────

    /**
     * Indica si el visitante frecuente tiene vehículo registrado.
     */
    public function tieneVehiculo(): bool
    {
        return $this->default_access_type === self::ACCESS_VEHICULAR
            && !empty($this->default_plate);
    }

    /**
     * Nombre para mostrar (alias si existe, si no, nombre).
     */
    public function nombreMostrar(): string
    {
        return $this->alias ?: $this->name;
    }

    /**
     * Genera un array resumen útil para mostrarlo en caseta.
     */
    public function resumenParaGuardia(): array
    {
        return [
            'id'         => $this->id,
            'nombre'     => $this->nombreMostrar(),
            'razon'      => $this->default_reason,
            'tipoAcceso' => $this->default_access_type,
            'placa'      => $this->default_plate,
            'unidad'     => $this->privateUnit?->lot_number ?? 'N/A',
        ];
    }

    // ─── Scopes ─────────────────────────────────────────────────────

    /**
     * Filtrar visitantes frecuentes de una unidad específica.
     */
    public function scopeDeUnidad(Builder $query, int $privateUnitId): Builder
    {
        return $query->where('private_unit_id', $privateUnitId);
    }

    /**
     * Buscar por nombre o alias.
     */
    public function scopeBuscar(Builder $query, string $term): Builder
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('alias', 'like', "%{$term}%");
        });
    }
}
