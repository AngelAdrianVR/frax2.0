<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Opcional: Eliminar las tablas viejas para limpiar tu base de datos
        // (Solo si estás dispuesta a perder los datos de prueba actuales)
        Schema::dropIfExists('residence_units');
        
        // 2. Crear la nueva tabla pivote
        Schema::create('private_unit_user', function (Blueprint $table) {
            $table->id();
            
            // Llaves foráneas
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('private_unit_id')->constrained('private_units')->onDelete('cascade');
            
            // Campos extra que tenías en residence_units (muy útiles)
            $table->enum('role_in_unit', ['Dueño', 'Inquilino'])->default('Dueño');
            $table->boolean('responsible_for_payments')->default(false);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_primary')->default(false); // Cambié 'primary' por 'is_primary' (mejor práctica)
            $table->string('alias', 50)->nullable();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('private_unit_user');
    }
};