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
        Schema::create('eksport_excel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pasukan')->constrained('pasukan')->cascadeOnDelete();
            $table->foreignId('id_pengurus')->constrained('users')->cascadeOnDelete();
            $table->string('nama_fail_excel');
            $table->timestamp('tarikh_muat_turun')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eksport_excel');
    }
};