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
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('species', 50); // Perro, Gato
            $table->string('race', 50)->nullable();
            $table->json('additionals')->nullable(); // Vacunas, foto, etc.
            
            $table->foreignId('resident_id')->constrained()->onDelete('cascade');
            $table->foreignId('private_unit_id')->constrained()->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};
