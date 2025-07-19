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
        Schema::table('rts', function (Blueprint $table) {
            // 1. Hapus index unique yang lama pada 'nomor_rt'
            // Pastikan nama index-nya benar. Default Laravel biasanya 'nama_tabel_nama_kolom_unique'
            $table->dropUnique(['nomor_rt']);

            // 2. Tambahkan index unique baru pada kombinasi 'rw_id' dan 'nomor_rt'
            $table->unique(['rw_id', 'nomor_rt']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rts', function (Blueprint $table) {
            // Balikkan perubahan saat rollback
            $table->dropUnique(['rw_id', 'nomor_rt']); // Hapus index composite
            $table->unique('nomor_rt'); // Tambahkan kembali index unique yang lama
        });
    }
};