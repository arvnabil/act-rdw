<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "--- START DEBUG ---\n";

// check products
if (Schema::hasTable('products')) {
    echo "Table 'products' EXISTS.\n";
    try {
        $create = DB::select('SHOW CREATE TABLE products');
        print_r($create[0]);
    } catch (\Throwable $e) {
        echo "Error getting CREATE TABLE products: " . $e->getMessage() . "\n";
    }
} else {
    echo "Table 'products' DOES NOT EXIST.\n";
}

// check pivot
if (Schema::hasTable('product_category_product')) {
    echo "Table 'product_category_product' EXISTS.\n";
    try {
        $create = DB::select('SHOW CREATE TABLE product_category_product');
        print_r($create[0]);
    } catch (\Throwable $e) {
        echo "Error getting CREATE TABLE product_category_product: " . $e->getMessage() . "\n";
    }
} else {
    echo "Table 'product_category_product' DOES NOT EXIST.\n";
}

echo "--- END DEBUG ---\n";
