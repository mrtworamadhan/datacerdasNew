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
        Schema::create('penerima_bantuans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('desa_id')->constrained('desas')->onDelete('cascade'); // Terhubung ke desa
            $table->foreignId('kategori_bantuan_id')->constrained('kategori_bantuans')->onDelete('cascade'); // Terhubung ke kategori
            $table->foreignId('warga_id')->nullable()->constrained('wargas')->onDelete('set null'); // Jika penerima adalah individu
            $table->foreignId('kartu_keluarga_id')->nullable()->constrained('kartu_keluargas')->onDelete('set null'); // Jika penerima adalah KK

            $table->date('tanggal_menerima');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Tambahkan unique constraint untuk mencegah duplikasi penerima per kategori
            // Ini akan kita atasi di logic controller berdasarkan allow_multiple_recipients_per_kk
            // $table->unique(['kategori_bantuan_id', 'warga_id']); // Contoh jika hanya per warga
            // $table->unique(['kategori_bantuan_id', 'kartu_keluarga_id']); // Contoh jika hanya per KK
            // Kita akan handle ini di validasi controller
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penerima_bantuans');
    }
};