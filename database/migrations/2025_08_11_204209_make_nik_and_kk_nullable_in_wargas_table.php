<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wargas', function (Blueprint $table) {
            // Izinkan NIK untuk kosong (nullable)
            $table->string('nik', 16)->nullable()->change();

            // Izinkan kartu_keluarga_id untuk kosong
            $table->foreignId('kartu_keluarga_id')->nullable()->change();

            // Tambah kolom status untuk menandai data
            $table->string('status_data')->default('Terverifikasi')->after('status_khusus');
        });
    }

    public function down(): void
    {
        // Perintah untuk rollback jika diperlukan
        Schema::table('wargas', function (Blueprint $table) {
            $table->string('nik', 16)->nullable(false)->change();
            $table->foreignId('kartu_keluarga_id')->nullable(false)->change();
            $table->dropColumn('status_data');
        });
    }
};