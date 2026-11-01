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
    Schema::create('pasukan', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
        $table->foreignId('kumpulan_id')->nullable()->constrained('kumpulan')->nullOnDelete();
        $table->string('nama_pasukan');
        $table->string('nama_kolej_atau_sekolah');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
   public function down(): void
{
    Schema::dropIfExists('pasukan');
}
};
