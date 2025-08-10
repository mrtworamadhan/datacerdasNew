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
        // 1. Tambahkan kolom baru ke tabel 'desas'
        Schema::table('desas', function (Blueprint $table) {
            $table->string('foto_kades_path')->nullable()->after('nama_kades')->comment('Path untuk foto kepala desa');
            $table->text('sambutan_kades')->nullable()->after('foto_kades_path')->comment('Teks sambutan untuk halaman welcome');
        });

        // 2. Tambahkan kolom baru ke tabel 'perangkat_desas'
        Schema::table('perangkat_desas', function (Blueprint $table) {
            $table->string('foto_path')->nullable()->after('jabatan')->comment('Path untuk foto perangkat desa');
        });
    }

    /**
     * Balikkan migrasi.
     */
    public function down(): void
    {
        Schema::table('desas', function (Blueprint $table) {
            $table->dropColumn(['foto_kades_path', 'sambutan_kades']);
        });

        Schema::table('perangkat_desas', function (Blueprint $table) {
            $table->dropColumn('foto_path');
        });
    }
};