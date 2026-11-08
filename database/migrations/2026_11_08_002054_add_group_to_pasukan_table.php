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
        Schema::table('kumpulan', function (Blueprint $table) {
            $table->unique('nama_kumpulan');
        });

        Schema::table('pasukan', function (Blueprint $table) {
            $table->foreignId('id_kumpulan')
                ->nullable()
                ->after('id')
                ->constrained('kumpulan')
                ->nullOnDelete();
            $table->unique('nama_pasukan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pasukan', function (Blueprint $table) {
            $table->dropForeign(['id_kumpulan']);
            $table->dropUnique(['nama_pasukan']);
            $table->dropColumn('id_kumpulan');
        });

        Schema::table('kumpulan', function (Blueprint $table) {
            $table->dropUnique(['nama_kumpulan']);
        });
    }
};
