<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // Tabel Master untuk menyimpan kode klasifikasi surat resmi
        Schema::create('klasifikasi_surats', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('deskripsi');
            $table->timestamps();
        });

        // Tabel untuk menyimpan pengaturan global surat per desa
        Schema::create('surat_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('desa_id')->unique()->constrained('desas')->onDelete('cascade');
            $table->string('path_kop_surat')->nullable();
            $table->string('penanda_tangan_nama')->nullable();
            $table->string('penanda_tangan_jabatan')->nullable();
            $table->timestamps();
        });

        // Tabel untuk menyimpan template/jenis surat yang dibuat oleh Admin Desa
        Schema::create('jenis_surats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('desa_id')->constrained('desas')->onDelete('cascade');
            $table->foreignId('klasifikasi_surat_id')->constrained('klasifikasi_surats')->onDelete('cascade');
            $table->string('nama_surat');
            $table->string('judul_surat');
            $table->longText('isi_template');
            $table->json('persyaratan')->nullable();
            $table->string('custom_fields')->nullable();
            $table->timestamps();
        });

        // Tabel untuk mencatat setiap transaksi pengajuan surat
        Schema::create('pengajuan_surats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('desa_id')->constrained('desas')->onDelete('cascade');
            $table->foreignId('jenis_surat_id')->constrained('jenis_surats')->onDelete('cascade');
            $table->foreignId('warga_id')->constrained('wargas')->onDelete('cascade');
            $table->foreignId('diajukan_oleh_user_id')->constrained('users')->onDelete('cascade');
            $table->string('status_permohonan')->default('Diajukan');
            $table->string('nomor_surat')->nullable()->unique();
            $table->date('tanggal_pengajuan');
            $table->date('tanggal_selesai')->nullable();
            $table->string('file_pendukung')->nullable();
            $table->json('detail_tambahan')->nullable(); 
            $table->enum('jalur_pengajuan', ['rt_rw', 'langsung_desa'])->default('langsung_desa');
            $table->string('file_pengantar_rt_rw')->nullable();
            $table->json('persyaratan_terpenuhi')->nullable();
            $table->text('catatan_penolakan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pengajuan_surats');
        Schema::dropIfExists('jenis_surats');
        Schema::dropIfExists('surat_settings');
        Schema::dropIfExists('klasifikasi_surats');
    }
};
