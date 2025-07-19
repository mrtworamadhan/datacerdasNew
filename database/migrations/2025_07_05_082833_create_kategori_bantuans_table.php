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
        Schema::create('kategori_bantuans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('desa_id')->constrained('desas')->onDelete('cascade'); // Terhubung ke desa
            $table->string('nama_kategori')->unique(); // Nama kategori unik secara global dulu, nanti bisa per desa
            $table->text('deskripsi')->nullable();
            $table->text('kriteria_json')->nullable(); // Menyimpan kriteria dalam format JSON
            $table->boolean('allow_multiple_recipients_per_kk')->default(false); // Apakah satu KK boleh menerima bantuan ini lebih dari sekali
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori_bantuans');
    }
};