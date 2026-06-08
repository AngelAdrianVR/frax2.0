<?php

namespace App\Models\AccessControl;

use App\Models\Community\PrivateUnit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

/**
 * Visita / Invitación de Acceso.
 * 
 * Representa una autorización de acceso para un visitante.
 * El residente genera un QR único que el visitante presenta en caseta.
 * El guardia escanea el QR y registra la entrada/salida.
 */
class Visit extends Model
{
    protected $fillable = [
        'name',
        'reason',
        'qr_code',
        'expiration_date',
        'date_of_use',
        'access_type',        // 'Peatonal' | 'Vehicular'
        'status',             // 'Pendiente','Ingresado','Expirado','Cancelado'
        'private_unit_id',
        'visit_event_id',
    ];

    protected $casts = [
        'expiration_date' => 'datetime',
        'date_of_use'     => 'datetime',
    ];

    // ─── Constantes ──────────────────────────────────────────────────
    public const ACCESS_PEATONAL  = 'Peatonal';
    public const ACCESS_VEHICULAR = 'Vehicular';

    public const STATUS_PENDIENTE  = 'Pendiente';
    public const STATUS_INGRESADO  = 'Ingresado';
    public const STATUS_EXPIRADO   = 'Expirado';
    public const STATUS_CANCELADO  = 'Cancelado';

    // ─── Relaciones ──────────────────────────────────────────────────
    public function privateUnit(): BelongsTo
    {
        return $this->belongsTo(PrivateUnit::class, 'private_unit_id');
    }

    public function visitEvent(): BelongsTo
    {
        return $this->belongsTo(VisitEvent::class, 'visit_event_id');
    }

    public function accessLogs(): HasMany
    {
        return $this->hasMany(AccessLog::class, 'visit_id');
    }

    // ─── Lógica de Negocio ──────────────────────────────────────────

    /**
     * Verifica si la visita aún está vigente (no ha expirado ni sido cancelada).
     */
    public function estaVigente(): bool
    {
        if (in_array($this->status, [self::STATUS_CANCELADO, self::STATUS_EXPIRADO])) {
            return false;
        }
        if ($this->expiration_date && $this->expiration_date->isPast()) {
            $this->update(['status' => self::STATUS_EXPIRADO]);
            return false;
        }
        return true;
    }

    /**
     * Registra el ingreso del visitante al fraccionamiento.
     */
    public function registrarIngreso(?string $identifier = null): AccessLog
    {
        $this->status = self::STATUS_INGRESADO;
        $this->date_of_use = now();
        $this->save();

        return AccessLog::registrarEntrada([
            'identifier'          => $identifier ?? $this->name,
            'verification_method' => AccessLog::METHOD_QR,
            'private_unit_id'     => $this->private_unit_id,
            'visit_id'            => $this->id,
            'visit_event_id'      => $this->visit_event_id,
        ]);
    }

    /**
     * Registra la salida del visitante.
     */
    public function registrarSalida(?string $identifier = null): AccessLog
    {
        return AccessLog::registrarSalida([
            'identifier'          => $identifier ?? $this->name,
            'verification_method' => AccessLog::METHOD_QR,
            'private_unit_id'     => $this->private_unit_id,
            'visit_id'            => $this->id,
        ]);
    }

    /**
     * Cancela la visita.
     */
    public function cancelar(): void
    {
        $this->status = self::STATUS_CANCELADO;
        $this->save();
    }

    /**
     * Crea una nueva visita generando QR único automáticamente.
     */
    public static function crearConQR(array $data): self
    {
        $data['qr_code'] = (string) Str::uuid();
        $data['status']  = self::STATUS_PENDIENTE;

        return static::create($data);
    }

    /**
     * Indica si la visita es vehicular.
     */
    public function esVehicular(): bool
    {
        return $this->access_type === self::ACCESS_VEHICULAR;
    }

    /**
     * Color del badge de estado para la UI.
     */
    public function statusColor(): string
    {
        return match ($this->status) {
            self::STATUS_INGRESADO => 'green',
            self::STATUS_PENDIENTE => 'amber',
            self::STATUS_CANCELADO => 'red',
            self::STATUS_EXPIRADO  => 'zinc',
            default                => 'blue',
        };
    }

    // ─── Scopes ─────────────────────────────────────────────────────

    public function scopePendientes(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDIENTE);
    }

    public function scopeIngresados(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_INGRESADO);
    }

    public function scopeDeUnidad(Builder $query, int $privateUnitId): Builder
    {
        return $query->where('private_unit_id', $privateUnitId);
    }

    public function scopeDelEvento(Builder $query, int $visitEventId): Builder
    {
        return $query->where('visit_event_id', $visitEventId);
    }

    /**
     * Visitas pendientes que ya expiraron (para tarea programada).
     */
    public function scopeExpiradasSinActualizar(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDIENTE)
                     ->whereNotNull('expiration_date')
                     ->where('expiration_date', '<', now());
    }
}
