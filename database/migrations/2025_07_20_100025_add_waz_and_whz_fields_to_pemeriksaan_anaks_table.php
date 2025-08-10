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
        Schema::table('pemeriksaan_anaks', function (Blueprint $table) {
            // Kolom untuk WAZ (Underweight)
            $table->decimal('zscore_bb_u', 5, 2)->nullable()->after('status_stunting');
            $table->string('status_underweight')->nullable()->after('zscore_bb_u');

            // Kolom untuk WHZ (Wasting)
            $table->decimal('zscore_bb_tb', 5, 2)->nullable()->after('status_underweight');
            $table->string('status_wasting')->nullable()->after('zscore_bb_tb');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemeriksaan_anaks', function (Blueprint $table) {
            //
        });
    }
};
