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
        Schema::create('frequent_visitors', function (Blueprint $table) {
            $table->id();
            $table->string('alias', 50)->nullable(); // "Jardinero", "Mamá"
            $table->string('name', 100); // nombre de la visita
            $table->string('identification', 50)->nullable();
            $table->string('default_reason', 100)->nullable();
            $table->string('default_access_type', 20)->default('Peatonal'); // Peatonal o Vehicular
            $table->string('default_plate', 20)->nullable();
            
            $table->foreignId('private_unit_id')->constrained()->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frequent_visitors');
    }
};
