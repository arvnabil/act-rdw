<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$latestImport = DB::table('imports')->latest()->first();

if (!$latestImport) {
    die("No imports found.\n");
}

echo "Latest Import ID: {$latestImport->id}\n";
echo "Importer: {$latestImport->importer}\n";
echo "Status: Total: {$latestImport->total_rows}, Success: {$latestImport->successful_rows}\n";

$failures = DB::table('failed_import_rows')
    ->where('import_id', $latestImport->id)
    ->get();

if ($failures->isEmpty()) {
    echo "No failed rows found for this import.\n";
}

foreach ($failures as $failure) {
    echo "Row #{$failure->index}\n";
    echo "Error: " . ($failure->validation_error ?? "NULL") . "\n";
    // echo "Data: " . $failure->data . "\n";
    echo "-------------------\n";
}
