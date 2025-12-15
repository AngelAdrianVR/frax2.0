<?php

namespace App\Http\Controllers;

use App\Models\PrivateUnit;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class PrivateUnitController extends Controller
{
    /**
     * Helper privado para obtener el ID del fraccionamiento actual.
     * Sigue la lógica de sesión -> DB -> null.
     */
    private function getCurrentSubdivisionId(Request $request)
    {
        $currentSubdivisionId = session('current_subdivision_id');

        if (!$currentSubdivisionId) {
            $currentSubdivisionId = DB::table('subdivision_user')
                ->where('user_id', $request->user()->id)
                ->value('subdivision_id');
            
            // Opcional: Persistir en sesión para futuras peticiones
            if ($currentSubdivisionId) {
                session(['current_subdivision_id' => $currentSubdivisionId]);
            }
        }

        return $currentSubdivisionId;
    }

    /**
     * Muestra el listado de unidades privadas del fraccionamiento activo.
     */
    public function index(Request $request)
    {
        $currentSubdivisionId = $this->getCurrentSubdivisionId($request);

        if (!$currentSubdivisionId) {
            return redirect()->back()->with('error', 'No tienes un fraccionamiento asignado o seleccionado.');
        }

        $search = $request->input('search');

        $units = PrivateUnit::query()
            // FILTRO MULTI-TENANCY: Solo unidades del fraccionamiento actual
            ->where('subdivision_id', $currentSubdivisionId)
            ->with(['residents' => function ($query) {
                // Traemos solo a los dueños para mostrar en la lista
                $query->wherePivot('role_in_unit', 'Dueño')
                      ->select('residents.id', 'residents.full_name');
            }])
            // Filtros de búsqueda
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('unit_street', 'like', "%{$search}%")
                      ->orWhere('exterior_number', 'like', "%{$search}%")
                      ->orWhere('lot_number', 'like', "%{$search}%")
                      ->orWhereHas('residents', function (Builder $q) use ($search) {
                          $q->where('full_name', 'like', "%{$search}%")
                            // CORRECCIÓN SQL: Usar el nombre de la tabla pivote explícitamente
                            // en lugar de wherePivot() que falla dentro de whereHas()
                            ->where('residence_units.role_in_unit', 'Dueño');
                      });
                });
            })
            // Para el semáforo: Contamos deudas vencidas y pendientes
            ->withCount([
                'generatedFees as overdue_fees_count' => function ($query) {
                    $query->whereIn('status', ['Pendiente', 'Parcial', 'Atrasada'])
                          ->where('expiration_date', '<', now());
                },
                'generatedFees as pending_fees_count' => function ($query) {
                    $query->whereIn('status', ['Pendiente', 'Parcial'])
                          ->where('expiration_date', '>=', now());
                }
            ])
            ->orderBy('unit_street')
            ->orderBy('exterior_number')
            ->paginate(50)
            ->withQueryString();

        // Transformación de datos para la vista
        $units->through(function ($unit) {
            
            // Lógica del semáforo
            $paymentStatus = 'green'; // Al corriente
            if ($unit->overdue_fees_count > 0) {
                $paymentStatus = 'red'; // Vencido
            } elseif ($unit->pending_fees_count > 0) {
                $paymentStatus = 'amber'; // Atrasado pero no vencido (Pendiente)
            }

            // Obtener nombre del propietario principal (o el primero que encuentre)
            $owner = $unit->residents->first();

            return [
                'id' => $unit->id,
                'lot_number' => $unit->lot_number,
                'unit_street' => $unit->unit_street,
                'exterior_number' => $unit->exterior_number,
                'int_number' => $unit->int_number,
                'full_address' => trim("{$unit->unit_street} {$unit->exterior_number}" . ($unit->int_number ? " Int. {$unit->int_number}" : "")),
                'owner_name' => $owner ? $owner->full_name : 'Sin propietario asignado',
                'status' => $unit->status, // Activo / Inactivo
                'access_block' => $unit->access_block,
                'payment_status' => $paymentStatus,
                'square_meters' => $unit->square_meters,
            ];
        });

        return Inertia::render('PrivateUnits/Index', [
            'units' => $units,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Muestra el formulario para crear una nueva unidad.
     */
    public function create()
    {
        // Redirige a la vista dedicada de creación (aún por crear)
        return Inertia::render('PrivateUnits/Create');
    }

    /**
     * Almacena una nueva unidad en el fraccionamiento actual.
     */
    public function store(Request $request)
    {
        $currentSubdivisionId = $this->getCurrentSubdivisionId($request);

        if (!$currentSubdivisionId) {
            return redirect()->back()->with('error', 'Error de sesión: No se identificó el fraccionamiento.');
        }

        $validated = $request->validate([
            'lot_number' => 'required|string|max:20',
            'unit_street' => 'nullable|string|max:100',
            'exterior_number' => 'nullable|string|max:20',
            'int_number' => 'nullable|string|max:20',
            'square_meters' => 'nullable|numeric',
            'status' => 'required|in:Activo,Inactivo'
        ]);

        // Inyectamos el ID del fraccionamiento
        $validated['subdivision_id'] = $currentSubdivisionId;

        PrivateUnit::create($validated);

        // Redirigir al index tras crear
        return redirect()->route('admin.private-units.index')->with('success', 'Unidad creada correctamente.');
    }

    /**
     * Muestra el detalle de una unidad específica con sus relaciones.
     */
    public function show(Request $request, PrivateUnit $privateUnit)
    {
        $currentSubdivisionId = $this->getCurrentSubdivisionId($request);
        
        if ($privateUnit->subdivision_id != $currentSubdivisionId) {
            abort(403, 'No tienes permiso para ver esta unidad.');
        }

        // Cargar relaciones necesarias para las pestañas
        $privateUnit->load([
            'residents', // Pivote incluye role_in_unit
            'vehicles',
            'pets',
            'generatedFees' => function($q) {
                $q->latest('expiration_date')->take(10); // Últimos 10 pagos/deudas
            },
            'visits' => function($q) {
                $q->latest('created_at')->take(20); // Últimas 20 visitas
            }
        ]);

        // Cálculo rápido de estado financiero para la vista
        $pendingFees = $privateUnit->generatedFees->whereIn('status', ['Pendiente', 'Parcial', 'Atrasada'])->count();
        $financialStatus = $pendingFees > 0 ? 'con_adeudo' : 'al_corriente';

        return Inertia::render('PrivateUnits/Show', [
            'unit' => $privateUnit,
            'financialStatus' => $financialStatus
        ]);
    }

    /**
     * Actualiza la unidad especificada.
     */
    public function update(Request $request, PrivateUnit $privateUnit)
    {
        // Validación de seguridad
        $currentSubdivisionId = $this->getCurrentSubdivisionId($request);
        if ($privateUnit->subdivision_id != $currentSubdivisionId) {
            abort(403, 'No tienes permiso para editar esta unidad.');
        }

        $validated = $request->validate([
            'lot_number' => 'required|string|max:20',
            'unit_street' => 'nullable|string|max:100',
            'exterior_number' => 'nullable|string|max:20',
            'int_number' => 'nullable|string|max:20',
            'square_meters' => 'nullable|numeric',
            'status' => 'required|in:Activo,Inactivo',
            'access_block' => 'boolean'
        ]);

        $privateUnit->update($validated);

        return redirect()->back()->with('success', 'Unidad actualizada correctamente.');
    }

    /**
     * Cambia el estatus de la unidad (Inactivación rápida).
     */
    public function toggleStatus(Request $request, PrivateUnit $privateUnit)
    {
        $currentSubdivisionId = $this->getCurrentSubdivisionId($request);
        if ($privateUnit->subdivision_id != $currentSubdivisionId) {
            abort(403, 'No autorizado.');
        }

        $newStatus = $privateUnit->status === 'Activo' ? 'Inactivo' : 'Activo';
        $privateUnit->update(['status' => $newStatus]);

        return redirect()->back()->with('success', "La unidad ahora está {$newStatus}.");
    }

    /**
     * Elimina la unidad.
     */
    public function destroy(Request $request, PrivateUnit $privateUnit)
    {
        $currentSubdivisionId = $this->getCurrentSubdivisionId($request);
        if ($privateUnit->subdivision_id != $currentSubdivisionId) {
            abort(403, 'No autorizado.');
        }

        $privateUnit->delete();
        return redirect()->back()->with('success', 'Unidad eliminada correctamente.');
    }


    // ----------- MÓDULO DE MOROSOS ------------

    public function slowPayersIndex(Request $request)
    {
        $currentSubdivisionId = $this->getCurrentSubdivisionId($request);
        
        // Obtenemos unidades con su deuda calculada
        $debtors = PrivateUnit::query()
            ->where('subdivision_id', $currentSubdivisionId)
            ->withTotalDebt() // Scope del modelo
            ->with(['residents' => function($q) {
                $q->wherePivot('role_in_unit', 'Dueño')->select('residents.id', 'residents.full_name');
            }])
            // Ordenamos por los que deben más
            ->orderByDesc('total_debt')
            ->paginate(50);

        $debtors->through(function ($unit) {
            return [
                'id' => $unit->id,
                'address' => trim("{$unit->unit_street} {$unit->exterior_number}"),
                'owner_name' => $unit->residents->first()->full_name ?? 'N/A',
                'total_debt' => (float) $unit->total_debt, // Casting a float para JS
                'status' => $unit->status,
                'is_debtor' => $unit->total_debt > 0
            ];
        });

        return Inertia::render('SlowPayers/Index', [
            'debtors' => $debtors
        ]);
    }

}