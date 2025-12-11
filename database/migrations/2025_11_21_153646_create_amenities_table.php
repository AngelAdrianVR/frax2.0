<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('amenities', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->decimal('reservation_cost', 10, 2)->default(0);
            $table->json('rules')->nullable(); // Reglas en formato JSON
            // Define si la reserva bloquea toda la amenidad (Salón) o es por cupos (Alberca)
            $table->enum('mode', ['Exclusivo', 'Compartido'])->default('Exclusivo');
            
            // Capacidad máxima (para modo compartido)
            $table->unsignedInteger('capacity')->nullable();
            
            // Tiempo necesario entre reservas para limpieza (en minutos)
            $table->unsignedInteger('buffer_minutes')->default(0);
            
            // Configuración detallada de disponibilidad (JSON estructurado)
            // Estructura esperada: { "monday": ["09:00-14:00", "16:00-22:00"], "tuesday": ... }
            $table->json('availability_schedule')->nullable();
            
            // Días de anticipación máxima para reservar (ej. no más de 30 días antes)
            $table->unsignedInteger('max_days_advance')->default(30);
            
            // Requiere aprobación manual del administrador?
            $table->boolean('requires_approval')->default(false);
            // Está o no activa para reservas
            $table->boolean('is_active')->default(true);
            
            $table->foreignId('subdivision_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amenities');
    }
};
