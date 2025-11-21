<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resident extends Model
{
    protected $fillable = [
        'full_name', 'phone', 'email', 'person_type', 
        'is_emergency_contact', 'emergency_contact_info', 
        'is_slow_payer', 'user_id'
    ];

    protected $casts = [
        'is_emergency_contact' => 'boolean',
        'is_slow_payer' => 'boolean',
        'emergency_contact_info' => 'array', // JSON
    ];

    public function user(): BelongsTo
    { 
        return $this->belongsTo(User::class, 'user_id'); 
    }

    // --- Estructura Inmobiliaria ---

    /**
     * Unidades donde reside o es dueño.
     */
    public function privateUnits(): BelongsToMany
    {
        // Nota: Corregí 'resident_id' en el pivote, asegúrate que tu migración
        // use el singular (resident_id) y no el plural (residents_id) como en el diagrama.
        return $this->belongsToMany(PrivateUnit::class, 'residence_units', 'resident_id', 'private_unit_id')
                    ->using(ResidenceUnit::class)
                    ->withPivot(['role_in_unit', 'is_primary_owner', 'primary'])
                    ->withTimestamps();
    }

    public function vehicles(): HasMany 
    { 
        return $this->hasMany(Vehicle::class, 'resident_id'); 
    }

    public function pets(): HasMany 
    { 
        return $this->hasMany(Pet::class, 'resident_id'); 
    }

    // --- Operaciones y Servicios ---

    /**
     * Reservaciones de amenidades hechas por el residente.
     * Tabla: reservations
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'resident_id');
    }

    /**
     * Reportes de mantenimiento creados por el residente.
     * Tabla: reports
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'resident_id');
    }

    /**
     * Calificaciones que ha dado a proveedores.
     * Tabla: supplier_ratings
     */
    public function supplierRatings(): HasMany
    {
        return $this->hasMany(SupplierRating::class, 'resident_id');
    }

    // --- Finanzas ---

    /**
     * Pagos realizados por el residente.
     * Tabla: payments
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'resident_id');
    }

    // --- Social ---

    /**
     * Publicaciones hechas en el muro.
     * Tabla: posts
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'resident_id');
    }

    /**
     * Reacciones (likes, etc) a eventos o posts.
     * Tabla: reactions
     */
    public function reactions(): HasMany
    {
        return $this->hasMany(Reaction::class, 'resident_id');
    }
}