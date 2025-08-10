<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Kolom ini hanya untuk user dengan tipe 'kader_posyandu'.
            // Kita buat nullable karena admin atau user lain tidak punya posyandu_id.
            // Kita taruh setelah kolom rw_id agar rapi.
            $table->foreignId('posyandu_id')->nullable()->constrained('posyandu')->after('rw_id');
        });
    }

    /**
     * Balikkan migrasi.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Ini untuk jaga-jaga jika perlu rollback
            $table->dropForeign(['posyandu_id']);
            $table->dropColumn('posyandu_id');
        });
    }
};