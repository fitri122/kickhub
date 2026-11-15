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
        Schema::table('sekatan_pemain', function (Blueprint $table) {
            $table->string('jenis_sekatan')->default('manual')->after('status');
            $table->foreignId('id_rekod_disiplin')
                ->nullable()
                ->unique()
                ->after('jenis_sekatan')
                ->constrained('rekod_disiplin')
                ->cascadeOnDelete();
            $table->foreignId('id_perlawanan_digantung')
                ->nullable()
                ->after('id_rekod_disiplin')
                ->constrained('perlawanan')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sekatan_pemain', function (Blueprint $table) {
            $table->dropForeign(['id_perlawanan_digantung']);
            $table->dropForeign(['id_rekod_disiplin']);
            $table->dropUnique(['id_rekod_disiplin']);
            $table->dropColumn([
                'id_perlawanan_digantung',
                'id_rekod_disiplin',
                'jenis_sekatan',
            ]);
        });
    }
};
