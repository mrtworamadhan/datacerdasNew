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
        Schema::table('penerima_bantuans', function (Blueprint $table) {
            // Tambahkan kolom status permohonan
            $table->enum('status_permohonan', ['Diajukan', 'Disetujui', 'Ditolak'])->default('Diajukan')->after('keterangan');
            
            // Tambahkan kolom siapa yang mengajukan
            $table->foreignId('diajukan_oleh_user_id')->nullable()->constrained('users')->onDelete('set null')->after('status_permohonan');
            
            // Tambahkan kolom siapa yang menyetujui/menolak dan kapan
            $table->foreignId('disetujui_oleh_user_id')->nullable()->constrained('users')->onDelete('set null')->after('diajukan_oleh_user_id');
            $table->timestamp('tanggal_verifikasi')->nullable()->after('disetujui_oleh_user_id');
            $table->text('catatan_persetujuan_penolakan')->nullable()->after('tanggal_verifikasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penerima_bantuans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('disetujui_oleh_user_id');
            $table->dropConstrainedForeignId('diajukan_oleh_user_id');
            $table->dropColumn('status_permohonan');
            $table->dropColumn('tanggal_verifikasi');
            $table->dropColumn('catatan_persetujuan_penolakan');
        });
    }
};
