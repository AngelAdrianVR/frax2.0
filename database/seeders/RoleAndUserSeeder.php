<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

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
                'int_number' => (string)$i,
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

        // Asignar permisos Sub 2 (Igual lógica)
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
                'int_number' => (string)$i,
                'status' => 'Activo',
                'subdivision_id' => $subdivisionId2,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        // =========================================================
        // CREAR USUARIOS
        // =========================================================

        // 1. Super Admin (Solo en Sub 1 para pruebas básicas)
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

        // 2. Guardia (Solo en Sub 1)
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

        // 3. Residente Multi-Tenancy (En AMBOS fraccionamientos)
        $residentUser = User::firstOrCreate(
            ['email' => 'residente@gmail.com'],
            ['name' => 'Juan Residente', 'password' => Hash::make('321321321'), 'email_verified_at' => now()]
        );

        // A) Asignar a Sub 1
        setPermissionsTeamId($subdivisionId1);
        $residentUser->assignRole($rolesSub1['Residente']);
        DB::table('subdivision_user')->updateOrInsert(
            ['user_id' => $residentUser->id, 'subdivision_id' => $subdivisionId1],
            ['is_current' => true, 'created_at' => now(), 'updated_at' => now()]
        );

        // B) Asignar a Sub 2 (Nota: is_current false para que inicie en el 1 por defecto)
        setPermissionsTeamId($subdivisionId2);
        $residentUser->assignRole($rolesSub2['Residente']);
        DB::table('subdivision_user')->updateOrInsert(
            ['user_id' => $residentUser->id, 'subdivision_id' => $subdivisionId2],
            ['is_current' => false, 'created_at' => now(), 'updated_at' => now()]
        );

        // =========================================================
        // PERFIL DE RESIDENTE Y ASIGNACIÓN DE UNIDADES
        // =========================================================
        
        // Crear perfil de residente único (O uno por sub si tu lógica lo requiere, aquí asumo uno global)
        $residentId = DB::table('residents')->insertGetId([
            'full_name' => $residentUser->name,
            'email' => $residentUser->email,
            'user_id' => $residentUser->id,
            'person_type' => 'Propietario',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // Asignar Casa en Sub 1 (Las Cumbres)
        if (isset($unitsSub1[0])) {
            DB::table('residence_units')->insert([
                'resident_id' => $residentId,
                'private_unit_id' => $unitsSub1[0],
                'role_in_unit' => 'Dueño',
                'primary' => true, // Casa principal
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        // Asignar Casa en Sub 2 (Los Olivos)
        if (isset($unitsSub2[0])) {
            DB::table('residence_units')->insert([
                'resident_id' => $residentId,
                'private_unit_id' => $unitsSub2[0],
                'role_in_unit' => 'Dueño',
                'primary' => false, // Casa secundaria
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        $this->command->info("Seed completo: Usuario Residente configurado con propiedades en 2 fraccionamientos.");
    }
}