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
        Schema::create('perlawanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_kumpulan')->nullable()->constrained('kumpulan')->nullOnDelete();
            $table->foreignId('id_pasukan_1')->constrained('pasukan');
            $table->foreignId('id_pasukan_2')->constrained('pasukan');
            $table->date('tarikh');
            $table->time('masa');
            $table->string('tempat');
            $table->unsignedTinyInteger('skor_pasukan_1')->nullable();
            $table->unsignedTinyInteger('skor_pasukan_2')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perlawanan');
    }
};