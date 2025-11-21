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
        Schema::create('private_units', function (Blueprint $table) {
            $table->id();
            $table->string('lot_number', 20);
            $table->decimal('square_meters', 10, 2)->nullable(); // Decimal: 120.50 m2
            $table->string('unit_street', 100)->nullable();
            $table->string('int_number', 20)->nullable();
            $table->enum('status', ['Activo', 'Inactivo'])->default('Activo');
            $table->boolean('access_block')->default(false); // Booleano: Bloqueado o no
            
            // Clave foránea
            $table->foreignId('subdivisions_id')->constrained()->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('private_units');
    }
};
