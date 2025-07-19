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
        Schema::create('kartu_keluargas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('desa_id')->constrained('desas')->onDelete('cascade'); // Terhubung ke desa
            $table->string('nomor_kk')->unique(); // Unique secara global dulu, nanti kita bisa tambahkan scope unique per desa
            $table->foreignId('rw_id')->constrained('rws')->onDelete('cascade'); // Terhubung ke RW
            $table->foreignId('rt_id')->constrained('rts')->onDelete('cascade'); // Terhubung ke RT
            $table->text('alamat_lengkap'); // Alamat lengkap KK
            $table->enum('klasifikasi', ['Pra-Sejahtera', 'Sejahtera I', 'Sejahtera II', 'Sejahtera III', 'Sejahtera III Plus'])->default('Pra-Sejahtera');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kartu_keluargas');
    }
};