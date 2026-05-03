<?php

namespace Database\Seeders;

use App\Models\Community\User;
use App\Models\Settings\Subdivision;
use App\Models\Community\PrivateUnit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Crear los Roles base del sistema
        // Usamos firstOrCreate para que no marque error si ya existen
        $roleSuperAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $roleAdmin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $roleResidente = Role::firstOrCreate(['name' => 'Residente', 'guard_name' => 'web']);
        // 2. Crear un Fraccionamiento de prueba
        $subdivision = Subdivision::create([
            'name' => 'Coto Las Lomas',
            'slug' => 'coto-las-lomas',
            'houses_amount' => 16,
            'address'=> '',
            'exterior_number'=> 302, 
            'suburb'=>'', 
            'town'=>'', 
            'federal_state'=>'', 
            'post_code'=>'', 
        ]);

        // 3. Crear tu Usuario Administrador Maestro
        $user = User::create([
            'name' => 'Admin Frax',
            'email' => 'admin@frax.com',
            'password' => Hash::make('password'), // La contraseña será: password
        ]);

        // 4. Asignarle el Rol de Admin al usuario
        $user->assignRole($roleAdmin);

        // 5. Vincular al usuario con el Fraccionamiento en la tabla pivote
        $subdivision->users()->attach($user->id, [
            'role_in_subdivision' => 'Administrador',
            'is_current' => true
        ]);

        // 6. Crear la primera Casa/Unidad para este administrador
        $unit = PrivateUnit::create([
            'subdivision_id' => $subdivision->id,
            'lot_number' => 'L-01',
            'unit_street' => 'Av. Principal',
            'exterior_number' => '100',
            'status' => 'Activo',
        ]);

        // 7. Vincular al usuario con su Casa en la tabla pivote
        $unit->users()->attach($user->id, [
            'role_in_unit' => 'Dueño',
            'is_primary' => true
        ]);

        // Opcional: Crear un vecino normal para que puedas probar cómo lo ve un residente
        $vecino = User::create([
            'name' => 'Juan Vecino',
            'email' => 'vecino@frax.com',
            'password' => Hash::make('password'),
        ]);
        
        $vecino->assignRole($roleResidente);
        
        $subdivision->users()->attach($vecino->id, [
            'role_in_subdivision' => 'Residente',
            'is_current' => true
        ]);

        $unitVecino = PrivateUnit::create([
            'subdivision_id' => $subdivision->id,
            'lot_number' => 'L-02',
            'unit_street' => 'Av. Principal',
            'exterior_number' => '102',
            'status' => 'Activo',
        ]);

        $unitVecino->users()->attach($vecino->id, [
            'role_in_unit' => 'Dueño',
            'is_primary' => true
        ]);
    }
}