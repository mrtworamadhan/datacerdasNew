<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint; // Perbaikan: dari Illuminate->Database->Schema->Blueprint
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('penerima_bantuan_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penerima_bantuan_id')->constrained('penerima_bantuans')->onDelete('cascade');
            $table->string('photo_name')->nullable(); // Nama deskriptif untuk foto (misal: "Foto Tampak Depan Rumah")
            $table->string('file_path'); // Path ke file gambar di storage
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penerima_bantuan_photos');
    }
};
