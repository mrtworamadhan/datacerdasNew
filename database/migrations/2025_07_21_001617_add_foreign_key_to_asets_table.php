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
        Schema::table('asets', function (Blueprint $table) {
            $table->foreign('desa_id')->references('id')->on('desas');
            $table->unsignedBigInteger('desa_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asets', function (Blueprint $table) {
            //
        });
    }
};
