<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aset_bidangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aset_golongan_id')->constrained('aset_golongans')->onDelete('cascade');
            $table->string('kode_bidang', 2);
            $table->string('nama_bidang');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aset_bidangs');
    }
};