<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Menambahkan kolom ke tabel pemeriksaan anak
        Schema::table('pemeriksaan_anaks', function (Blueprint $table) {
            $table->foreignId('posyandu_id')->nullable()->constrained('posyandu')->onDelete('set null')->after('warga_id');
        });

        // Menambahkan kolom ke tabel pemeriksaan ibu hamil
        // Asumsi nama tabelnya 'pemeriksaan_ibu_hamils'
        Schema::table('pemeriksaan_ibu_hamils', function (Blueprint $table) {
            $table->foreignId('posyandu_id')->nullable()->constrained('posyandu')->onDelete('set null')->after('warga_id');
        });
    }
};
