<?php

namespace App\Http\Controllers\Security;

use App\Models\Security\Patrol;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PatrolController extends Controller
{
    public function index(Request $request)
    {
        // Obtenemos los patrullajes y cargamos al guardia (usuario)
        $patrols = Patrol::with('user')
            ->latest('start_time')
            ->paginate(15)
            ->through(function ($patrol) {
                return [
                    'id' => $patrol->id,
                    'guard_name' => $patrol->user->name ?? 'Guardia no asignado',
                    'start_time' => $patrol->start_time ? $patrol->start_time->format('d/m/Y H:i') : 'N/A',
                    'end_time' => $patrol->end_time ? $patrol->end_time->format('d/m/Y H:i') : 'En curso',
                    'status' => $patrol->status,
                    'scanned_points' => $patrol->scanned_points ?? 0,
                ];
            });

        return Inertia::render('Security/Patrols/Index', [
            'patrols' => $patrols
        ]);
    }
}