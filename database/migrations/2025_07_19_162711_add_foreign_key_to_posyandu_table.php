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
            // STEP 2: Now that the column has data, we apply the rule.
            // We also change it so it can't be null anymore.
            $table->foreign('desa_id')->references('id')->on('desas');
            $table->unsignedBigInteger('desa_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posyandu', function (Blueprint $table) {
            //
        });
    }
};
