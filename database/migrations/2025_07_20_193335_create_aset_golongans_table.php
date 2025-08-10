<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aset_golongans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_golongan', 2)->unique();
            $table->string('nama_golongan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aset_golongans');
    }
};