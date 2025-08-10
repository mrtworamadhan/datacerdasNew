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
        Schema::create('kader_posyandu', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel posyandu
            $table->foreignId('posyandu_id')->constrained('posyandu')->onDelete('cascade');
            // Relasi ke tabel penduduk
            $table->foreignId('warga_id')->constrained('wargas')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kader_posyandu');
    }
};