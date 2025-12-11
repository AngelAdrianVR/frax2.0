<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Amenity;
use App\Models\GeneratedFee;
use App\Models\BillingConcept;
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

        // Iterar por cada día del mes
        $current = $startOfMonth->copy();
        while ($current <= $endOfMonth) {
            $dateStr = $current->format('Y-m-d');
            $dayOfWeek = strtolower($current->format('l'));
            
            // 1. Checar Schedule Base
            $schedule = $amenity->availability_schedule;
            $isOpen = isset($schedule[$dayOfWeek]) && ($schedule[$dayOfWeek]['active'] ?? false);
            
            // 2. Checar Mantenimiento
            $isMaintenance = $amenity->maintenanceBlocks()
                ->whereDate('start_date_time', '<=', $current)
                ->whereDate('end_date_time', '>=', $current)
                ->exists();

            $status = 'available'; // available, low, full, closed
            $percent = 0;

            if (!$isOpen || $isMaintenance) {
                $status = 'closed';
            } else {
                // Calcular ocupación real
                // Obtener reservas de ese día
                $reservations = $amenity->reservations()
                    ->whereNotIn('status', ['Cancelada', 'Rechazada'])
                    ->whereDate('start_date_time', $dateStr)
                    ->get();

                if ($amenity->mode === 'Exclusivo') {
                    // Si es exclusivo, calculamos horas ocupadas vs horas totales del día
                    $openTime = Carbon::parse($dateStr . ' ' . $schedule[$dayOfWeek]['start']);
                    $closeTime = Carbon::parse($dateStr . ' ' . $schedule[$dayOfWeek]['end']);
                    $totalMinutes = $closeTime->diffInMinutes($openTime);
                    
                    $occupiedMinutes = 0;
                    foreach ($reservations as $res) {
                        $occupiedMinutes += $res->end_date_time->diffInMinutes($res->start_date_time);
                        // Añadir buffer si aplica
                        $occupiedMinutes += $amenity->buffer_minutes; 
                    }

                    $occupancy = $totalMinutes > 0 ? ($occupiedMinutes / $totalMinutes) * 100 : 100;
                    
                    if ($occupancy >= 90) $status = 'full';
                    elseif ($occupancy >= 50) $status = 'low'; // Poca disponibilidad
                    else $status = 'high'; // Mucha disponibilidad

                } else {
                    // Compartido: Suma de asistentes vs capacidad diaria (simplificado)
                    // Una métrica mejor sería por slots, pero para vista mensual usamos promedio
                    $totalAttendees = $reservations->sum('attendees_amount');
                    // Estimación: si el total de gente en el día supera X veces la capacidad
                    // O simplemente comparamos contra capacidad instantánea
                    if ($totalAttendees >= ($amenity->capacity * 2)) $status = 'low'; // Muy concurrido
                    else $status = 'high';
                }
            }
            
            // Obtenemos las horas ocupadas para pintar en el frontend si se requiere
            $busySlots = $amenity->reservations()
                ->whereDate('start_date_time', $dateStr)
                ->whereNotIn('status', ['Cancelada'])
                ->get()
                ->map(fn($r) => [
                    'start' => $r->start_date_time->format('H:i'),
                    'end' => $r->end_date_time->format('H:i')
                ]);

            $availability[] = [
                'date' => $dateStr,
                'day' => $current->day,
                'status' => $status, // high, low, full, closed
                'busy_slots' => $busySlots
            ];

            $current->addDay();
        }

        return response()->json($availability);
    }

    public function store(Request $request, Amenity $amenity)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'attendees' => 'required|integer|min:1',
        ]);

        $startDateTime = Carbon::parse($request->date . ' ' . $request->start_time);
        $endDateTime = Carbon::parse($request->date . ' ' . $request->end_time);

        if (!$amenity->isAvailableFor($startDateTime, $endDateTime, $request->attendees)) {
            return Redirect::back()->with('error', 'La amenidad no está disponible en este horario o excede el aforo.');
        }

        $user = $request->user();
        $unitId = $user->getCurrentPropertyId();

        DB::beginTransaction();
        try {
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
                'resident_id' => $user->resident->id ?? null,
            ]);

            DB::commit();
            return Redirect::back()->with('success', 'Apartado realizado. Confirma tu pago en finanzas.');

        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with('error', 'Error al procesar: ' . $e->getMessage());
        }
    }
}