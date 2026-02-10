<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$clients = \Illuminate\Support\Facades\DB::table('clients')
    ->where('is_active', true)
    ->get();

echo "Active Clients: " . $clients->count() . "\n";
echo $clients->toJson(JSON_PRETTY_PRINT);
