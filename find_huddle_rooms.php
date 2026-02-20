<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$solutions = \Modules\ServiceSolutions\Models\ServiceSolution::with('service')
    ->where('title', 'like', '%Huddle Room%')
    ->get();

echo "Found " . $solutions->count() . " 'Huddle Room' solutions:\n";
foreach ($solutions as $s) {
    echo "- Service: " . ($s->service ? $s->service->name : 'Unknown Service') . " (Service ID: " . $s->service_id . ") [Solution ID: " . $s->id . "]\n";
}
