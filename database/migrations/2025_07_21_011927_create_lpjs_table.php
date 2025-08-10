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
        Schema::create('lpjs', function (Blueprint $table) {
            $table->id();
            // Relasi 1-ke-1 dengan tabel kegiatan
            $table->foreignId('kegiatan_id')->constrained('kegiatans')->onDelete('cascade');

            $table->text('hasil_kegiatan');
            $table->decimal('realisasi_anggaran', 15, 2);
            $table->text('evaluasi_kendala')->nullable();
            $table->text('rekomendasi_lanjutan')->nullable();
            $table->date('tanggal_pelaporan');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lpjs');
    }
};
