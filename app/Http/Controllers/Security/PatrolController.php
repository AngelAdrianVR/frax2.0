<?php

namespace App\Http\Controllers\Security;

use App\Models\Security\Patrol;
use App\Models\Community\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PatrolController extends Controller
{
    public function index(Request $request)
    {
        $patrols = Patrol::with('user')
            ->latest('start_time')
            ->paginate(15)
            ->through(function ($patrol) {
                return [
                    'id' => $patrol->id,
                    'guard_name' => $patrol->user->name ?? 'Guardia no asignado',
                    'start_time' => $patrol->start_time ? $patrol->start_time->format('d/m/Y H:i') : 'N/A',
                    'end_time' => $patrol->end_time ? $patrol->end_time->format('d/m/Y H:i') : 'En curso',
                    'status' => $patrol->status,
                    'scanned_points' => $patrol->scanned_points ?? 0,
                ];
            });

        return Inertia::render('Security/Patrols/Index', [
            'patrols' => $patrols
        ]);
    }

    public function create()
    {
        // Obtenemos los usuarios para asignarlos como guardias.
        // Tip: Si tienes un rol específico, puedes usar: User::role('Guardia')->get()
        $guards = User::select('id', 'name')->orderBy('name')->get();

        return Inertia::render('Security/Patrols/Create', [
            'guards' => $guards
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'status' => 'required|in:Activo,Terminado,Incidente',
            'scanned_points' => 'nullable|integer|min:0',
        ]);

        $validated['scanned_points'] = $validated['scanned_points'] ?? 0;

        Patrol::create($validated);

        return redirect()->route('patrols.index')->with('success', 'Rondín registrado exitosamente.');
    }

    public function edit(Patrol $patrol)
    {
        $guards = User::select('id', 'name')->orderBy('name')->get();

        return Inertia::render('Security/Patrols/Edit', [
            'patrol' => [
                'id' => $patrol->id,
                'user_id' => $patrol->user_id,
                // Formateamos para el input type="datetime-local" de HTML5
                'start_time' => $patrol->start_time ? $patrol->start_time->format('Y-m-d\TH:i') : null,
                'end_time' => $patrol->end_time ? $patrol->end_time->format('Y-m-d\TH:i') : null,
                'status' => $patrol->status,
                'scanned_points' => $patrol->scanned_points,
            ],
            'guards' => $guards
        ]);
    }

    public function update(Request $request, Patrol $patrol)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'status' => 'required|in:Activo,Terminado,Incidente',
            'scanned_points' => 'nullable|integer|min:0',
        ]);

        $validated['scanned_points'] = $validated['scanned_points'] ?? 0;

        $patrol->update($validated);

        return redirect()->route('patrols.index')->with('success', 'Rondín actualizado correctamente.');
    }

    public function destroy(Patrol $patrol)
    {
        $patrol->delete();

        return redirect()->route('patrols.index')->with('success', 'Rondín eliminado correctamente.');
    }
}