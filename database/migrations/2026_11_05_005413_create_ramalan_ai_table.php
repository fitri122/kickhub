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
        Schema::create('ramalan_ai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_perlawanan')->constrained('perlawanan')->cascadeOnDelete();
            $table->text('ulasan_ai');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ramalan_ai');
    }
};