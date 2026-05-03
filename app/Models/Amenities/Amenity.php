<?php

namespace App\Models\Amenities;

use App\Models\Settings\subdivision;
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
        'availability_schedule', // Horarios JSON: {'monday': {'start': '09:00', 'end': '20:00', 'active': true}}
        'max_days_advance',    // Ventana de reserva
        'requires_approval',   // Si requiere confirmación manual
        'is_active'            // Nuevo campo para el switch
    ];

    protected $casts = [
        'reservation_cost' => 'decimal:2',
        'rules' => 'array', 
        'availability_schedule' => 'array', 
        'requires_approval' => 'boolean',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function subdivision(): BelongsTo
    {
        return $this->belongsTo(Subdivision::class, 'subdivision_id');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function maintenanceBlocks(): HasMany
    {
        return $this->hasMany(AmenityMaintenanceBlock::class);
    }

    /**
     * Verifica disponibilidad básica (usado para validaciones rápidas)
     */
    public function isAvailableFor($start, $end, $requestedAttendees = 1): bool
    {
        $start = Carbon::parse($start);
        $end = Carbon::parse($end);

        // 1. Verificar si está activa
        if (!$this->is_active) return false;

        // 2. Verificar Mantenimiento
        $maintenance = $this->maintenanceBlocks()
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('start_date_time', [$start, $end])
                      ->orWhereBetween('end_date_time', [$start, $end]);
            })->exists();

        if ($maintenance) return false;

        // 3. Verificar Horario Semanal
        $dayName = strtolower($start->format('l')); 
        $schedule = $this->availability_schedule;
        
        // Si no hay configuración para ese día o está marcado como inactivo
        if (!isset($schedule[$dayName]) || 
            (isset($schedule[$dayName]['active']) && !$schedule[$dayName]['active'])) {
            return false;
        }

        // Verificar horas dentro del horario permitido
        $allowStart = Carbon::parse($start->format('Y-m-d') . ' ' . $schedule[$dayName]['start']);
        $allowEnd = Carbon::parse($start->format('Y-m-d') . ' ' . $schedule[$dayName]['end']);

        if ($start->lt($allowStart) || $end->gt($allowEnd)) {
            return false;
        }

        // 4. Verificar Colisiones
        if ($this->mode === 'Exclusivo') {
            $bufferEnd = $end->copy()->addMinutes($this->buffer_minutes);
            return !$this->reservations()
                ->whereNotIn('status', ['Cancelada', 'Rechazada'])
                ->where(function ($q) use ($start, $bufferEnd) {
                    $q->whereBetween('start_date_time', [$start, $bufferEnd])
                      ->orWhereBetween('end_date_time', [$start, $bufferEnd])
                      ->orWhere(function ($sub) use ($start, $bufferEnd) {
                          $sub->where('start_date_time', '<', $start)
                              ->where('end_date_time', '>', $bufferEnd);
                      });
                })->exists();
        } else {
            $currentAttendees = $this->reservations()
                ->whereNotIn('status', ['Cancelada', 'Rechazada'])
                ->where(function ($q) use ($start, $end) {
                     $q->whereBetween('start_date_time', [$start, $end])
                       ->orWhereBetween('end_date_time', [$start, $end]);
                })
                ->sum('attendees_amount');

            return ($currentAttendees + $requestedAttendees) <= $this->capacity;
        }
    }
}