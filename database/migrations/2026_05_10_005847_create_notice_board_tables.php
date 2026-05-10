<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla de Publicaciones (Posts)
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('content');
            $table->enum('type', ['general', 'announcement', 'alert'])->default('general'); // Para distinguir avisos oficiales de vecinales
            $table->boolean('is_pinned')->default(false); // Para fijar avisos importantes hasta arriba
            $table->timestamps();
        });

        // Tabla de Reacciones (Likes, etc.)
        Schema::create('reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->string('type')->default('like'); 
            $table->timestamps();
            
            // Un usuario solo puede reaccionar una vez con el mismo tipo a una publicación
            $table->unique(['user_id', 'post_id', 'type']);
        });

        // Tabla de Comentarios (Opcional para el futuro, pero la dejamos lista)
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->text('content');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
        Schema::dropIfExists('reactions');
        Schema::dropIfExists('posts');
    }
};