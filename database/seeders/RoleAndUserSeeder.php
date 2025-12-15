<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Carbon\Carbon;

class RoleAndUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar caché de permisos
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ---------------------------------------------------------
        // 1. DEFINIR ESTRUCTURA DE PERMISOS AGRUPADOS
        // ---------------------------------------------------------
        $permissionStructure = [
            'Seguridad y Accesos' => [
                'Escanear QR de Acceso',
                'Gestionar Bitácora de Accesos',
                'Crear Rondin',
                'Gestionar Puntos de Control', // Checkpoints
                'Ver Historial de Patrullaje',
            ],
            'Residentes y Visitas' => [
                'Gestionar Residentes',
                'Crear Invitaciones de Visita',
                'Aprobar Visitas',
                'Gestionar Mascotas',
                'Gestionar Vehículos',
            ],
            'Amenidades y Reservas' => [
                'Crear Amenidades',
                'Editar Amenidades',
                'Reservar Amenidades',
                'Aprobar Reservas',
                'Gestionar Reglas de Amenidades',
            ],
            'Finanzas y Pagos' => [
                'Ver Finanzas y Reportes',
                'Gestionar Conceptos de Cobro', // Billing Concepts
                'Generar Cuotas',               // Generated Fees
                'Registrar Pagos',              // Payments
                'Conciliar Bancos',             // Bank Reconciliations
                'Gestionar Proveedores',
            ],
            'Comunicación y Comunidad' => [
                'Publicar Avisos',              // Posts
                'Crear Eventos Comunitarios',   // Events
                'Gestionar Incidentes/Reportes',
                'Moderar Comentarios',
            ],
            'Administración General' => [
                'Gestionar Usuarios',           // Crear guardias, admins
                'Configurar Fraccionamiento',   // Theme, Notifications
                'Gestionar Paquetería',
            ]
        ];

        // Aplanar permisos para creación masiva
        $allPermissions = [];
        foreach ($permissionStructure as $group => $perms) {
            foreach ($perms as $permName) {
                Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
                $allPermissions[] = $permName;
            }
        }

        // =========================================================
        // FRACCIONAMIENTO 1: RESIDENCIAL LAS CUMBRES
        // =========================================================
        $subdivisionId1 = DB::table('subdivisions')->insertGetId([
            'name' => 'Residencial Las Cumbres',
            'slug' => 'residencial-las-cumbres',
            'address' => 'Av. Principal 123',
            'exterior_number' => 'S/N',
            'suburb' => 'Lomas Verdes',
            'town' => 'Zapopan',
            'federal_state' => 'Jalisco',
            'post_code' => '45000',
            'houses_amount' => 100,
            'configuration' => json_encode(['theme' => 'dark', 'notifications' => true]),
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->command->info("Fraccionamiento 1 (Las Cumbres) creado.");

        // Crear Roles Sub 1
        $rolesSub1 = [
            'Admin' => Role::create(['name' => 'Admin', 'team_id' => $subdivisionId1, 'guard_name' => 'web']),
            'Residente' => Role::create(['name' => 'Residente', 'team_id' => $subdivisionId1, 'guard_name' => 'web']),
            'Empleado' => Role::create(['name' => 'Empleado', 'team_id' => $subdivisionId1, 'guard_name' => 'web']),
            'Guardia' => Role::create(['name' => 'Guardia', 'team_id' => $subdivisionId1, 'guard_name' => 'web']),
        ];

        // Asignar permisos Sub 1
        $rolesSub1['Admin']->givePermissionTo($allPermissions);
        
        $rolesSub1['Residente']->givePermissionTo([
            'Crear Invitaciones de Visita', 'Reservar Amenidades', 'Gestionar Mascotas', 
            'Gestionar Vehículos', 'Ver Finanzas y Reportes', 'Publicar Avisos', 
            'Gestionar Incidentes/Reportes'
        ]);
        
        $rolesSub1['Guardia']->givePermissionTo([
            'Escanear QR de Acceso', 'Gestionar Bitácora de Accesos', 'Crear Rondin', 
            'Gestionar Incidentes/Reportes', // Corregido
            'Gestionar Paquetería'
        ]);

        $rolesSub1['Empleado']->givePermissionTo([
            'Ver Finanzas y Reportes', 'Registrar Pagos', 'Gestionar Residentes', 
            'Aprobar Reservas', 'Publicar Avisos'
        ]);

        // Unidades Privadas Sub 1
        $unitsSub1 = [];
        for ($i = 1; $i <= 5; $i++) {
            $unitsSub1[] = DB::table('private_units')->insertGetId([
                'lot_number' => 'CUMBRES-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'square_meters' => rand(120, 300),
                'unit_street' => 'Calle Roble',
                'exterior_number' => (string)$i, 
                'int_number' => null,
                'status' => 'Activo',
                'subdivision_id' => $subdivisionId1,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        // =========================================================
        // FRACCIONAMIENTO 2: RESIDENCIAL LOS OLIVOS
        // =========================================================
        $subdivisionId2 = DB::table('subdivisions')->insertGetId([
            'name' => 'Residencial Los Olivos',
            'slug' => 'residencial-los-olivos',
            'address' => 'Av. Vallarta 555',
            'exterior_number' => '100',
            'suburb' => 'Jardines Vallarta',
            'town' => 'Zapopan',
            'federal_state' => 'Jalisco',
            'post_code' => '45020',
            'houses_amount' => 50,
            'configuration' => json_encode(['theme' => 'light', 'notifications' => true]),
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->command->info("Fraccionamiento 2 (Los Olivos) creado.");

        // Crear Roles Sub 2
        $rolesSub2 = [
            'Admin' => Role::create(['name' => 'Admin', 'team_id' => $subdivisionId2, 'guard_name' => 'web']),
            'Residente' => Role::create(['name' => 'Residente', 'team_id' => $subdivisionId2, 'guard_name' => 'web']),
            'Guardia' => Role::create(['name' => 'Guardia', 'team_id' => $subdivisionId2, 'guard_name' => 'web']),
        ];

        // Asignar permisos Sub 2
        $rolesSub2['Admin']->givePermissionTo($allPermissions);
        $rolesSub2['Residente']->givePermissionTo([
            'Crear Invitaciones de Visita', 'Reservar Amenidades', 'Gestionar Mascotas', 
            'Gestionar Vehículos', 'Ver Finanzas y Reportes', 'Publicar Avisos', 
            'Gestionar Incidentes/Reportes'
        ]);
        $rolesSub2['Guardia']->givePermissionTo([
            'Escanear QR de Acceso', 'Gestionar Bitácora de Accesos', 'Crear Rondin', 
            'Gestionar Incidentes/Reportes', 'Gestionar Paquetería'
        ]);

        // Unidades Privadas Sub 2
        $unitsSub2 = [];
        for ($i = 1; $i <= 5; $i++) {
            $unitsSub2[] = DB::table('private_units')->insertGetId([
                'lot_number' => 'OLIVOS-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'square_meters' => rand(90, 200),
                'unit_street' => 'Calle Olivo',
                'exterior_number' => (string)$i,
                'int_number' => null,
                'status' => 'Activo',
                'subdivision_id' => $subdivisionId2,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        // =========================================================
        // CREAR USUARIOS
        // =========================================================

        // 1. Super Admin
        $adminUser = User::firstOrCreate(
            ['email' => 'angel@gmail.com'],
            ['name' => 'Angel Admin', 'password' => Hash::make('321321321'), 'email_verified_at' => now()]
        );
        
        setPermissionsTeamId($subdivisionId1); 
        $adminUser->assignRole($rolesSub1['Admin']);
        DB::table('subdivision_user')->updateOrInsert(
            ['user_id' => $adminUser->id, 'subdivision_id' => $subdivisionId1],
            ['is_current' => true, 'created_at' => now(), 'updated_at' => now()]
        );

        setPermissionsTeamId($subdivisionId2);
        $adminUser->assignRole($rolesSub2['Admin']);
        DB::table('subdivision_user')->updateOrInsert(
            ['user_id' => $adminUser->id, 'subdivision_id' => $subdivisionId2],
            ['is_current' => false, 'created_at' => now(), 'updated_at' => now()]
        );

        // 2. Guardia
        $guardUser = User::firstOrCreate(
            ['email' => 'guardia@gmail.com'],
            ['name' => 'Pedro Guardia', 'password' => Hash::make('321321321'), 'email_verified_at' => now()]
        );
        setPermissionsTeamId($subdivisionId1);
        $guardUser->assignRole($rolesSub1['Guardia']);
        DB::table('subdivision_user')->updateOrInsert(
            ['user_id' => $guardUser->id, 'subdivision_id' => $subdivisionId1],
            ['is_current' => true, 'created_at' => now(), 'updated_at' => now()]
        );

        // 3. Residente 1: JUAN (Multi-Fraccionamiento)
        $residentUser = User::firstOrCreate(
            ['email' => 'residente@gmail.com'],
            ['name' => 'Juan Residente', 'password' => Hash::make('321321321'), 'email_verified_at' => now()]
        );

        // Juan en Sub 1
        setPermissionsTeamId($subdivisionId1);
        $residentUser->assignRole($rolesSub1['Residente']);
        DB::table('subdivision_user')->updateOrInsert(
            ['user_id' => $residentUser->id, 'subdivision_id' => $subdivisionId1],
            ['is_current' => true, 'created_at' => now(), 'updated_at' => now()]
        );

        // Juan en Sub 2
        setPermissionsTeamId($subdivisionId2);
        $residentUser->assignRole($rolesSub2['Residente']);
        DB::table('subdivision_user')->updateOrInsert(
            ['user_id' => $residentUser->id, 'subdivision_id' => $subdivisionId2],
            ['is_current' => false, 'created_at' => now(), 'updated_at' => now()]
        );
        
        // Perfil Residente Juan
        $residentIdJuan = DB::table('residents')->insertGetId([
            'full_name' => $residentUser->name,
            'email' => $residentUser->email,
            'user_id' => $residentUser->id,
            'person_type' => 'Propietario',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // Unidades de Juan
        if (isset($unitsSub1[0])) {
            DB::table('residence_units')->insert([
                'resident_id' => $residentIdJuan,
                'private_unit_id' => $unitsSub1[0],
                'role_in_unit' => 'Dueño',
                'primary' => true,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
        if (isset($unitsSub2[0])) {
            DB::table('residence_units')->insert([
                'resident_id' => $residentIdJuan,
                'private_unit_id' => $unitsSub2[0],
                'role_in_unit' => 'Dueño',
                'primary' => false,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        // =========================================================
        // 4. NUEVO RESIDENTE: CARLOS (Solo Sub 1, con atraso)
        // =========================================================
        $residentUserCarlos = User::firstOrCreate(
            ['email' => 'residente2@gmail.com'],
            ['name' => 'Carlos Inquilino', 'password' => Hash::make('321321321'), 'email_verified_at' => now()]
        );

        // Carlos solo en Sub 1
        setPermissionsTeamId($subdivisionId1);
        $residentUserCarlos->assignRole($rolesSub1['Residente']);
        DB::table('subdivision_user')->updateOrInsert(
            ['user_id' => $residentUserCarlos->id, 'subdivision_id' => $subdivisionId1],
            ['is_current' => true, 'created_at' => now(), 'updated_at' => now()]
        );

        // Perfil Residente Carlos
        $residentIdCarlos = DB::table('residents')->insertGetId([
            'full_name' => $residentUserCarlos->name,
            'email' => $residentUserCarlos->email,
            'user_id' => $residentUserCarlos->id,
            'person_type' => 'Inquilino',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // Unidad de Carlos (Usamos la segunda unidad disponible del array unitsSub1)
        if (isset($unitsSub1[1])) {
            DB::table('residence_units')->insert([
                'resident_id' => $residentIdCarlos,
                'private_unit_id' => $unitsSub1[1], // Unidad Diferente a la de Juan
                'role_in_unit' => 'Inquilino',
                'primary' => true,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        $this->command->info("Usuarios creados: Juan (Multi) y Carlos (Solo Cumbres).");

        // =========================================================
        // 5. GENERACIÓN DE CONCEPTOS, CUOTAS Y PAGOS
        // =========================================================

        // Crear Conceptos de Cobro (Billing Concepts)
        $conceptSub1 = DB::table('billing_concepts')->insertGetId([
            'name' => 'Mantenimiento Mensual',
            'base_amount' => 1500.00,
            'recurrence_type' => 'Mensual',
            'slow_payers_apply' => true,
            'subdivision_id' => $subdivisionId1,
            'created_at' => now(), 'updated_at' => now()
        ]);

        $conceptSub2 = DB::table('billing_concepts')->insertGetId([
            'name' => 'Mantenimiento Mensual',
            'base_amount' => 2000.00,
            'recurrence_type' => 'Mensual',
            'slow_payers_apply' => true,
            'subdivision_id' => $subdivisionId2,
            'created_at' => now(), 'updated_at' => now()
        ]);

        // --- CASO A: JUAN en SUB 1 (AL CORRIENTE) ---
        // Generamos 3 cuotas pasadas y las pagamos todas.
        // Resultado: 0 pagos expirados -> Al corriente.
        for ($i = 3; $i >= 1; $i--) {
            $date = Carbon::now()->subMonths($i);
            
            // 1. Generar la Cuota (Fee)
            DB::table('generated_fees')->insert([
                'payment_reference' => 'REF-S1-JUAN-' . $i,
                'total_amount' => 1500.00,
                'amount_paid' => 1500.00, // Pagado completo
                'expiration_date' => $date->copy()->addDays(10), // Expiró hace tiempo
                'start_period' => $date->copy()->startOfMonth(),
                'end_period' => $date->copy()->endOfMonth(),
                'status' => 'Pagado',
                'private_unit_id' => $unitsSub1[0],
                'billing_concept_id' => $conceptSub1,
                'created_at' => $date, 'updated_at' => $date
            ]);

            // 2. Registrar el Pago (Payment)
            DB::table('payments')->insert([
                'transaction_folio' => 'TX-S1-JUAN-' . $i,
                'amount' => 1500.00,
                'payment_date' => $date->copy()->addDays(5), // Pagó antes de expirar
                'payment_method' => 'Transferencia',
                'billing_concept_id' => $conceptSub1,
                'resident_id' => $residentIdJuan,
                'created_at' => $date, 'updated_at' => $date
            ]);
        }

        // --- CASO B: JUAN en SUB 2 (MOROSO) ---
        // Generamos 3 cuotas pasadas y NO las pagamos.
        // Resultado: 3 pagos expirados (>=3) -> Moroso.
        for ($i = 3; $i >= 1; $i--) {
            $date = Carbon::now()->subMonths($i);
            
            // 1. Generar la Cuota (Fee) Vencida
            DB::table('generated_fees')->insert([
                'payment_reference' => 'REF-S2-JUAN-' . $i,
                'total_amount' => 2000.00,
                'amount_paid' => 0.00, // Nada pagado
                'expiration_date' => $date->copy()->addDays(5), // Expiró hace tiempo
                'start_period' => $date->copy()->startOfMonth(),
                'end_period' => $date->copy()->endOfMonth(),
                'status' => 'Atrasada', // Estatus de deuda
                'private_unit_id' => $unitsSub2[0],
                'billing_concept_id' => $conceptSub2,
                'created_at' => $date, 'updated_at' => $date
            ]);

            // NO generamos registro en la tabla 'payments'
        }

        // --- CASO C: CARLOS en SUB 1 (PAGO RETRASADO) ---
        // Generamos 1 cuota pasada y NO la pagamos.
        // Resultado: 1 pago expirado (< 2) -> Retrasado (No Moroso).
        $dateCarlos = Carbon::now()->subMonth(1);
        
        DB::table('generated_fees')->insert([
            'payment_reference' => 'REF-S1-CARLOS-1',
            'total_amount' => 1500.00,
            'amount_paid' => 0.00, // Nada pagado
            'expiration_date' => $dateCarlos->copy()->addDays(5), // Ya expiró
            'start_period' => $dateCarlos->copy()->startOfMonth(),
            'end_period' => $dateCarlos->copy()->endOfMonth(),
            'status' => 'Atrasada',
            'private_unit_id' => $unitsSub1[1], // Unidad de Carlos
            'billing_concept_id' => $conceptSub1,
            'created_at' => $dateCarlos, 'updated_at' => $dateCarlos
        ]);

        $this->command->info("Seed financiero completo: Juan (Limpio en Sub1, Moroso en Sub2), Carlos (Retrasado en Sub1).");
    }
}