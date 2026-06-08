<?php

namespace App\Models\AccessControl;

use App\Models\Community\PrivateUnit;
use App\Models\Community\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

/**
 * Invitación de Registro.
 * 
 * Permite a un residente invitar a otra persona (familiar, inquilino)
 * a registrarse y vincularse a su unidad privada.
 * 
 * La invitación expira en 48 horas si no es aceptada.
 */
class RegisterInvitation extends Model
{
    protected $fillable = [
        'email',
        'token',
        'role_type',           // 'Dueño', 'Familiar'
        'status',              // 'Pendiente', 'Aceptado', 'Expirado'
        'expires_at',
        'invited_by_user_id',
        'private_unit_id',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    // ─── Constantes ──────────────────────────────────────────────────
    public const ROLE_DUENIO    = 'Dueño';
    public const ROLE_FAMILIAR  = 'Familiar';

    public const STATUS_PENDIENTE = 'Pendiente';
    public const STATUS_ACEPTADO  = 'Aceptado';
    public const STATUS_EXPIRADO  = 'Expirado';

    private const EXPIRATION_HOURS = 48;

    // ─── Relaciones ──────────────────────────────────────────────────
    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by_user_id');
    }

    public function privateUnit(): BelongsTo
    {
        return $this->belongsTo(PrivateUnit::class, 'private_unit_id');
    }

    // ─── Lógica de Negocio ──────────────────────────────────────────

    /**
     * Verifica si la invitación sigue vigente.
     */
    public function estaVigente(): bool
    {
        return $this->status === self::STATUS_PENDIENTE
            && $this->expires_at
            && $this->expires_at->isFuture();
    }

    /**
     * Verifica si la invitación ya expiró.
     */
    public function estaExpirada(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Marca la invitación como aceptada.
     */
    public function aceptar(): void
    {
        $this->status = self::STATUS_ACEPTADO;
        $this->save();
    }

    /**
     * Genera una nueva invitación con token único y expiración.
     */
    public static function generarInvitacion(array $data): self
    {
        return static::create([
            'email'              => $data['email'],
            'role_type'          => $data['role_type'],
            'token'              => Str::random(40),
            'status'             => self::STATUS_PENDIENTE,
            'expires_at'         => now()->addHours(self::EXPIRATION_HOURS),
            'invited_by_user_id' => $data['invited_by_user_id'],
            'private_unit_id'    => $data['private_unit_id'],
        ]);
    }

    // ─── Scopes ─────────────────────────────────────────────────────

    public function scopePendientes(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDIENTE);
    }

    public function scopeDeUnidad(Builder $query, int $privateUnitId): Builder
    {
        return $query->where('private_unit_id', $privateUnitId);
    }

    /**
     * Invitaciones que ya expiraron pero siguen en status 'Pendiente'.
     */
    public function scopeExpiradasSinActualizar(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDIENTE)
                     ->where('expires_at', '<', now());
    }
}
