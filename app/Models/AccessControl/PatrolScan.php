<?php

namespace App\Models\AccessControl;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * Registro de Escaneo en Rondín.
 * 
 * Cada vez que un guardia escanea un punto de control (Checkpoint)
 * durante un Patrol, se crea este registro.
 * 
 * Reemplaza al antiguo CheckLogPatrol.
 */
class PatrolScan extends Model
{
    protected $table = 'check_log_patrols';

    protected $fillable = [
        'scan_date_time',
        'patrol_id',
        'checkpoint_id',
    ];

    protected $casts = [
        'scan_date_time' => 'datetime',
    ];

    // ─── Relaciones ──────────────────────────────────────────────────
    public function patrol(): BelongsTo
    {
        return $this->belongsTo(Patrol::class, 'patrol_id');
    }

    public function checkpoint(): BelongsTo
    {
        return $this->belongsTo(Checkpoint::class, 'checkpoint_id');
    }

    // ─── Lógica de Negocio ──────────────────────────────────────────

    /**
     * Nombre del punto de control escaneado.
     */
    public function nombreCheckpoint(): string
    {
        return $this->checkpoint?->name ?? 'Punto desconocido';
    }

    /**
     * Tiempo transcurrido desde el escaneo.
     */
    public function haceCuanto(): string
    {
        return $this->scan_date_time->diffForHumans();
    }

    // ─── Scopes ─────────────────────────────────────────────────────

    public function scopeDelRondin(Builder $query, int $patrolId): Builder
    {
        return $query->where('patrol_id', $patrolId);
    }

    public function scopeRecientes(Builder $query): Builder
    {
        return $query->orderBy('scan_date_time', 'desc');
    }
}
