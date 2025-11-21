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
        Schema::create('generated_fees', function (Blueprint $table) {
            $table->id();
            $table->string('payment_reference', 50)->unique(); // Referencia bancaria
            $table->decimal('total_amount', 10, 2);
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->date('expiration_date');
            $table->date('start_period'); // Inicio del mes/periodo que cubre
            $table->date('end_period');   // Fin del periodo
            $table->enum('status', ['Pendiente', 'Parcial', 'Pagado', 'Atrasada', 'Cancelado'])->default('Pendiente');
            
            $table->foreignId('private_units_id')->constrained()->onDelete('cascade');
            $table->foreignId('billing_concepts_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('generated_fees');
    }
};
