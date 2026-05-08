<?php

namespace App\Http\Controllers\Gatehouse;

use App\Models\Gatehouse\Visit;
use App\Models\Community\PrivateUnit;
use App\Models\Community\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class VisitController extends Controller
{
    /**
     * Muestra la bitácora de visitas del usuario actual.
     */
    public function index(Request $request)
    {
        $currentPropertyId = $request->user()->getCurrentPropertyId();

        $visits = Visit::query()
            ->where('private_unit_id', $currentPropertyId)
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->through(function ($visit) {
                return [
                    'id' => $visit->id,
                    'name' => $visit->name,
                    'reason' => $visit->reason,
                    'access_type' => $visit->access_type,
                    'status' => $visit->status,
                    'date_of_use' => $visit->date_of_use ? $visit->date_of_use->format('d/m/Y H:i') : 'N/A',
                ];
            });

        return Inertia::render('Gatehouse/Visits/Index', [
            'visits' => $visits
        ]);
    }

    /**
     * Muestra el formulario para crear una nueva visita.
     */
    public function create()
    {
        // Si el usuario es administrador, podríamos pasarle todas las unidades
        // Si es residente, la unidad se asigna automáticamente en el store
        return Inertia::render('Gatehouse/Visits/Create', [
        ]);
    }

    /**
     * Almacena una nueva visita en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'reason' => 'nullable|string|max:500',
            'access_type' => 'required|in:Peatonal,Vehicular',
            'expiration_date' => 'nullable|date',
            'private_unit_id' => 'nullable|exists:private_units,id',
        ]);

        // Si no se envía private_unit_id (residente), usamos su propiedad actual
        if (!$request->filled('private_unit_id')) {
            $validated['private_unit_id'] = $request->user()->getCurrentPropertyId();
        }

        // Generamos un código QR único para la invitación
        $validated['qr_code'] = (string) Str::uuid();
        $validated['status'] = 'Pendiente';

        Visit::create($validated);

        return redirect()->route('visits.index')
            ->with('message', 'Invitación generada exitosamente.');
    }

    /**
     * Muestra el detalle de una visita específica (incluyendo el QR).
     */
    public function show(Visit $visit)
    {
        // Formateamos los datos para la vista
        $visitData = [
            'id' => $visit->id,
            'name' => $visit->name,
            'reason' => $visit->reason,
            'qr_code' => $visit->qr_code,
            'access_type' => $visit->access_type,
            'status' => $visit->status,
            'expiration_date' => $visit->expiration_date ? $visit->expiration_date->format('d/m/Y H:i') : 'Sin expiración',
            'date_of_use' => $visit->date_of_use ? $visit->date_of_use->format('d/m/Y H:i') : 'No utilizada',
            'private_unit_name' => $visit->privateUnit ? $visit->privateUnit->name : 'N/A',
        ];

        return Inertia::render('Gatehouse/Visits/Show', [
            'visit' => $visitData
        ]);
    }

    /**
     * Muestra el formulario para editar una visita.
     */
    public function edit(Visit $visit)
    {
        return Inertia::render('Gatehouse/Visits/Edit', [
            'visit' => $visit
        ]);
    }

    /**
     * Actualiza la información de la visita.
     */
    public function update(Request $request, Visit $visit)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'reason' => 'nullable|string|max:500',
            'access_type' => 'required|in:Peatonal,Vehicular',
            'expiration_date' => 'nullable|date',
            'status' => 'required|in:Pendiente,Ingresado,Expirado,Cancelado',
        ]);

        $visit->update($validated);

        return redirect()->route('visits.index')
            ->with('message', 'Visita actualizada correctamente.');
    }

    /**
     * Elimina una visita de la bitácora.
     */
    public function destroy(Visit $visit)
    {
        $visit->delete();

        return redirect()->route('visits.index')
            ->with('message', 'Invitación eliminada.');
    }
}