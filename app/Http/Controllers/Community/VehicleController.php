<?php

namespace App\Http\Controllers\Community;

use App\Models\Community\Vehicle;
use App\Models\Community\PrivateUnit;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class VehicleController extends Controller
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

        $vehicles = Vehicle::with(['user', 'media']) 
            ->where('private_unit_id', $currentPropertyId)
            ->latest()
            ->paginate(10);

        $this->transformVehicles($vehicles);

        return Inertia::render('Community/Vehicles/Index', [
            'vehicles' => $vehicles
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

        $query = Vehicle::query()
            ->with(['user', 'privateUnit', 'media']) 
            ->whereHas('privateUnit', function (Builder $q) use ($currentSubdivisionId) {
                $q->where('subdivision_id', $currentSubdivisionId);
            });

        if ($search) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('plate', 'like', "%{$search}%")
                  ->orWhereHas('privateUnit', function (Builder $qUnit) use ($search) {
                      $qUnit->where('lot_number', 'like', "%{$search}%")
                            ->orWhere('unit_street', 'like', "%{$search}%")
                            ->orWhere('int_number', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function (Builder $qUser) use ($search) {
                      $qUser->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $vehicles = $query->latest()->paginate(15)->withQueryString();

        $vehicles->getCollection()->transform(function ($vehicle) {
            return [
                'id' => $vehicle->id,
                'plate' => $vehicle->plate,
                'brand' => $vehicle->brand,
                'model' => $vehicle->model,
                'color' => $vehicle->color,
                'tag_access' => $vehicle->tag_access,
                'created_at' => $vehicle->created_at,
                'photo_url' => $vehicle->getFirstMediaUrl('vehicles') ?: null,
                'resident_name' => $vehicle->user ? $vehicle->user->name : 'N/A', 
                'house_info' => $vehicle->privateUnit ? 
                    ($vehicle->privateUnit->unit_street . ' ' . $vehicle->privateUnit->exterior_number . ' ' . ($vehicle->privateUnit->int_number ? 'Int ' . $vehicle->privateUnit->int_number : '')) 
                    : 'Sin Asignar',
            ];
        });

        return Inertia::render('Community/Vehicles/AdminIndex', [
            'vehicles' => $vehicles,
            'filters' => $request->only(['search']),
        ]);
    }

    private function transformVehicles($paginator)
    {
        $paginator->getCollection()->transform(function ($vehicle) {
            return [
                'id' => $vehicle->id,
                'plate' => $vehicle->plate,
                'brand' => $vehicle->brand,
                'model' => $vehicle->model,
                'color' => $vehicle->color,
                'tag_access' => $vehicle->tag_access,
                'created_at' => $vehicle->created_at,
                'photo_url' => $vehicle->getFirstMediaUrl('vehicles') ?: null,
            ];
        });
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

        return Inertia::render('Community/Vehicles/Create', [
            'privateUnits' => $privateUnits,
            'isAdmin' => $isAdmin,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        
        $isAdmin = $this->checkAdminRole($user);

        $validated = $request->validate([
            'plate' => ['required', 'string', 'max:20', 'unique:vehicles'],
            'brand' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'color' => 'required|string|max:30',
            'tag_access' => 'nullable|string|max:50',
            'photo' => 'nullable|image|max:5120',
            'private_unit_id' => ['nullable', Rule::requiredIf($isAdmin), 'exists:private_units,id'],
        ]);

        if ($isAdmin) {
            $targetUnitId = $request->private_unit_id;
            
            $primaryUser = DB::table('private_unit_user')
                                ->where('private_unit_id', $targetUnitId)
                                ->where('role_in_unit', 'Dueño')
                                ->first();

            if (!$primaryUser) {
                $primaryUser = DB::table('private_unit_user')
                                    ->where('private_unit_id', $targetUnitId)
                                    ->first();
            }

            if (!$primaryUser) {
                return back()->withErrors(['private_unit_id' => 'La unidad seleccionada no tiene usuarios asignados.']);
            }

            $userId = $primaryUser->user_id;

        } else {
            $targetUnitId = $user->getCurrentPropertyId();
            $userId = $request->user_id ?? $user->id;
        }

        $vehicle = Vehicle::create([
            'private_unit_id' => $targetUnitId,
            'user_id'         => $userId, 
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

        $route = $isAdmin ? 'admin.vehicles.index' : 'vehicles.index';

        return redirect()->route($route)
            ->with('success', 'Vehículo registrado exitosamente.');
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $user = $request->user();
        
        $isAdmin = $this->checkAdminRole($user);

        $validated = $request->validate([
            'plate' => ['required', 'string', 'max:20', Rule::unique('vehicles')->ignore($vehicle->id)],
            'brand' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'color' => 'required|string|max:30',
            'tag_access' => 'nullable|string|max:50',
            'photo' => 'nullable|image|max:5120',
            'private_unit_id' => ['nullable', 'exists:private_units,id'], 
        ]);

        $dataToUpdate = [
            'plate'      => $validated['plate'],
            'brand'      => $validated['brand'],
            'model'      => $validated['model'],
            'color'      => $validated['color'],
            'tag_access' => $validated['tag_access'] ?? null,
        ];

        if ($isAdmin && $request->filled('private_unit_id') && $request->private_unit_id != $vehicle->private_unit_id) {
            $dataToUpdate['private_unit_id'] = $request->private_unit_id;
            
            $newPrimaryUser = DB::table('private_unit_user')
                ->where('private_unit_id', $request->private_unit_id)
                ->where('role_in_unit', 'Dueño')
                ->first() 
                ?? DB::table('private_unit_user')->where('private_unit_id', $request->private_unit_id)->first();
            
            if ($newPrimaryUser) {
                $dataToUpdate['user_id'] = $newPrimaryUser->user_id;
            }
        }

        $vehicle->update($dataToUpdate);

        if ($request->hasFile('photo')) {
            $vehicle->clearMediaCollection('vehicles');
            $vehicle->addMediaFromRequest('photo')
                    ->toMediaCollection('vehicles');
        }

        return redirect()->back()
            ->with('success', 'Vehículo actualizado correctamente.');
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();

        return redirect()->back()
            ->with('success', 'Vehículo eliminado correctamente.');
    }
}