<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

\Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
echo \Illuminate\Support\Facades\Artisan::output() . PHP_EOL;

if (!\Illuminate\Support\Facades\Schema::hasColumn('permohonans', 'notif_read_at')) {
    \Illuminate\Support\Facades\Schema::table('permohonans', function (\Illuminate\Database\Schema\Blueprint $table) {
        $table->timestamp('notif_read_at')->nullable()->after('keterangan');
    });
    echo "Column added manually." . PHP_EOL;
} else {
    echo "Column notif_read_at exists!" . PHP_EOL;
}
