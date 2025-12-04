<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\PrivateUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;

class PetController extends Controller
{
    private function checkAdminRole($user)
    {
        $tableNames = config('permission.table_names');
        
        return DB::table($tableNames['model_has_roles'])
            ->join($tableNames['roles'], $tableNames['model_has_roles'] . '.role_id', '=', $tableNames['roles'] . '.id')
            ->where('model_id', $user->id)
            ->where('model_type', get_class($user))
            ->whereIn('name', ['Admin', 'Empleado'])
            ->exists();
    }

    public function index(Request $request)
    {
        $currentPropertyId = $request->user()->getCurrentPropertyId();

        $pets = Pet::query()
            ->where('private_unit_id', $currentPropertyId)
            ->with(['media'])
            ->latest()
            ->paginate(10)
            ->through(function ($pet) {
                return [
                    'id' => $pet->id,
                    'name' => $pet->name,
                    'species' => $pet->species,
                    'race' => $pet->race,
                    'additionals' => $pet->additionals,
                    'private_unit_id' => $pet->private_unit_id,
                    'photo_url' => $pet->getFirstMediaUrl('pets') ?: null,
                    // ACTUALIZADO: Ahora retornamos la colección completa de documentos, igual que en admin
                    'documents' => $pet->getMedia('documents')->map(function($media) {
                        return [
                            'id' => $media->id,
                            'url' => $media->getUrl(),
                            'name' => $media->file_name,
                            'mime_type' => $media->mime_type
                        ];
                    }),
                    'created_at' => $pet->created_at,
                ];
            });

        return Inertia::render('Pets/Index', [
            'pets' => $pets,
        ]);
    }

    public function adminIndex(Request $request)
    {
        $currentSubdivisionId = session('current_subdivision_id');

        if (!$currentSubdivisionId) {
            $currentSubdivisionId = DB::table('subdivision_user')
                ->where('user_id', $request->user()->id)
                ->value('subdivision_id');
        }

        if (!$currentSubdivisionId) {
            return redirect()->back()->with('error', 'No tienes un fraccionamiento asignado.');
        }

        $search = $request->input('search');

        $query = Pet::query()
            ->with(['privateUnit', 'media'])
            ->whereHas('privateUnit', function (Builder $q) use ($currentSubdivisionId) {
                $q->where('subdivision_id', $currentSubdivisionId);
            });

        if ($search) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('race', 'like', "%{$search}%")
                  ->orWhere('species', 'like', "%{$search}%")
                  ->orWhereHas('privateUnit', function (Builder $qUnit) use ($search) {
                      $qUnit->where('lot_number', 'like', "%{$search}%")
                            ->orWhere('unit_street', 'like', "%{$search}%")
                            ->orWhere('int_number', 'like', "%{$search}%");
                  });
            });
        }

        $pets = $query->latest()->paginate(15)->withQueryString();

        $pets->getCollection()->transform(function ($pet) {
            $primaryResident = $pet->privateUnit ? $pet->privateUnit->residents()->first() : null;
            $residentName = $primaryResident ? ($primaryResident->name . ' ' . $primaryResident->last_name) : 'N/A';

            return [
                'id' => $pet->id,
                'name' => $pet->name,
                'species' => $pet->species,
                'race' => $pet->race,
                'created_at' => $pet->created_at,
                'photo_url' => $pet->getFirstMediaUrl('pets') ?: null,
                'resident_name' => $residentName,
                'house_info' => $pet->privateUnit ? 
                    ($pet->privateUnit->unit_street . ' ' . $pet->privateUnit->exterior_number . ' ' . ($pet->privateUnit->int_number ? 'Int ' . $pet->privateUnit->int_number : '')) 
                    : 'Sin Asignar',
                
                'additionals' => $pet->additionals ?? [],
                'documents' => $pet->getMedia('documents')->map(function($media) {
                    return [
                        'id' => $media->id,
                        'url' => $media->getUrl(),
                        'name' => $media->file_name,
                        'mime_type' => $media->mime_type
                    ];
                }),
            ];
        });

        return Inertia::render('Pets/AdminIndex', [
            'pets' => $pets,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(Request $request)
    {
        $user = $request->user();
        $isAdmin = $this->checkAdminRole($user);
        
        $privateUnits = [];

        if ($isAdmin) {
            $privateUnits = PrivateUnit::select('id', 'lot_number', 'unit_street', 'int_number', 'exterior_number')
                ->get()
                ->map(function ($unit) {
                    $label = "Lote: {$unit->lot_number}";
                    if ($unit->unit_street) {
                        $label .= " - {$unit->unit_street} #{$unit->exterior_number}";
                    }
                    if ($unit->int_number) {
                        $label .= " Int. {$unit->int_number}";
                    }
                    return [
                        'id' => $unit->id,
                        'label' => $label,
                    ];
                });
        }

        return Inertia::render('Pets/Create', [
            'privateUnits' => $privateUnits,
            'isAdmin' => $isAdmin,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $isAdmin = $this->checkAdminRole($user);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'species' => 'required|string|max:50',
            'race' => 'nullable|string|max:100',
            'photo' => 'nullable|image|max:5120',
            'additionals' => 'nullable|array',
            'additionals.chip_id' => 'nullable|string|max:50',
            'additionals.pedigree' => 'nullable|string|max:50',
            'additionals.sterilized' => 'nullable|boolean',
            'additionals.vaccinated' => 'nullable|boolean',
            'additionals.notes' => 'nullable|string|max:500',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:jpg,jpeg,png,pdf|max:10240',
            'private_unit_id' => [Rule::requiredIf($isAdmin), 'exists:private_units,id'],
        ]);

        if ($isAdmin) {
            $targetUnitId = $request->private_unit_id;
        } else {
            $targetUnitId = $user->getCurrentPropertyId();
        }

        $pet = Pet::create([
            'name' => $validated['name'],
            'species' => $validated['species'],
            'race' => $validated['race'],
            'private_unit_id' => $targetUnitId,
            'additionals' => $validated['additionals'] ?? null,
        ]);

        if ($request->hasFile('photo')) {
            $pet->addMediaFromRequest('photo')->toMediaCollection('pets');
        }

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $pet->addMedia($file)->toMediaCollection('documents');
            }
        }

        $route = $isAdmin ? 'admin.pets.index' : 'pets.index';

        return Redirect::route($route)->with('success', 'Mascota registrada correctamente.');
    }

    public function update(Request $request, Pet $pet)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'species' => 'required|string|max:50',
            'race' => 'nullable|string|max:100',
            'photo' => 'nullable|image|max:5120',
            
            'additionals' => 'nullable|array',
            'additionals.chip_id' => 'nullable|string|max:50',
            'additionals.pedigree' => 'nullable|string|max:50',
            'additionals.sterilized' => 'nullable|boolean',
            'additionals.vaccinated' => 'nullable|boolean',
            'additionals.notes' => 'nullable|string|max:500',

            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        $pet->update([
            'name' => $validated['name'],
            'species' => $validated['species'],
            'race' => $validated['race'],
            'additionals' => $validated['additionals'] ?? null,
        ]);

        if ($request->hasFile('photo')) {
            $pet->clearMediaCollection('pets');
            $pet->addMediaFromRequest('photo')->toMediaCollection('pets');
        }

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $pet->addMedia($file)->toMediaCollection('documents');
            }
        }

        return Redirect::back()->with('success', 'Mascota actualizada correctamente.');
    }

    public function destroy(Pet $pet)
    {
        $pet->delete();
        return Redirect::back()->with('success', 'Mascota eliminada correctamente.');
    }
}