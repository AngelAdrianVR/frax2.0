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
        Schema::create('register_invitations', function (Blueprint $table) {
            $table->id();
            $table->string('email', 100);
            $table->string('token', 64)->unique();
            $table->enum('role_type', ['Dueño', 'Familiar']);
            $table->enum('status', ['Pendiente', 'Aceptado', 'Expirado'])->default('Pendiente');
            $table->dateTime('expires_at');
            
            // Quién invitó (Usuario existente)
            $table->foreignId('invited_by_user_id')->constrained('users');
            // A qué unidad pertenece la invitación (opcional)
            $table->foreignId('private_units_id')->nullable()->constrained();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('register_invitations');
    }
};
