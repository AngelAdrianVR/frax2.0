<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Estructura base (Roles, Usuarios, Fraccionamientos, Casas)
        $this->call([
            RoleAndUserSeeder::class,
        ]);

        // 2. Módulos adicionales (Amenidades)
        // Se ejecuta después para asegurar que los fraccionamientos ya existen
        $this->call([
            AmenitySeeder::class,
        ]);
    }
}
