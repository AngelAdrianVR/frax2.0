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
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100); // Nombre del visitante
            $table->string('reason', 100)->nullable();
            $table->string('qr_code', 255)->nullable();
            $table->dateTime('expiration_date')->nullable();
            $table->dateTime('date_of_use')->nullable();
            $table->enum('access_type', ['Peatonal', 'Vehicular']);
            $table->enum('status', ['Pendiente','Ingresado','Expirado','Cancelado'])->default('Pendiente');
            
            $table->foreignId('private_units_id')->constrained()->onDelete('cascade');
            $table->foreignId('visit_events_id')->nullable()->constrained()->onDelete('cascade'); // En caso de que sea parte de un evento
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
