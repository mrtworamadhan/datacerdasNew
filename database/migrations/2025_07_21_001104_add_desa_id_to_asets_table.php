<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asets', function (Blueprint $table) {
            // Tambahkan foreign key ke tabel desas
            // Kita letakkan setelah 'id' agar rapi
            $table->unsignedBigInteger('desa_id')->nullable()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('asets', function (Blueprint $table) {
            // Ini untuk jaga-jaga jika perlu rollback
            $table->dropColumn('desa_id');
        });
    }
};