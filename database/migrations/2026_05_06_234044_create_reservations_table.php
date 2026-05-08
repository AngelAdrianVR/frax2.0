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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            
            // Relaciones
            // Se usa nullOnDelete() para no borrar la reservación si se elimina un residente o casa, 
            // pero si se elimina la amenidad, sí borramos las reservas en cascada.
            $table->foreignId('amenity_id')
                  ->constrained('amenities')
                  ->cascadeOnDelete();
                  
            $table->foreignId('resident_id')
                  ->nullable()
                  ->constrained('residents')
                  ->nullOnDelete();
                  
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete(); // Lo agregamos porque en el controlador haces 'user_id' => $user->id
                  
            $table->foreignId('private_unit_id')
                  ->nullable()
                  ->constrained('private_units')
                  ->nullOnDelete();
                  
            $table->foreignId('generated_fee_id')
                  ->nullable()
                  ->constrained('generated_fees')
                  ->nullOnDelete();

            // Datos de la reservación
            $table->dateTime('start_date_time');
            $table->dateTime('end_date_time');
            $table->decimal('total_cost', 10, 2)->default(0.00);
            $table->integer('attendees_amount')->default(1);
            
            // Estado y Notas
            $table->enum('status', ['Pendiente', 'Aprobada', 'Rechazada', 'Cancelada', 'Completada'])->default('Pendiente');
            $table->text('admin_notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};