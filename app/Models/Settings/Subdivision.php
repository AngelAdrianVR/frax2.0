<?php

namespace App\Models\Settings;

use App\Models\Community\User;
use App\Models\Community\PrivateUnit;
use App\Models\Amenities\Amenity;
use App\Models\Finances\BillingConcept;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subdivision extends Model
{
    protected $fillable = [
        'name', 
        'slug', 
        'address', 
        'exterior_number', 
        'suburb', 
        'town', 
        'federal_state', 
        'post_code', 
        'configuration', 
        'houses_amount'
    ];

    protected $casts = [
        'configuration' => 'array', // JSON a Array asociativo
        'houses_amount' => 'integer',
    ];
    protected $guard_name = 'web';

    // --- Estructura Inmobiliaria ---

    /**
     * Todos los usuarios vinculados a este fraccionamiento 
     * (Administradores, Vecinos, Guardias, etc.)
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
                    ->withPivot('role_in_subdivision', 'is_current')
                    ->withTimestamps();
    }

    /**
     * Un fraccionamiento tiene muchas unidades privativas (casas/lotes).
     */
    public function privateUnits(): HasMany
    {
        return $this->hasMany(PrivateUnit::class, 'subdivision_id');
    }

    // --- Operaciones y Servicios ---

    /**
     * Un fraccionamiento tiene muchas amenidades.
     */
    public function amenities(): HasMany
    {
        return $this->hasMany(Amenity::class, 'subdivision_id');
    }

    /**
     * Activos de inventario (ej. podadoras, herramientas, mobiliario de oficina).
     */
    // public function inventoryAssets(): HasMany
    // {
    //     return $this->hasMany(InventoryAsset::class, 'subdivision_id');
    // }

    /**
     * Proveedores registrados para dar servicio al fraccionamiento.
     */
    // public function suppliers(): HasMany
    // {
    //     return $this->hasMany(Supplier::class, 'subdivision_id');
    // }

    // --- Social ---

    /**
     * Eventos creados en el fraccionamiento.
     */
    // public function events(): HasMany
    // {
    //     return $this->hasMany(Event::class, 'subdivision_id');
    // }

    /**
     * Publicaciones en el muro social del fraccionamiento.
     */
    // public function posts(): HasMany
    // {
    //     return $this->hasMany(Post::class, 'subdivision_id');
    // }

    // --- Finanzas y Cobranza ---

    /**
     * Conceptos de cobro definidos (ej. cuota mantenimiento, cuota extraordinaria).
     */
    public function billingConcepts(): HasMany
    {
        return $this->hasMany(BillingConcept::class, 'subdivision_id');
    }

    /**
     * Suscripciones del fraccionamiento al servicio SaaS o planes internos.
     */
    // public function subscriptions(): HasMany
    // {
    //     return $this->hasMany(Subscription::class, 'subdivision_id');
    // }
}