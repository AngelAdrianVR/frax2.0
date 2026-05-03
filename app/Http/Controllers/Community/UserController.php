<?php

namespace App\Http\Controllers\Community;

use App\Models\Community\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class UserController extends Controller
{
    /**
     * 1. Pestaña Principal: Directorio de Residentes
     */
    public function index(Request $request)
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

        $query = User::query()
            ->whereHas('privateUnits', function (Builder $q) use ($currentSubdivisionId) {
                $q->where('subdivision_id', $currentSubdivisionId);
            })
            ->with(['privateUnits' => function ($q) use ($currentSubdivisionId) {
                $q->where('subdivision_id', $currentSubdivisionId);
            }]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('alias', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(16)
            ->withQueryString()
            ->through(function ($user) {
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

                // Usar alias si lo tiene activo y no está vacío
                $displayName = !empty($user->alias) ? $user->alias : $user->name;

                return [
                    'id' => $user->id,
                    'name' => $displayName,
                    'email' => $user->show_email ? $user->email : null,
                    'phone' => $user->show_phone ? $user->phone : null,
                    'accept_messages' => $user->accept_messages,
                    'avatar' => $user->profile_photo_url ?? "https://ui-avatars.com/api/?name=".urlencode($displayName)."&color=7F9CF5&background=EBF4FF",
                    'property' => $propertyLabel,
                    'role' => $roleInUnit,
                ];
            });

        return Inertia::render('Community/Users/Index', [
            'users' => $users,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * 2. Pestaña: Emergencias
     */
    public function emergencies(Request $request)
    {
        // NOTA: Aquí deberás hacer tu consulta a tu modelo de emergencias real.
        // Por ejemplo: Emergency::where('subdivision_id', $currentSubdivisionId)->get();
        // Por ahora enviamos arrays vacíos para que la vista cargue sin errores.
        $emergencies = [
            'internal' => [],
            'external' => []
        ];

        return Inertia::render('Community/Users/Emergencies', [
            'emergencies' => $emergencies,
            'isAdmin' => true, // Cambiar por validación de rol real
        ]);
    }

    /**
     * 3. Pestaña: Servicios y Oficios
     */
    public function services(Request $request)
    {
        // NOTA: Aquí deberás hacer tu consulta a tu modelo de servicios.
        // Por ahora agrupamos las categorías vacías para que el frontend no falle.
        $services = [
            'profesional' => [],
            'oficio' => [],
            'emprendimiento' => []
        ];

        return Inertia::render('Community/Users/Services', [
            'services' => $services,
            'isAdmin' => true, // Cambiar por validación de rol real
        ]);
    }

    /**
     * 4. Vista de Ajustes de Privacidad (Mi Perfil en el directorio)
     */
    public function settings(Request $request)
    {
        return Inertia::render('Community/Users/Settings');
    }

    /**
     * 5. Actualizar Ajustes de Privacidad
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'alias' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'show_phone' => 'boolean',
            'show_email' => 'boolean',
            'accept_messages' => 'boolean',
        ]);

        $request->user()->update($validated);

        return back()->with('success', 'Preferencias de privacidad actualizadas.');
    }

    /* |--------------------------------------------------------------------------
     | MÉTODOS CRUD EXTRAS PARA SERVICIOS Y EMERGENCIAS (Vienen de DirectoryController)
     |--------------------------------------------------------------------------
     | Aquí puedes pegar tus métodos store, update y destroy que tenías antes 
     | en DirectoryController para guardar los servicios y emergencias, 
     | solo asegúrate de actualizar las rutas en web.php.
     */
}