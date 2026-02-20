<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$product = \Modules\Core\Models\Product::where('name', 'like', '%Logitech Zone Wireless%')->first();
if ($product) {
    echo "Product ID: " . $product->id . "\n";
    $solutions = \Illuminate\Support\Facades\DB::table('product_service_solution')
        ->where('product_id', $product->id)
        ->get();
    
    echo "Found " . $solutions->count() . " associations:\n";
    foreach ($solutions as $sol) {
        echo "- Service Solution ID: " . $sol->service_solution_id . "\n";
    }
} else {
    echo "Product not found\n";
}
