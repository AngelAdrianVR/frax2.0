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
        Schema::create('subdivision_user', function (Blueprint $table) {
            $table->id();

            // Relación con el Usuario
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');

            // Relación con el Fraccionamiento
            $table->foreignId('subdivision_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->string('role_in_subdivision')->nullable(); 

            // Opcional: Para saber cuál es el fraccionamiento activo actualmente en su sesión
            $table->boolean('is_current')->default(false);

            $table->timestamps();

            // Evitar duplicados: Un usuario no puede estar dos veces en el mismo fraccionamiento
            $table->unique(['user_id', 'subdivision_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subdivision_user');
    }
};
