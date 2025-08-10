<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aset_sub_sub_kelompoks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aset_sub_kelompok_id')->constrained('aset_sub_kelompoks')->onDelete('cascade');
            $table->string('kode_sub_sub_kelompok', 3);
            $table->string('nama_sub_sub_kelompok');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aset_sub_sub_kelompoks');
    }
};