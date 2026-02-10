<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Fix ID 16 specifically
\Illuminate\Support\Facades\DB::table('clients')
    ->where('id', 16)
    ->update(['logo' => null]);

echo "Fixed Client ID 16 (PT Telkom) logo to null.\n";
