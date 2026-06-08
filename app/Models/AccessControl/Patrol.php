<?php

namespace App\Models\AccessControl;

use App\Models\Community\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * Rondín de Seguridad (Patrullaje).
 * 
 * Representa un recorrido de vigilancia realizado por un guardia.
 * El guardia escanea puntos de control (checkpoints) durante su ruta,
 * generando registros en PatrolScan.
 */
class Patrol extends Model
{
    protected $fillable = [
        'start_time',
        'end_time',
        'status',           // 'Activo', 'Terminado', 'Incidente'
        'scanned_points',
        'user_id',          // Guardia que realiza el rondín
    ];

    protected $casts = [
        'start_time'     => 'datetime',
        'end_time'       => 'datetime',
        'scanned_points' => 'integer',
    ];

    // ─── Constantes ──────────────────────────────────────────────────
    public const STATUS_ACTIVO    = 'Activo';
    public const STATUS_TERMINADO = 'Terminado';
    public const STATUS_INCIDENTE = 'Incidente';

    // ─── Relaciones ──────────────────────────────────────────────────
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Escaneos/puntos registrados durante este rondín.
     */
    public function scans(): HasMany
    {
        return $this->hasMany(PatrolScan::class, 'patrol_id');
    }

    /**
     * Incidencias reportadas durante este rondín.
     */
    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class, 'patrol_id');
    }

    // ─── Lógica de Negocio ──────────────────────────────────────────

    /**
     * Indica si el rondín está actualmente en curso.
     */
    public function estaActivo(): bool
    {
        return $this->status === self::STATUS_ACTIVO;
    }

    /**
     * Finaliza el rondín.
     */
    public function finalizar(?string $status = null): void
    {
        $this->status = $status ?? self::STATUS_TERMINADO;
        $this->end_time = now();
        $this->save();
    }

    /**
     * Marca el rondín con incidente.
     */
    public function marcarIncidente(): void
    {
        $this->status = self::STATUS_INCIDENTE;
        $this->save();
    }

    /**
     * Registra un escaneo de punto de control en este rondín.
     */
    public function registrarEscaneo(Checkpoint $checkpoint): PatrolScan
    {
        $scan = $this->scans()->create([
            'scan_date_time' => now(),
            'checkpoint_id'  => $checkpoint->id,
        ]);

        $this->increment('scanned_points');

        return $scan;
    }

    /**
     * Duración del rondín en minutos.
     */
    public function duracionMinutos(): ?int
    {
        if (!$this->end_time) {
            return null;
        }
        return (int) $this->start_time->diffInMinutes($this->end_time);
    }

    /**
     * Progreso del rondín (requiere conocer total de checkpoints).
     */
    public function progreso(int $totalCheckpoints): float
    {
        if ($totalCheckpoints <= 0) {
            return 0;
        }
        return min(100, round(($this->scanned_points / $totalCheckpoints) * 100, 1));
    }

    // ─── Scopes ─────────────────────────────────────────────────────

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVO);
    }

    public function scopeDelGuardia(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeDelTurno(Builder $query): Builder
    {
        return $query->where('start_time', '>=', now()->subHours(12));
    }
}
