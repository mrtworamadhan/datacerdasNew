<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posyandu', function (Blueprint $table) {
            // STEP 1: Just add the column and allow it to be empty (nullable)
            // We remove ->constrained('desas') for now.
            $table->unsignedBigInteger('desa_id')->nullable()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('posyandu', function (Blueprint $table) {
            $table->dropColumn('desa_id');
        });
    }
};
