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
        Schema::table('pengajuan_surats', function (Blueprint $table) {
            $table->text('keperluan')->nullable()->after('warga_id');
            $table->timestamp('disetujui_rt_at')->nullable()->after('status_permohonan');
            $table->foreignId('disetujui_rt_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('disetujui_rw_at')->nullable()->after('disetujui_rt_at');
            $table->foreignId('disetujui_rw_oleh')->nullable()->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_surats', function (Blueprint $table) {
            //
        });
    }
};
