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
        Schema::table('desas', function (Blueprint $table) {
            // Kolom untuk status langganan
            $table->enum('subscription_status', ['aktif', 'trial', 'nonaktif'])->default('trial')->after('kode_pos');
            // Tanggal berakhirnya langganan
            $table->timestamp('subscription_ends_at')->nullable()->after('subscription_status');
            // Tanggal berakhirnya masa percobaan (jika ada)
            $table->timestamp('trial_ends_at')->nullable()->after('subscription_ends_at');
            // Jika Anda ingin menambahkan kolom plan_id untuk berbagai paket langganan di masa depan:
            // $table->foreignId('plan_id')->nullable()->constrained('plans')->onDelete('set null')->after('trial_ends_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('desas', function (Blueprint $table) {
            $table->dropColumn('subscription_status');
            $table->dropColumn('subscription_ends_at');
            $table->dropColumn('trial_ends_at');
            // Jika Anda menambahkan plan_id, pastikan juga untuk menghapusnya:
            // $table->dropConstrainedForeignId('plan_id');
        });
    }
};
