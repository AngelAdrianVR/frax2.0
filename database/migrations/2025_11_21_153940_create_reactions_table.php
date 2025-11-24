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
        Schema::create('reactions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['like', 'love', 'care', 'haha', 'wow', 'sad', 'angry']);
            
            // Campos polimórficos (crea reactable_id y reactable_type)
            $table->morphs('reactable'); 
            
            $table->foreignId('resident_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            // Evitar duplicados: Un residente solo una reacción por item
            $table->unique(['resident_id', 'reactable_id', 'reactable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reactions');
    }
};
