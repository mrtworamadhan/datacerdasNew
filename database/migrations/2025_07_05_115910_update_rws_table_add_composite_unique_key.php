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
        Schema::table('rws', function (Blueprint $table) {
            // Hapus index unique yang lama pada 'nomor_rw'
            // Nama default index unique di Laravel adalah 'nama_tabel_nama_kolom_unique'
            // Jadi untuk 'rws' dan 'nomor_rw', nama index-nya adalah 'rws_nomor_rw_unique'
            $table->dropUnique(['nomor_rw']);

            // Tambahkan index unique baru pada kombinasi 'desa_id' dan 'nomor_rw'
            $table->unique(['desa_id', 'nomor_rw']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rws', function (Blueprint $table) {
            // Balikkan perubahan saat rollback
            $table->dropUnique(['desa_id', 'nomor_rw']); // Hapus index composite
            $table->unique('nomor_rw'); // Tambahkan kembali index unique yang lama
        });
    }
};
