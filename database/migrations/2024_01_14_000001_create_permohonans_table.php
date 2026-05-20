<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('permohonans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('jenis_layanan'); // 'Dispen Nikah', 'Rekomendasi Bantuan', 'Izin Keramaian'
            $table->string('no_antrean');
            $table->string('status')->default('pending'); // pending, ditolak, menunggu_camat, disetujui
            $table->string('file_path')->nullable();
            $table->text('keterangan')->nullable(); // For rejection reason
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permohonans');
    }
};
