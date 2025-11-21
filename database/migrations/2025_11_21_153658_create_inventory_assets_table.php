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
        Schema::create('inventory_assets', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('inventory_code', 50)->unique();
            $table->text('description')->nullable();
            $table->date('purchase_date');
            $table->unsignedInteger('useful_life_in_hours')->nullable(); // Vida útil estimada
            $table->unsignedInteger('current_usage_hours')->default(0);
            
            $table->foreignId('subdivisions_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_assets');
    }
};
