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
        Schema::create('posyandu', function (Blueprint $table) {
            $table->id();
            $table->string('nama_posyandu', 100);
            // Asumsi kita punya tabel 'rw' untuk relasi.
            // Jika nama tabelnya beda, sesuaikan 'rw' dan 'id_rw'.
            $table->foreignId('rw_id')->constrained('rws')->onDelete('cascade');
            $table->text('alamat');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posyandu');
    }
};
