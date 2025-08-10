<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lpjs', function (Blueprint $table) {
            // Tambahkan kolom-kolom ini untuk menyimpan narasi versi LPJ
            $table->text('latar_belakang_lpj')->nullable()->after('kegiatan_id');
            $table->text('tujuan_lpj')->nullable()->after('latar_belakang_lpj');
            $table->text('deskripsi_lpj')->nullable()->after('tujuan_lpj');
            $table->text('penutup_lpj')->nullable()->after('rekomendasi_lanjutan');
        });
    }

    public function down(): void
    {
        Schema::table('lpjs', function (Blueprint $table) {
            $table->dropColumn(['latar_belakang_lpj', 'tujuan_lpj', 'deskripsi_lpj', 'penutup_lpj']);
        });
    }
};