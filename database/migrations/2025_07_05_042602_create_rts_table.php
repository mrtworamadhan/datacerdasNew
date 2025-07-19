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
        Schema::create('rts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rw_id')->constrained('rws')->onDelete('cascade'); // Terhubung ke RW
            $table->foreignId('desa_id')->constrained('desas')->onDelete('cascade'); // Redundant tapi memudahkan query multi-tenant
            $table->string('nomor_rt')->unique(); // Contoh: 001, 002. Unique per RW nanti di logic
            $table->string('nama_ketua')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rts');
    }
};