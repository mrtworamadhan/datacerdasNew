<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wargas', function (Blueprint $table) {
            // Hapus kolom lama yang berupa string
            $table->dropColumn([
                'agama',
                'status_perkawinan',
                'pekerjaan',
                'pendidikan',
                'golongan_darah',
                'hubungan_keluarga',
                'status_kependudukan',
            ]);

            // Tambahkan kolom foreign key
            $table->foreignId('agama_id')->nullable()->constrained('agamas')->nullOnDelete();
            $table->foreignId('status_perkawinan_id')->nullable()->constrained('status_perkawinans')->nullOnDelete();
            $table->foreignId('pekerjaan_id')->nullable()->constrained('pekerjaans')->nullOnDelete();
            $table->foreignId('pendidikan_id')->nullable()->constrained('pendidikans')->nullOnDelete();
            $table->foreignId('golongan_darah_id')->nullable()->constrained('golongan_darahs')->nullOnDelete();
            $table->foreignId('hubungan_keluarga_id')->nullable()->constrained('hubungan_keluargas')->nullOnDelete();
            $table->foreignId('status_kependudukan_id')->nullable()->constrained('status_kependudukans')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('wargas', function (Blueprint $table) {
            // Hapus foreign key
            $table->dropForeign(['agama_id']);
            $table->dropForeign(['status_perkawinan_id']);
            $table->dropForeign(['pekerjaan_id']);
            $table->dropForeign(['pendidikan_id']);
            $table->dropForeign(['golongan_darah_id']);
            $table->dropForeign(['hubungan_keluarga_id']);
            $table->dropForeign(['status_kependudukan_id']);

            $table->dropColumn([
                'agama_id',
                'status_perkawinan_id',
                'pekerjaan_id',
                'pendidikan_id',
                'golongan_darah_id',
                'hubungan_keluarga_id',
                'status_kependudukan_id',
            ]);

            // Kembalikan kolom lama
            $table->string('agama')->nullable();
            $table->string('status_perkawinan')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->string('pendidikan')->nullable();
            $table->string('golongan_darah')->nullable();
            $table->string('hubungan_keluarga')->nullable();
            $table->string('status_kependudukan')->nullable();
        });
    }
};
