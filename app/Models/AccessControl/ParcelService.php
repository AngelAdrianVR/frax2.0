<?php

namespace App\Models\AccessControl;

use App\Models\Community\PrivateUnit;
use App\Models\Community\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * Paquetería / Mensajería recibida en Caseta.
 * 
 * Gestiona los paquetes que llegan al fraccionamiento y quedan
 * bajo resguardo del guardia hasta que el residente los recoja.
 */
class ParcelService extends Model
{
    protected $fillable = [
        'name',                // Ej: "Amazon", "MercadoLibre"
        'tracking_number',
        'receipt_date',
        'delivery_date',
        'imagen_etiqueta_url',
        'status',              // 'Recibido', 'Entregado', 'Regresado'
        'private_unit_id',
        'user_id',             // Guardia que recibió
    ];

    protected $casts = [
        'receipt_date'  => 'datetime',
        'delivery_date' => 'datetime',
    ];

    // ─── Constantes ──────────────────────────────────────────────────
    public const STATUS_RECIBIDO  = 'Recibido';
    public const STATUS_ENTREGADO = 'Entregado';
    public const STATUS_REGRESADO = 'Regresado';

    // ─── Relaciones ──────────────────────────────────────────────────
    public function privateUnit(): BelongsTo
    {
        return $this->belongsTo(PrivateUnit::class, 'private_unit_id');
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ─── Lógica de Negocio ──────────────────────────────────────────

    /**
     * Marcar el paquete como entregado al residente.
     */
    public function marcarEntregado(): void
    {
        $this->status = self::STATUS_ENTREGADO;
        $this->delivery_date = now();
        $this->save();
    }

    /**
     * Marcar el paquete como regresado / devuelto.
     */
    public function marcarRegresado(): void
    {
        $this->status = self::STATUS_REGRESADO;
        $this->save();
    }

    /**
     * Indica si el paquete sigue en caseta.
     */
    public function estaEnCaseta(): bool
    {
        return $this->status === self::STATUS_RECIBIDO;
    }

    /**
     * Calcula los días que lleva en caseta sin recoger.
     */
    public function diasEnCaseta(): int
    {
        return $this->receipt_date
            ? $this->receipt_date->diffInDays(now())
            : 0;
    }

    /**
     * Datos resumidos para mostrar al guardia en el dashboard.
     */
    public function resumenDashboard(): array
    {
        return [
            'id'              => $this->id,
            'mensajeria'      => $this->name,
            'guia'            => $this->tracking_number,
            'unidad'          => $this->privateUnit?->lot_number ?? 'N/A',
            'diasEnCaseta'    => $this->diasEnCaseta(),
            'status'          => $this->status,
        ];
    }

    // ─── Scopes ─────────────────────────────────────────────────────

    public function scopeEnCaseta(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_RECIBIDO);
    }

    public function scopeDeUnidad(Builder $query, int $privateUnitId): Builder
    {
        return $query->where('private_unit_id', $privateUnitId);
    }

    /**
     * Paquetes que llevan más de N días sin recoger (para alertar).
     */
    public function scopeAntiguos(Builder $query, int $dias = 7): Builder
    {
        return $query->where('status', self::STATUS_RECIBIDO)
                     ->where('receipt_date', '<=', now()->subDays($dias));
    }
}
