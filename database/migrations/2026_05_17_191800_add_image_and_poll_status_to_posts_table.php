<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('content'); // Para guardar la ruta de la imagen
            $table->boolean('is_poll_closed')->default(false)->after('is_multiple_choice'); // Para saber si la encuesta finalizó
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['image_path', 'is_poll_closed']);
        });
    }
};