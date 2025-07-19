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
        Schema::table('fasums', function (Blueprint $table) {
            $table->foreignId('rw_id')->nullable()->after('desa_id')->constrained('rws')->onDelete('set null');
            $table->foreignId('rt_id')->nullable()->after('rw_id')->constrained('rts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fasums', function (Blueprint $table) {
            $table->dropForeign(['rw_id']);
            $table->dropColumn('rw_id');
            $table->dropForeign(['rt_id']);
            $table->dropColumn('rt_id');
        });
    }
};
