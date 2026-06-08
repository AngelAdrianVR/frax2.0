<?php

namespace App\Models\AccessControl;

use App\Models\Community\PrivateUnit;
use App\Models\Community\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * Bitácora Universal de Accesos.
 * 
 * Registra toda entrada/salida del fraccionamiento, sin importar si es:
 * - Un residente (user_id)
 * - Una visita autorizada (visit_id)
 * - Un evento (visit_event_id)
 * - Un vehículo detectado por placa (identifier)
 * - Un walk-in registrado manualmente por el guardia
 */
class AccessLog extends Model
{
    protected $table = 'access_logs';

    // ─── Constantes de Dominio ───────────────────────────────────────
    public const MOVEMENT_ENTRADA = 'Entrada';
    public const MOVEMENT_SALIDA  = 'Salida';

    public const METHOD_QR         = 'QR';
    public const METHOD_RFID       = 'RFID';
    public const METHOD_MANUAL     = 'Manual';
    public const METHOD_BIOMETRICO = 'Biometrico';

    public const CATEGORY_VISITA      = 'Visita';
    public const CATEGORY_PROVEEDOR   = 'Proveedor';
    public const CATEGORY_CONTRATISTA = 'Contratista';
    public const CATEGORY_CONDUCTOR   = 'Conductor';
    public const CATEGORY_DELIVERY    = 'Delivery';
    public const CATEGORY_OTRO        = 'Otro';

    public const CATEGORIES = [
        self::CATEGORY_VISITA,
        self::CATEGORY_PROVEEDOR,
        self::CATEGORY_CONTRATISTA,
        self::CATEGORY_CONDUCTOR,
        self::CATEGORY_DELIVERY,
        self::CATEGORY_OTRO,
    ];

    // ─── Fillable & Casts ────────────────────────────────────────────
    protected $fillable = [
        'identifier',
        'visitor_name',
        'visitor_company',
        'visitor_identification',
        'vehicle_plate',
        'vehicle_brand',
        'vehicle_color',
        'movement_type',
        'date_time',
        'exit_time',
        'verification_method',
        'access_category',
        'notes',
        'foto_url',
        'private_unit_id',
        'user_id',
        'visit_event_id',
        'visit_id',
    ];

    protected $casts = [
        'date_time' => 'datetime',
        'exit_time' => 'datetime',
    ];

    // ─── Relaciones ──────────────────────────────────────────────────
    public function privateUnit(): BelongsTo
    {
        return $this->belongsTo(PrivateUnit::class, 'private_unit_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function visitEvent(): BelongsTo
    {
        return $this->belongsTo(VisitEvent::class, 'visit_event_id');
    }

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class, 'visit_id');
    }

    // ─── Scopes ─────────────────────────────────────────────────────
    
    public function scopeEntradas(Builder $query): Builder
    {
        return $query->where('movement_type', self::MOVEMENT_ENTRADA);
    }

    public function scopeSalidas(Builder $query): Builder
    {
        return $query->where('movement_type', self::MOVEMENT_SALIDA);
    }

    public function scopeTurnoActual(Builder $query): Builder
    {
        return $query->where('date_time', '>=', now()->subHours(12));
    }

    public function scopeHoy(Builder $query): Builder
    {
        return $query->whereDate('date_time', today());
    }

    public function scopePendientesSalida(Builder $query): Builder
    {
        return $query->where('movement_type', self::MOVEMENT_ENTRADA)
                     ->whereNull('exit_time');
    }

    public function scopePorCategoria(Builder $query, string $category): Builder
    {
        return $query->where('access_category', $category);
    }

    // ─── Métodos de Dominio ─────────────────────────────────────────

    /**
     * Registrar una entrada al fraccionamiento.
     */
    public static function registrarEntrada(array $data): self
    {
        $data['movement_type'] = self::MOVEMENT_ENTRADA;
        $data['date_time'] = $data['date_time'] ?? now();

        return static::create($data);
    }

    /**
     * Registrar una salida del fraccionamiento.
     */
    public static function registrarSalida(array $data): self
    {
        $data['movement_type'] = self::MOVEMENT_SALIDA;
        $data['date_time'] = $data['date_time'] ?? now();

        return static::create($data);
    }

    /**
     * Registrar la salida (checkout) de un registro de entrada existente.
     * Actualiza exit_time en el registro de entrada en lugar de crear uno nuevo.
     */
    public function realizarCheckout(): self
    {
        if ($this->movement_type !== self::MOVEMENT_ENTRADA) {
            throw new \LogicException('Solo se puede hacer checkout de un registro de Entrada.');
        }

        if ($this->exit_time) {
            throw new \LogicException('Este acceso ya tiene registrada la salida.');
        }

        $this->exit_time = now();
        $this->save();

        return $this;
    }

    /**
     * Determina si este acceso fue vehicular.
     */
    public function esVehicular(): bool
    {
        if ($this->visit) {
            return $this->visit->access_type === Visit::ACCESS_VEHICULAR;
        }
        return !empty($this->vehicle_plate);
    }

    /**
     * Determina si la persona sigue dentro (entrada sin checkout).
     */
    public function sigueDentro(): bool
    {
        return $this->movement_type === self::MOVEMENT_ENTRADA && is_null($this->exit_time);
    }

    /**
     * Color del badge de movimiento para la UI.
     */
    public function movementColor(): string
    {
        return match ($this->movement_type) {
            self::MOVEMENT_ENTRADA => 'emerald',
            self::MOVEMENT_SALIDA  => 'rose',
            default                => 'zinc',
        };
    }

    /**
     * Etiqueta legible para la categoría de acceso.
     */
    public function categoryLabel(): string
    {
        return $this->access_category ?? '—';
    }

    /**
     * Nombre visible del visitante/residente (resuelve de relaciones).
     */
    public function displayName(): string
    {
        if ($this->visitor_name) {
            return $this->visitor_name;
        }
        if ($this->visit?->name) {
            return $this->visit->name;
        }
        if ($this->user?->name) {
            return $this->user->name;
        }
        return $this->identifier ?? 'Desconocido';
    }

    /**
     * Tiempo de estancia legible (si aplica).
     */
    public function estanciaHumana(): ?string
    {
        if (!$this->exit_time || !$this->date_time) {
            return null;
        }
        return $this->date_time->diffForHumans($this->exit_time, ['syntax' => 1]);
    }
}
