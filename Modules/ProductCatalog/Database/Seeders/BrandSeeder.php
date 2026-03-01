<?php

namespace Modules\ProductCatalog\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\ProductCatalog\Models\Brand;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        Brand::firstOrCreate(['slug' => 'logitech'], ['name' => 'Logitech']);
        Brand::firstOrCreate(['slug' => 'yealink'], ['name' => 'Yealink']);
        Brand::firstOrCreate(['slug' => 'jabra'], ['name' => 'Jabra']);
    }
}
