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
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('tag_code', 50)->unique();
            $table->enum('status', ['Activo', 'Inactivo'])->default('Activo');
            
            // Llave foránea hacia la casa (private_unit)
            $table->foreignId('private_unit_id')
                  ->constrained('private_units')
                  ->cascadeOnDelete();
                  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};