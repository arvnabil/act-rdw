<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$failures = DB::table('failed_import_rows')
    ->join('imports', 'failed_import_rows.import_id', '=', 'imports.id')
    ->where('imports.importer', 'App\Filament\Imports\BrandImporter')
    ->select('failed_import_rows.*')
    ->latest()
    ->limit(6)
    ->get();

foreach ($failures as $failure) {
    echo "Row Index: {$failure->index}\n";
    echo "Data: " . json_encode($failure->data, JSON_PRETTY_PRINT) . "\n";
    echo "Error: " . $failure->validation_error . "\n";
    echo "-------------------------------------------\n";
}
