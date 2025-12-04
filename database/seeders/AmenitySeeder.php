<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Amenity;
use App\Models\Subdivision;

class AmenitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Definir horarios comunes para reutilizar
        $horarioEstandar = [
            'monday'    => ['09:00-22:00'],
            'tuesday'   => ['09:00-22:00'],
            'wednesday' => ['09:00-22:00'],
            'thursday'  => ['09:00-22:00'],
            'friday'    => ['09:00-23:00'],
            'saturday'  => ['10:00-23:00'],
            'sunday'    => ['10:00-20:00'],
        ];

        $horarioMatutino = [
            'monday'    => ['06:00-11:00', '16:00-21:00'],
            'tuesday'   => ['06:00-11:00', '16:00-21:00'],
            'wednesday' => ['06:00-11:00', '16:00-21:00'],
            'thursday'  => ['06:00-11:00', '16:00-21:00'],
            'friday'    => ['06:00-11:00', '16:00-21:00'],
            'saturday'  => ['08:00-14:00'],
            'sunday'    => [], // Cerrado
        ];

        // ---------------------------------------------------------
        // 1. FRACCIONAMIENTO: RESIDENCIAL LAS CUMBRES
        // ---------------------------------------------------------
        $cumbres = Subdivision::where('slug', 'residencial-las-cumbres')->first();
        
        if ($cumbres) {
            // Amenidad 1: Casa Club (Modo Exclusivo - Se renta completa)
            Amenity::create([
                'subdivision_id'    => $cumbres->id,
                'name'              => 'Casa Club Principal',
                'description'       => 'Salón de eventos con cocina y baños independientes.',
                'reservation_cost'  => 1500.00,
                'rules'             => ['No fumar', 'Limpiar antes de salir', 'Max 80 personas'],
                'mode'              => 'Exclusivo', // Bloquea todo el lugar
                'capacity'          => 80,
                'buffer_minutes'    => 60, // 1 hora de limpieza entre reservas
                'max_days_advance'  => 60,
                'requires_approval' => true, // Admin debe aprobar
                'availability_schedule' => $horarioEstandar
            ]);

            // Amenidad 2: Alberca (Modo Compartido - Por aforo)
            Amenity::create([
                'subdivision_id'    => $cumbres->id,
                'name'              => 'Alberca Recreativa',
                'description'       => 'Alberca climatizada al aire libre.',
                'reservation_cost'  => 0.00, // Gratis
                'rules'             => ['Ducha obligatoria', 'No alimentos', 'Traje de baño obligatorio'],
                'mode'              => 'Compartido', // Pueden reservar varios vecinos a la vez
                'capacity'          => 20, // Max 20 personas simultáneas
                'buffer_minutes'    => 0,
                'max_days_advance'  => 7,
                'requires_approval' => false, // Aprobación automática
                'availability_schedule' => $horarioEstandar
            ]);
            
            $this->command->info('Amenidades creadas para: Las Cumbres');
        }

        // ---------------------------------------------------------
        // 2. FRACCIONAMIENTO: RESIDENCIAL LOS OLIVOS
        // ---------------------------------------------------------
        $olivos = Subdivision::where('slug', 'residencial-los-olivos')->first();

        if ($olivos) {
            // Amenidad 1: Cancha de Padel (Modo Exclusivo pero periodos cortos)
            Amenity::create([
                'subdivision_id'    => $olivos->id,
                'name'              => 'Cancha de Padel',
                'description'       => 'Cancha profesional con iluminación nocturna.',
                'reservation_cost'  => 200.00, // Cobro por luz
                'rules'             => ['Uso de calzado adecuado', 'Tiempo max 90 min'],
                'mode'              => 'Exclusivo',
                'capacity'          => 4, 
                'buffer_minutes'    => 0,
                'max_days_advance'  => 3,
                'requires_approval' => false,
                'availability_schedule' => $horarioMatutino
            ]);

            // Amenidad 2: Roof Garden (Modo Compartido - Terraza común)
            Amenity::create([
                'subdivision_id'    => $olivos->id,
                'name'              => 'Roof Garden Común',
                'description'       => 'Área de asadores y mesas en azotea.',
                'reservation_cost'  => 100.00,
                'rules'             => ['Limpiar asador', 'No música alta después de las 10pm'],
                'mode'              => 'Compartido',
                'capacity'          => 15, // Caba hasta 15 personas (quizás 3 familias de 5)
                'buffer_minutes'    => 30,
                'max_days_advance'  => 15,
                'requires_approval' => true,
                'availability_schedule' => $horarioEstandar
            ]);

            $this->command->info('Amenidades creadas para: Los Olivos');
        }
    }
}