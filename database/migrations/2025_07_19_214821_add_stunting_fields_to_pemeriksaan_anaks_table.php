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
        Schema::table('pemeriksaan_anaks', function (Blueprint $table) {
            // Kolom untuk hasil kalkulasi stunting
            $table->decimal('zscore_tb_u', 5, 2)->nullable()->after('status_gizi')->comment('Z-score Tinggi Badan per Umur (HAZ)');
            $table->string('status_stunting')->nullable()->after('zscore_tb_u')->comment('Contoh: Normal, Pendek, Sangat Pendek');

            // Kolom Antropometri Tambahan
            $table->decimal('lila', 4, 1)->nullable()->after('tinggi_badan')->comment('Lingkar Lengan Atas');

            // Kolom Riwayat Kesehatan & Gizi (disimpan per pemeriksaan)
            $table->boolean('diare_2_minggu')->default(false)->after('status_stunting');
            $table->boolean('ispa_2_minggu')->default(false)->after('diare_2_minggu')->comment('Infeksi Saluran Pernapasan Akut');

            // Kolom Intervensi yang diberikan saat pemeriksaan ini
            $table->boolean('dapat_vitamin_a')->default(false)->after('ispa_2_minggu');
            $table->boolean('dapat_obat_cacing')->default(false)->after('dapat_vitamin_a');
            $table->boolean('dapat_imunisasi_polio')->default(false)->after('dapat_obat_cacing');

            // Kolom Petugas & Keterangan
            $table->string('petugas_pengukur')->nullable()->after('dapat_imunisasi_polio');
            $table->text('keterangan_pemeriksaan')->nullable()->after('petugas_pengukur');
        });
    }

    /**
     * Balikkan migrasi.
     */
    public function down(): void
    {
        Schema::table('pemeriksaan_anaks', function (Blueprint $table) {
            $table->dropColumn([
                'zscore_tb_u',
                'status_stunting',
                'lila',
                'diare_2_minggu',
                'ispa_2_minggu',
                'dapat_vitamin_a',
                'dapat_obat_cacing',
                'dapat_imunisasi_polio',
                'petugas_pengukur',
                'keterangan_pemeriksaan',
            ]);
        });
    }
};