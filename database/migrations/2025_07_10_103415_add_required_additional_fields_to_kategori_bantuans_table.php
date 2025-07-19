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
            $table->text('required_additional_fields_json')->nullable()->after('kriteria_json');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kategori_bantuans', function (Blueprint $table) {
            $table->dropColumn('required_additional_fields_json');
        });
    }
};
