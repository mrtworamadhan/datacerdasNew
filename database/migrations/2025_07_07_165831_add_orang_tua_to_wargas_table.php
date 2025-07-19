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
            $table->string('nama_ayah_kandung')->nullable()->after('kewarganegaraan');
            $table->string('nama_ibu_kandung')->nullable()->after('nama_ayah_kandung');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wargas', function (Blueprint $table) {
            $table->dropColumn('nama_ayah_kandung', 'nama_ibu_kandung');
        });
    }
};
