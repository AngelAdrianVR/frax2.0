<?php

namespace App\Http\Controllers\AccessControl;

use App\Http\Controllers\Controller;
use App\Models\AccessControl\FrequentVisitor;
use App\Models\Community\PrivateUnit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FrequentVisitorController extends Controller
{
    public function index(Request $request)
    {
        $query = FrequentVisitor::with('privateUnit')->latest();

        if ($request->user()->getCurrentPropertyId()) {
            $query->deUnidad($request->user()->getCurrentPropertyId());
        }

        if ($request->filled('search')) {
            $query->buscar($request->input('search'));
        }

        $visitors = $query->paginate(15)
            ->through(fn($visitor) => $visitor->resumenParaGuardia());

        return Inertia::render('AccessControl/FrequentVisitors/Index', [
            'visitors' => $visitors,
            'filters'  => $request->only('search'),
        ]);
    }

    public function create()
    {
        return Inertia::render('AccessControl/FrequentVisitors/Create', [
            'privateUnits' => PrivateUnit::orderBy('lot_number')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'alias'              => 'nullable|string|max:50',
            'name'               => 'required|string|max:100',
            'identification'     => 'nullable|string|max:50',
            'default_reason'     => 'nullable|string|max:100',
            'default_access_type' => 'required|in:Peatonal,Vehicular',
            'default_plate'      => 'nullable|string|max:20',
            'private_unit_id'    => 'required|exists:private_units,id',
        ]);

        FrequentVisitor::create($validated);

        return redirect()->route('frequent-visitors.index')
            ->with('success', 'Visitante frecuente registrado.');
    }

    public function edit(FrequentVisitor $frequentVisitor)
    {
        return Inertia::render('AccessControl/FrequentVisitors/Edit', [
            'visitor' => $frequentVisitor->only([
                'id', 'alias', 'name', 'identification',
                'default_reason', 'default_access_type',
                'default_plate', 'private_unit_id',
            ]),
            'privateUnits' => PrivateUnit::orderBy('lot_number')->get(),
        ]);
    }

    public function update(Request $request, FrequentVisitor $frequentVisitor)
    {
        $validated = $request->validate([
            'alias'              => 'nullable|string|max:50',
            'name'               => 'required|string|max:100',
            'identification'     => 'nullable|string|max:50',
            'default_reason'     => 'nullable|string|max:100',
            'default_access_type' => 'required|in:Peatonal,Vehicular',
            'default_plate'      => 'nullable|string|max:20',
            'private_unit_id'    => 'required|exists:private_units,id',
        ]);

        $frequentVisitor->update($validated);

        return redirect()->route('frequent-visitors.index')
            ->with('success', 'Visitante frecuente actualizado.');
    }

    public function destroy(FrequentVisitor $frequentVisitor)
    {
        $frequentVisitor->delete();

        return redirect()->route('frequent-visitors.index')
            ->with('success', 'Visitante frecuente eliminado.');
    }
}
