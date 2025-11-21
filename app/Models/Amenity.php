<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Amenity extends Model
{
    protected $fillable = [
        'name', 
        'description', 
        'reservation_cost', 
        'rules', 
        'subdivision_id'
    ];

    protected $casts = [
        'reservation_cost' => 'decimal:2',
        'rules' => 'array', // JSON para reglas específicas (ej. "No vidrio", "Max 50 personas")
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación: La amenidad pertenece al Fraccionamiento.
     */
    public function subdivision(): BelongsTo
    {
        return $this->belongsTo(Subdivision::class, 'subdivision_id');
    }

    /**
     * Relación: Una amenidad tiene muchas reservaciones futuras o pasadas.
     */
    public function reservations(): HasMany
    {
        // Asumiendo que tienes un modelo Reservation
        return $this->hasMany(Reservation::class);
    }

    // Polimorfismo para comentarios/reseñas de la amenidad
    public function comments(): MorphMany 
    { 
        return $this->morphMany(Comment::class, 'commentable'); 
    }
}