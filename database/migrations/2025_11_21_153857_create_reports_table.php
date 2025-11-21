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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100);
            $table->text('description');
            $table->enum('priority', ['Baja', 'Alta', 'Critica'])->default('Alta');
            $table->enum('status', ['Abierto', 'En progreso', 'Resuelto', 'Cerrada'])->default('Abierto');
            $table->string('exact_location', 150)->nullable();
            
            $table->foreignId('private_unit_id')->nullable()->constrained();
            $table->foreignId('resident_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
