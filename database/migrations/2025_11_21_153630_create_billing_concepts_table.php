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
        Schema::create('billing_concepts', function (Blueprint $table) {
            $table->id();
        $table->string('name', 100);
        $table->decimal('base_amount', 10, 2); // Dinero: 10 dígitos, 2 decimales
        $table->enum('recurrence_type', ['Semanal', 'Quincenal', 'Mensual', 'Bimestral', 'Anual', 'Pago unico'])->default('Pago unico');
        $table->boolean('slow_payers_apply')->default(true); // Aplica a morosos?
        
        $table->foreignId('subdivision_id')->constrained()->onDelete('cascade');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_concepts');
    }
};
