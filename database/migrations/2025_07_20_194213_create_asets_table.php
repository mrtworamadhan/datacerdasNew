<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asets', function (Blueprint $table) {
            $table->id();
            $table->string('kode_aset')->unique()->comment('Kode lengkap, e.g., 02.06.01.01.02.0001');

            // Foreign key ke level paling bawah dari hirarki kodifikasi
            $table->foreignId('aset_sub_sub_kelompok_id')->constrained('aset_sub_sub_kelompoks');

            $table->string('nama_aset');
            $table->year('tahun_perolehan');
            $table->decimal('nilai_perolehan', 15, 2)->default(0);
            $table->unsignedInteger('jumlah')->default(1);
            $table->string('kondisi')->comment('Baik, Rusak Ringan, Rusak Berat');
            $table->string('sumber_dana')->nullable();
            $table->text('lokasi')->nullable();
            $table->string('penanggung_jawab')->nullable();
            $table->string('foto_aset')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asets');
    }
};