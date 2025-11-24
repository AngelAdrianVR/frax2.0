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
        Schema::create('residence_units', function (Blueprint $table) {
           $table->id();
            $table->foreignId('resident_id')->constrained()->onDelete('cascade');
            $table->foreignId('private_unit_id')->constrained()->onDelete('cascade');
            
            $table->enum('role_in_unit', ['Dueño', 'Inquilino'])->default('Dueño');
            $table->boolean('responsible_for_payments')->default(false);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('primary')->default(false); // Residencia principal
            $table->boolean('is_primary_owner')->default(false);
            $table->json('permissions_level')->nullable(); // Permisos específicos
            $table->string('alias', 50)->nullable(); // "Casa de verano"
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('residence_units');
    }
};
