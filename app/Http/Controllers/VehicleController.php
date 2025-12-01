<?php

namespace App\Http\Controllers;

use App\Models\PrivateUnit;
use App\Models\ResidenceUnit;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class VehicleController extends Controller
{
    /**
     * Helper para verificar rol ignorando el Team Scope de Spatie.
     * Verifica si el usuario tiene rol Admin/Empleado en CUALQUIER equipo o globalmente.
     */
    private function checkAdminRole($user)
    {
        $tableNames = config('permission.table_names');
        
        // Consulta directa a la base de datos para saltar la restricción de current_team_id
        return DB::table($tableNames['model_has_roles'])
            ->join($tableNames['roles'], $tableNames['model_has_roles'] . '.role_id', '=', $tableNames['roles'] . '.id')
            ->where('model_id', $user->id)
            ->where('model_type', get_class($user))
            ->whereIn('name', ['Admin', 'Empleado'])
            ->exists();
    }

    /**
     * Muestra la lista de vehículos de la unidad actual (VISTA RESIDENTE).
     */
    public function index(Request $request)
    {
        $currentPropertyId = $request->user()->getCurrentPropertyId();

        // Obtenemos los vehículos y cargamos la relación media
        $vehicles = Vehicle::with(['resident', 'media'])
            ->where('private_unit_id', $currentPropertyId)
            ->latest()
            ->paginate(10);

        // Transformamos la colección
        $this->transformVehicles($vehicles);

        return Inertia::render('Vehicles/Index', [
            'vehicles' => $vehicles
        ]);
    }

    /**
     * Muestra la lista de TODOS los vehículos del fraccionamiento (VISTA ADMIN).
     */
    public function adminIndex(Request $request)
    {
        // 1. Intentamos obtener el ID de la sesión
        $currentSubdivisionId = session('current_subdivision_id');

        // 2. Si no hay sesión, buscamos directamente en la tabla pivote evitando Eloquent
        // Esto soluciona el error "Unknown column role_in_subdivision"
        if (!$currentSubdivisionId) {
            $currentSubdivisionId = DB::table('subdivision_user')
                ->where('user_id', $request->user()->id)
                ->value('subdivision_id');
        }

        // Si aún así no hay fraccionamiento, podrías manejar el error o redirigir
        if (!$currentSubdivisionId) {
            return redirect()->back()->with('error', 'No tienes un fraccionamiento asignado.');
        }

        $search = $request->input('search');

        $query = Vehicle::query()
            ->with(['resident', 'privateUnit', 'media']) // Cargamos 'privateUnit' para ver la casa
            // Filtramos vehículos que pertenezcan a unidades DENTRO del fraccionamiento actual
            ->whereHas('privateUnit', function (Builder $q) use ($currentSubdivisionId) {
                $q->where('subdivision_id', $currentSubdivisionId);
            });

        // Lógica del Buscador
        if ($search) {
            $query->where(function (Builder $q) use ($search) {
                // Buscar por placa
                $q->where('plate', 'like', "%{$search}%")
                  // O buscar por datos de la casa (Calle o número de lote/interior)
                  ->orWhereHas('privateUnit', function (Builder $qUnit) use ($search) {
                      $qUnit->where('lot_number', 'like', "%{$search}%")
                            ->orWhere('unit_street', 'like', "%{$search}%")
                            ->orWhere('int_number', 'like', "%{$search}%");
                  })
                  // O buscar por nombre del residente
                  ->orWhereHas('resident', function (Builder $qRes) use ($search) {
                      $qRes->where('full_name', 'like', "%{$search}%");
                  });
            });
        }

        $vehicles = $query->latest()->paginate(15)->withQueryString();

        // Transformación personalizada para incluir datos de la casa
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
                // Datos extra para el admin
                'resident_name' => $vehicle->resident ? $vehicle->resident->full_name : 'N/A',
                'house_info' => $vehicle->privateUnit ? 
                    ($vehicle->privateUnit->unit_street . ' ' . $vehicle->privateUnit->exterior_number . ' ' . ($vehicle->privateUnit->int_number ? 'Int ' . $vehicle->privateUnit->int_number : '')) 
                    : 'Sin Asignar',
            ];
        });

        return Inertia::render('Vehicles/AdminIndex', [
            'vehicles' => $vehicles,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Helper para transformar la colección (reutilizable)
     */
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
        
        // USAMOS EL HELPER MANUAL
        $isAdmin = $this->checkAdminRole($user);
        
        $privateUnits = [];

        if ($isAdmin) {
            $privateUnits = PrivateUnit::select('id', 'lot_number', 'unit_street', 'int_number')
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

        return Inertia::render('Vehicles/Create', [
            'privateUnits' => $privateUnits,
            'isAdmin' => $isAdmin,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        
        // USAMOS EL HELPER MANUAL
        $isAdmin = $this->checkAdminRole($user);

        $validated = $request->validate([
            'plate' => ['required', 'string', 'max:20', 'unique:vehicles'],
            'brand' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'color' => 'required|string|max:30',
            'tag_access' => 'nullable|string|max:50',
            'photo' => 'nullable|image|max:5120',
            'private_unit_id' => [Rule::requiredIf($isAdmin), 'exists:private_units,id'],
        ]);

        if ($isAdmin) {
            $targetUnitId = $request->private_unit_id;
            
            // Buscar residente dueño de esa casa
            $primaryResident = ResidenceUnit::where('private_unit_id', $targetUnitId)
                                ->where('role_in_unit', 'Dueño')
                                ->first();

            if (!$primaryResident) {
                // Fallback a cualquier residente
                $primaryResident = ResidenceUnit::where('private_unit_id', $targetUnitId)->first();
            }

            if (!$primaryResident) {
                return back()->withErrors(['private_unit_id' => 'La unidad seleccionada no tiene residentes asignados.']);
            }

            $residentId = $primaryResident->resident_id;

        } else {
            // Usuario normal: usa su propiedad actual
            $targetUnitId = $user->getCurrentPropertyId();
            $residentId = $request->resident_id ?? $user->resident_id;
        }

        $vehicle = Vehicle::create([
            'private_unit_id' => $targetUnitId,
            'resident_id'     => $residentId,
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
        
        // USAMOS EL HELPER MANUAL
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