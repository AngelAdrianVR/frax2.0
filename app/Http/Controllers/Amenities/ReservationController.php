<?php

namespace App\Http\Controllers\Amenities;

use App\Models\Amenities\Reservation;
use App\Models\Amenities\Amenity;
use App\Models\Finances\GeneratedFee;
use App\Models\Finances\BillingConcept;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;

class ReservationController extends Controller
{
    /**
     * API para obtener disponibilidad mensual (para el Calendario Robusto)
     * Retorna un array de días con su estado de ocupación.
     */
    public function getAvailability(Request $request, Amenity $amenity)
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $startOfMonth = Carbon::createFromDate($year, $month, 1);
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $availability = [];

        // Aseguramos que el schedule sea un array.
        // Si viene como string JSON, lo decodificamos.
        $schedule = $amenity->availability_schedule;
        if (is_string($schedule)) {
            $schedule = json_decode($schedule, true);
        }

        // Iterar por cada día del mes
        $current = $startOfMonth->copy();
        while ($current <= $endOfMonth) {
            $dateStr = $current->format('Y-m-d');
            $dayOfWeek = strtolower($current->format('l')); // monday, tuesday...
            
            // 1. Checar Schedule Base
            $dayConfig = $schedule[$dayOfWeek] ?? null;
            
            // Usamos filter_var para validar booleanos correctamente incluso si vienen como strings "1" o "0"
            $isActive = isset($dayConfig['active']) && filter_var($dayConfig['active'], FILTER_VALIDATE_BOOLEAN);
            $isOpen = $dayConfig && $isActive;
            
            // 2. Checar Mantenimiento
            $isMaintenance = false;
            if (method_exists($amenity, 'maintenanceBlocks')) {
                $isMaintenance = $amenity->maintenanceBlocks()
                    ->whereDate('start_date_time', '<=', $current)
                    ->whereDate('end_date_time', '>=', $current)
                    ->exists();
            }

            // Estado inicial
            $status = 'high'; 
            
            if (!$isOpen || $isMaintenance) {
                $status = 'closed';
            } else {
                // Obtener reservas activas para el día
                $reservations = $amenity->reservations()
                    ->whereNotIn('status', ['Cancelada', 'Rechazada'])
                    ->whereDate('start_date_time', $dateStr)
                    ->get();

                if ($amenity->mode === 'Exclusivo') {
                    // Modo Exclusivo: Calcular tiempo ocupado vs tiempo total
                    $startTimeStr = $dayConfig['start'] ?? '00:00';
                    $endTimeStr = $dayConfig['end'] ?? '00:00';

                    $openTime = Carbon::parse($dateStr . ' ' . $startTimeStr);
                    $closeTime = Carbon::parse($dateStr . ' ' . $endTimeStr);
                    
                    // Si cierra antes o igual que abre, no hay tiempo disponible -> Cerrado
                    if ($closeTime->lessThanOrEqualTo($openTime)) {
                         $status = 'closed';
                    } else {
                        $totalMinutes = $closeTime->diffInMinutes($openTime);
                        
                        $occupiedMinutes = 0;
                        foreach ($reservations as $res) {
                            // Asegurar que las fechas de reserva sean Carbon
                            $resStart = Carbon::parse($res->start_date_time);
                            $resEnd = Carbon::parse($res->end_date_time);
                            
                            $occupiedMinutes += $resEnd->diffInMinutes($resStart);
                            // Buffer entre reservas
                            $occupiedMinutes += $amenity->buffer_minutes ?? 0; 
                        }

                        // LÓGICA CORREGIDA:
                        // Si no hay minutos ocupados, forzamos estado 'high' para evitar errores de división o redondeo
                        if ($occupiedMinutes == 0) {
                            $status = 'high';
                        } else {
                            // Cálculo de porcentaje
                            $occupancy = $totalMinutes > 0 ? ($occupiedMinutes / $totalMinutes) * 100 : 100;
                            
                            if ($occupancy >= 95) $status = 'full'; // Casi lleno o lleno
                            elseif ($occupancy >= 50) $status = 'low'; // Medio lleno
                            else $status = 'high'; // Disponible
                        }
                    }

                } else {
                    // Modo Compartido: Asistentes vs Capacidad
                    $totalAttendees = $reservations->sum('attendees_amount');
                    $capacity = $amenity->capacity > 0 ? $amenity->capacity : 1;

                    if ($totalAttendees >= $capacity) $status = 'full';
                    elseif ($totalAttendees >= ($capacity * 0.8)) $status = 'low'; 
                    else $status = 'high';
                }
            }
            
            // Slots ocupados para UI
            $busySlots = $amenity->reservations()
                ->whereDate('start_date_time', $dateStr)
                ->whereNotIn('status', ['Cancelada', 'Rechazada'])
                ->get()
                ->map(fn($r) => [
                    'start' => Carbon::parse($r->start_date_time)->format('H:i'),
                    'end' => Carbon::parse($r->end_date_time)->format('H:i')
                ]);

            $availability[] = [
                'date' => $dateStr,
                'day' => $current->day,
                'status' => $status,
                'busy_slots' => $busySlots
            ];

            $current->addDay();
        }

        return response()->json($availability);
    }

    public function store(Request $request)
    {
        $amenity = Amenity::findOrFail($request->amenityId);
        
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'attendees' => 'required|integer|min:1',
        ]);

        $startDateTime = Carbon::parse($request->date . ' ' . $request->start_time);
        $endDateTime = Carbon::parse($request->date . ' ' . $request->end_time);

        // Validación de disponibilidad exacta
        if (method_exists($amenity, 'isAvailableFor')) {
             if (!$amenity->isAvailableFor($startDateTime, $endDateTime, $request->attendees)) {
                return Redirect::back()->with('error', 'La amenidad no está disponible en este horario o excede el aforo.');
            }
        }

        $user = $request->user();
        // Fallback robusto para obtener unitId
        $unitId = method_exists($user, 'getCurrentPropertyId') ? $user->getCurrentPropertyId() : ($user->resident->private_unit_id ?? null);

        // DB::beginTransaction();
        // try {
            $totalCost = $amenity->reservation_cost; 

            $concept = BillingConcept::firstOrCreate(
                ['name' => 'Reserva de Amenidad', 'subdivision_id' => $amenity->subdivision_id],
                ['base_amount' => 0, 'recurrence_type' => 'Pago unico']
            );

            $fee = GeneratedFee::create([
                'payment_reference' => 'RES-' . time() . '-' . $user->id,
                'total_amount' => $totalCost,
                'amount_paid' => 0,
                'expiration_date' => Carbon::now()->addDays(3),
                'start_period' => Carbon::now(),
                'end_period' => Carbon::now(),
                'status' => 'Pendiente',
                'private_unit_id' => $unitId,
                'billing_concept_id' => $concept->id,
            ]);

            Reservation::create([
                'start_date_time' => $startDateTime,
                'end_date_time' => $endDateTime,
                'total_cost' => $totalCost,
                'status' => 'Pendiente',
                'private_unit_id' => $unitId,
                'attendees_amount' => $request->attendees,
                'generated_fee_id' => $fee->id,
                'amenity_id' => $amenity->id,
                'user_id' => $user->id ?? null,
            ]);

            // DB::commit();
            // return Redirect::back()->with('success', 'Apartado realizado. Confirma tu pago en finanzas.');

        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     return Redirect::back()->with('error', 'Error al procesar: ' . $e->getMessage());
        // }
    }
}