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
        Schema::table('kartu_keluargas', function (Blueprint $table) {
            // Tambahkan kolom kepala_keluarga_id
            // Akan menjadi foreign key ke tabel 'wargas'
            $table->foreignId('kepala_keluarga_id')
                  ->nullable() // Bisa null di awal jika KK dibuat tanpa kepala keluarga langsung
                  ->after('klasifikasi') // Posisikan setelah kolom 'klasifikasi'
                  ->constrained('wargas') // Terhubung ke tabel 'wargas'
                  ->onDelete('set null'); // Jika warga dihapus, set ID ini menjadi null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kartu_keluargas', function (Blueprint $table) {
            // Hapus foreign key constraint terlebih dahulu
            $table->dropConstrainedForeignId('kepala_keluarga_id');
            // Kemudian hapus kolomnya
            $table->dropColumn('kepala_keluarga_id');
        });
    }
};
