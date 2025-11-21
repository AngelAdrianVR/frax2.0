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
        Schema::create('bank_reconciliations', function (Blueprint $table) {
            $table->id();
            $table->string('bank_reference', 100);
            $table->decimal('amount', 10, 2);
            $table->dateTime('transaction_date');
            $table->enum('status', ['Pendiente', 'Conciliado', 'Error', 'Manual'])->default('Pendiente');
            $table->string('error_message', 255)->nullable();
            
            $table->foreignId('payments_id')->unique()->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_reconciliations');
    }
};
