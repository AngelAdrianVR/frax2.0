<?php

namespace App\Models\AccessControl;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * Punto de Control para Rondines de Seguridad.
 * 
 * Representa una ubicación física que el guardia debe escanear
 * durante su recorrido (patrullaje). Puede identificarse por:
 * - Tag NFC físico
 * - Código QR impreso
 * - Coordenadas GPS
 */
class Checkpoint extends Model
{
    protected $fillable = [
        'name',
        'coordinates',
        'tag_nfc_id',
    ];

    // ─── Relaciones ──────────────────────────────────────────────────
    
    /**
     * Historial de escaneos de este punto de control en todos los rondines.
     */
    public function scans(): HasMany
    {
        return $this->hasMany(PatrolScan::class, 'checkpoint_id');
    }

    // ─── Lógica de Negocio ──────────────────────────────────────────

    /**
     * Verifica si este punto tiene un tag NFC/QR asignado.
     */
    public function tieneTag(): bool
    {
        return !empty($this->tag_nfc_id);
    }

    /**
     * Verifica si el punto tiene coordenadas GPS registradas.
     */
    public function tieneCoordenadas(): bool
    {
        return !empty($this->coordinates);
    }

    /**
     * Devuelve las coordenadas como array [lat, lng].
     */
    public function coordenadasArray(): ?array
    {
        if (!$this->coordinates) {
            return null;
        }
        $parts = explode(',', $this->coordinates);
        return count($parts) === 2
            ? ['lat' => trim($parts[0]), 'lng' => trim($parts[1])]
            : null;
    }

    /**
     * Cantidad de veces que ha sido escaneado (total histórico).
     */
    public function totalEscaneos(): int
    {
        return $this->scans()->count();
    }

    /**
     * Último escaneo registrado en este punto.
     */
    public function ultimoEscaneo(): ?PatrolScan
    {
        return $this->scans()->latest('scan_date_time')->first();
    }

    // ─── Scopes ─────────────────────────────────────────────────────

    /**
     * Filtrar puntos que tienen tag NFC asignado.
     */
    public function scopeConTag(Builder $query): Builder
    {
        return $query->whereNotNull('tag_nfc_id')->where('tag_nfc_id', '!=', '');
    }

    /**
     * Buscar por código de tag NFC/QR.
     */
    public function scopePorTag(Builder $query, string $tagCode): Builder
    {
        return $query->where('tag_nfc_id', $tagCode);
    }
}
