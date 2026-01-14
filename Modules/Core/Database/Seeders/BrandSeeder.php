<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Models\Brand;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        Brand::firstOrCreate(['slug' => 'logitech'], ['name' => 'Logitech']);
        Brand::firstOrCreate(['slug' => 'yealink'], ['name' => 'Yealink']);
        Brand::firstOrCreate(['slug' => 'jabra'], ['name' => 'Jabra']);
    }
}
