<?php

namespace App\Http\Controllers\AccessControl;

use App\Http\Controllers\Controller;
use App\Models\AccessControl\Visit;
use App\Models\Community\PrivateUnit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VisitController extends Controller
{
    /**
     * Bitácora de visitas (residente ve sus propias visitas,
     * guardia/admin ven todas).
     */
    public function index(Request $request)
    {
        $query = Visit::with('privateUnit')->latest();

        // Residentes solo ven las de su unidad actual
        if ($request->user()->getCurrentPropertyId()) {
            $query->deUnidad($request->user()->getCurrentPropertyId());
        }

        // Filtro por estatus
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $visits = $query->paginate(15)
            ->through(fn($visit) => [
                'id'           => $visit->id,
                'name'         => $visit->name,
                'reason'       => $visit->reason,
                'access_type'  => $visit->access_type,
                'status'       => $visit->status,
                'statusColor'  => $visit->statusColor(),
                'date_of_use'  => $visit->date_of_use?->format('d/m/Y H:i') ?? 'N/A',
                'unidad'       => $visit->privateUnit?->lot_number ?? null,
            ]);

        return Inertia::render('AccessControl/Visits/Index', [
            'visits' => $visits,
            'filters' => $request->only('status'),
        ]);
    }

    public function create()
    {
        return Inertia::render('AccessControl/Visits/Create', [
            'privateUnits' => PrivateUnit::orderBy('lot_number')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'reason'          => 'nullable|string|max:500',
            'access_type'     => 'required|in:Peatonal,Vehicular',
            'expiration_date' => 'nullable|date',
            'private_unit_id' => 'nullable|exists:private_units,id',
        ]);

        if (!$request->filled('private_unit_id')) {
            $validated['private_unit_id'] = $request->user()->getCurrentPropertyId();
        }

        Visit::crearConQR($validated);

        return redirect()->route('visits.index')
            ->with('success', 'Invitación generada exitosamente.');
    }

    public function show(Visit $visit)
    {
        $visit->load('privateUnit', 'accessLogs');

        return Inertia::render('AccessControl/Visits/Show', [
            'visit' => [
                'id'                => $visit->id,
                'name'              => $visit->name,
                'reason'            => $visit->reason,
                'qr_code'           => $visit->qr_code,
                'access_type'       => $visit->access_type,
                'status'            => $visit->status,
                'statusColor'       => $visit->statusColor(),
                'vigente'           => $visit->estaVigente(),
                'expiration_date'   => $visit->expiration_date?->format('d/m/Y H:i'),
                'date_of_use'       => $visit->date_of_use?->format('d/m/Y H:i'),
                'unidad'            => $visit->privateUnit?->lot_number ?? 'N/A',
                'accessLogs'        => $visit->accessLogs->map(fn($log) => [
                    'movimiento' => $log->movement_type,
                    'metodo'     => $log->verification_method,
                    'fechaHora'  => $log->date_time?->format('d/m/Y H:i'),
                ]),
            ],
        ]);
    }

    public function edit(Visit $visit)
    {
        return Inertia::render('AccessControl/Visits/Edit', [
            'visit' => $visit->only([
                'id', 'name', 'reason', 'access_type',
                'expiration_date', 'private_unit_id', 'status',
            ]),
            'privateUnits' => PrivateUnit::orderBy('lot_number')->get(),
        ]);
    }

    public function update(Request $request, Visit $visit)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'reason'          => 'nullable|string|max:500',
            'access_type'     => 'required|in:Peatonal,Vehicular',
            'expiration_date' => 'nullable|date',
            'private_unit_id' => 'nullable|exists:private_units,id',
        ]);

        $visit->update($validated);

        return redirect()->route('visits.index')
            ->with('success', 'Visita actualizada correctamente.');
    }

    public function destroy(Visit $visit)
    {
        $visit->cancelar();

        return redirect()->route('visits.index')
            ->with('success', 'Visita cancelada.');
    }

    // ─── Acciones de Guardia ────────────────────────────────────────

    /**
     * API: Registrar ingreso de una visita por QR.
     */
    public function registrarIngreso(Visit $visit)
    {
        if (!$visit->estaVigente()) {
            return back()->with('error', 'Esta visita ya no está vigente.');
        }

        $visit->registrarIngreso();

        return back()->with('success', "Ingreso registrado: {$visit->name}");
    }

    /**
     * API: Registrar salida de una visita.
     */
    public function registrarSalida(Visit $visit)
    {
        $visit->registrarSalida();

        return back()->with('success', "Salida registrada: {$visit->name}");
    }
}
