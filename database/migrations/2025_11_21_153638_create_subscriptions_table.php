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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('service_plan', 100); // Ej: "Premium Plan"
            $table->decimal('cost', 10, 2);
            $table->date('start_date');
            $table->date('current_expiration_date');
            $table->enum('status', ['Activo', 'Suspendido', 'Cancelado'])->default('Activo');
            $table->json('payment_details')->nullable(); // Info de tarjeta enmascarada o token
            
            $table->foreignId('subdivisions_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
