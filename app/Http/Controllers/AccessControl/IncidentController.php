<?php

namespace App\Http\Controllers\AccessControl;

use App\Http\Controllers\Controller;
use App\Models\AccessControl\Incident;
use App\Models\AccessControl\Patrol;
use App\Models\Community\PrivateUnit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IncidentController extends Controller
{
    public function index(Request $request)
    {
        $query = Incident::with(['reportedBy', 'privateUnit'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('severity')) {
            $query->where('severity', $request->input('severity'));
        }

        $incidents = $query->paginate(15)
            ->through(fn($incident) => [
                'id'            => $incident->id,
                'title'         => $incident->title,
                'severity'      => $incident->severity,
                'severityColor' => $incident->severidadColor(),
                'status'        => $incident->status,
                'incident_type' => $incident->incident_type,
                'prioritaria'   => $incident->esPrioritaria(),
                'guardia'       => $incident->reportedBy?->name ?? 'N/A',
                'unidad'        => $incident->privateUnit?->lot_number,
                'reported_at'   => $incident->created_at?->format('d/m/Y H:i'),
            ]);

        return Inertia::render('AccessControl/Incidents/Index', [
            'incidents' => $incidents,
            'filters'   => $request->only('status', 'severity'),
        ]);
    }

    public function create()
    {
        return Inertia::render('AccessControl/Incidents/Create', [
            'privateUnits' => PrivateUnit::orderBy('lot_number')->get(),
            'patrolsActivos' => Patrol::activos()
                ->with('user')
                ->get()
                ->map(fn($p) => [
                    'id'       => $p->id,
                    'guardia'  => $p->user?->name ?? 'N/A',
                    'inicio'   => $p->start_time?->format('H:i'),
                ]),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'                => 'required|string|max:150',
            'description'          => 'nullable|string|max:2000',
            'severity'             => 'required|in:Baja,Media,Alta,Critica',
            'incident_type'        => 'required|in:Seguridad,Trafico,Danios,Ruido,Emergencia,Otro',
            'location_description' => 'nullable|string|max:255',
            'foto_url'             => 'nullable|string|max:500',
            'private_unit_id'      => 'nullable|exists:private_units,id',
            'patrol_id'            => 'nullable|exists:patrols,id',
        ]);

        $validated['reported_by_user_id'] = $request->user()->id;
        $validated['reported_at'] = now();
        $validated['status'] = Incident::STATUS_ABIERTO;

        Incident::create($validated);

        // Si está vinculado a un rondín, marcarlo como Incidente
        if ($request->filled('patrol_id')) {
            $patrol = Patrol::find($request->input('patrol_id'));
            if ($patrol && $patrol->estaActivo()) {
                $patrol->marcarIncidente();
            }
        }

        return redirect()->route('incidents.index')
            ->with('success', 'Incidencia reportada exitosamente.');
    }

    public function show(Incident $incident)
    {
        $incident->load(['reportedBy', 'privateUnit', 'patrol.user']);

        return Inertia::render('AccessControl/Incidents/Show', [
            'incident' => [
                'id'                  => $incident->id,
                'title'               => $incident->title,
                'description'         => $incident->description,
                'severity'            => $incident->severity,
                'severityColor'       => $incident->severidadColor(),
                'status'              => $incident->status,
                'incident_type'       => $incident->incident_type,
                'foto_url'            => $incident->foto_url,
                'location_description' => $incident->location_description,
                'prioritaria'         => $incident->esPrioritaria(),
                'reported_at'         => $incident->reported_at?->format('d/m/Y H:i'),
                'resolved_at'         => $incident->resolved_at?->format('d/m/Y H:i'),
                'guardia'             => $incident->reportedBy?->name ?? 'N/A',
                'unidad'              => $incident->privateUnit?->lot_number,
                'patrol'              => $incident->patrol ? [
                    'id'       => $incident->patrol->id,
                    'guardia'  => $incident->patrol->user?->name,
                    'inicio'   => $incident->patrol->start_time?->format('d/m/Y H:i'),
                ] : null,
            ],
        ]);
    }

    public function edit(Incident $incident)
    {
        return Inertia::render('AccessControl/Incidents/Edit', [
            'incident' => $incident->only([
                'id', 'title', 'description', 'severity',
                'status', 'incident_type', 'foto_url',
                'location_description', 'private_unit_id', 'patrol_id',
            ]),
            'privateUnits' => PrivateUnit::orderBy('lot_number')->get(),
            'patrolsActivos' => Patrol::activos()->with('user')->get(),
        ]);
    }

    public function update(Request $request, Incident $incident)
    {
        $validated = $request->validate([
            'title'                => 'required|string|max:150',
            'description'          => 'nullable|string|max:2000',
            'severity'             => 'required|in:Baja,Media,Alta,Critica',
            'status'               => 'required|in:Abierto,EnProceso,Resuelto,Cerrado',
            'incident_type'        => 'required|in:Seguridad,Trafico,Danios,Ruido,Emergencia,Otro',
            'location_description' => 'nullable|string|max:255',
            'foto_url'             => 'nullable|string|max:500',
            'private_unit_id'      => 'nullable|exists:private_units,id',
            'patrol_id'            => 'nullable|exists:patrols,id',
        ]);

        // Si cambia a Resuelto, registrar fecha
        if ($validated['status'] === Incident::STATUS_RESUELTO
            && $incident->status !== Incident::STATUS_RESUELTO) {
            $validated['resolved_at'] = now();
        }

        $incident->update($validated);

        return redirect()->route('incidents.index')
            ->with('success', 'Incidencia actualizada correctamente.');
    }

    public function destroy(Incident $incident)
    {
        $incident->delete();

        return redirect()->route('incidents.index')
            ->with('success', 'Incidencia eliminada.');
    }

    // ─── Acciones Rápidas ───────────────────────────────────────────

    /**
     * Marcar incidencia como resuelta (desde el dashboard).
     */
    public function resolver(Incident $incident)
    {
        $incident->resolver();

        return back()->with('success', 'Incidencia marcada como resuelta.');
    }

    /**
     * Iniciar atención de una incidencia.
     */
    public function iniciarAtencion(Incident $incident)
    {
        $incident->iniciarAtencion();

        return back()->with('success', 'Atención de incidencia iniciada.');
    }
}
