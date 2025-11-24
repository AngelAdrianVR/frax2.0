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
            $table->foreignId('visit_event_id')->nullable()->constrained(); // Si entró por evento
            $table->foreignId('visit_id')->nullable()->constrained(); // Si fue una visita única
            
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
