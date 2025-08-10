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
        Schema::create('kelompoks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('desa_id')->constrained('desas')->onDelete('cascade'); // Terhubung ke desa
            $table->string('nama_kelompok');
            $table->text('deskripsi')->nullable();
            $table->string('sk_kepala_desa_path')->nullable(); // Path untuk file PDF SK
            $table->timestamps();
        });
    }

    /**
     * Balikkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelompoks');
    }
};