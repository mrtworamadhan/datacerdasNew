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
        Schema::create('detail_barangs', function (Blueprint $table) {
            $table->id();
            // Hubungkan setiap baris ke satu record pengeluaran
            $table->foreignId('pengeluaran_id')->constrained('pengeluarans')->onDelete('cascade');

            $table->string('nama_barang');
            $table->float('volume');
            $table->string('satuan');
            $table->decimal('harga_satuan', 15, 2);

            $table->timestamps();
        });
    }

    /**
     * Balikkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_barangs');
    }
};