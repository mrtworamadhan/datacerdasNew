<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kegiatans', function (Blueprint $table) {
            // Tambahkan kolom baru setelah 'nama_kegiatan'
            $table->string('tipe_kegiatan')->default('Acara')->after('nama_kegiatan')->comment('Pembangunan, Acara');
        });
    }

    public function down(): void
    {
        Schema::table('kegiatans', function (Blueprint $table) {
            $table->dropColumn('tipe_kegiatan');
        });
    }
};