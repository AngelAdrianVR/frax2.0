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
        Schema::create('private_unit_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('private_unit_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('relation');
            $table->string('phone');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('private_unit_contacts');
    }
};
