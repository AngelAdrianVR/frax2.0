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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_folio', 100)->unique();
            $table->decimal('amount', 10, 2);
            $table->dateTime('payment_date');
            $table->enum('payment_method', ['Transferencia', 'Efectivo', 'Tarjeta', 'Cheque']);
            
            // Relaciones según diagrama
            $table->foreignId('billing_concept_id')->constrained(); 
            $table->foreignId('resident_id')->nullable()->constrained(); 
            // Sugerencia: Deberías considerar vincular esto a generated_fees en el futuro
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
