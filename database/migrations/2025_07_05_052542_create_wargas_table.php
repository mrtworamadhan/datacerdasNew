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
        Schema::create('wargas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('desa_id')->constrained('desas')->onDelete('cascade'); // Terhubung ke desa
            $table->foreignId('kartu_keluarga_id')->nullable()->constrained('kartu_keluargas')->onDelete('set null'); // Terhubung ke KK
            $table->foreignId('rw_id')->constrained('rws')->onDelete('cascade'); // Terhubung ke RW
            $table->foreignId('rt_id')->constrained('rts')->onDelete('cascade'); // Terhubung ke RT
            $table->string('nik', 16)->unique(); // NIK unik
            $table->string('nama_lengkap');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->string('agama');
            $table->enum('status_perkawinan', ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati']);
            $table->string('pekerjaan'); // Akan jadi FK ke master_pekerjaan nanti
            $table->string('kewarganegaraan'); // Akan jadi FK ke master_kewarganegaraan nanti
            $table->string('golongan_darah')->nullable(); // Akan jadi FK ke master_golongan_darah nanti
            $table->string('alamat_lengkap'); // Alamat warga (bisa sama dengan KK)
            $table->string('hubungan_keluarga')->nullable(); // Contoh: Kepala Keluarga, Istri, Anak, Cucu
            $table->enum('status_kependudukan', ['Warga Asli', 'Pendatang', 'Sementara', 'Pindah', 'Meninggal'])->default('Warga Asli');
            $table->json('status_khusus')->nullable(); // Untuk disabilitas, lansia, dll. (JSON array)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wargas');
    }
};