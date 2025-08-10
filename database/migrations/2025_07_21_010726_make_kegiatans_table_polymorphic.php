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
        Schema::table('kegiatans', function (Blueprint $table) {
            // Hapus foreign key yang lama dulu
            $table->dropForeign(['lembaga_id']);
            $table->dropColumn('lembaga_id');

            // Tambahkan kolom polymorphic baru setelah desa_id
            $table->morphs('kegiatanable'); // Ini akan membuat kolom kegiatanable_id dan kegiatanable_type

            // Tambahkan kolom status untuk alur Proposal -> LPJ
            $table->string('status')->default('Proposal'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
