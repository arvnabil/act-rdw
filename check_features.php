<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use Modules\Core\Models\Product;

$products = Product::whereNotNull('features')->take(5)->get();

if ($products->isEmpty()) {
    echo "No products with features found.\n";
} else {
    foreach ($products as $product) {
        echo "Product ID: " . $product->id . "\n";
        echo "Features:\n";
        print_r($product->features);
        echo "\n-------------------\n";
    }
}
