<?php

namespace App\Http\Controllers\Community;

use App\Models\Community\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

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
            ->with('roles') // Cargamos los roles de Spatie para evitar N+1 queries
            ->whereHas('subdivisions', function (Builder $q) use ($currentSubdivisionId) {
                $q->where('subdivisions.id', $currentSubdivisionId);
            })
            ->with(['privateUnits' => function ($q) use ($currentSubdivisionId) {
                $q->where('subdivision_id', $currentSubdivisionId);
            }]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('alias', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('committee_role', 'like', "%{$search}%");
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

                $displayName = !empty($user->alias) ? $user->alias : $user->name;
                
                // Prioridad de etiquetas:
                // 1. Puesto específico (Tesorero, Mesa Directiva)
                // 2. Rol de sistema (Administrador, Residente, etc)
                $systemRole = $user->roles->first()->name ?? 'Residente';
                $displayRole = $user->committee_role ?: $systemRole;

                return [
                    'id' => $user->id,
                    'name' => $displayName,
                    'email' => $user->show_email ? $user->email : ($user->email ?? null), 
                    'phone' => $user->show_phone ? $user->phone : ($user->phone ?? null),
                    'accept_messages' => $user->accept_messages,
                    'avatar' => $user->profile_photo_url ?? "https://ui-avatars.com/api/?name=".urlencode($displayName)."&color=7F9CF5&background=EBF4FF",
                    'property' => $propertyLabel,
                    'unit_role' => $roleInUnit,
                    'system_role' => $systemRole, // Lo enviamos a la vista para decidir el color de la tarjeta
                    'display_role' => $displayRole // El texto que dirá el gafete
                ];
            });

        return Inertia::render('Community/Users/Index', [
            'users' => $users,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Formulario para Crear Usuario
     */
    public function create()
    {
        $roles = Role::all(['id', 'name']);
        
        return Inertia::render('Community/Users/Create', [
            'roles' => $roles
        ]);
    }

    /**
     * Guardar Nuevo Usuario
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', Password::defaults()],
            'alias' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'role_name' => 'nullable|string|exists:roles,name', // Validamos que el rol exista en la DB
            'committee_role' => 'nullable|string|max:255',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['show_email'] = true;
        $validated['show_phone'] = true;
        $validated['accept_messages'] = true;

        $user = User::create($validated);

        // Asignar el rol del sistema (Spatie)
        if (!empty($validated['role_name'])) {
            $user->assignRole($validated['role_name']);
        } else {
            $user->assignRole('Residente'); // Rol por defecto
        }

        // Vincular al fraccionamiento actual
        $currentSubdivisionId = session('current_subdivision_id') ?? 
            DB::table('subdivision_user')->where('user_id', $request->user()->id)->value('subdivision_id');
            
        if ($currentSubdivisionId) {
            $user->subdivisions()->attach($currentSubdivisionId, [
                'role_in_subdivision' => 'Residente', 
                'is_current' => true
            ]);
        }

        return redirect()->route('users.index')->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Formulario para Editar Usuario
     */
    public function edit(User $user)
    {
        $roles = Role::all(['id', 'name']);

        return Inertia::render('Community/Users/Edit', [
            'userEdit' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'alias' => $user->alias,
                'phone' => $user->phone,
                'role_name' => $user->roles->first()->name ?? '', // Pasamos el rol que ya tiene
                'committee_role' => $user->committee_role,
            ],
            'roles' => $roles
        ]);
    }

    /**
     * Actualizar Usuario
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'alias' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'role_name' => 'nullable|string|exists:roles,name',
            'committee_role' => 'nullable|string|max:255',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        // Sincronizar (actualizar) el rol de sistema
        if (!empty($validated['role_name'])) {
            $user->syncRoles([$validated['role_name']]);
        }

        return redirect()->route('users.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Eliminar Usuario
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Usuario eliminado exitosamente.');
    }

    /**
     * 2. Pestaña: Emergencias
     */
    public function emergencies(Request $request)
    {
        $emergencies = [
            'internal' => [],
            'external' => []
        ];

        return Inertia::render('Community/Users/Emergencies', [
            'emergencies' => $emergencies,
            'isAdmin' => true, 
        ]);
    }

    /**
     * 3. Pestaña: Servicios y Oficios
     */
    public function services(Request $request)
    {
        $services = [
            'profesional' => [],
            'oficio' => [],
            'emprendimiento' => []
        ];

        return Inertia::render('Community/Users/Services', [
            'services' => $services,
            'isAdmin' => true,
        ]);
    }

    /**
     * 4. Vista de Ajustes de Privacidad (Mi Perfil en el directorio)
     */
    public function settings(Request $request)
    {
        // Le pasamos el usuario autenticado a la vista para que cargue sus preferencias guardadas
        return Inertia::render('Community/Users/Settings', [
            'user' => $request->user()
        ]);
    }

    /**
     * 5. Actualizar Ajustes de Privacidad y Foto
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'alias' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'show_phone' => 'boolean',
            'show_email' => 'boolean',
            'accept_messages' => 'boolean',
            'photo' => 'nullable|image|max:1024', // Validación de imagen (máx 1MB)
        ]);

        $user = $request->user();

        // 1. Guardar la foto usando el trait de Jetstream si se subió una
        if ($request->hasFile('photo')) {
            $user->updateProfilePhoto($request->file('photo'));
        }

        // 2. Guardar el resto de la información
        $user->update([
            'alias' => $validated['alias'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'show_phone' => $validated['show_phone'] ?? false,
            'show_email' => $validated['show_email'] ?? false,
            'accept_messages' => $validated['accept_messages'] ?? false,
        ]);

        // Redirigimos de vuelta a la vista del directorio en lugar de recargar settings
        return redirect()->route('users.index')->with('success', 'Preferencias de privacidad actualizadas.');
    }
}