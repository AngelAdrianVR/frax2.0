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

                $tableNames = config('permission.table_names');
                
                // Obtenemos los roles "crudos" directamente de la DB
                $rawRoles = DB::table($tableNames['model_has_roles'])
                    ->join($tableNames['roles'], $tableNames['model_has_roles'] . '.role_id', '=', $tableNames['roles'] . '.id')
                    ->where('model_id', $user->id)
                    ->where('model_type', get_class($user))
                    ->select($tableNames['roles'] . '.name as role_name', $tableNames['model_has_roles'] . '.team_id')
                    ->get();
                
                $availableProperties = collect();

                // CASO 1: El usuario es un RESIDENTE
                if ($user->resident) {
                    $user->load(['resident.privateUnits.subdivision']);
                    
                    $availableProperties = $user->resident->privateUnits->map(function ($unit) use ($user, $rawRoles) {
                        $subdivision = $unit->subdivision;
                        
                        $housingRole = $unit->pivot->role_in_unit ?? 'Habitante';
                        $systemRoleObj = $rawRoles->firstWhere('team_id', $subdivision->id);
                        $systemRole = $systemRoleObj ? $systemRoleObj->role_name : 'Residente';

                        // --- NUEVO: Formato legible de dirección ---
                        $addressLabel = $unit->unit_street 
                            ? $unit->unit_street . ' #' . $unit->exterior_number
                            : 'Lote ' . $unit->lot_number;
                            
                        if ($unit->int_number) {
                            $addressLabel .= ' Int. ' . $unit->int_number;
                        }
                        // -------------------------------------------

                        return [
                            'resident_id' => $user->resident->id,
                            'property_id' => $unit->id, 
                            'subdivision_id' => $subdivision->id,
                            'subdivision_name' => $subdivision->name,
                            'unit_number' => $unit->lot_number . ' ' . $unit->int_number, // Mantenemos el técnico por si acaso
                            'address_label' => $addressLabel, // Campo nuevo para mostrar en AppLayout
                            'role_in_unit' => $housingRole,    
                            'community_role' => $systemRole,
                            'is_primary_owner' => $unit->pivot->is_primary_owner ?? false, 
                        ];
                    });
                }
                
                // CASO 2: El usuario es EMPLEADO/ADMIN (sin residencia vinculada en esa unidad)
                $adminTeamIds = $rawRoles->whereNotNull('team_id')->pluck('team_id')->unique();
                
                if ($availableProperties->isEmpty() && $adminTeamIds->isNotEmpty()) {
                    $subdivisions = \App\Models\Subdivision::whereIn('id', $adminTeamIds)->get();

                    $adminProperties = $subdivisions->map(function($sub) use ($rawRoles) {
                        $roleObj = $rawRoles->firstWhere('team_id', $sub->id);
                        
                        return [
                            'resident_id' => null,
                            'property_id' => 'admin_' . $sub->id, 
                            'subdivision_id' => $sub->id,
                            'subdivision_name' => $sub->name,
                            'unit_number' => 'Administración',
                            'address_label' => 'Super Admin', // Etiqueta para admin
                            'role_in_unit' => 'Staff', 
                            'community_role' => $roleObj ? $roleObj->role_name : 'Empleado',
                            'is_primary_owner' => false,
                        ];
                    });
                    
                    $availableProperties = $availableProperties->merge($adminProperties);
                }

                // Determinamos la propiedad ACTIVA
                $currentPropertyId = Session::get('current_property_id');
                
                $currentProperty = $availableProperties->first(function($prop) use ($currentPropertyId) {
                    return (string)$prop['property_id'] === (string)$currentPropertyId;
                });

                if (!$currentProperty && $availableProperties->isNotEmpty()) {
                    $currentProperty = $availableProperties->first();
                }

                $currentActiveRole = 'Usuario';
                if ($currentProperty) {
                    $currentActiveRole = $currentProperty['community_role'];
                } else {
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
                        'role' => $currentActiveRole,
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
