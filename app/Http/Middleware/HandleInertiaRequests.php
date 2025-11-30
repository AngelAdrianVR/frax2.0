<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        // return [
        //     ...parent::share($request),
        //     //
        // ];

        
        $user = $request->user();

        return array_merge(parent::share($request), [
            // Información de autenticación y contexto inmobiliario
            'auth' => function () use ($user) {
                if (!$user) {
                    return ['user' => null];
                }

                // CORRECCIÓN PRINCIPAL:
                // Spatie filtra $user->roles por el "Team Actual". Al inicio, esto puede ser null.
                // Obtenemos los roles "crudos" directamente de la DB para ver TODO el panorama del usuario.
                $tableNames = config('permission.table_names');
                
                $rawRoles = DB::table($tableNames['model_has_roles'])
                    ->join($tableNames['roles'], $tableNames['model_has_roles'] . '.role_id', '=', $tableNames['roles'] . '.id')
                    ->where('model_id', $user->id)
                    ->where('model_type', get_class($user))
                    ->select($tableNames['roles'] . '.name as role_name', $tableNames['model_has_roles'] . '.team_id')
                    ->get();
                
                $availableProperties = collect();

                // CASO 1: El usuario es un RESIDENTE (tiene registro en la tabla residents)
                if ($user->resident) {
                    $user->load(['resident.privateUnits.subdivision']);
                    
                    $availableProperties = $user->resident->privateUnits->map(function ($unit) use ($user, $rawRoles) {
                        $subdivision = $unit->subdivision;
                        
                        // 1. Rol Habitacional (De la tabla pivote residence_units)
                        $housingRole = $unit->pivot->role_in_unit ?? 'Habitante';

                        // 2. Rol del Sistema (Buscamos en nuestros roles crudos por team_id)
                        $systemRoleObj = $rawRoles->firstWhere('team_id', $subdivision->id);
                        $systemRole = $systemRoleObj ? $systemRoleObj->role_name : 'Residente';

                        return [
                            'resident_id' => $user->resident->id,
                            'property_id' => $unit->id, 
                            'subdivision_id' => $subdivision->id,
                            'subdivision_name' => $subdivision->name,
                            'unit_number' => $unit->lot_number . ' ' . $unit->int_number,
                            'role_in_unit' => $housingRole,    
                            'community_role' => $systemRole,
                            'is_primary_owner' => $unit->pivot->is_primary_owner ?? false, 
                        ];
                    });
                }
                
                // CASO 2: El usuario es EMPLEADO/ADMIN (o Residente con roles administrativos extra)
                // Buscamos roles que tengan team_id pero que NO estén ya en la lista (si aplica)
                $adminTeamIds = $rawRoles->whereNotNull('team_id')->pluck('team_id')->unique();
                
                // Filtramos para no duplicar si ya se agregaron como residente (opcional, según tu lógica)
                // Si quieres que aparezca como opción separada "Administración", quita este filtro.
                // Aquí asumimos que si ya salió arriba, no lo duplicamos, o si availableProperties está vacío.
                
                if ($availableProperties->isEmpty() && $adminTeamIds->isNotEmpty()) {
                    $subdivisions = \App\Models\Subdivision::whereIn('id', $adminTeamIds)->get();

                    $adminProperties = $subdivisions->map(function($sub) use ($rawRoles) {
                        $roleObj = $rawRoles->firstWhere('team_id', $sub->id);
                        
                        return [
                            'resident_id' => null,
                            // Usamos un ID negativo o string para diferenciarlo de una unidad real en el frontend
                            'property_id' => 'admin_' . $sub->id, 
                            'subdivision_id' => $sub->id,
                            'subdivision_name' => $sub->name,
                            'unit_number' => 'Administración',
                            
                            'role_in_unit' => 'Staff', 
                            'community_role' => $roleObj ? $roleObj->role_name : 'Empleado',
                            
                            'is_primary_owner' => false,
                        ];
                    });
                    
                    $availableProperties = $availableProperties->merge($adminProperties);
                }

                // Determinamos la propiedad ACTIVA
                $currentPropertyId = Session::get('current_property_id');
                
                // Buscamos coincidencia exacta (ahora property_id puede ser string 'admin_1' o int 1)
                $currentProperty = $availableProperties->first(function($prop) use ($currentPropertyId) {
                    return (string)$prop['property_id'] === (string)$currentPropertyId;
                });

                // Si no hay propiedad seleccionada o no es válida, tomamos la primera
                if (!$currentProperty && $availableProperties->isNotEmpty()) {
                    $currentProperty = $availableProperties->first();
                }

                // 3. Definimos el rol activo para mostrar en la UI (Navbar)
                $currentActiveRole = 'Usuario';
                
                if ($currentProperty) {
                    $currentActiveRole = $currentProperty['community_role'];
                } else {
                    // Si no hay propiedades, buscamos un rol global (sin team_id)
                    $globalRole = $rawRoles->whereNull('team_id')->first();
                    $currentActiveRole = $globalRole ? $globalRole->role_name : 'Usuario';
                }

                return [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'resident_id' => $user->resident ? $user->resident->id : null,
                        'avatar' => $user->profile_photo_url ?? null,
                        'role' => $currentActiveRole, // Ahora dirá "Admin" correctamente
                    ],
                    'properties' => $availableProperties->values(),
                    'current_property' => $currentProperty,
                    'global_roles' => $rawRoles->whereNull('team_id')->pluck('role_name'),
                ];
            },

            'flash' => function () {
                return [
                    'success' => Session::get('success'),
                    'error' => Session::get('error'),
                    'warning' => Session::get('warning'),
                    'info' => Session::get('info'),
                ];
            },
        ]);
    }
}
