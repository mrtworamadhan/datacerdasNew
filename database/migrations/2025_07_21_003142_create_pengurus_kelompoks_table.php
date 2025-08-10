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
        Schema::create('pengurus_kelompoks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelompok_id')->constrained('kelompoks')->onDelete('cascade'); // Terhubung ke lembaga
            // Tidak perlu desa_id langsung di sini karena sudah terhubung via lembaga_id
            $table->string('nama_pengurus');
            $table->string('jabatan'); // Jabatan custom
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengurus_kelompoks');
    }
};
