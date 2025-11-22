<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RoleAndUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ---------------------------------------------------------
        // 1. CREAR ROLES
        // ---------------------------------------------------------
        $roles = [
            ['name' => 'Admin', 'description' => 'Administrador General del Sistema'],
            ['name' => 'Resident', 'description' => 'Residente o Propietario'],
            ['name' => 'Employee', 'description' => 'Empleado (Guardia, Mantenimiento, etc.)'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['name' => $role['name']], // Busca por nombre para no duplicar
                ['description' => $role['description']]
            );
        }

        // Recuperamos los IDs para usarlos en la asignación
        $adminRoleId = DB::table('roles')->where('name', 'Admin')->value('id');
        $residentRoleId = DB::table('roles')->where('name', 'Resident')->value('id');
        $employeeRoleId = DB::table('roles')->where('name', 'Employee')->value('id');

        // ---------------------------------------------------------
        // 2. CREAR USUARIOS Y ASIGNAR ROLES
        // ---------------------------------------------------------
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'angel@gmail.com',
                'password' => '321321321',
                'role_id' => $adminRoleId,
                'primary' => 1 // Rol principal
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

        foreach ($users as $userData) {
            // A. Crear Usuario (si no existe)
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make($userData['password']),
                ]
            );

            // B. Vincular en tabla pivote 'role_user'
            DB::table('role_user')->updateOrInsert(
                [
                    'user_id' => $user->id,
                    'role_id' => $userData['role_id']
                ],
                [
                    'primary' => $userData['primary']
                ]
            );
        }
    }
}