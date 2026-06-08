<?php

namespace App\Models\AccessControl;

use App\Models\Community\PrivateUnit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

/**
 * Evento de Visitas.
 * 
 * Permite a un residente organizar un evento (fiesta, reunión)
 * y generar un QR maestro que pueden usar múltiples invitados.
 * Lleva conteo de usos y límite configurable.
 */
class VisitEvent extends Model
{
    protected $fillable = [
        'name',
        'qr_code',
        'date_time_start',
        'date_time_end',
        'guest_amount',
        'max_qr_uses',
        'current_use_count',
        'status',              // 'Activo', 'Inactivo', 'Cancelado'
        'description',
        'private_unit_id',
    ];

    protected $casts = [
        'date_time_start'    => 'datetime',
        'date_time_end'      => 'datetime',
        'guest_amount'       => 'integer',
        'max_qr_uses'        => 'integer',
        'current_use_count'  => 'integer',
    ];

    // ─── Constantes ──────────────────────────────────────────────────
    public const STATUS_ACTIVO    = 'Activo';
    public const STATUS_INACTIVO  = 'Inactivo';
    public const STATUS_CANCELADO = 'Cancelado';

    // ─── Relaciones ──────────────────────────────────────────────────
    public function privateUnit(): BelongsTo
    {
        return $this->belongsTo(PrivateUnit::class, 'private_unit_id');
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class, 'visit_event_id');
    }

    public function accessLogs(): HasMany
    {
        return $this->hasMany(AccessLog::class, 'visit_event_id');
    }

    // ─── Lógica de Negocio ──────────────────────────────────────────

    /**
     * Verifica si el evento está activo y en curso.
     */
    public function estaEnCurso(): bool
    {
        if ($this->status !== self::STATUS_ACTIVO) {
            return false;
        }
        $now = now();
        return $now->between($this->date_time_start, $this->date_time_end);
    }

    /**
     * Verifica si el QR del evento aún puede usarse (no ha alcanzado el límite).
     */
    public function qrDisponible(): bool
    {
        if (!$this->estaEnCurso()) {
            return false;
        }
        if ($this->max_qr_uses === null) {
            return true; // Sin límite
        }
        return $this->current_use_count < $this->max_qr_uses;
    }

    /**
     * Incrementa el contador de usos del QR.
     */
    public function incrementarUso(): void
    {
        $this->increment('current_use_count');
    }

    /**
     * Calcula el progreso del evento en porcentaje de tiempo.
     */
    public function progresoTiempo(): float
    {
        $now = now();
        $total = $this->date_time_start->diffInMinutes($this->date_time_end);
        if ($total <= 0) {
            return 100;
        }
        $transcurrido = $this->date_time_start->diffInMinutes($now);
        return min(100, max(0, round(($transcurrido / $total) * 100, 1)));
    }

    /**
     * Cancela el evento y todas sus visitas pendientes.
     */
    public function cancelar(): void
    {
        $this->status = self::STATUS_CANCELADO;
        $this->save();

        // Cancelar visitas pendientes asociadas
        $this->visits()
             ->where('status', Visit::STATUS_PENDIENTE)
             ->update(['status' => Visit::STATUS_CANCELADO]);
    }

    /**
     * Crea un evento con QR maestro.
     */
    public static function crearConQR(array $data): self
    {
        $data['qr_code'] = (string) Str::uuid();
        $data['status']  = self::STATUS_ACTIVO;
        $data['current_use_count'] = 0;

        return static::create($data);
    }

    /**
     * Resumen para el dashboard del guardia.
     */
    public function resumenDashboard(): array
    {
        return [
            'id'            => $this->id,
            'nombre'        => $this->name,
            'invitados'     => "{$this->current_use_count}/{$this->guest_amount}",
            'progreso'      => $this->progresoTiempo() . '%',
            'enCurso'       => $this->estaEnCurso(),
            'qrDisponible'  => $this->qrDisponible(),
        ];
    }

    // ─── Scopes ─────────────────────────────────────────────────────

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVO);
    }

    public function scopeEnCurso(Builder $query): Builder
    {
        $now = now();
        return $query->where('status', self::STATUS_ACTIVO)
                     ->where('date_time_start', '<=', $now)
                     ->where('date_time_end', '>=', $now);
    }

    public function scopeDeUnidad(Builder $query, int $privateUnitId): Builder
    {
        return $query->where('private_unit_id', $privateUnitId);
    }

    public function scopeHoy(Builder $query): Builder
    {
        return $query->whereDate('date_time_start', today());
    }
}
