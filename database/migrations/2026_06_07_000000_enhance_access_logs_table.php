<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('access_logs', function (Blueprint $table) {
            // ── Categoría del acceso (para entradas manuales del guardia) ──
            $table->enum('access_category', ['Visita', 'Proveedor', 'Contratista', 'Conductor', 'Delivery', 'Otro'])
                  ->nullable()
                  ->after('verification_method');

            // ── Datos del visitante (walk-in) ──
            $table->string('visitor_name', 150)->nullable()->after('identifier');
            $table->string('visitor_company', 150)->nullable()->after('visitor_name');
            $table->string('visitor_identification', 100)->nullable()->after('visitor_company');
            $table->string('vehicle_plate', 20)->nullable()->after('visitor_identification');
            $table->string('vehicle_brand', 80)->nullable()->after('vehicle_plate');
            $table->string('vehicle_color', 50)->nullable()->after('vehicle_brand');

            // ── Hora de salida (checkout) ──
            $table->dateTime('exit_time')->nullable()->after('date_time');

            // ── Índices ──
            $table->index('access_category');
            $table->index('visitor_name');
            $table->index('vehicle_plate');
            $table->index('exit_time');
        });
    }

    public function down(): void
    {
        Schema::table('access_logs', function (Blueprint $table) {
            $table->dropColumn([
                'access_category',
                'visitor_name',
                'visitor_company',
                'visitor_identification',
                'vehicle_plate',
                'vehicle_brand',
                'vehicle_color',
                'exit_time',
            ]);
        });
    }
};
