<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Carbon\Carbon;

class Amenity extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name', 
        'description', 
        'reservation_cost', 
        'rules', 
        'subdivision_id',
        'mode',                // 'Exclusivo' o 'Compartido'
        'capacity',            // Aforo máximo
        'buffer_minutes',      // Tiempo de limpieza
        'availability_schedule', // Horarios JSON
        'max_days_advance',    // Ventana de reserva
        'requires_approval'    // Si requiere confirmación manual
    ];

    protected $casts = [
        'reservation_cost' => 'decimal:2',
        'rules' => 'array', 
        'availability_schedule' => 'array', // Cast automático a array PHP
        'requires_approval' => 'boolean',
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
     * Relación: Reservaciones asociadas.
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Relación: Bloqueos por mantenimiento.
     */
    public function maintenanceBlocks(): HasMany
    {
        return $this->hasMany(AmenityMaintenanceBlock::class);
    }

    public function comments(): MorphMany 
    { 
        return $this->morphMany(Comment::class, 'commentable'); 
    }

    // --- Helpers de Lógica de Negocio ---

    /**
     * Verifica si la amenidad está disponible en un rango de fechas dado.
     * Esta función es el corazón del calendario.
     */
    public function isAvailableFor($start, $end, $requestedAttendees = 1): bool
    {
        $start = Carbon::parse($start);
        $end = Carbon::parse($end);

        // 1. Verificar Mantenimiento
        $maintenance = $this->maintenanceBlocks()
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('start_date_time', [$start, $end])
                      ->orWhereBetween('end_date_time', [$start, $end]);
            })->exists();

        if ($maintenance) return false;

        // 2. Verificar Horario (availability_schedule)
        // Lógica simple: verificar si el día de la semana tiene slots abiertos
        // En una implementación real, iterarías por cada día del rango.
        $dayName = strtolower($start->format('l')); // monday, tuesday...
        $schedule = $this->availability_schedule;
        
        if (isset($schedule[$dayName]) && empty($schedule[$dayName])) {
            return false; // Cerrado ese día
        }

        // 3. Verificar Colisiones de Reservas
        if ($this->mode === 'Exclusivo') {
            // Si es exclusivo, no puede haber NINGUNA reserva solapada
            // Incluimos el buffer_minutes para que no peguen reservas
            $bufferEnd = $end->copy()->addMinutes($this->buffer_minutes);
            
            return !$this->reservations()
                ->where('status', '!=', 'Cancelada')
                ->where('status', '!=', 'Rechazada')
                ->where(function ($q) use ($start, $bufferEnd) {
                    $q->whereBetween('start_date_time', [$start, $bufferEnd])
                      ->orWhereBetween('end_date_time', [$start, $bufferEnd])
                      ->orWhere(function ($sub) use ($start, $bufferEnd) {
                          $sub->where('start_date_time', '<', $start)
                              ->where('end_date_time', '>', $bufferEnd);
                      });
                })->exists();
        } else {
            // Si es Compartido, sumamos los asistentes de las reservas en ese lapso
            $currentAttendees = $this->reservations()
                ->where('status', '!=', 'Cancelada')
                ->where(function ($q) use ($start, $end) {
                     $q->whereBetween('start_date_time', [$start, $end])
                       ->orWhereBetween('end_date_time', [$start, $end]);
                })
                ->sum('attendees_amount');

            return ($currentAttendees + $requestedAttendees) <= $this->capacity;
        }
    }
}