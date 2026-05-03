<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    /**
     * Muestra la lista de roles y permisos.
     */
    public function index()
    {
        $subdivisionId = Auth::user()->current_team_id; 

        // Aseguramos el contexto de Spatie para este request
        setPermissionsTeamId($subdivisionId);

        // 1. Obtener Roles solo de este fraccionamiento
        $roles = Role::where('team_id', $subdivisionId)
            ->with('permissions')
            ->get();

        // 2. Obtener TODOS los permisos disponibles en el sistema
        $permissions = Permission::all();

        return Inertia::render('Settings/Roles/Index', [
            'roles' => $roles,
            'permissions' => $permissions,
        ]);
    }

    /**
     * Guarda un nuevo rol.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'array'
        ]);

        $subdivisionId = Auth::user()->current_team_id;
        setPermissionsTeamId($subdivisionId);

        $role = Role::create([
            'name' => $request->name,
            'team_id' => $subdivisionId,
            'guard_name' => 'web'
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->back()->with('success', 'Rol creado correctamente.');
    }

    /**
     * Actualiza un rol existente.
     */
    public function update(Request $request, Role $role)
    {
        if ($role->team_id !== Auth::user()->current_team_id) {
            abort(403, 'No tienes permiso para editar este rol.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'array'
        ]);

        setPermissionsTeamId(Auth::user()->current_team_id);

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        return redirect()->back()->with('success', 'Rol actualizado correctamente.');
    }

    /**
     * Elimina un rol.
     */
    public function destroy(Role $role)
    {
        if ($role->team_id !== Auth::user()->current_team_id) {
            abort(403, 'No tienes permiso para eliminar este rol.');
        }

        if ($role->name === 'Admin') {
            return redirect()->back()->with('error', 'No puedes eliminar el rol de Administrador principal.');
        }

        $role->delete();

        return redirect()->back()->with('success', 'Rol eliminado correctamente.');
    }

    // --------------------------------------------------------------------------
    // GESTIÓN DE PERMISOS (NUEVO)
    // --------------------------------------------------------------------------

    /**
     * Guarda un nuevo permiso.
     */
    public function storePermission(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
        ]);

        // Los permisos se crean globales (sin team_id) para consistencia en todo el sistema
        Permission::create([
            'name' => $request->name,
            'guard_name' => 'web'
        ]);

        return redirect()->back()->with('success', 'Permiso creado correctamente.');
    }

    /**
     * Actualiza un permiso existente.
     */
    public function updatePermission(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
        ]);

        $permission->update(['name' => $request->name]);

        return redirect()->back()->with('success', 'Permiso actualizado correctamente.');
    }

    /**
     * Elimina un permiso.
     */
    public function destroyPermission(Permission $permission)
    {
        // Validación opcional: Evitar borrar permisos críticos si es necesario
        // if (in_array($permission->name, ['Gestionar Usuarios', ...])) { ... }

        $permission->delete();

        return redirect()->back()->with('success', 'Permiso eliminado correctamente.');
    }
}