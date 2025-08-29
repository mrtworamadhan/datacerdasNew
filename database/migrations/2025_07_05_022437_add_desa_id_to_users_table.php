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
            // Tambahkan kolom desa_id
           $table->foreignId('desa_id')->nullable()->after('password')->constrained('desas');

            // Tambahkan kolom user_type
            $table->string('user_type')->after('email'); // Default ke 'warga'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['desa_id']);
            // Drop the column
            $table->dropColumn('desa_id');
            // Drop user_type column
            $table->dropColumn('user_type');
        });
    }
};