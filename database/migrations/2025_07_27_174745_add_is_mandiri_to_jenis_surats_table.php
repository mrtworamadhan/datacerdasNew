<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jenis_surats', function (Blueprint $table) {
            // Tambahkan kolom baru setelah 'nama_surat'
            // Defaultnya 'false', artinya surat tidak bisa diakses mandiri
            // kecuali kita izinkan secara eksplisit.
            $table->boolean('is_mandiri')->default(false)->after('nama_surat');
        });
    }

    public function down(): void
    {
        Schema::table('jenis_surats', function (Blueprint $table) {
            $table->dropColumn('is_mandiri');
        });
    }
};