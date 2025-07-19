<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fasums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('desa_id')->constrained('desas')->onDelete('cascade');

            // --- KOLOM DATA UMUM (WAJIB UNTUK SEMUA FASUM) ---
            $table->string('kategori'); // Akan berisi: 'Fasilitas Pendidikan', 'Fasilitas Transportasi & Ekonomi', dll.
            $table->string('nama_fasum');
            $table->text('deskripsi')->nullable();
            $table->string('status_kondisi'); // Misal: 'Baik', 'Rusak Ringan', 'Rusak Berat'
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            
            // --- KOLOM SPESIAL UNTUK DETAIL TAMBAHAN ---
            // Kolom JSON ini akan menyimpan data seperti panjang, lebar, tinggi, kapasitas, dll.
            $table->json('detail_spesifikasi')->nullable();

            $table->timestamps();
        });

        // Tabel terpisah untuk menyimpan foto (satu fasum bisa punya banyak foto)
        Schema::create('fasum_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fasum_id')->constrained('fasums')->onDelete('cascade');
            $table->string('path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fasum_photos');
        Schema::dropIfExists('fasums');
    }
};
