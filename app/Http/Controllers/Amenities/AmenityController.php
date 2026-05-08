<?php

namespace App\Http\Controllers\Amenities;

use App\Models\Amenities\Amenity;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class AmenityController extends Controller
{
    private function checkAdminRole($user)
    {
        // Tu lógica existente de roles...
        $tableNames = config('permission.table_names');
        return DB::table($tableNames['model_has_roles'])
            ->join($tableNames['roles'], $tableNames['model_has_roles'] . '.role_id', '=', $tableNames['roles'] . '.id')
            ->where('model_id', $user->id)
            ->where('model_type', get_class($user))
            ->whereIn('name', ['Admin', 'Empleado'])
            ->exists();
    }

    private function getSubdivisionId($user, $isAdmin)
    {
        if ($isAdmin) {
            return session('current_subdivision_id') ?? DB::table('subdivision_user')->where('user_id', $user->id)->value('subdivision_id');
        }
        $currentPropertyId = $user->getCurrentPropertyId(); 
        $unit = \App\Models\Community\PrivateUnit::find($currentPropertyId);
        return $unit ? $unit->subdivision_id : null;
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $isAdmin = $this->checkAdminRole($user);
        $subdivisionId = $this->getSubdivisionId($user, $isAdmin);

        if (!$subdivisionId) {
            return redirect()->back()->with('error', 'No se ha detectado un fraccionamiento activo.');
        }

        $query = Amenity::query()->where('subdivision_id', $subdivisionId);

        // Residentes solo ven activas, Admin ve todas
        if (!$isAdmin) {
            $query->where('is_active', true);
        }

        $amenities = $query->with(['media'])->get()->map(function ($amenity) {
            return [
                'id' => $amenity->id,
                'name' => $amenity->name,
                'description' => $amenity->description,
                'reservation_cost' => (float) $amenity->reservation_cost,
                'capacity' => $amenity->capacity,
                'mode' => $amenity->mode,
                'rules' => $amenity->rules, 
                'availability_schedule' => $amenity->availability_schedule,
                'photo_url' => $amenity->getFirstMediaUrl('cover') ?: null,
                'is_active' => (bool) $amenity->is_active,
            ];
        });

        return Inertia::render('Amenities/Catalog/Index', [
            'amenities' => $amenities,
        ]);
    }

    /**
     * Muestra la vista de creación (Create.vue)
     */
    public function create()
    {
        return Inertia::render('Amenities/Catalog/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'reservation_cost' => 'required|numeric|min:0',
            'capacity' => 'nullable|integer',
            'mode' => 'required|in:Exclusivo,Compartido',
            'photo' => 'nullable|image|max:5120',
            'rules' => 'nullable|array',
            'availability_schedule' => 'nullable|array', 
        ]);

        $user = $request->user();
        $subdivisionId = $this->getSubdivisionId($user, true); // Asumimos que solo admin crea

        $amenity = Amenity::create([
            ...$validated,
            'subdivision_id' => $subdivisionId,
            'buffer_minutes' => 30,
            'max_days_advance' => 30,
            'is_active' => true 
        ]);

        if ($request->hasFile('photo')) {
            $amenity->addMediaFromRequest('photo')->toMediaCollection('cover');
        }

        return Redirect::route('amenities.index')->with('success', 'Amenidad creada con éxito.');
    }

    /**
     * Muestra la vista de edición (Edit.vue)
     */
    public function edit(Amenity $amenity)
    {
        // Preparamos los datos igual que en el index para que Vue los reciba limpios
        $amenityData = [
            'id' => $amenity->id,
            'name' => $amenity->name,
            'description' => $amenity->description,
            'reservation_cost' => (float) $amenity->reservation_cost,
            'capacity' => $amenity->capacity,
            'mode' => $amenity->mode,
            'rules' => $amenity->rules, 
            'availability_schedule' => $amenity->availability_schedule,
            'photo_url' => $amenity->getFirstMediaUrl('cover') ?: null,
            'is_active' => (bool) $amenity->is_active,
        ];

        return Inertia::render('Amenities/Catalog/Edit', [
            'amenity' => $amenityData
        ]);
    }

    /**
     * Actualiza la amenidad en base de datos
     */
    public function update(Request $request, Amenity $amenity)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'reservation_cost' => 'required|numeric|min:0',
            'capacity' => 'nullable|integer',
            'mode' => 'required|in:Exclusivo,Compartido',
            'photo' => 'nullable|image|max:5120', 
            'rules' => 'nullable|array',
            'availability_schedule' => 'nullable|array', 
        ]);

        // Actualizamos los campos básicos
        $amenity->update($validated);

        // Si se subió una NUEVA foto, borramos la anterior y guardamos la nueva
        if ($request->hasFile('photo')) {
            $amenity->clearMediaCollection('cover');
            $amenity->addMediaFromRequest('photo')->toMediaCollection('cover');
        }

        return Redirect::route('amenities.index')->with('success', 'Amenidad actualizada correctamente.');
    }

    public function toggleStatus(Amenity $amenity)
    {
        $amenity->is_active = !$amenity->is_active;
        $amenity->save();

        return Redirect::back()->with('success', 'Estado de la amenidad actualizado.');
    }

    public function destroy(Amenity $amenity)
    {
        $amenity->delete();
        return Redirect::back()->with('success', 'Amenidad eliminada.');
    }
}