<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengeluarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained('kegiatans')->onDelete('cascade');

            $table->string('tipe_pengeluaran')->comment('Contoh: Biasa, Pembelian Pesanan, Upah Kerja');
            $table->date('tanggal_transaksi');
            $table->string('uraian');
            $table->decimal('jumlah', 15, 2);

            // Kolom khusus untuk 'Pembelian Pesanan' (bisa kosong)
            $table->date('tanggal_pesanan')->nullable();
            $table->string('penyedia')->nullable()->comment('Nama toko atau penyedia jasa');

            // Kolom khusus untuk 'Upah Kerja' (bisa kosong)
            $table->string('nama_pekerja')->nullable();
            $table->string('tanda_tangan_path')->nullable()->comment('Path file gambar tanda tangan');

            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengeluarans');
    }
};