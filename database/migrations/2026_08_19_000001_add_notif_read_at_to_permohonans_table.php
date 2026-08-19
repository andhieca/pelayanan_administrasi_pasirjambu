<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('permohonans', function (Blueprint $table) {
            if (!Schema::hasColumn('permohonans', 'notif_read_at')) {
                $table->timestamp('notif_read_at')->nullable()->after('keterangan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('permohonans', function (Blueprint $table) {
            if (Schema::hasColumn('permohonans', 'notif_read_at')) {
                $table->dropColumn('notif_read_at');
            }
        });
    }
};
