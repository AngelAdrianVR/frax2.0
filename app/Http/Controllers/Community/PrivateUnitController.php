<?php

namespace App\Http\Controllers\Community;

use App\Models\Community\PrivateUnit;
use App\Models\Community\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PrivateUnitController extends Controller
{
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

    public function index(Request $request)
    {
        $currentSubdivisionId = $this->getCurrentSubdivisionId($request);

        if (!$currentSubdivisionId) {
            return redirect()->back()->with('error', 'No tienes un fraccionamiento asignado o seleccionado.');
        }

        $search = $request->input('search');

        $totalUnits = PrivateUnit::where('subdivision_id', $currentSubdivisionId)->count();
        
        $occupiedUnits = PrivateUnit::where('subdivision_id', $currentSubdivisionId)
            ->whereHas('users')
            ->count();
        $occupancyRate = $totalUnits > 0 ? round(($occupiedUnits / $totalUnits) * 100) : 0;

        $totalDebt = DB::table('generated_fees')
            ->join('private_units', 'generated_fees.private_unit_id', '=', 'private_units.id')
            ->where('private_units.subdivision_id', $currentSubdivisionId)
            ->whereIn('generated_fees.status', ['Pendiente', 'Parcial', 'Atrasada'])
            ->sum(DB::raw('generated_fees.total_amount - generated_fees.amount_paid'));

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
                'owner_id' => $owner ? $owner->id : null, 
                'status' => $unit->status,
                'access_block' => $unit->access_block,
                'payment_status' => $paymentStatus,
                'square_meters' => $unit->square_meters,
            ];
        });

        return Inertia::render('Community/PrivateUnits/Index', [
            'units' => $units,
            'filters' => $request->only(['search']),
            'kpis' => $kpis
        ]);
    }

    // NUEVO MÉTODO PARA EL MÓDULO DE MOROSOS
    public function slowPayersIndex(Request $request)
    {
        $currentSubdivisionId = $this->getCurrentSubdivisionId($request);

        if (!$currentSubdivisionId) {
            return redirect()->back()->with('error', 'No tienes un fraccionamiento asignado o seleccionado.');
        }

        $search = $request->input('search');

        // Usamos el scopeSlowPayers() que ya tienes en tu modelo PrivateUnit
        $units = PrivateUnit::query()
            ->where('subdivision_id', $currentSubdivisionId)
            ->slowPayers() 
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
            // Ordenamos a los que deben más dinero hasta arriba
            ->orderByDesc('total_debt') 
            ->paginate(50)
            ->withQueryString();

        $units->through(function ($unit) {
            $owner = $unit->users->first();

            return [
                'id' => $unit->id,
                'lot_number' => $unit->lot_number,
                'unit_street' => $unit->unit_street,
                'exterior_number' => $unit->exterior_number,
                'int_number' => $unit->int_number,
                'full_address' => trim("{$unit->unit_street} {$unit->exterior_number}" . ($unit->int_number ? " Int. {$unit->int_number}" : "")),
                'owner_name' => $owner ? $owner->name : 'Sin asignar',
                'owner_id' => $owner ? $owner->id : null, 
                'total_debt' => $unit->total_debt ?? 0,
                'payment_status' => 'red', // Al ser morosos, su estado es rojo por defecto
                'status' => $unit->status,
            ];
        });

        // Asegúrate de tener la vista Vue en esta ruta, o cámbiala por la que estés usando
        return Inertia::render('Finances/SlowPayers/Index', [
            'units' => $units,
            'filters' => $request->only(['search']),
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

        // SOLUCIÓN ERR_NETWORK: Se amplió la validación para recibir todos los campos del formulario de Vue
        $validated = $request->validate([
            'lot_number' => 'required|string|max:20',
            'unit_street' => 'nullable|string|max:100',
            'exterior_number' => 'nullable|string|max:20',
            'int_number' => 'nullable|string|max:20',
            'square_meters' => 'nullable|numeric',
            'status' => 'required|in:Activo,Inactivo',
            'access_block' => 'boolean',
            
            // Campos de usuario
            'owner_name' => 'nullable|string|max:255',
            'owner_email' => 'nullable|email|max:255',
            'owner_phone' => 'nullable|string|max:20',
            'owner_role' => 'nullable|string|in:Dueño,Inquilino',
            
            // Archivos (Se validan pero por ahora no se procesan en base de datos)
            'deed_file' => 'nullable|file|mimes:pdf|max:5120',
            'lease_file' => 'nullable|file|mimes:pdf|max:5120',
            'id_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            
            // Arreglos dinámicos
            'access_tags' => 'nullable|array',
            'emergency_contacts' => 'nullable|array',
        ]);

        DB::beginTransaction();

        try {
            // 1. Crear Unidad
            $unit = PrivateUnit::create([
                'lot_number' => $validated['lot_number'],
                'unit_street' => $validated['unit_street'],
                'exterior_number' => $validated['exterior_number'],
                'int_number' => $validated['int_number'],
                'square_meters' => $validated['square_meters'] ?? 0,
                'status' => $validated['status'],
                'access_block' => $validated['access_block'] ?? false,
                'subdivision_id' => $currentSubdivisionId,
            ]);

            // 2. Lógica para crear/vincular dueño inicial
            if (!empty($validated['owner_name'])) {
                $user = User::firstOrCreate(
                    ['email' => $validated['owner_email'] ?? Str::slug($validated['owner_name']).'@placeholder.com'],
                    [
                        'name' => $validated['owner_name'],
                        'phone' => $validated['owner_phone'],
                        'password' => Hash::make('password') // Contraseña temporal
                    ]
                );

                $unit->users()->attach($user->id, [
                    'role_in_unit' => $validated['owner_role'] ?? 'Dueño',
                    'is_primary' => true
                ]);
            }

            // Aquí podrías agregar la lógica para guardar los access_tags y emergency_contacts en sus respectivas tablas

            DB::commit();
            return redirect()->route('admin.private-units.index')->with('success', 'Propiedad registrada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocurrió un error al guardar la propiedad: ' . $e->getMessage());
        }
    }

    public function show(Request $request, PrivateUnit $privateUnit)
    {
        $currentSubdivisionId = $this->getCurrentSubdivisionId($request);
        
        if ($privateUnit->subdivision_id != $currentSubdivisionId) {
            abort(403, 'No tienes permiso para ver esta unidad.');
        }

        // AQUÍ ES DONDE SE DEBE AGREGAR 'tags'
        $privateUnit->load([
            'users',
            'vehicles',
            'pets',
            'tags', // <--- Agrega esta línea exacta aquí
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

    // NUEVO MÉTODO EDITAR
    public function edit(Request $request, PrivateUnit $privateUnit)
    {
        $currentSubdivisionId = $this->getCurrentSubdivisionId($request);
        
        if ($privateUnit->subdivision_id != $currentSubdivisionId) {
            abort(403, 'No tienes permiso para editar esta unidad.');
        }

        $privateUnit->load(['users' => function($q) {
            $q->wherePivot('is_primary', true)->take(1);
        }]);

        return Inertia::render('Community/PrivateUnits/Edit', [
            'unit' => $privateUnit
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
            'access_block' => 'boolean',
            'owner_name' => 'nullable|string|max:255',
            'owner_email' => 'nullable|email|max:255',
            'owner_phone' => 'nullable|string|max:20',
            'owner_role' => 'nullable|string|in:Dueño,Inquilino',
        ]);

        DB::beginTransaction();

        try {
            $privateUnit->update([
                'lot_number' => $validated['lot_number'],
                'unit_street' => $validated['unit_street'],
                'exterior_number' => $validated['exterior_number'],
                'int_number' => $validated['int_number'],
                'square_meters' => $validated['square_meters'] ?? 0,
                'status' => $validated['status'],
                'access_block' => $validated['access_block'] ?? false,
            ]);

            // Actualizar dueño principal si se proporcionó información
            if (!empty($validated['owner_email'])) {
                $user = User::firstOrCreate(
                    ['email' => $validated['owner_email']],
                    [
                        'name' => $validated['owner_name'],
                        'phone' => $validated['owner_phone'],
                        'password' => Hash::make('password')
                    ]
                );

                $privateUnit->users()->syncWithoutDetaching([
                    $user->id => ['role_in_unit' => $validated['owner_role'] ?? 'Dueño', 'is_primary' => true]
                ]);
            }

            DB::commit();
            return redirect()->route('admin.private-units.index')->with('success', 'Propiedad actualizada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
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