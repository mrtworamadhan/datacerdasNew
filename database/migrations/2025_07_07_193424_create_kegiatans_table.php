<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('kegiatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('desa_id')->constrained('desas')->onDelete('cascade');
            $table->foreignId('lembaga_id')->constrained('lembagas')->onDelete('cascade');
            $table->string('nama_kegiatan');
            $table->date('tanggal_kegiatan');
            $table->text('latar_belakang')->nullable();
            $table->text('tujuan_kegiatan')->nullable();
            $table->text('deskripsi_kegiatan');
            $table->string('lokasi_kegiatan');
            $table->decimal('anggaran_biaya', 15, 2)->nullable();
            $table->string('sumber_dana')->nullable();
            $table->text('penutup')->nullable();
            // Nanti kita bisa tambahkan kolom untuk foto kegiatan, dll.
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('kegiatans');
    }
};