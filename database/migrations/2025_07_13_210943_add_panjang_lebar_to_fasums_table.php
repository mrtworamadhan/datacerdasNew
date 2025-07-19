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
        Schema::table('fasums', function (Blueprint $table) {
            // Tambahkan kolom panjang dan lebar setelah kolom 'detail_spesifikasi'
            // Jika Anda ingin tipe data numerik (misal untuk perhitungan):
            $table->decimal('panjang', 8, 2)->nullable()->after('detail_spesifikasi'); // Contoh decimal (total 8 digit, 2 di belakang koma)
            $table->decimal('lebar', 8, 2)->nullable()->after('panjang');
            $table->string('alamat_lengkap')->nullable()->after('lebar');
            $table->string('luas_area')->nullable()->after('alamat_lengkap');
            $table->string('kapasitas')->nullable()->after('luas_area');
            $table->string('kontak_pengelola')->nullable()->after('kapasitas');
            $table->string('status_kepemilikan')->nullable()->after('kontak_pengelola');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fasums', function (Blueprint $table) {
            $table->dropColumn('panjang');
            $table->dropColumn('lebar');
            $table->dropColumn('alamat_lengkap');
            $table->dropColumn('luas_area');
            $table->dropColumn('kapasitas');
            $table->dropColumn('kontak_pengelola');
            $table->dropColumn('status_kepemilikan');
        });
    }
};
