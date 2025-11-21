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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->dateTime('date_time');
            $table->unsignedInteger('participants')->nullable(); // Número de participantes
            $table->text('description')->nullable();
            $table->string('location', 150);
            $table->decimal('cost', 10, 2)->default(0);
            $table->unsignedInteger('capacity_event')->nullable();
            $table->unsignedInteger('capacity_per_resident')->nullable();
            $table->json('rules')->nullable();
            
            $table->foreignId('subdivision_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
