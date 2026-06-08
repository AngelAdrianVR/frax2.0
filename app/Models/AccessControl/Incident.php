<?php

namespace App\Models\AccessControl;

use App\Models\Community\PrivateUnit;
use App\Models\Community\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * Incidencia de Seguridad / Caseta.
 * 
 * Permite a los guardias reportar cualquier novedad durante su turno:
 * - Persona sospechosa
 * - Vehículo no autorizado
 * - Daños a propiedad común
 * - Ruido excesivo
 * - Emergencias
 * 
 * Se integra con el media library de Spatie para fotos de evidencia.
 */
class Incident extends Model
{
    protected $fillable = [
        'title',
        'description',
        'severity',            // 'Baja', 'Media', 'Alta', 'Critica'
        'status',              // 'Abierto', 'EnProceso', 'Resuelto', 'Cerrado'
        'incident_type',       // 'Seguridad', 'Trafico', 'Danios', 'Ruido', 'Emergencia', 'Otro'
        'foto_url',
        'location_description',
        'reported_by_user_id', // Guardia que reporta
        'private_unit_id',     // Unidad involucrada (nullable)
        'patrol_id',           // Rondín en el que se detectó (nullable)
    ];

    protected $casts = [
        'reported_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    // ─── Constantes de Dominio ───────────────────────────────────────
    public const SEVERITY_BAJA    = 'Baja';
    public const SEVERITY_MEDIA   = 'Media';
    public const SEVERITY_ALTA    = 'Alta';
    public const SEVERITY_CRITICA = 'Critica';

    public const STATUS_ABIERTO    = 'Abierto';
    public const STATUS_EN_PROCESO = 'EnProceso';
    public const STATUS_RESUELTO   = 'Resuelto';
    public const STATUS_CERRADO    = 'Cerrado';

    public const TYPE_SEGURIDAD  = 'Seguridad';
    public const TYPE_TRAFICO    = 'Trafico';
    public const TYPE_DANIOS     = 'Danios';
    public const TYPE_RUIDO      = 'Ruido';
    public const TYPE_EMERGENCIA = 'Emergencia';
    public const TYPE_OTRO       = 'Otro';

    // ─── Relaciones ──────────────────────────────────────────────────
    public function reportedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by_user_id');
    }

    public function privateUnit(): BelongsTo
    {
        return $this->belongsTo(PrivateUnit::class, 'private_unit_id');
    }

    public function patrol(): BelongsTo
    {
        return $this->belongsTo(Patrol::class, 'patrol_id');
    }

    // ─── Lógica de Negocio ──────────────────────────────────────────

    /**
     * Indica si la incidencia es de alta prioridad.
     */
    public function esPrioritaria(): bool
    {
        return in_array($this->severity, [self::SEVERITY_ALTA, self::SEVERITY_CRITICA]);
    }

    /**
     * Marca la incidencia como resuelta.
     */
    public function resolver(): void
    {
        $this->status = self::STATUS_RESUELTO;
        $this->resolved_at = now();
        $this->save();
    }

    /**
     * Cambia el estado a "En Proceso".
     */
    public function iniciarAtencion(): void
    {
        $this->status = self::STATUS_EN_PROCESO;
        $this->save();
    }

    /**
     * Etiqueta de severidad con color para UI.
     */
    public function severidadColor(): string
    {
        return match ($this->severity) {
            self::SEVERITY_BAJA    => 'blue',
            self::SEVERITY_MEDIA   => 'amber',
            self::SEVERITY_ALTA    => 'orange',
            self::SEVERITY_CRITICA => 'red',
            default                => 'zinc',
        };
    }

    // ─── Scopes ─────────────────────────────────────────────────────

    public function scopeAbiertos(Builder $query): Builder
    {
        return $query->whereIn('status', [self::STATUS_ABIERTO, self::STATUS_EN_PROCESO]);
    }

    public function scopePrioritarias(Builder $query): Builder
    {
        return $query->whereIn('severity', [self::SEVERITY_ALTA, self::SEVERITY_CRITICA]);
    }

    public function scopeDelTurno(Builder $query): Builder
    {
        return $query->where('created_at', '>=', now()->subHours(12));
    }
}
