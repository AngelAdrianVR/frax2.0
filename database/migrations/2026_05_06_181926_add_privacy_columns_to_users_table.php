<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Agregamos las dos columnas que faltaban
            if (!Schema::hasColumn('users', 'show_email')) {
                $table->boolean('show_email')->default(true)->after('email');
            }
            if (!Schema::hasColumn('users', 'show_phone')) {
                $table->boolean('show_phone')->default(true)->after('show_email');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'show_email')) {
                $table->dropColumn('show_email');
            }
            if (Schema::hasColumn('users', 'show_phone')) {
                $table->dropColumn('show_phone');
            }
        });
    }
};