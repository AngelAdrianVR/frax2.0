<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class PetController extends Controller
{
    public function index(Request $request)
    {
        $currentPropertyId = $request->user()->getCurrentPropertyId();

        // Obtener mascotas con paginación
        // NOTA: Ajusta el 'where' según tu lógica de usuario (ej. auth()->user()->resident->id)
        $pets = Pet::query()
            ->where('private_unit_id', $currentPropertyId)
            ->with(['media']) // Cargar medios de Spatie
            // ->where('resident_id', auth()->user()->resident_id) // Descomentar si es vista de residente
            ->latest()
            ->paginate(10)
            ->through(function ($pet) {
                return [
                    'id' => $pet->id,
                    'name' => $pet->name,
                    'species' => $pet->species, // Perro, Gato, etc.
                    'race' => $pet->race,       // Labrador, Siamés, etc.
                    'additionals' => $pet->additionals,
                    'private_unit_id' => $pet->private_unit_id,
                    // Obtener URL de la imagen usando Spatie Media Library
                    'photo_url' => $pet->getFirstMediaUrl('pets') ?: null,
                    'created_at' => $pet->created_at,
                ];
            });

        return Inertia::render('Pets/Index', [
            'pets' => $pets,
        ]);
    }

    public function create()
    {
        return Inertia::render('Pets/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'species' => 'required|string|max:50',
            'race' => 'nullable|string|max:100',
            'photo' => 'nullable|image|max:2048', // 2MB Max
            'private_unit_id' => 'required|numeric',
        ]);

        $pet = Pet::create($validated);

        if ($request->hasFile('photo')) {
            $pet->addMediaFromRequest('photo')->toMediaCollection('pets');
        }

        return Redirect::route('pets.index')->with('success', 'Mascota registrada correctamente.');
    }

    public function show(Pet $pet)
    {
        //
    }

    public function edit(Pet $pet)
    {
        //
    }

    public function update(Request $request, Pet $pet)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'species' => 'required|string|max:50',
            'race' => 'nullable|string|max:100',
            'photo' => 'nullable|image|max:2048',
        ]);

        $pet->update($validated);

        // Manejo de nueva foto con Spatie
        if ($request->hasFile('photo')) {
            $pet->clearMediaCollection('pets'); // Borrar anterior
            $pet->addMediaFromRequest('photo')->toMediaCollection('pets');
        }

        return Redirect::back()->with('success', 'Mascota actualizada correctamente.');
    }

    public function destroy(Pet $pet)
    {
        $pet->delete();
        // Spatie borra los archivos físicos automáticamente al eliminar el modelo
        return Redirect::back()->with('success', 'Mascota eliminada correctamente.');
    }
}