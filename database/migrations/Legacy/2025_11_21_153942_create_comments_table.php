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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->text('body');
            
            // Quién comentó (puede ser User o Resident, sugiero User para abarcar admins y residentes)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // A qué le comentó (Post, Report, Amenity, etc.)
            $table->morphs('commentable');
            
            // SoftDeletes por si se borra un comentario ofensivo
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
