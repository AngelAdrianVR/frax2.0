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
use Inertia\Inertia;
use Carbon\Carbon;

class ReservationController extends Controller
{
    // --- Métodos de Ayuda para Roles y Fraccionamiento ---
    private function checkAdminRole($user)
    {
        $tableNames = config('permission.table_names');
        if(!$tableNames) return false;
        
        return DB::table($tableNames['model_has_roles'])
            ->join($tableNames['roles'], $tableNames['model_has_roles'] . '.role_id', '=', $tableNames['roles'] . '.id')
            ->where('model_id', $user->id)
            ->where('model_type', get_class($user))
            ->whereIn('name', ['Admin', 'Empleado'])
            ->exists();
    }

    private function getSubdivisionId($user, $isAdmin)
    {
        if ($isAdmin) {
            return session('current_subdivision_id') ?? DB::table('subdivision_user')->where('user_id', $user->id)->value('subdivision_id');
        }
        $currentPropertyId = method_exists($user, 'getCurrentPropertyId') ? $user->getCurrentPropertyId() : null; 
        if(!$currentPropertyId) return null;
        $unit = \App\Models\Community\PrivateUnit::find($currentPropertyId);
        return $unit ? $unit->subdivision_id : null;
    }

    // --- 1. CATÁLOGO DE RESERVACIONES (INDEX) ---
    public function index(Request $request)
    {
        $user = $request->user();
        $isAdmin = $this->checkAdminRole($user);
        $subdivisionId = $this->getSubdivisionId($user, $isAdmin);

        if (!$subdivisionId) {
            return redirect()->back()->with('error', 'No se ha detectado un fraccionamiento activo.');
        }

        // Obtener reservaciones del fraccionamiento actual
        $query = Reservation::with(['amenity', 'privateUnit'])
            ->whereHas('amenity', function ($q) use ($subdivisionId) {
                $q->where('subdivision_id', $subdivisionId);
            });

        // Si es residente, solo ve sus propias reservaciones
        if (!$isAdmin) {
            $unitId = method_exists($user, 'getCurrentPropertyId') ? $user->getCurrentPropertyId() : ($user->private_unit_id ?? null);
            $query->where(function($q) use ($user, $unitId) {
                $q->where('user_id', $user->id)
                  ->orWhere('private_unit_id', $unitId);
            });
        }

        // AGREGADO: Mapeamos más datos (como las notas) para que el modal los reciba completos
        $reservations = $query->orderBy('start_date_time', 'desc')->get()->map(function ($res) {
            return [
                'id' => $res->id,
                'amenity_id' => $res->amenity_id,
                'amenity_name' => $res->amenity->name ?? 'Desconocida',
                'start_date' => Carbon::parse($res->start_date_time)->format('Y-m-d H:i'),
                'end_date' => Carbon::parse($res->end_date_time)->format('Y-m-d H:i'),
                'total_cost' => (float) $res->total_cost,
                'status' => $res->status,
                'attendees' => $res->attendees_amount,
                'unit_name' => $res->privateUnit->name ?? 'N/A', 
                'admin_notes' => $res->admin_notes, // Necesario para la edición
                'created_at' => $res->created_at ? $res->created_at->format('Y-m-d H:i') : 'N/A',
            ];
        });

        // AGREGADO: Obtenemos el catálogo de amenidades disponibles para el botón "+ Nueva Reservación"
        $amenities = Amenity::where('subdivision_id', $subdivisionId)
            ->where('is_active', true)
            ->get(['id', 'name', 'capacity', 'reservation_cost']);

        return Inertia::render('Amenities/Reservations/Index', [
            'reservations' => $reservations,
            'amenities' => $amenities, // Pasamos las amenidades a la vista
            'isAdmin' => $isAdmin
        ]);
    }

    // --- 2. VISTA DE EDICIÓN ---
    public function edit(Reservation $reservation)
    {
        $reservation->load(['amenity', 'privateUnit']);
        
        return Inertia::render('Amenities/Reservations/Edit', [
            'reservation' => $reservation
        ]);
    }

