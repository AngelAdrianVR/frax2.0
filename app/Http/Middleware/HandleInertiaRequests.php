<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Illuminate\Support\Facades\Auth;
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
                    return [
                        'user' => null,
                    ];
                }

                // CORRECCIÓN 1: Cargar la relación correcta 'privateUnits' y su 'subdivision'.
                // Asumimos que en tu modelo Resident la relación se llama 'privateUnits' (BelongsToMany).
                // Si en tu modelo Resident se llama 'residenceUnit', cambia 'privateUnits' por ese nombre aquí abajo.
                $user->load(['resident.privateUnits.subdivision']);

                // 1. Construimos la lista de todas las propiedades disponibles para este usuario
                $availableProperties = $user->residents->flatMap(function ($resident) {
                    
                    // CORRECCIÓN 2: Iteramos sobre las unidades (PrivateUnit) directamente
                    // Nota: Si tu relación en Resident.php se llama 'residenceUnit', cambia '$resident->privateUnits' por '$resident->residenceUnit'
                    return $resident->privateUnits->map(function ($unit) use ($resident) {
                        
                        // $unit es el modelo PrivateUnit
                        // $unit->pivot es el modelo ResidenceUnit (tabla intermedia)
                        
                        $subdivision = $unit->subdivision;
                        
                        return [
                            'resident_id' => $resident->id, 
                            'property_id' => $unit->id,
                            'subdivision_id' => $subdivision->id,
                            'subdivision_name' => $subdivision->name,
                            'unit_number' => $unit->lot_number . ' ' . $unit->int_number, 
                            
                            // ACCESO CORRECTO A LA PIVOTE:
                            'role_in_unit' => $unit->pivot->role_in_unit, 
                            'is_primary_owner' => $unit->pivot->is_primary_owner,
                        ];
                    });
                });

                // 2. Determinamos la propiedad ACTIVA
                $currentPropertyId = Session::get('current_property_id');
                
                $currentProperty = $availableProperties->firstWhere('property_id', $currentPropertyId);

                if (!$currentProperty && $availableProperties->isNotEmpty()) {
                    $currentProperty = $availableProperties->first();
                }

                return [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'resident_id' => $user->resident->id,
                        'avatar' => $user->profile_photo_url ?? null,
                        'role' => $user->roles->first()->name ?? 'Residente',
                    ],
                    'properties' => $availableProperties->values(),
                    'current_property' => $currentProperty,
                    'global_roles' => $user->roles->pluck('name'),
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
