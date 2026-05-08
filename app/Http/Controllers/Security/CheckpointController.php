<?php

namespace App\Http\Controllers\Security;

use App\Models\Security\Checkpoint;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CheckpointController extends Controller
{
    public function index(Request $request)
    {
        $checkpoints = Checkpoint::latest()
            ->paginate(15)
            ->through(function ($checkpoint) {
                return [
                    'id' => $checkpoint->id,
                    'name' => $checkpoint->name,
                    'coordinates' => $checkpoint->coordinates ?? 'No definidas',
                    'tag_nfc_id' => $checkpoint->tag_nfc_id ?? 'Sin Tag Asignado',
                ];
            });

        return Inertia::render('Security/Checkpoints/Index', [
            'checkpoints' => $checkpoints
        ]);
    }

    public function create()
    {
        return Inertia::render('Security/Checkpoints/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'coordinates' => 'nullable|string|max:255',
            'tag_nfc_id' => 'nullable|string|max:255|unique:checkpoints,tag_nfc_id',
        ]);

        Checkpoint::create($validated);

        return redirect()->route('checkpoints.index')->with('success', 'Punto de control creado exitosamente.');
    }

    public function show(Checkpoint $checkpoint)
    {
        // Cargamos el punto de control junto con sus últimos 10 escaneos (si tienes la relación)
        $checkpoint->load(['logs' => function ($query) {
            $query->latest()->take(10);
        }]);

        return Inertia::render('Security/Checkpoints/Show', [
            'checkpoint' => $checkpoint
        ]);
    }

    public function edit(Checkpoint $checkpoint)
    {
        return Inertia::render('Security/Checkpoints/Edit', [
            'checkpoint' => $checkpoint
        ]);
    }

    public function update(Request $request, Checkpoint $checkpoint)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'coordinates' => 'nullable|string|max:255',
            'tag_nfc_id' => 'nullable|string|max:255|unique:checkpoints,tag_nfc_id,' . $checkpoint->id,
        ]);

        $checkpoint->update($validated);

        return redirect()->route('checkpoints.index')->with('success', 'Punto de control actualizado exitosamente.');
    }

    public function destroy(Checkpoint $checkpoint)
    {
        $checkpoint->delete();

        return redirect()->route('checkpoints.index')->with('success', 'Punto de control eliminado.');
    }
}