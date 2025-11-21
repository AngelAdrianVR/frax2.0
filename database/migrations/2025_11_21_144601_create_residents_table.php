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
        Schema::create('residents', function (Blueprint $table) {
            $table->id();
            $table->string('full_name', 150);
            $table->string('phone', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->enum('person_type', ['Propietario', 'Inquilino', 'Empleado', 'miembro familiar'])->default('Inquilino');
            $table->boolean('is_emergency_contact')->default(false);
            $table->json('emergency_contact_info')->nullable();
            $table->boolean('is_slow_payer')->default(false); // Moroso
            
            // Un residente puede tener un usuario de sistema asociado, o no.
            $table->foreignId('users_id')->nullable()->constrained()->onDelete('set null');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('residents');
    }
};
