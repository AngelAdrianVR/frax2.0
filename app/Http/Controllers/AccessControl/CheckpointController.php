<?php

namespace App\Http\Controllers\AccessControl;

use App\Http\Controllers\Controller;
use App\Models\AccessControl\Checkpoint;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CheckpointController extends Controller
{
    public function index()
    {
        $checkpoints = Checkpoint::latest()
            ->paginate(15)
            ->through(fn($cp) => [
                'id'           => $cp->id,
                'name'         => $cp->name,
                'coordinates'  => $cp->coordinates ?? 'No definidas',
                'tag_nfc_id'   => $cp->tag_nfc_id ?? 'Sin Tag',
                'tieneTag'     => $cp->tieneTag(),
                'totalEscaneos' => $cp->totalEscaneos(),
            ]);

        return Inertia::render('AccessControl/Checkpoints/Index', [
            'checkpoints' => $checkpoints,
        ]);
    }

    public function create()
    {
        return Inertia::render('AccessControl/Checkpoints/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'coordinates' => 'nullable|string|max:255',
            'tag_nfc_id'  => 'nullable|string|max:255|unique:checkpoints,tag_nfc_id',
        ]);

        Checkpoint::create($validated);

        return redirect()->route('checkpoints.index')
            ->with('success', 'Punto de control creado exitosamente.');
    }

    public function show(Checkpoint $checkpoint)
    {
        $checkpoint->load(['scans' => fn($q) => $q->recientes()->take(20)]);

        return Inertia::render('AccessControl/Checkpoints/Show', [
            'checkpoint' => [
                'id'            => $checkpoint->id,
                'name'          => $checkpoint->name,
                'coordinates'   => $checkpoint->coordinates,
                'tag_nfc_id'    => $checkpoint->tag_nfc_id,
                'tieneTag'      => $checkpoint->tieneTag(),
                'coordenadas'   => $checkpoint->coordenadasArray(),
                'totalEscaneos' => $checkpoint->totalEscaneos(),
                'ultimoEscaneo' => $checkpoint->ultimoEscaneo()?->scan_date_time?->format('d/m/Y H:i'),
                'scans'         => $checkpoint->scans->map(fn($s) => [
                    'fechaHora' => $s->scan_date_time?->format('d/m/Y H:i'),
                    'patrolId'  => $s->patrol_id,
                    'hace'      => $s->haceCuanto(),
                ]),
            ],
        ]);
    }

    public function edit(Checkpoint $checkpoint)
    {
        return Inertia::render('AccessControl/Checkpoints/Edit', [
            'checkpoint' => $checkpoint->only([
                'id', 'name', 'coordinates', 'tag_nfc_id',
            ]),
        ]);
    }

    public function update(Request $request, Checkpoint $checkpoint)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'coordinates' => 'nullable|string|max:255',
            'tag_nfc_id'  => 'nullable|string|max:255|unique:checkpoints,tag_nfc_id,' . $checkpoint->id,
        ]);

        $checkpoint->update($validated);

        return redirect()->route('checkpoints.index')
            ->with('success', 'Punto de control actualizado.');
    }

    public function destroy(Checkpoint $checkpoint)
    {
        $checkpoint->delete();

        return redirect()->route('checkpoints.index')
            ->with('success', 'Punto de control eliminado.');
    }
}
