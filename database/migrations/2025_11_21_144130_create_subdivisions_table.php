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
        Schema::create('subdivisions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique(); // Para URLs amigables
            $table->string('address', 255);
            $table->string('exterior_number', 20);
            $table->string('suburb', 100); // Colonia
            $table->string('town', 100);   // Municipio/Ciudad
            $table->string('federal_state', 100);
            $table->string('post_code', 10);
            $table->json('configuration')->nullable(); // Configuración flexible
            $table->unsignedInteger('houses_amount')->default(0); // Entero positivo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subdivisions');
    }
};
