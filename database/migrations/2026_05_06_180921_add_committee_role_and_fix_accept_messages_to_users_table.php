<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            
            // 1. Agregamos la columna para el rol (si no existe)
            if (!Schema::hasColumn('users', 'committee_role')) {
                $table->string('committee_role')->nullable()->after('phone');
            }

            // 2. Eliminamos 'accept_messages' si la creaste como TEXT para evitar conflictos
            if (Schema::hasColumn('users', 'accept_messages')) {
                $table->dropColumn('accept_messages');
            }
            
            // 3. Volvemos a crear 'accept_messages' pero ahora con el tipo de dato correcto (booleano)
            $table->boolean('accept_messages')->default(true)->after('committee_role');
            
            // Si en un futuro necesitas show_email y show_phone, descomenta estas líneas:
            // $table->boolean('show_email')->default(true)->after('email');
            // $table->boolean('show_phone')->default(true)->after('show_email');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'committee_role')) {
                $table->dropColumn('committee_role');
            }
            if (Schema::hasColumn('users', 'accept_messages')) {
                $table->dropColumn('accept_messages');
            }
        });
    }
};