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
        Schema::create('jaringan_gol', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_perlawanan')->constrained('perlawanan')->cascadeOnDelete();
            $table->foreignId('id_pemain')->constrained('pemain')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jaringan_gol');
    }
};