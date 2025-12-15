<?php

use App\Http\Controllers\AmenityController;
use App\Http\Controllers\BankReconciliationController;
use App\Http\Controllers\BillingConceptController;
use App\Http\Controllers\GeneratedFeeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentStatusController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\PrivateUnitController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ResidentController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    // --- NUEVA RUTA PARA EL SEMÁFORO DEL SIDEBAR ---
    Route::get('/payment-status', [PaymentStatusController::class, 'show'])->name('payment.status');
});



// ruta para cambiar contexto de propiedad (multiples propiedades) para residentes y admins
// Se cambia la sesion en el handleinertiaRequest y se obtiene el id de la propiedad seleccionada
Route::post('/switch-property', function (Request $request) {
    // Aceptamos string o integer porque los admins envían "admin_1"
    $request->validate(['property_id' => 'required']);

    $user = $request->user();
    $propertyId = $request->input('property_id');
    $targetSubdivisionId = null;

    // --- LÓGICA DE ADMINISTRADOR ---
    if (is_string($propertyId) && str_starts_with($propertyId, 'admin_')) {
        // Extraer el ID del fraccionamiento (ej: "admin_5" -> 5)
        $targetSubdivisionId = (int) str_replace('admin_', '', $propertyId);

        // Verificar si el usuario tiene roles en ese equipo (Fraccionamiento)
        $hasAdminAccess = DB::table(config('permission.table_names.model_has_roles'))
            ->where('model_id', $user->id)
            ->where('model_type', get_class($user))
            ->where('team_id', $targetSubdivisionId)
            ->exists();

        if (!$hasAdminAccess) {
            abort(403, 'No tienes permisos administrativos en este fraccionamiento.');
        }

        // Configurar contexto de Admin
        setPermissionsTeamId($targetSubdivisionId);
    } 
    // --- LÓGICA DE RESIDENTE ---
    else {
        // Verificar si el usuario tiene acceso a esta unidad privada
        $targetUnit = $user->residents->flatMap->privateUnits->firstWhere('id', $propertyId);

        if (!$targetUnit) {
            abort(403, 'No tienes acceso a esta propiedad.');
        }
        
        $targetSubdivisionId = $targetUnit->subdivision_id;
        
        // Configurar contexto de Residente
        setPermissionsTeamId($targetSubdivisionId);
    }
    
    // Guardar en sesión
    session([
        'current_property_id' => $propertyId,      // Para el frontend (UI Selector)
        'current_subdivision_id' => $targetSubdivisionId // Para Spatie y consultas backend
    ]);
    
    return back();
})->name('context.switch');



// Roles y permisos (Solo para admins) ======================================================
// ==========================================================================================
// CRUD de Roles (Resource estándar)
Route::resource('roles', RoleController::class)->middleware('auth');


// CRUD de Permisos (Métodos personalizados en RoleController)
Route::post('/permissions', [RoleController::class, 'storePermission'])->name('permissions.store')->middleware('auth');
Route::put('/permissions/{permission}', [RoleController::class, 'updatePermission'])->name('permissions.update')->middleware('auth');
Route::delete('/permissions/{permission}', [RoleController::class, 'destroyPermission'])->name('permissions.destroy')->middleware('auth');


// Unidades privativas (casas) ==============================================================
// ==========================================================================================
Route::resource('/admin/private-units', PrivateUnitController::class)->names('admin.private-units')->middleware('auth');
// Ruta extra para inactivación rápida (Toggle)
Route::patch('/admin/private-units/{privateUnit}/toggle-status', [PrivateUnitController::class, 'toggleStatus'])->name('admin.private-units.toggle-status')->middleware('auth');
// Vista de morosos
Route::get('/morosos', [PrivateUnitController::class, 'slowPayersIndex'])->name('slowPayers.index')->middleware('auth');


// Vehiculos ================================================================================
// ==========================================================================================
// Ruta específica para ADMIN (debe ir antes del resource para evitar conflictos o usar un prefijo)
Route::get('/admin/vehicles', [VehicleController::class, 'adminIndex'])->name('admin.vehicles.index')->middleware('auth');
// CRUD Estándar (usado por Residentes y por Admin para update/destroy)
Route::resource('vehicles', VehicleController::class)->except(['show', 'edit'])->middleware('auth');


// Mascotas =================================================================================
// ==========================================================================================
Route::get('/admin/pets', [PetController::class, 'adminIndex'])->name('admin.pets.index')->middleware('auth');
Route::resource('pets', PetController::class)->middleware('auth');


// Amenidades ===============================================================================
// ==========================================================================================
Route::resource('amenities', AmenityController::class)->middleware('auth');
// Ruta específica para procesar la reserva del residente
Route::post('amenities/{amenity}/reserve', [AmenityController::class, 'storeReservation'])->middleware('auth')->name('amenities.reserve');
// Ruta para desactivar/activar (soft delete o cambio de estado)
Route::patch('amenities/{amenity}/toggle', [AmenityController::class, 'toggleStatus'])->middleware('auth')->name('amenities.toggle');
Route::get('amenities/{amenity}/availability', [ReservationController::class, 'getAvailability'])->middleware('auth');


// Reservaciones de Amenidades ==============================================================
// ==========================================================================================
Route::resource('reservations', ReservationController::class)->middleware('auth');


// Residentes ===============================================================================
// ==========================================================================================
// Route::resource('residents', ResidentController::class)->middleware('auth');


// ==========================================================================================
// MÓDULO DE FINANZAS Y CUOTAS (NUEVO)
// ==========================================================================================

// 1. Mis Cuotas: Usamos resource para permitir expansión (show, print, etc.)
// La URL será /mis-cuotas, pero los nombres de ruta serán fees.index, fees.show, etc.
Route::resource('mis-cuotas', GeneratedFeeController::class)
    ->names('fees')
    ->middleware('auth');

// 2. Pagos: Solo listado y creación de nuevos pagos
Route::resource('payments', PaymentController::class)
    ->only(['index', 'store'])
    ->middleware('auth');

// 3. Conciliación Bancaria
Route::get('/conciliacion', [BankReconciliationController::class, 'index'])
    ->name('bank.reconciliations')
    ->middleware('auth');

// 4. Conceptos de Cobro (Admin)
Route::get('/billing-concepts', [BillingConceptController::class, 'index'])
    ->name('billing-concepts.index')
    ->middleware('auth');