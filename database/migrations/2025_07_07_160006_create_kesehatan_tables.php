<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Tabel untuk data dasar anak (Buku KIA/KMS Digital)
        Schema::create('data_kesehatan_anaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warga_id')->unique()->constrained('wargas')->onDelete('cascade');
            $table->date('tanggal_lahir');
            $table->float('bb_lahir')->nullable(); // Berat Badan Lahir
            $table->float('tb_lahir')->nullable(); // Tinggi Badan Lahir
            $table->string('nama_ayah')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->timestamps();
        });

        // Tabel untuk mencatat setiap riwayat pemeriksaan anak
        Schema::create('pemeriksaan_anaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('data_kesehatan_anak_id')->constrained('data_kesehatan_anaks')->onDelete('cascade');
            $table->date('tanggal_pemeriksaan');
            $table->integer('usia_saat_periksa');
            $table->float('berat_badan');
            $table->float('tinggi_badan');
            $table->string('status_gizi')->nullable();
            $table->string('imunisasi_diterima')->nullable();
            $table->boolean('vitamin_a_diterima')->default(false);
            $table->boolean('obat_cacing_diterima')->default(false);
            $table->text('catatan_kader')->nullable();
            $table->timestamps();
        });

        // Tabel untuk data dasar ibu hamil
        Schema::create('data_ibu_hamils', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warga_id')->constrained('wargas')->onDelete('cascade');
            $table->integer('kehamilan_ke');
            $table->date('hpht'); // Hari Pertama Haid Terakhir
            $table->date('hpl');  // Hari Perkiraan Lahir
            $table->string('jarak_kehamilan')->nullable(); // Jarak dengan anak sebelumnya
            $table->boolean('memiliki_bpjs')->default(false);
            $table->timestamps();
        });

        // Tabel untuk mencatat setiap riwayat pemeriksaan ibu hamil
        Schema::create('pemeriksaan_ibu_hamils', function (Blueprint $table) {
            $table->id();
            $table->foreignId('data_ibu_hamil_id')->constrained('data_ibu_hamils')->onDelete('cascade');
            $table->date('tanggal_pemeriksaan');
            $table->float('berat_badan');
            $table->float('tinggi_badan');
            $table->string('tensi_darah');
            $table->float('hb')->nullable(); // Hemoglobin
            $table->boolean('pemberian_fe')->default(false); // Tablet Tambah Darah
            $table->text('catatan_kader')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pemeriksaan_ibu_hamils');
        Schema::dropIfExists('data_ibu_hamils');
        Schema::dropIfExists('pemeriksaan_anaks');
        Schema::dropIfExists('data_kesehatan_anaks');
    }
};
