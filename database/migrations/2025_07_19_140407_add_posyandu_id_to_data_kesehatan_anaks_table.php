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
        Schema::table('data_kesehatan_anaks', function (Blueprint $table) {
            // Menambahkan kolom setelah 'warga_id'.
            // Ini akan menjadi penghubung utama ke posyandu.
            $table->foreignId('posyandu_id')->nullable()->constrained('posyandu')->onDelete('set null')->after('warga_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_kesehatan_anaks', function (Blueprint $table) {
            // Ini untuk jaga-jaga jika perlu rollback
            $table->dropForeign(['posyandu_id']);
            $table->dropColumn('posyandu_id');
        });
    }
};
