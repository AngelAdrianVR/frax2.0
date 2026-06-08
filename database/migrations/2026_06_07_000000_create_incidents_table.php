<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tabla de incidencias: reportes de guardias durante su turno.
     * Unifica el concepto de "novedad" que antes no tenía tabla propia.
     */
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150);                          // Título breve
            $table->text('description')->nullable();               // Descripción detallada
            $table->enum('severity', ['Baja', 'Media', 'Alta', 'Critica'])->default('Media');
            $table->enum('status', ['Abierto', 'EnProceso', 'Resuelto', 'Cerrado'])->default('Abierto');
            $table->enum('incident_type', ['Seguridad', 'Trafico', 'Danios', 'Ruido', 'Emergencia', 'Otro'])->default('Otro');
            $table->string('foto_url', 500)->nullable();           // Evidencia fotográfica
            $table->string('location_description', 255)->nullable(); // Dónde ocurrió
            $table->dateTime('reported_at')->useCurrent();
            $table->dateTime('resolved_at')->nullable();

            // Quién reporta (guardia)
            $table->foreignId('reported_by_user_id')->constrained('users')->onDelete('cascade');

            // Unidad involucrada (opcional, puede ser un incidente en áreas comunes)
            $table->foreignId('private_unit_id')->nullable()->constrained()->onDelete('set null');

            // Rondín en el que se detectó (opcional)
            $table->foreignId('patrol_id')->nullable()->constrained()->onDelete('set null');

            // Índices para filtros rápidos
            $table->index(['status', 'severity']);
            $table->index('reported_at');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
