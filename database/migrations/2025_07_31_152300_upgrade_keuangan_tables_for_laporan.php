<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        // 1. Tambahkan kolom untuk Kop Surat di tabel Lembaga
        Schema::table('lembagas', function (Blueprint $table) {
            $table->string('path_kop_surat')->nullable()->after('deskripsi');
        });

        // 2. Tambahkan kolom untuk Kop Surat di tabel Kelompok
        Schema::table('kelompoks', function (Blueprint $table) {
            $table->string('path_kop_surat')->nullable()->after('deskripsi');
        });

        // 3. Tambahkan kolom Pemesan & Penerima di tabel Pengeluaran
        Schema::table('pengeluarans', function (Blueprint $table) {
            $table->string('nama_pemesan')->nullable()->after('penyedia');
            $table->string('nama_penerima')->nullable()->after('nama_pemesan');
        });
    }

    /**
     * Balikkan migrasi.
     */
    public function down(): void
    {
        Schema::table('lembagas', function (Blueprint $table) {
            $table->dropColumn('path_kop_surat');
        });

        Schema::table('kelompoks', function (Blueprint $table) {
            $table->dropColumn('path_kop_surat');
        });

        Schema::table('pengeluarans', function (Blueprint $table) {
            $table->dropColumn(['nama_pemesan', 'nama_penerima']);
        });
    }
};