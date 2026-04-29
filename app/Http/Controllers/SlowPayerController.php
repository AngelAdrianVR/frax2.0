<?php

namespace App\Http\Controllers;

use App\Models\PrivateUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SlowPayerController extends Controller
{
    public function index(Request $request)
    {
        // 1. Obtener el Fraccionamiento Actual de la sesión o base de datos
        $currentSubdivisionId = session('current_subdivision_id');
        if (!$currentSubdivisionId) {
            $currentSubdivisionId = DB::table('subdivision_user')
                ->where('user_id', $request->user()->id)
                ->value('subdivision_id');
        }

        if (!$currentSubdivisionId) {
            return redirect()->back()->with('error', 'No tienes un fraccionamiento asignado.');
        }

        // 2. Construir la consulta de unidades morosas
        $debtors = PrivateUnit::query()
            ->where('subdivision_id', $currentSubdivisionId)
            // Filtramos las unidades que tengan una suma de deuda mayor a 0 en generated_fees
            ->whereRaw('(SELECT COALESCE(SUM(total_amount - amount_paid), 0) FROM generated_fees WHERE private_unit_id = private_units.id AND status IN ("Pendiente", "Parcial", "Atrasada")) > 0')
            
            // Subconsulta para obtener el total de la deuda
            ->addSelect(['total_debt' => function ($query) {
                $query->selectRaw('COALESCE(SUM(total_amount - amount_paid), 0)')
                    ->from('generated_fees')
                    ->whereColumn('private_unit_id', 'private_units.id')
                    ->whereIn('status', ['Pendiente', 'Parcial', 'Atrasada']);
            }])
            
            // Subconsulta para obtener el nombre del dueño usando la tabla pivote (private_unit_user)
            ->addSelect(['owner_name' => function ($query) {
                $query->select('users.name')
                    ->from('users')
                    ->join('private_unit_user', 'users.id', '=', 'private_unit_user.user_id')
                    ->whereColumn('private_unit_user.private_unit_id', 'private_units.id')
                    ->where('private_unit_user.role_in_unit', 'Dueño')
                    ->limit(1);
            }])
            
            // Paginamos y mapeamos los resultados para enviarlos a tu vista Vue
            ->paginate(15)
            ->through(function ($unit) {
                // Formateamos la dirección basada en las columnas de private_units
                $address = "Lote {$unit->lot_number}";
                if ($unit->unit_street) {
                    $address .= " - {$unit->unit_street} #{$unit->exterior_number}";
                }
                
                return [
                    'id' => $unit->id,
                    'address' => $address,
                    'owner_name' => $unit->owner_name ?? 'Sin Propietario Registrado',
                    'total_debt' => (float) $unit->total_debt,
                ];
            });

        return Inertia::render('SlowPayers/Index', [
            'debtors' => $debtors
        ]);
    }
}