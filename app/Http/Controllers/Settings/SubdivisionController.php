<?php

namespace App\Http\Controllers\Settings;

use App\Models\Settings\Subdivision;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class SubdivisionController extends Controller
{
    public function index(Request $request)
    {
        // Obtener el ID del fraccionamiento actual de la sesión o base de datos
        $currentSubdivisionId = session('current_subdivision_id');
        if (!$currentSubdivisionId) {
            $currentSubdivisionId = DB::table('subdivision_user')
                ->where('user_id', $request->user()->id)
                ->value('subdivision_id');
        }

        if (!$currentSubdivisionId) {
            return redirect()->back()->with('error', 'No tienes un fraccionamiento asignado.');
        }

        // Obtener los datos del fraccionamiento actual
        $subdivision = Subdivision::find($currentSubdivisionId);

        return Inertia::render('Settings/Subdivisions/Index', [
            'subdivision' => $subdivision
        ]);
    }

    public function update(Request $request, Subdivision $subdivision)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'tax_id' => 'nullable|string|max:50',
            'bank_account' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:100',
            // Agrega aquí otras reglas de validación según las columnas de tu tabla
        ]);

        $subdivision->update($validated);

        return redirect()->back()->with('success', 'Datos del coto actualizados correctamente.');
    }
}