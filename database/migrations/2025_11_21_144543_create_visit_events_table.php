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
        Schema::create('visit_events', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100); // "Fiesta de cumpleaños"
            $table->string('qr_code', 255)->nullable(); // Código maestro del evento
            $table->dateTime('date_time_start');
            $table->dateTime('date_time_end');
            $table->unsignedInteger('guest_amount')->default(0);
            $table->unsignedInteger('max_qr_uses')->nullable(); // Límite de usos del QR
            $table->unsignedInteger('current_use_count')->default(0);
            $table->enum('status', ['Activo', 'Inactivo', 'Cancelado'])->default('Activo');
            $table->text('description')->nullable(); // Renombrado de visit_eventscol para claridad
            
            $table->foreignId('private_unit_id')->constrained()->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visit_events');
    }
};
