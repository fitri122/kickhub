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
        Schema::create('pencapaian_pemain', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pemain')->constrained('pemain')->cascadeOnDelete();
            $table->string('nama_pencapaian');
            $table->year('tahun');
            $table->string('peringkat');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pencapaian_pemain');
    }
};