    // --- 3. ACTUALIZAR RESERVACIÓN ---
    public function update(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'status' => 'required|in:Pendiente,Aprobada,Rechazada,Cancelada,Completada',
            'admin_notes' => 'nullable|string',
        ]);

        $reservation->update($validated);

        return Redirect::route('reservations.index')->with('success', 'Reservación actualizada correctamente.');
    }

    // --- 4. CANCELAR RESERVACIÓN ---
    public function cancel(Reservation $reservation)
    {
        $reservation->update(['status' => 'Cancelada']);
        return Redirect::back()->with('success', 'Reservación cancelada.');
    }

    /**
     * API para obtener disponibilidad mensual (para el Calendario Robusto)
     * Retorna un array de días con su estado de ocupación.
     */
    public function getAvailability(Request $request, Amenity $amenity)
    {
        try {
            $month = $request->input('month', Carbon::now()->month);
            $year = $request->input('year', Carbon::now()->year);

            $startOfMonth = Carbon::createFromDate($year, $month, 1);
            $endOfMonth = $startOfMonth->copy()->endOfMonth();

            $availability = [];

            // 1. Obtener horario y proteger contra nulos o strings JSON mal formados
            $schedule = $amenity->availability_schedule;
            
            if (is_string($schedule)) {
                $schedule = json_decode($schedule, true);
            }

            // 2. FALLBACK SALVAVIDAS: Si el horario está vacío en la base de datos, 
            // le inyectamos un horario abierto por defecto para evitar que todo salga "Cerrado"
            if (empty($schedule) || !is_array($schedule)) {
                $defaultDay = ['active' => true, 'start' => '09:00', 'end' => '22:00'];
                $schedule = [
                    'monday'    => $defaultDay,
                    'tuesday'   => $defaultDay,
                    'wednesday' => $defaultDay,
                    'thursday'  => $defaultDay,
                    'friday'    => $defaultDay,
                    'saturday'  => $defaultDay,
                    'sunday'    => $defaultDay,
                ];
            }

            // Iterar por cada día del mes
            $current = $startOfMonth->copy();
            while ($current <= $endOfMonth) {
                $dateStr = $current->format('Y-m-d');
                $dayOfWeek = strtolower($current->format('l')); // monday, tuesday...
                
                // Checar Schedule Base del día
                $dayConfig = $schedule[$dayOfWeek] ?? null;
                
                // Validar si el día está activo. filter_var maneja strings como "true" o "1" perfectamente.
                $isActive = isset($dayConfig['active']) && filter_var($dayConfig['active'], FILTER_VALIDATE_BOOLEAN);
                $isOpen = $dayConfig && $isActive;
                
                // Checar Mantenimiento
                $isMaintenance = false;
                if (method_exists($amenity, 'maintenanceBlocks')) {
                    $isMaintenance = $amenity->maintenanceBlocks()
                        ->whereDate('start_date_time', '<=', $current)
                        ->whereDate('end_date_time', '>=', $current)
                        ->exists();
                }

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
                        $startTimeStr = $dayConfig['start'] ?? '00:00';
                        $endTimeStr = $dayConfig['end'] ?? '23:59';

                        $openTime = Carbon::parse($dateStr . ' ' . $startTimeStr);
                        $closeTime = Carbon::parse($dateStr . ' ' . $endTimeStr);
                        
                        if ($closeTime->lessThanOrEqualTo($openTime)) {
                             $status = 'closed';
                        } else {
                            $totalMinutes = $closeTime->diffInMinutes($openTime);
                            
                            $occupiedMinutes = 0;
                            foreach ($reservations as $res) {
                                $resStart = Carbon::parse($res->start_date_time);
                                $resEnd = Carbon::parse($res->end_date_time);
                                $occupiedMinutes += $resEnd->diffInMinutes($resStart);
                                $occupiedMinutes += $amenity->buffer_minutes ?? 0; 
                            }

                            if ($occupiedMinutes == 0) {
                                $status = 'high';
                            } else {
                                $occupancy = $totalMinutes > 0 ? ($occupiedMinutes / $totalMinutes) * 100 : 100;
                                if ($occupancy >= 95) $status = 'full';
                                elseif ($occupancy >= 50) $status = 'low';
                                else $status = 'high';
                            }
                        }

                    } else {
                        // Modo Compartido
                        $totalAttendees = $reservations->sum('attendees_amount');
                        $capacity = $amenity->capacity > 0 ? $amenity->capacity : 1;

                        if ($totalAttendees >= $capacity) $status = 'full';
                        elseif ($totalAttendees >= ($capacity * 0.8)) $status = 'low'; 
                        else $status = 'high';
                    }
                }
                
                // Slots ocupados para la UI
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

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al calcular disponibilidad: ' . $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $amenity = Amenity::findOrFail($request->amenityId);
        
        $request->validate([
            // Quitamos la regla after_or_equal:today para evitar falsos positivos por Zona Horaria
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'attendees' => 'required|integer|min:1',
        ]);

        $startDateTime = Carbon::parse($request->date . ' ' . $request->start_time);
        $endDateTime = Carbon::parse($request->date . ' ' . $request->end_time);

        // Validación Manual: Verificamos si la fecha y hora seleccionada ya pasaron.
        // Damos un margen de 5 minutos de tolerancia por si el usuario está reservando algo "para ahora mismo".
        if ($startDateTime->isPast() && $startDateTime->diffInMinutes(Carbon::now()) > 5) {
             return Redirect::back()->with('error', 'No puedes reservar en una fecha u hora que ya ha pasado.');
        }

        if (method_exists($amenity, 'isAvailableFor')) {
             if (!$amenity->isAvailableFor($startDateTime, $endDateTime, $request->attendees)) {
                return Redirect::back()->with('error', 'La amenidad no está disponible en este horario o excede el aforo.');
            }
        }

        $user = $request->user();
        $unitId = method_exists($user, 'getCurrentPropertyId') ? $user->getCurrentPropertyId() : ($user->private_unit_id ?? null);

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

        return Redirect::back()->with('success', 'Apartado realizado. Confirma tu pago en finanzas.');
    }
}