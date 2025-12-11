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
        Schema::create('amenity_maintenance_blocks', function (Blueprint $table) {
            $table->id();
            // Relación con la amenidad que se bloqueará
            $table->foreignId('amenity_id')->constrained()->onDelete('cascade');
            
            $table->dateTime('start_date_time');
            $table->dateTime('end_date_time');
            
            // Razón del bloqueo (ej. "Mantenimiento", "Fumigación")
            $table->string('reason'); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amenity_maintenance_blocks');
    }
};
