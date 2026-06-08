<?php

namespace App\Http\Controllers\AccessControl;

use App\Http\Controllers\Controller;
use App\Models\AccessControl\Patrol;
use App\Models\Community\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PatrolController extends Controller
{
    public function index(Request $request)
    {
        $query = Patrol::with('user')->latest('start_time');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $patrols = $query->paginate(15)
            ->through(fn($patrol) => [
                'id'             => $patrol->id,
                'guard_name'     => $patrol->user?->name ?? 'N/A',
                'start_time'     => $patrol->start_time?->format('d/m/Y H:i'),
                'end_time'       => $patrol->end_time?->format('d/m/Y H:i') ?? 'En curso',
                'status'         => $patrol->status,
                'scanned_points' => $patrol->scanned_points,
                'activo'         => $patrol->estaActivo(),
                'duracionMin'    => $patrol->duracionMinutos(),
            ]);

        return Inertia::render('AccessControl/Patrols/Index', [
            'patrols' => $patrols,
            'filters' => $request->only('status'),
        ]);
    }

    public function create()
    {
        return Inertia::render('AccessControl/Patrols/Create', [
            'guards' => User::select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'        => 'required|exists:users,id',
            'start_time'     => 'required|date',
            'end_time'       => 'nullable|date|after_or_equal:start_time',
            'status'         => 'required|in:Activo,Terminado,Incidente',
            'scanned_points' => 'nullable|integer|min:0',
        ]);

        $validated['scanned_points'] = $validated['scanned_points'] ?? 0;

        Patrol::create($validated);

        return redirect()->route('patrols.index')
            ->with('success', 'Rondín registrado exitosamente.');
    }

    public function show(Patrol $patrol)
    {
        $patrol->load(['user', 'scans.checkpoint', 'incidents']);

        return Inertia::render('AccessControl/Patrols/Show', [
            'patrol' => [
                'id'             => $patrol->id,
                'guardia'        => $patrol->user?->name ?? 'N/A',
                'start_time'     => $patrol->start_time?->format('d/m/Y H:i'),
                'end_time'       => $patrol->end_time?->format('d/m/Y H:i'),
                'status'         => $patrol->status,
                'activo'         => $patrol->estaActivo(),
                'scanned_points' => $patrol->scanned_points,
                'duracionMin'    => $patrol->duracionMinutos(),
                'scans'          => $patrol->scans->map(fn($s) => [
                    'checkpoint' => $s->nombreCheckpoint(),
                    'fechaHora'  => $s->scan_date_time?->format('d/m/Y H:i'),
                    'hace'       => $s->haceCuanto(),
                ]),
                'incidents' => $patrol->incidents->map(fn($i) => [
                    'id'        => $i->id,
                    'title'     => $i->title,
                    'severity'  => $i->severity,
                ]),
            ],
        ]);
    }

    public function edit(Patrol $patrol)
    {
        return Inertia::render('AccessControl/Patrols/Edit', [
            'patrol' => [
                'id'             => $patrol->id,
                'user_id'        => $patrol->user_id,
                'start_time'     => $patrol->start_time?->format('Y-m-d\TH:i'),
                'end_time'       => $patrol->end_time?->format('Y-m-d\TH:i'),
                'status'         => $patrol->status,
                'scanned_points' => $patrol->scanned_points,
            ],
            'guards' => User::select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Patrol $patrol)
    {
        $validated = $request->validate([
            'user_id'        => 'required|exists:users,id',
            'start_time'     => 'required|date',
            'end_time'       => 'nullable|date|after_or_equal:start_time',
            'status'         => 'required|in:Activo,Terminado,Incidente',
            'scanned_points' => 'nullable|integer|min:0',
        ]);

        $validated['scanned_points'] = $validated['scanned_points'] ?? 0;

        $patrol->update($validated);

        return redirect()->route('patrols.index')
            ->with('success', 'Rondín actualizado correctamente.');
    }

    public function destroy(Patrol $patrol)
    {
        $patrol->delete();

        return redirect()->route('patrols.index')
            ->with('success', 'Rondín eliminado.');
    }

    // ─── Acciones de Guardia ────────────────────────────────────────

    /**
     * Finalizar un rondín activo.
     */
    public function finalizar(Patrol $patrol)
    {
        if (!$patrol->estaActivo()) {
            return back()->with('error', 'Este rondín ya no está activo.');
        }

        $patrol->finalizar();

        return back()->with('success', 'Rondín finalizado correctamente.');
    }
}
