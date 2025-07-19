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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('rw_id')->nullable()->after('desa_id')->constrained('rws')->onDelete('set null');
            $table->foreignId('rt_id')->nullable()->after('rw_id')->constrained('rts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('rw_id');
            $table->dropColumn('rw_id');
            $table->dropConstrainedForeignId('rt_id');
            $table->dropColumn('rt_id');
        });
    }
};