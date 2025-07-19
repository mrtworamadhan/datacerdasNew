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
        Schema::table('kategori_bantuans', function (Blueprint $table) {
            $table->boolean('is_active_for_submission')->default(false)->after('allow_multiple_recipients_per_kk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kategori_bantuans', function (Blueprint $table) {
            $table->dropColumn('is_active_for_submission');
        });
    }
};
