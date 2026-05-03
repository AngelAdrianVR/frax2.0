<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

// ==============================================================================
// IMPORTACIONES DE CONTROLADORES (Organizados por tus nuevas carpetas)
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

// ==============================================================================
// RUTAS PÚBLICAS
// ==============================================================================

// Ruta Raíz: Muestra el estado de carga (animación)
Route::get('/', function () {
    // Nota: Si moviste Loading.vue, actualiza esto (ej: 'General/Loading')
    return Inertia::render('Loading');
});

// Ruta Welcome: La landing page a la que se redirige después de cargar
Route::get('/inicio', function () {
    return Inertia::render('Welcome'); 
})->name('welcome');


// ==============================================================================
// ZONA PROTEGIDA: Todas estas rutas requieren inicio de sesión verificado
// ==============================================================================
Route::middleware(['auth', 'verified'])->group(function () {

    // --- DASHBOARD ---
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // --- SWITCH DE CONTEXTO (Cambio de Fraccionamiento/Propiedad) ---
    Route::post('/switch-property', function (Request $request) {$request->validate(['property_id' => 'required']);

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
            // Esto es vital para el manejo de "Teams" en Spatie
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
    // ⚙️ MÓDULO: SETTINGS (Configuración global)
    // ==========================================
    Route::resource('subdivisions', SubdivisionController::class);
    
    Route::resource('roles', RoleController::class);
    Route::post('/permissions', [RoleController::class, 'storePermission'])->name('permissions.store');
    Route::put('/permissions/{permission}', [RoleController::class, 'updatePermission'])->name('permissions.update');
    Route::delete('/permissions/{permission}', [RoleController::class, 'destroyPermission'])->name('permissions.destroy');

    // ==========================================
    // 🏘️ MÓDULO: COMMUNITY (Propiedades y Residentes)
    // ==========================================
    
    // Unidades Privadas (Casas/Lotes)
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
    Route::post('/vehicles', [VehicleController::class, 'store'])->name('vehicles.store');
    Route::put('/vehicles/{vehicle}', [VehicleController::class, 'update'])->name('vehicles.update');
    Route::delete('/vehicles/{vehicle}', [VehicleController::class, 'destroy'])->name('vehicles.destroy');

    // Mascotas
    Route::get('/community/pets/admin', [PetController::class, 'adminIndex'])->name('admin.pets.index');
    Route::resource('pets', PetController::class);

    // Morosos (Relacionado a la comunidad)
    Route::get('/slow-payers', [PrivateUnitController::class, 'slowPayersIndex'])->name('slowPayers.index');

    // Directorio Comunitario y Pestañas
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/emergencies', [UserController::class, 'emergencies'])->name('users.emergencies');
    Route::get('/users/services', [UserController::class, 'services'])->name('users.services');
    
    // Ajustes de privacidad del perfil
    Route::get('/users/settings', [UserController::class, 'settings'])->name('users.settings');
    Route::post('/users/settings', [UserController::class, 'updateSettings'])->name('users.settings.update');

    // ==========================================
    // 💰 MÓDULO: FINANCES (Finanzas)
    // ==========================================
    
    // Cuotas y Conceptos
    Route::get('/fees', [GeneratedFeeController::class, 'index'])->name('fees.index');
    Route::get('/billing-concepts', [BillingConceptController::class, 'index'])->name('billing-concepts.index');
    
    // Pagos y Conciliaciones
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/bank-reconciliations', [BankReconciliationController::class, 'index'])->name('bank_reconciliations.index');

    // Acciones financieras directas sobre las propiedades
    Route::post('/propiedades/{privateUnit}/cargos-manuales', [GeneratedFeeController::class, 'storeManual'])->name('admin.fees.storeManual');
    Route::post('/propiedades/{privateUnit}/abonos', [GeneratedFeeController::class, 'addBalance'])->name('admin.fees.addBalance');

    // ==========================================
    // 🏊 MÓDULO: AMENITIES (Amenidades)
    // ==========================================
    Route::get('/amenities', [AmenityController::class, 'index'])->name('amenities.index');
    Route::get('/amenities/create', [AmenityController::class, 'create'])->name('amenities.create');
    Route::post('/amenities', [AmenityController::class, 'store'])->name('amenities.store');
    Route::get('/amenities/{amenity}/edit', [AmenityController::class, 'edit'])->name('amenities.edit');
    Route::put('/amenities/{amenity}', [AmenityController::class, 'update'])->name('amenities.update');
    Route::delete('/amenities/{amenity}', [AmenityController::class, 'destroy'])->name('amenities.destroy');
    Route::patch('/amenities/{amenity}/toggle', [AmenityController::class, 'toggleStatus'])->name('amenities.toggle');
    
    // Reservas de Amenidades
    Route::get('/amenities/{amenity}/availability', [AmenityController::class, 'availability'])->name('amenities.availability');
    Route::post('/amenities/{amenityId}/reservations', [AmenityController::class, 'storeReservation'])->name('reservations.store');

    // ==========================================
    // 🛡️ MÓDULO: GATEHOUSE & SECURITY (Caseta)
    // ==========================================
    Route::resource('register-invitations', RegisterInvitationController::class)->names('register_invitations');
    Route::resource('visits', VisitController::class);
    Route::resource('parcel-services', ParcelServiceController::class)->names('parcel_services');
    
    Route::resource('patrols', PatrolController::class);
    Route::resource('checkpoints', CheckpointController::class);

});