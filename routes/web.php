<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

// ==============================================================================
// IMPORTACIONES DE CONTROLADORES
// ==============================================================================

// ⚙️ Settings (Configuración)
use App\Http\Controllers\Settings\RoleController;
use App\Http\Controllers\Settings\SubdivisionController;

// 🏘️ Community (Comunidad y Propiedades)
use App\Http\Controllers\Community\UserController;
use App\Http\Controllers\Community\PrivateUnitController;
use App\Http\Controllers\Community\PrivateUnitContactController;
use App\Http\Controllers\Community\VehicleController;
use App\Http\Controllers\Community\PetController;
use App\Http\Controllers\Community\NoticeBoardController;
use App\Http\Controllers\Community\TagController;

// 💰 Finances (Finanzas y Cobranza)
use App\Http\Controllers\Finances\GeneratedFeeController;
use App\Http\Controllers\Finances\PaymentController;
use App\Http\Controllers\Finances\BankReconciliationController;
use App\Http\Controllers\Finances\BillingConceptController;

// 🏊 Amenities (Amenidades y Reservas)
use App\Http\Controllers\Amenities\AmenityController;
use App\Http\Controllers\Amenities\ReservationController;

// 🛡️ Gatehouse (Caseta y Accesos)
use App\Http\Controllers\Gatehouse\RegisterInvitationController;
use App\Http\Controllers\Gatehouse\VisitController;
use App\Http\Controllers\Gatehouse\ParcelServiceController;

// 🚓 Security (Seguridad y Rondines)
use App\Http\Controllers\Security\PatrolController;
use App\Http\Controllers\Security\CheckpointController;
use App\Models\Finances\Payment;

// ==============================================================================
// RUTAS PÚBLICAS
// ==============================================================================

Route::get('/', function () {
    return Inertia::render('Loading');
});

Route::get('/inicio', function () {
    return Inertia::render('Welcome'); 
})->name('welcome');


