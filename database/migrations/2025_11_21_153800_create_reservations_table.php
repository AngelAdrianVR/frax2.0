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
            $table->dateTime('start_date_time');
            $table->dateTime('end_date_time');
            $table->decimal('total_cost', 10, 2)->default(0);
            $table->enum('status', ['Pendiente', 'Aprobada', 'Rechazada', 'Cancelada', 'Completada'])->default('Pendiente');
            // Relación con la casa (Crucial para tu requerimiento de "cobro a la unidad")
            $table->foreignId('private_unit_id')->constrained('private_units')->onDelete('cascade');

            // Número de personas que asistirán (importante para validar aforo en modo 'Compartido')
            $table->unsignedInteger('attendees_amount')->default(1);
            
            // Notas internas o razón de rechazo
            $table->text('admin_notes')->nullable();

            // Relación opcional con la cuota generada (para saber si ya se generó el cobro)
            // Se asume que generated_fees ya existe según tu PDF
            $table->foreignId('generated_fee_id')->nullable()->constrained('generated_fees')->onDelete('set null');
            
            $table->foreignId('amenity_id')->constrained()->onDelete('cascade');
            $table->foreignId('resident_id')->nullable()->constrained()->onDelete('cascade');
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
