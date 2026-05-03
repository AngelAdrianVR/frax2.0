<?php

namespace App\Http\Controllers\Community;

use App\Models\Community\PrivateUnit;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class PrivateUnitController extends Controller
{
    /**
     * Helper privado para obtener el ID del fraccionamiento actual.
     */
    private function getCurrentSubdivisionId(Request $request)
    {
        $currentSubdivisionId = session('current_subdivision_id');

        if (!$currentSubdivisionId) {
            $currentSubdivisionId = DB::table('subdivision_user')
                ->where('user_id', $request->user()->id)
                ->value('subdivision_id');
            
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

        // --- 1. CÁLCULO DE KPIs (Estilo Dashboard) ---
        $totalUnits = PrivateUnit::where('subdivision_id', $currentSubdivisionId)->count();
        
        // Ocupación: Casas con al menos un usuario (Dueño/Inquilino) asignado
        $occupiedUnits = PrivateUnit::where('subdivision_id', $currentSubdivisionId)
            ->whereHas('users')
            ->count();
        $occupancyRate = $totalUnits > 0 ? round(($occupiedUnits / $totalUnits) * 100) : 0;

        // Caja Total (Suma de Deudas Pendientes de este fraccionamiento)
        $totalDebt = DB::table('generated_fees')
            ->join('private_units', 'generated_fees.private_unit_id', '=', 'private_units.id')
            ->where('private_units.subdivision_id', $currentSubdivisionId)
            ->whereIn('generated_fees.status', ['Pendiente', 'Parcial', 'Atrasada'])
            ->sum(DB::raw('generated_fees.total_amount - generated_fees.amount_paid'));

        // Índice de Morosidad: Cuántas unidades están al corriente vs cuántas deben
        $debtorUnitsCount = PrivateUnit::where('subdivision_id', $currentSubdivisionId)
            ->whereHas('generatedFees', function($q) {
                $q->whereIn('status', ['Pendiente', 'Parcial', 'Atrasada'])
                  ->whereRaw('(total_amount - amount_paid) > 0');
            })->count();
            
        $paidUnitsCount = $totalUnits - $debtorUnitsCount;
        $paymentRate = $totalUnits > 0 ? round(($paidUnitsCount / $totalUnits) * 100) : 0;

        $kpis = [
            'payment_rate' => $paymentRate,
            'debtor_units' => $debtorUnitsCount,
            'total_debt' => $totalDebt,
            'occupancy_rate' => $occupancyRate,
            'occupied_units' => $occupiedUnits,
            'total_units' => $totalUnits
        ];

        // --- 2. CONSULTA DE UNIDADES ---
        $units = PrivateUnit::query()
            ->where('subdivision_id', $currentSubdivisionId)
            ->with(['users' => function ($query) {
                $query->wherePivot('role_in_unit', 'Dueño')->select('users.id', 'users.name');
            }])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('unit_street', 'like', "%{$search}%")
                      ->orWhere('exterior_number', 'like', "%{$search}%")
                      ->orWhere('lot_number', 'like', "%{$search}%")
                      ->orWhereHas('users', function (Builder $q) use ($search) {
                          $q->where('name', 'like', "%{$search}%")
                            ->where('private_unit_user.role_in_unit', 'Dueño');
                      });
                });
            })
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

        $units->through(function ($unit) {
            $paymentStatus = 'green'; 
            if ($unit->overdue_fees_count > 0) {
                $paymentStatus = 'red'; 
            } elseif ($unit->pending_fees_count > 0) {
                $paymentStatus = 'amber'; 
            }

            $owner = $unit->users->first();

            return [
                'id' => $unit->id,
                'lot_number' => $unit->lot_number,
                'unit_street' => $unit->unit_street,
                'exterior_number' => $unit->exterior_number,
                'int_number' => $unit->int_number,
                'full_address' => trim("{$unit->unit_street} {$unit->exterior_number}" . ($unit->int_number ? " Int. {$unit->int_number}" : "")),
                'owner_name' => $owner ? $owner->name : 'Sin asignar',
                'status' => $unit->status,
                'access_block' => $unit->access_block,
                'payment_status' => $paymentStatus,
                'square_meters' => $unit->square_meters,
            ];
        });

        return Inertia::render('Community/PrivateUnits/Index', [
            'units' => $units,
            'filters' => $request->only(['search']),
            'kpis' => $kpis // Pasamos los KPIs a la vista
        ]);
    }

    public function create()
    {
        return Inertia::render('Community/PrivateUnits/Create');
    }

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

        $validated['subdivision_id'] = $currentSubdivisionId;

        PrivateUnit::create($validated);

        return redirect()->route('admin.private-units.index')->with('success', 'Unidad creada correctamente.');
    }

    public function show(Request $request, PrivateUnit $privateUnit)
    {
        $currentSubdivisionId = $this->getCurrentSubdivisionId($request);
        
        if ($privateUnit->subdivision_id != $currentSubdivisionId) {
            abort(403, 'No tienes permiso para ver esta unidad.');
        }

        $privateUnit->load([
            'users',
            'vehicles',
            'pets',
            'generatedFees' => function($q) {
                $q->latest('expiration_date')->take(10);
            },
            'visits' => function($q) {
                $q->latest('created_at')->take(20);
            }
        ]);

        $pendingFees = $privateUnit->generatedFees->whereIn('status', ['Pendiente', 'Parcial', 'Atrasada'])->count();
        $financialStatus = $pendingFees > 0 ? 'con_adeudo' : 'al_corriente';

        return Inertia::render('Community/PrivateUnits/Show', [
            'unit' => $privateUnit,
            'financialStatus' => $financialStatus
        ]);
    }

    public function update(Request $request, PrivateUnit $privateUnit)
    {
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

    public function destroy(Request $request, PrivateUnit $privateUnit)
    {
        $currentSubdivisionId = $this->getCurrentSubdivisionId($request);
        if ($privateUnit->subdivision_id != $currentSubdivisionId) {
            abort(403, 'No autorizado.');
        }

        $privateUnit->delete();
        return redirect()->back()->with('success', 'Unidad eliminada correctamente.');
    }
}