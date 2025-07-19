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
        Schema::table('wargas', function (Blueprint $table) {
            $table->enum('pendidikan', [
                'Tidak/Belum Sekolah', 'SD', 'SMP', 'SMA', 'S1', 'S2', 'S3'
            ])->nullable()->after('pekerjaan'); // Tambahkan setelah 'pekerjaan'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wargas', function (Blueprint $table) {
            $table->dropColumn('pendidikan');
        });
    }
};