<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class VehicleController extends Controller
{
    /**
     * Muestra la lista de vehículos de la unidad actual.
     */
    public function index(Request $request)
    {
        $currentPropertyId = $request->user()->getCurrentPropertyId();

        // Obtenemos los vehículos y cargamos la relación media
        $vehicles = Vehicle::with(['resident', 'media'])
            ->where('private_unit_id', $currentPropertyId)
            ->latest()
            ->paginate(10);

        // Transformamos la colección para incluir la URL de la foto de forma sencilla
        $vehicles->getCollection()->transform(function ($vehicle) {
            return [
                'id' => $vehicle->id,
                'plate' => $vehicle->plate,
                'brand' => $vehicle->brand,
                'model' => $vehicle->model,
                'color' => $vehicle->color,
                'tag_access' => $vehicle->tag_access,
                'created_at' => $vehicle->created_at,
                // Obtenemos la URL de Spatie Media Library (o null si no hay)
                'photo_url' => $vehicle->getFirstMediaUrl('vehicles') ?: null,
            ];
        });

        return Inertia::render('Vehicles/Index', [
            'vehicles' => $vehicles
        ]);
    }

    public function create()
    {
        return Inertia::render('Vehicles/Create');
    }

    /**
     * Almacena un nuevo vehículo en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'plate' => 'required|string|max:20',
            'brand' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'color' => 'required|string|max:30',
            'tag_access' => 'nullable|string|max:50',
            'photo' => 'nullable|image|max:5120',
        ]);

        $currentPropertyId = $request->user()->getCurrentPropertyId();

        $vehicle = Vehicle::create([
            'private_unit_id' => $currentPropertyId,
            'resident_id'     => $request->resident_id,
            'plate'           => $request->plate,
            'brand'           => $request->brand,
            'model'           => $request->model,
            'color'           => $request->color,
            'tag_access'      => $request->tag_access,
        ]);

        if ($request->hasFile('photo')) {
            $vehicle->addMediaFromRequest('photo')
                    ->toMediaCollection('vehicles');
        }

        return redirect()->route('vehicles.index')
            ->with('success', 'Vehículo registrado exitosamente.');
    }

    public function show(Vehicle $vehicle)
    {
        // Generalmente no se usa si mostramos detalles en modal o index
    }

    public function edit(Vehicle $vehicle)
    {
        // Mantenemos esto por si acceden por URL directa, 
        // aunque el Index ahora maneja la edición en modal.
        return Inertia::render('Vehicles/Edit', [
            'vehicle' => $vehicle
        ]);
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        // 1. Validamos. Usamos 'sometimes' para permitir actualizaciones parciales si fuera necesario
        // y validamos que la placa sea única excepto para este vehículo (opcional según tu lógica de negocio)
        $validated = $request->validate([
            'plate' => 'required|string|max:20',
            'brand' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'color' => 'required|string|max:30',
            'tag_access' => 'nullable|string|max:50',
            'photo' => 'nullable|image|max:5120', 
        ]);

        // 2. Actualizamos datos básicos
        $vehicle->update([
            'plate'      => $validated['plate'],
            'brand'      => $validated['brand'],
            'model'      => $validated['model'],
            'color'      => $validated['color'],
            'tag_access' => $validated['tag_access'] ?? null,
        ]);

        // 3. Manejo de imagen (Spatie Media Library)
        if ($request->hasFile('photo')) {
            // Borramos la imagen anterior de la colección 'vehicles'
            $vehicle->clearMediaCollection('vehicles');
            
            // Agregamos la nueva
            $vehicle->addMediaFromRequest('photo')
                    ->toMediaCollection('vehicles');
        }

        return redirect()->back()
            ->with('success', 'Vehículo actualizado correctamente.');
    }

    public function destroy(Vehicle $vehicle)
    {
        // Spatie borra automáticamente los archivos asociados al borrar el modelo
        $vehicle->delete();

        return redirect()->route('vehicles.index')
            ->with('success', 'Vehículo eliminado correctamente.');
    }
}