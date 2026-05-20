<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('logs_antrean', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_id')->constrained()->cascadeOnDelete();
            $table->string('action'); // submitted, rejected, approved_petugas, approved_camat
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('timestamp')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logs_antrean');
    }
};
