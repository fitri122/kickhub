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
        Schema::create('rekod_import', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pasukan')->constrained('pasukan')->cascadeOnDelete();
            $table->foreignId('id_pengurus')->constrained('users')->cascadeOnDelete();
            $table->string('nama_fail');
            $table->string('jenis_fail');
            $table->timestamp('tarikh_import')->useCurrent();
            $table->string('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekod_import');
    }
};