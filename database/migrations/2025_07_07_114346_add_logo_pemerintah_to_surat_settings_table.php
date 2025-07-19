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
        Schema::table('surat_settings', function (Blueprint $table) {
            $table->string('path_logo_pemerintah')->nullable()->after('path_kop_surat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_settings', function (Blueprint $table) {
            $table->dropColumn(columns: 'path_logo_pemerintah');
        });
    }
};
