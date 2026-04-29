<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // 1. Obtener el Fraccionamiento Actual
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

        // 2. Buscar usuarios que tengan propiedades dentro de este fraccionamiento
        $query = User::query()
            ->whereHas('privateUnits', function (Builder $q) use ($currentSubdivisionId) {
                $q->where('subdivision_id', $currentSubdivisionId);
            })
            // Cargamos la propiedad para saber en qué casa viven
            ->with(['privateUnits' => function ($q) use ($currentSubdivisionId) {
                $q->where('subdivision_id', $currentSubdivisionId);
            }]);

        // 3. Lógica del Buscador
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // 4. Paginación y transformación de datos para Vue
        $users = $query->paginate(16)
            ->withQueryString()
            ->through(function ($user) {
                // Obtenemos la primera propiedad coincidente para mostrarla en el directorio
                $mainUnit = $user->privateUnits->first();
                
                $propertyLabel = 'Sin propiedad asignada';
                $roleInUnit = 'Residente';

                if ($mainUnit) {
                    $propertyLabel = trim("{$mainUnit->unit_street} {$mainUnit->exterior_number}");
                    if ($mainUnit->int_number) {
                        $propertyLabel .= " Int. {$mainUnit->int_number}";
                    }
                    $roleInUnit = $mainUnit->pivot->role_in_unit ?? 'Residente';
                }

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->profile_photo_url ?? "https://ui-avatars.com/api/?name=".urlencode($user->name)."&color=7F9CF5&background=EBF4FF",
                    'property' => $propertyLabel,
                    'role' => $roleInUnit,
                ];
            });

        return Inertia::render('Users/Index', [
            'users' => $users,
            'filters' => $request->only(['search']),
        ]);
    }
}