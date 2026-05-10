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
            
            // Datos de la invitación
            $table->string('email');
            $table->string('token', 64)->unique(); // Token seguro para la URL
            $table->enum('role_type', ['Dueño', 'Familiar']); 
            $table->enum('status', ['Pendiente', 'Aceptado', 'Expirado'])->default('Pendiente');
            $table->timestamp('expires_at')->nullable();
            
            // Relaciones (Llaves foráneas)
            $table->foreignId('invited_by_user_id')
                  ->constrained('users')
                  ->onDelete('cascade'); // Si se elimina el usuario que invitó, se borra la invitación
                  
            $table->foreignId('private_unit_id')
                  ->constrained('private_units')
                  ->onDelete('cascade'); // Si se elimina la casa, se borran sus invitaciones
            
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