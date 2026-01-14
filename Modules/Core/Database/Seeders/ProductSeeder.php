<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Models\Product;
use Modules\Core\Models\Brand;
use Modules\ServiceSolutions\Models\Service;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure dependecies exist if running standalone
        $brandLogi = Brand::firstOrCreate(['slug' => 'logitech'], ['name' => 'Logitech']);
        $brandYealink = Brand::firstOrCreate(['slug' => 'yealink'], ['name' => 'Yealink']);
        $brandJabra = Brand::firstOrCreate(['slug' => 'jabra'], ['name' => 'Jabra']);

        $svcVC = Service::firstOrCreate(['slug' => 'video-conference'], ['name' => 'Video Conference']);

        // 1. Logitech MeetUp 2
        Product::firstOrCreate(
            ['slug' => 'meetup-2-prod'],
            [
                'name' => 'Logitech MeetUp 2',
                'service_id' => $svcVC->id,
                'brand_id' => $brandLogi->id,
                'description' => 'All-in-One ConferenceCam with AI Viewfinder.',
                'image_path' => '/assets/img/product/product_1_3.png',
                'price' => 9500000,
                'is_active' => true
            ]
        );

        // 2. Logitech Rally Bar
        Product::firstOrCreate(
            ['slug' => 'rally-bar-prod'],
            [
                'name' => 'Logitech Rally Bar',
                'service_id' => $svcVC->id,
                'brand_id' => $brandLogi->id,
                'description' => 'Premium All-in-One Video Bar for Small to Medium Rooms.',
                'image_path' => '/assets/img/product/product_1_2.png',
                'price' => 45000000,
                'is_active' => true
            ]
        );

        // 3. Jabra PanaCast 50
        Product::firstOrCreate(
            ['slug' => 'panacast-50-prod'],
            [
                'name' => 'Jabra PanaCast 50',
                'service_id' => $svcVC->id,
                'brand_id' => $brandJabra->id,
                'description' => 'The first new-normal-ready intelligent video bar.',
                'image_path' => '/assets/img/product/product_1_5.png',
                'price' => 42000000,
                'is_active' => true
            ]
        );

        // Add other referenced products as needed
    }
}
