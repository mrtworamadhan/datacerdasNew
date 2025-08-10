<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aset_kelompoks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aset_bidang_id')->constrained('aset_bidangs')->onDelete('cascade');
            $table->string('kode_kelompok', 2);
            $table->string('nama_kelompok');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aset_kelompoks');
    }
};