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
        Schema::create('pemain', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pasukan')->constrained('pasukan')->cascadeOnDelete();
            $table->string('nama');
            $table->string('no_ic', 12)->unique();
            $table->unsignedSmallInteger('no_jersi');
            $table->unsignedTinyInteger('umur')->nullable();
            $table->string('gambar')->nullable();
            $table->string('role_pemain')->default('Pemain Biasa');
            $table->string('position');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemain');
    }
};