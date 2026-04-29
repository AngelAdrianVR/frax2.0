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
use App\Http\Controllers\RoleController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RegisterInvitationController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\ParcelServiceController;
use App\Http\Controllers\PatrolController;
use App\Http\Controllers\CheckpointController;
use App\Http\Controllers\SubdivisionController;
use App\Http\Controllers\SlowPayerController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Ruta Raíz: Muestra el estado de carga (animación)
Route::get('/', function () {
    return Inertia::render('Loading');
});

// Ruta Welcome: La landing page a la que se redirige después de cargar
Route::get('/inicio', function () {
    return Inertia::render('Welcome'); 
})->name('welcome');

// ==========================================================================================
// ZONA PROTEGIDA: Todas estas rutas requieren que el usuario haya iniciado sesión
// ==========================================================================================
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Semáforo del sidebar
    Route::get('/payment-status', [PaymentStatusController::class, 'show'])->name('payment.status');

    // Cambiar contexto de propiedad (multiples propiedades)
    Route::post('/switch-property', function (Request $request) {
        $request->validate(['property_id' => 'required']);

        $user = $request->user();
        $propertyId = $request->input('property_id');
        $targetSubdivisionId = null;

        if (is_string($propertyId) && str_starts_with($propertyId, 'admin_')) {
            $targetSubdivisionId = (int) str_replace('admin_', '', $propertyId);
            $hasAdminAccess = DB::table(config('permission.table_names.model_has_roles'))
                ->where('model_id', $user->id)
                ->where('model_type', get_class($user))
                ->where('team_id', $targetSubdivisionId)
                ->exists();

            if (!$hasAdminAccess) {
                abort(403, 'No tienes permisos administrativos en este fraccionamiento.');
            }
            setPermissionsTeamId($targetSubdivisionId);
        } else {
            $targetUnit = $user->privateUnits()->where('private_units.id', $propertyId)->first();
            if (!$targetUnit) {
                abort(403, 'No tienes acceso a esta propiedad.');
            }
            
            $targetSubdivisionId = $targetUnit->subdivision_id;
            setPermissionsTeamId($targetSubdivisionId);
        }
        
        session([
            'current_property_id' => $propertyId,
            'current_subdivision_id' => $targetSubdivisionId
        ]);
        
        return back();
    })->name('context.switch');

    // --- Roles y permisos ---
    Route::resource('roles', RoleController::class);
    Route::post('/permissions', [RoleController::class, 'storePermission'])->name('permissions.store');
    Route::put('/permissions/{permission}', [RoleController::class, 'updatePermission'])->name('permissions.update');
    Route::delete('/permissions/{permission}', [RoleController::class, 'destroyPermission'])->name('permissions.destroy');

    // --- Propiedades (Private Units) ---
    Route::get('/admin/private-units', [PrivateUnitController::class, 'index'])->name('admin.private-units.index');
    Route::get('/admin/private-units/create', [PrivateUnitController::class, 'create'])->name('admin.private-units.create');
    Route::post('/admin/private-units', [PrivateUnitController::class, 'store'])->name('admin.private-units.store');
    Route::get('/admin/private-units/{privateUnit}', [PrivateUnitController::class, 'show'])->name('admin.private-units.show');
    Route::put('/admin/private-units/{privateUnit}', [PrivateUnitController::class, 'update'])->name('admin.private-units.update');
    Route::patch('/admin/private-units/{privateUnit}/toggle-status', [PrivateUnitController::class, 'toggleStatus'])->name('admin.private-units.toggle-status');
    Route::delete('/admin/private-units/{privateUnit}', [PrivateUnitController::class, 'destroy'])->name('admin.private-units.destroy');

    // --- Morosos ---
    Route::get('/slow-payers', [PrivateUnitController::class, 'slowPayersIndex'])->name('slowPayers.index');

    // --- Vehículos ---
    Route::get('/vehicles', [VehicleController::class, 'index'])->name('vehicles.index');
    Route::get('/admin/vehicles', [VehicleController::class, 'adminIndex'])->name('admin.vehicles.index');
    Route::get('/vehicles/create', [VehicleController::class, 'create'])->name('vehicles.create');
    Route::post('/vehicles', [VehicleController::class, 'store'])->name('vehicles.store');
    Route::put('/vehicles/{vehicle}', [VehicleController::class, 'update'])->name('vehicles.update');
    Route::delete('/vehicles/{vehicle}', [VehicleController::class, 'destroy'])->name('vehicles.destroy');

    // --- Mascotas ---
    Route::get('/pets/admin', [PetController::class, 'adminIndex'])->name('admin.pets.index');
    Route::resource('pets', PetController::class);

    // --- Amenidades ---
    Route::get('/amenities', [AmenityController::class, 'index'])->name('amenities.index');
    Route::get('/amenities/create', [AmenityController::class, 'create'])->name('amenities.create');
    Route::post('/amenities', [AmenityController::class, 'store'])->name('amenities.store');
    Route::get('/amenities/{amenity}/edit', [AmenityController::class, 'edit'])->name('amenities.edit');
    Route::put('/amenities/{amenity}', [AmenityController::class, 'update'])->name('amenities.update');
    Route::delete('/amenities/{amenity}', [AmenityController::class, 'destroy'])->name('amenities.destroy');
    Route::patch('/amenities/{amenity}/toggle', [AmenityController::class, 'toggleStatus'])->name('amenities.toggle');
    Route::get('/amenities/{amenity}/availability', [AmenityController::class, 'availability'])->name('amenities.availability');
    Route::post('/amenities/{amenityId}/reservations', [AmenityController::class, 'storeReservation'])->name('reservations.store');

    // --- Finanzas y Cuotas ---
    Route::get('/fees', [GeneratedFeeController::class, 'index'])->name('fees.index');
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/bank-reconciliations', [BankReconciliationController::class, 'index'])->name('bank_reconciliations.index');
    Route::get('/billing-concepts', [BillingConceptController::class, 'index'])->name('billing-concepts.index');

    // --- Directorio / Residentes ---
    Route::resource('users', UserController::class);

    // --- Caseta y Accesos ---
    Route::resource('register-invitations', RegisterInvitationController::class)->names('register_invitations');
    Route::resource('visits', VisitController::class);
    Route::resource('parcel-services', ParcelServiceController::class)->names('parcel_services');
    
    // --- Seguridad ---
    Route::resource('patrols', PatrolController::class);
    Route::resource('checkpoints', CheckpointController::class);

    // --- Configuración ---
    Route::resource('subdivisions', SubdivisionController::class);

}); 