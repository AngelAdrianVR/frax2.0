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
        Schema::create('check_log_patrols', function (Blueprint $table) {
            $table->id();
            $table->dateTime('scan_date_time');
            
            $table->foreignId('patrols_id')->constrained()->onDelete('cascade');
            $table->foreignId('checkpoints_id')->constrained()->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('check_log_patrols');
    }
};
