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
        Schema::create('parcel_services', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100); // Amazon, MercadoLibre
            $table->string('tracking_number', 100)->nullable();
            $table->dateTime('receipt_date')->useCurrent(); // Fecha recepción
            $table->dateTime('delivery_date')->nullable(); // Fecha entrega a residente
            $table->string('imagen_etiqueta_url', 255)->nullable();
            $table->enum('status', ['Recibido', 'Entregado', 'Regresado'])->default('Recibido');
            
            $table->foreignId('private_unit_id')->constrained()->onDelete('cascade'); // Destino
            $table->foreignId('user_id')->constrained(); // Guardia que recibió
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parcel_services');
    }
};
