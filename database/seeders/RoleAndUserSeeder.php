<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RoleAndUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ---------------------------------------------------------
        // 0. PREPARACIÓN: LIMPIAR TABLAS (OPCIONAL, PERO RECOMENDADO EN DEV)
        // ---------------------------------------------------------
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // DB::table('residence_units')->truncate();
        // DB::table('residents')->truncate();
        // DB::table('private_units')->truncate();
        // DB::table('amenities')->truncate();
        // DB::table('subdivisions')->truncate();
        // DB::table('role_user')->truncate();
        // DB::table('users')->truncate();
        // DB::table('roles')->truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // ---------------------------------------------------------
        // 1. CREAR DATOS DEL ENTORNO (FRACCIONAMIENTO, AMENIDADES, UNIDADES)
        // ---------------------------------------------------------
        
        // 1.1 Crear Fraccionamiento
        $subdivisionId = DB::table('subdivisions')->insertGetId([
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
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info("Fraccionamiento creado: ID $subdivisionId");

        // 1.2 Crear Amenidades
        $amenities = [
            [
                'name' => 'Casa Club',
                'description' => 'Salón de eventos principal con aire acondicionado',
                'reservation_cost' => 1500.00,
                'subdivision_id' => $subdivisionId,
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'name' => 'Alberca General',
                'description' => 'Alberca templada de uso común',
                'reservation_cost' => 0.00,
                'subdivision_id' => $subdivisionId,
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'name' => 'Cancha de Tenis',
                'description' => 'Cancha profesional de concreto',
                'reservation_cost' => 200.00,
                'subdivision_id' => $subdivisionId,
                'created_at' => now(), 'updated_at' => now()
            ]
        ];
        DB::table('amenities')->insert($amenities);

        // 1.3 Crear Unidades Privadas (Lotes/Casas)
        // Creamos 10 unidades de prueba
        $privateUnitIds = [];
        for ($i = 1; $i <= 10; $i++) {
            $privateUnitIds[] = DB::table('private_units')->insertGetId([
                'lot_number' => 'L-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'square_meters' => rand(120, 300),
                'unit_street' => 'Calle Roble',
                'int_number' => (string)$i,
                'status' => 'Activo',
                'access_block' => false,
                'subdivision_id' => $subdivisionId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ---------------------------------------------------------
        // 2. CREAR ROLES
        // ---------------------------------------------------------
        $roles = [
            ['name' => 'Admin', 'description' => 'Administrador General del Sistema'],
            ['name' => 'Residente', 'description' => 'Residente o Propietario'],
            ['name' => 'Empleado', 'description' => 'Empleado (Guardia, Mantenimiento, etc.)'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['name' => $role['name']], 
                ['description' => $role['description'], 'updated_at' => now()]
            );
        }

        $adminRoleId = DB::table('roles')->where('name', 'Admin')->value('id');
        $residentRoleId = DB::table('roles')->where('name', 'Residente')->value('id');
        $employeeRoleId = DB::table('roles')->where('name', 'Empleado')->value('id');

        // ---------------------------------------------------------
        // 3. CREAR USUARIOS Y ASIGNAR ROLES + RESIDENCIAS
        // ---------------------------------------------------------
        $usersData = [
            [
                'name' => 'Super Admin',
                'email' => 'angel@gmail.com',
                'password' => '321321321',
                'role_id' => $adminRoleId,
                'primary' => 1
            ],
            [
                'name' => 'Juan Residente',
                'email' => 'residente@gmail.com',
                'password' => '321321321',
                'role_id' => $residentRoleId,
                'primary' => 0
            ],
            [
                'name' => 'Pedro Guardia',
                'email' => 'empleado@gmail.com',
                'password' => '321321321',
                'role_id' => $employeeRoleId,
                'primary' => 0
            ]
        ];

        foreach ($usersData as $userData) {
            // A. Crear Usuario
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make($userData['password']),
                    'email_verified_at' => now(),
                ]
            );

            // B. Vincular Rol
            DB::table('role_user')->updateOrInsert(
                [
                    'user_id' => $user->id,
                    'role_id' => $userData['role_id']
                ],
                [
                    'primary' => $userData['primary']
                ]
            );

            // C. LÓGICA ESPECÍFICA PARA RESIDENTES
            // Si el usuario tiene rol de Residente, le creamos perfil y asignamos casas
            if ($userData['role_id'] == $residentRoleId) {
                
                // 1. Crear Perfil en tabla 'residents'
                // Usamos updateOrInsert para no duplicar si corres el seeder dos veces
                $residentId = DB::table('residents')->where('user_id', $user->id)->value('id');

                if (!$residentId) {
                    $residentId = DB::table('residents')->insertGetId([
                        'full_name' => $user->name,
                        'phone' => '555-123-4567',
                        'email' => $user->email,
                        'person_type' => 'Propietario',
                        'is_emergency_contact' => true,
                        'is_slow_payer' => false,
                        'user_id' => $user->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // 2. Asignar 2 Propiedades (Unidades) a este residente
                // Tomamos las primeras 2 unidades creadas (índices 0 y 1)
                $unitsToAssign = array_slice($privateUnitIds, 0, 2);

                foreach ($unitsToAssign as $index => $unitId) {
                    DB::table('residence_units')->updateOrInsert(
                        [
                            'resident_id' => $residentId,
                            'private_unit_id' => $unitId
                        ],
                        [
                            'role_in_unit' => 'Dueño',
                            'responsible_for_payments' => true,
                            'start_date' => now(),
                            'primary' => ($index === 0), // La primera es la principal
                            'is_primary_owner' => true,
                            'alias' => ($index === 0) ? 'Casa Principal' : 'Casa de Renta',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }
                
                $this->command->info("Usuario {$user->name} configurado como Residente con 2 propiedades.");
            }
        }
    }
}