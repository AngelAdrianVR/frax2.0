<?php

use App\Http\Controllers\PetController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\VehicleController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Http\Request;


// Ruta Raíz: Muestra el estado de carga (animación)
Route::get('/', function () {
    // Corregido el nombre del componente a 'Loading'
    return Inertia::render('Loading');
});

// Ruta Welcome: La landing page a la que se redirige después de cargar
Route::get('/inicio', function () {
    return Inertia::render('Welcome'); // Asegúrate de tener un componente Welcome.vue
})->name('welcome');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});


// ruta para cambiar contexto de propiedad (multiples propiedades)
// Se cambia la sesion en el handleinertiaRequest y se obtiene el id de la propiedad seleccionada
Route::post('/switch-property', function (Request $request) {
    $request->validate(['property_id' => 'required|integer']);

    $user = $request->user();
    
    // Verificar si el usuario tiene acceso a esta propiedad a través de alguna de sus residencias
    $hasAccess = $user->residents->flatMap->privateUnits->contains('id', $request->property_id);

    if (!$hasAccess) {
        abort(403, 'No tienes acceso a esta propiedad.');
    }
    
    session(['current_property_id' => $request->property_id]);
    
    return back();
})->name('context.switch');



// Roles y permisos (Solo para admins) =================================================================
// ==========================================================================================
// CRUD de Roles (Resource estándar)
Route::resource('roles', RoleController::class)->middleware('auth');

// CRUD de Permisos (Métodos personalizados en RoleController)
Route::post('/permissions', [RoleController::class, 'storePermission'])->name('permissions.store')->middleware('auth');
Route::put('/permissions/{permission}', [RoleController::class, 'updatePermission'])->name('permissions.update')->middleware('auth');
Route::delete('/permissions/{permission}', [RoleController::class, 'destroyPermission'])->name('permissions.destroy')->middleware('auth');



// Vehiculos ================================================================================
// ==========================================================================================
Route::resource('vehicles', VehicleController::class)->except(['show', 'edit'])->middleware('auth');


// Mascotas =================================================================================
// ==========================================================================================
Route::resource('pets', PetController::class)->middleware('auth');