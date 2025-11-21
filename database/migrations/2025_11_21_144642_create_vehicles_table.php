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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('plate', 20)->unique();
            $table->string('brand', 50);
            $table->string('model', 50); // Modelo puede ser "Civic" (texto) o "2022" (año), string cubre ambos
            $table->string('color', 30);
            $table->string('tag_access', 50)->nullable(); // Tag RFID
            
            $table->foreignId('residents_id')->constrained()->onDelete('cascade');
            $table->foreignId('private_units_id')->constrained()->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
