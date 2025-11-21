<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

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

    // --- Estructura Inmobiliaria ---

    /**
     * Un fraccionamiento tiene muchas unidades privativas (casas/lotes).
     */
    public function privateUnits(): HasMany
    {
        return $this->hasMany(PrivateUnit::class, 'subdivision_id');
    }

    /**
     * Relación "Tiene muchos a través de".
     * Obtener todos los residentes del fraccionamiento a través de las unidades privativas.
     * Útil para listados generales de personas.
     */
    public function residents(): HasManyThrough
    {
        // Primer argumento: Modelo destino (Resident)
        // Segundo argumento: Modelo intermedio (PrivateUnit)
        // Tercer argumento: FK en tabla intermedia (private_units.subdivision_id)
        // Cuarto argumento: FK en tabla destino (residents.private_unit_id)
        return $this->hasManyThrough(
            Resident::class, 
            PrivateUnit::class, 
            'subdivision_id', 
            'private_unit_id'
        );
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
     * Tabla: inventory_assets
     */
    public function inventoryAssets(): HasMany
    {
        return $this->hasMany(InventoryAsset::class, 'subdivision_id');
    }

    /**
     * Proveedores registrados para dar servicio al fraccionamiento.
     * Tabla: suppliers
     */
    public function suppliers(): HasMany
    {
        return $this->hasMany(Supplier::class, 'subdivision_id');
    }

    // --- Social ---

    /**
     * Eventos creados en el fraccionamiento.
     * Tabla: events
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'subdivision_id');
    }

    /**
     * Publicaciones en el muro social del fraccionamiento.
     * Tabla: posts
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'subdivision_id');
    }

    // --- Finanzas y Cobranza ---

    /**
     * Conceptos de cobro definidos (ej. cuota mantenimiento, cuota extraordinaria).
     * Tabla: billing_concepts
     */
    public function billingConcepts(): HasMany
    {
        return $this->hasMany(BillingConcept::class, 'subdivision_id');
    }

    /**
     * Suscripciones del fraccionamiento al servicio SaaS o planes internos.
     * Tabla: suscriptions (Ojo: en inglés correcto es 'subscriptions', ajusta según tu modelo real)
     */
    public function subscriptions(): HasMany
    {
        // Asumiendo que el modelo se llama 'Subscription' o 'Suscription'
        return $this->hasMany(Subscription::class, 'subdivision_id');
    }
}