// ==============================================================================
// ZONA PROTEGIDA
// ==============================================================================
Route::middleware(['auth', 'verified'])->group(function () {

    // --- DASHBOARD ---
    Route::get('/dashboard', function () {
    return Inertia::render('Dashboard/Index'); // <--- Ruta actualizada
    })->name('dashboard');

    // --- SWITCH DE CONTEXTO ---
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

    // ==========================================
    // ⚙️ MÓDULO: SETTINGS
    // ==========================================
    Route::resource('subdivisions', SubdivisionController::class);
    Route::resource('roles', RoleController::class);
    Route::post('/permissions', [RoleController::class, 'storePermission'])->name('permissions.store');
    Route::put('/permissions/{permission}', [RoleController::class, 'updatePermission'])->name('permissions.update');
    Route::delete('/permissions/{permission}', [RoleController::class, 'destroyPermission'])->name('permissions.destroy');

    // ==========================================
    // 🏘️ MÓDULO: COMMUNITY
    // ==========================================
    
    // Unidades Privadas
    Route::resource('private-units', PrivateUnitController::class)->names('admin.private-units');
    Route::get('/admin/private-units', [PrivateUnitController::class, 'index'])->name('admin.private-units.index');
    Route::get('/admin/private-units/create', [PrivateUnitController::class, 'create'])->name('admin.private-units.create');
    Route::post('/admin/private-units', [PrivateUnitController::class, 'store'])->name('admin.private-units.store');
    Route::get('/admin/private-units/{privateUnit}', [PrivateUnitController::class, 'show'])->name('admin.private-units.show');
    Route::put('/admin/private-units/{privateUnit}', [PrivateUnitController::class, 'update'])->name('admin.private-units.update');
    Route::patch('/admin/private-units/{privateUnit}/toggle-status', [PrivateUnitController::class, 'toggleStatus'])->name('admin.private-units.toggle-status');
    Route::delete('/admin/private-units/{privateUnit}', [PrivateUnitController::class, 'destroy'])->name('admin.private-units.destroy');
    
    // Vehículos
    Route::get('/vehicles', [VehicleController::class, 'index'])->name('vehicles.index');
    Route::get('/admin/vehicles', [VehicleController::class, 'adminIndex'])->name('admin.vehicles.index');
    Route::get('/vehicles/create', [VehicleController::class, 'create'])->name('vehicles.create');
    Route::post('/vehicles', [VehicleController::class, 'store'])->name('admin.vehicles.store');
    Route::put('/vehicles/{vehicle}', [VehicleController::class, 'update'])->name('vehicles.update');
    Route::delete('/vehicles/{vehicle}', [VehicleController::class, 'destroy'])->name('vehicles.destroy');

    // Mascotas
    Route::get('/community/pets/admin', [PetController::class, 'adminIndex'])->name('admin.pets.index');
    Route::post('/pets', [VehicleController::class, 'store'])->name('admin.vehicles.store');
    Route::resource('pets', PetController::class);

    // AGREGA ESTAS LÍNEAS PARA MASCOTAS:
    Route::post('/pets', [PetController::class, 'store'])->name('admin.pets.store');
    Route::delete('/pets/{pet}', [PetController::class, 'destroy'])->name('admin.pets.destroy');

    // AGREGA ESTAS LÍNEAS PARA TAGS (Para que tampoco te dé error ese botón):
    Route::post('/tags', [TagController::class, 'store'])->name('admin.tags.store');
    Route::delete('/tags/{tag}', [TagController::class, 'destroy'])->name('admin.tags.destroy');

    // Morosos
    Route::get('/slow-payers', [PrivateUnitController::class, 'slowPayersIndex'])->name('slowPayers.index');

    // --- DIRECTORIO COMUNITARIO (Usuarios CRUD y Pestañas) ---
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    
    Route::get('/users/emergencies', [UserController::class, 'emergencies'])->name('users.emergencies');
    Route::get('/users/services', [UserController::class, 'services'])->name('users.services');
    
    Route::get('/users/settings', [UserController::class, 'settings'])->name('users.settings');
    Route::post('/users/settings', [UserController::class, 'updateSettings'])->name('users.settings.update');

    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::prefix('community/notice-board')->name('notice-board.')->group(function () {
    Route::get('/', [NoticeBoardController::class, 'index'])->name('index');
    Route::post('/', [NoticeBoardController::class, 'store'])->name('store');
    Route::put('/{post}', [NoticeBoardController::class, 'update'])->name('update');
    Route::delete('/{post}', [NoticeBoardController::class, 'destroy'])->name('destroy');
    Route::post('/{post}/react', [NoticeBoardController::class, 'toggleReact'])->name('react');
    Route::post('/{post}/comment', [NoticeBoardController::class, 'storeComment'])->name('comment.store');

    Route::post('/poll-option/{pollOption}/vote', [NoticeBoardController::class, 'vote'])->name('poll.vote');
    });

    // ==========================================
    // 💰 MÓDULO: FINANCES
    // ==========================================
    Route::get('/fees', [GeneratedFeeController::class, 'index'])->name('fees.index');
    Route::get('/fees/{fee}/pay', [GeneratedFeeController::class, 'pay'])->name('fees.pay');
    Route::post('/fees/{fee}/process', [GeneratedFeeController::class, 'processPayment'])->name('fees.process');
    Route::resource('billing-concepts', BillingConceptController::class);
    Route::resource('payments', PaymentController::class);
    Route::get('/bank-reconciliations', [BankReconciliationController::class, 'index'])->name('bank_reconciliations.index');
    Route::put('/bank-reconciliations/{bankReconciliation}', [BankReconciliationController::class, 'update'])->name('bank-reconciliations.update');
    Route::post('/propiedades/{privateUnit}/cargos-manuales', [GeneratedFeeController::class, 'storeManual'])->name('admin.fees.storeManual');
    Route::post('/propiedades/{privateUnit}/abonos', [GeneratedFeeController::class, 'addBalance'])->name('admin.fees.addBalance');

    // ==========================================
    // 🏊 MÓDULO: AMENITIES
    // ==========================================
    Route::get('/amenities', [AmenityController::class, 'index'])->name('amenities.index');
    Route::get('/amenities/create', [AmenityController::class, 'create'])->name('amenities.create');
    Route::post('/amenities', [AmenityController::class, 'store'])->name('amenities.store');
    Route::get('/amenities/{amenity}/edit', [AmenityController::class, 'edit'])->name('amenities.edit');
    Route::put('/amenities/{amenity}', [AmenityController::class, 'update'])->name('amenities.update');
    Route::delete('/amenities/{amenity}', [AmenityController::class, 'destroy'])->name('amenities.destroy');
    Route::patch('/amenities/{amenity}/toggle', [AmenityController::class, 'toggleStatus'])->name('amenities.toggle');
    Route::get('/amenities/{amenity}/availability', [AmenityController::class, 'availability'])->name('amenities.availability');
    // Route::post('/amenities/{amenityId}/reservations', [AmenityController::class, 'storeReservation'])->name('reservations.store');
    Route::get('/amenities/{amenity}/availability', [ReservationController::class, 'getAvailability'])->name('api.amenities.availability');

    // Rutas para Reservaciones
    Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservations/{reservation}/edit', [ReservationController::class, 'edit'])->name('reservations.edit');
    Route::patch('/reservations/{reservation}', [ReservationController::class, 'update'])->name('reservations.update');
    Route::patch('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');
    Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');

    // ==========================================
    // 🛡️ MÓDULO: GATEHOUSE & SECURITY
    // ==========================================
    Route::resource('register-invitations', RegisterInvitationController::class);
    Route::resource('visits', VisitController::class);
    Route::resource('parcel-services', ParcelServiceController::class);
    Route::resource('patrols', PatrolController::class);
    Route::resource('checkpoints', CheckpointController::class);

});