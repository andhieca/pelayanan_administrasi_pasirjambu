<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$p = App\Models\Permohonan::where('status', 'ditolak')->latest()->first();
if ($p) {
    echo 'ID: ' . $p->id . PHP_EOL;
    echo 'Status: ' . $p->status . PHP_EOL;
    echo 'Invalid Items (cast): ' . json_encode($p->invalid_items) . PHP_EOL;
    echo 'Invalid Items (raw): ' . $p->getRawOriginal('invalid_items') . PHP_EOL;
    echo 'Keterangan: ' . $p->keterangan . PHP_EOL;
} else {
    echo 'No rejected permohonan found' . PHP_EOL;
}
