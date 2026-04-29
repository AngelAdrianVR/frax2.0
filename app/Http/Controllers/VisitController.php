<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VisitController extends Controller
{
    public function index(Request $request)
    {
        // Obtenemos la propiedad actual del residente
        $currentPropertyId = $request->user()->getCurrentPropertyId();

        $visits = Visit::query()
            ->where('private_unit_id', $currentPropertyId)
            ->orderBy('date_of_use', 'desc')
            ->paginate(15)
            ->through(function ($visit) {
                return [
                    'id' => $visit->id,
                    'name' => $visit->name,
                    'reason' => $visit->reason,
                    'access_type' => $visit->access_type,
                    'status' => $visit->status,
                    'date_of_use' => $visit->date_of_use ? $visit->date_of_use->format('d/m/Y H:i') : 'N/A',
                ];
            });

        return Inertia::render('Visits/Index', [
            'visits' => $visits
        ]);
    }

    // Los demás métodos (create, store, etc.) irán aquí conforme los necesites
}