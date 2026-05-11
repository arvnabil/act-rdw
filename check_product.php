<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Modules\ProductCatalog\Models\Product;

$sku = 'PAR1-PRO-BASE-NH1Y';
$product = Product::where('sku', $sku)->first();

if ($product) {
    echo "FOUND: " . $product->name . " (ID: " . $product->id . ")\n";
} else {
    echo "NOT FOUND: SKU " . $sku . " does not exist in local database.\n";
    
    // Let's see some samples
    echo "\nSample products in database:\n";
    $samples = Product::limit(5)->get(['id', 'name', 'sku']);
    foreach ($samples as $s) {
        echo "- ID: {$s->id} | Name: {$s->name} | SKU: " . ($s->sku ?: 'NULL') . "\n";
    }
}
