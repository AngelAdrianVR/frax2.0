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
        Schema::create('access_logs', function (Blueprint $table) {
            $table->id();
            $table->string('identifier', 100)->nullable(); // Placa o Nombre detectado
            $table->enum('movement_type', ['Entrada', 'Salida']); // Enum para control estricto
            $table->dateTime('date_time')->useCurrent();
            $table->enum('verification_method', ['QR', 'RFID', 'Manual', 'Biometrico']);
            $table->text('notes')->nullable();
            
            // Referencias (Nullables porque un acceso puede ser de diferentes tipos de actores)
            $table->foreignId('private_unit_id')->nullable()->constrained();
            $table->foreignId('user_id')->nullable()->constrained(); // Si fue un residente/guardia
            $table->foreignId('visit_event_id')->nullable()->constrained(); 
            $table->foreignId('visit_id')->nullable()->constrained(); 
            
            // 🔥 Índices de rendimiento
            $table->index(['private_unit_id', 'date_time']); // Para el historial de la casa
            $table->index('date_time'); // Para el corte de turno del guardia
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('access_logs');
    }
};
