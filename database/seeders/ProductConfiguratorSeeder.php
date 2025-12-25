<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Brand;
use App\Models\ConfiguratorCriteria;
use App\Models\Product;

class ProductConfiguratorSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Services
        $roomService = Service::firstOrCreate(
            ['slug' => 'room-solutions'],
            [
                'name' => 'Room Solutions',
                'description' => 'Meeting room conference solutions',
            ]
        );

        $serverService = Service::firstOrCreate(
            ['slug' => 'server-storage'],
            [
                'name' => 'Server & Storage',
                'description' => 'Enterprise server and storage infrastructure',
            ]
        );

        $surveillanceService = Service::firstOrCreate(
            ['slug' => 'smart-surveillance'],
            [
                'name' => 'Smart Surveillance',
                'description' => 'Advanced security and monitoring systems',
            ]
        );

        // 2. Create Brands
        // Room Brands
        $logitech = Brand::firstOrCreate(['slug' => 'logitech'], ['name' => 'Logitech', 'logo_path' => '/assets/img/partners/logi.jpg']);
        $yealink = Brand::firstOrCreate(['slug' => 'yealink'], ['name' => 'Yealink', 'logo_path' => '/assets/img/partners/yealink.jpg']);

        // Server Brands
        $dell = Brand::firstOrCreate(['slug' => 'dell'], ['name' => 'Dell Technologies', 'logo_path' => '/assets/img/partners/dell.png']);
        $hpe = Brand::firstOrCreate(['slug' => 'hpe'], ['name' => 'HPE', 'logo_path' => '/assets/img/partners/hpe.png']);

        // Surveillance Brands
        $hikvision = Brand::firstOrCreate(['slug' => 'hikvision'], ['name' => 'Hikvision', 'logo_path' => '/assets/img/partners/hikvision.png']);
        $dahua = Brand::firstOrCreate(['slug' => 'dahua'], ['name' => 'Dahua', 'logo_path' => '/assets/img/partners/dahua.png']);
        $axis = Brand::firstOrCreate(['slug' => 'axis'], ['name' => 'Axis', 'logo_path' => '/assets/img/partners/axis.png']);

        // 3. Create Configurator Criteria (Examples)
        // Room Criteria
        $smallRoom = ConfiguratorCriteria::firstOrCreate(
            ['service_id' => $roomService->id, 'type' => 'room_type', 'value' => 'small'],
            ['label' => 'Small Meeting Room']
        );
        $mediumRoom = ConfiguratorCriteria::firstOrCreate(
            ['service_id' => $roomService->id, 'type' => 'room_type', 'value' => 'medium'],
            ['label' => 'Medium Meeting Room']
        );

        // Server Criteria
        $dbScenario = ConfiguratorCriteria::firstOrCreate(
            ['service_id' => $serverService->id, 'type' => 'usage_scenario', 'value' => 'database'],
            ['label' => 'Database Server']
        );
        $rackForm = ConfiguratorCriteria::firstOrCreate(
            ['service_id' => $serverService->id, 'type' => 'form_factor', 'value' => 'rack'],
            ['label' => 'Rackmount']
        );

        // Surveillance Criteria
        $retailEnv = ConfiguratorCriteria::firstOrCreate(
            ['service_id' => $surveillanceService->id, 'type' => 'environment', 'value' => 'retail'],
            ['label' => 'Retail Store']
        );
        $cameraDome = ConfiguratorCriteria::firstOrCreate(
            ['service_id' => $surveillanceService->id, 'type' => 'camera_type', 'value' => 'dome'],
            ['label' => 'Dome Camera']
        );

        // 4. Create Products
        // Logitech MeetUp (Room / Small)
        $meetup = Product::firstOrCreate(
            ['slug' => 'logitech-meetup'],
            [
                'service_id' => $roomService->id,
                'brand_id' => $logitech->id,
                'name' => 'Logitech MeetUp',
                'description' => 'Logitech MeetUp is a premier ConferenceCam designed for small conference rooms and huddle rooms. With a room capturing, super-wide 120-degree field of view, MeetUp makes every seat at the table clearly visible.',
                'image_path' => '/assets/img/product/product_1_1.png', // Using the asset path from ProductDetails.txt context
                'sku' => '960-001101',
                'solution_type' => 'Unified Communication',
                'datasheet_url' => 'https://www.logitech.com/assets/65250/meetup-datasheet.pdf',
                'tags' => ['Camera', 'Conference', '4K', 'USB'],
                'specification_text' => 'MeetUp offers excellent video quality with a 4K Ultra HD camera and a full-range speaker system.',
                'specs' => [
                    ['name' => 'Brand', 'value' => 'Logitech'], // Explicit Brand spec for the frontend
                    ['name' => 'Field of View', 'value' => '120 degrees'],
                    ['name' => 'Microphone', 'value' => 'Integrated 3-beamforming array'],
                    ['name' => 'Camera', 'value' => '4K Ultra HD'],
                    ['name' => 'Speaker', 'value' => 'Full range speaker system'],
                    ['name' => 'Connectivity', 'value' => 'USB Plug-and-Play']
                ],
                'features_text' => 'Designed for huddle rooms and other smaller spaces, MeetUp packs big features into a compact form factor.',
                'features' => [ // Array of objects matching the frontend Features table
                    [
                        'name' => 'Super-wide 120° Field of View',
                        'description' => 'Widest field of view of any Logitech ConferenceCam',
                        'additional' => 'Perfect for small rooms'
                    ],
                    [
                        'name' => '4K Ultra HD Sensor',
                        'description' => 'High resolution video',
                        'additional' => 'Supports 1080p and 720p'
                    ],
                    [
                        'name' => '3-Microphone Beamforming Array',
                        'description' => 'Optimized for acoustics in huddle rooms',
                        'additional' => 'Optional expansion mic available'
                    ]
                ],
                'is_active' => true,
            ]
        );

        // Link to criteria
        if ($meetup->wasRecentlyCreated) {
            $meetup->criteria()->syncWithoutDetaching([$smallRoom->id]);
        }

        // Additional Room Products for "Related Products"
        $rallyUser = Product::firstOrCreate(
            ['slug' => 'logitech-rally-bar'],
            [
                'service_id' => $roomService->id,
                'brand_id' => $logitech->id,
                'name' => 'Logitech Rally Bar',
                'description' => 'Premier all-in-one video bar for midsize rooms.',
                'image_path' => '/assets/img/product/product_1_2.png',
                'sku' => '960-001308',
                'solution_type' => 'Video Bar',
                'tags' => ['Camera', 'Video Bar', 'Midsize'],
                'specs' => [
                    ['name' => 'Brand', 'value' => 'Logitech'],
                    ['name' => 'Field of View', 'value' => '90 degrees'],
                    ['name' => 'Zoom', 'value' => '15x HD Zoom'],
                ],
                'is_active' => true,
            ]
        );

        $yealinkBar = Product::firstOrCreate(
            ['slug' => 'yealink-meetingbar-a20'],
            [
                'service_id' => $roomService->id,
                'brand_id' => $yealink->id,
                'name' => 'Yealink MeetingBar A20',
                'description' => 'All-in-one video collaboration bar for small and huddle rooms.',
                'image_path' => '/assets/img/product/product_1_3.png',
                'sku' => 'A20-010',
                'solution_type' => 'Video Bar',
                'tags' => ['Yealink', 'Android', 'Teams'],
                'specs' => [
                    ['name' => 'Brand', 'value' => 'Yealink'],
                    ['name' => 'Camera', 'value' => '20MP'],
                    ['name' => 'OS', 'value' => 'Android 9.0'],
                ],
                'is_active' => true,
            ]
        );

        $polyStudio = Product::firstOrCreate(
            ['slug' => 'poly-studio-x50'],
            [
                'service_id' => $roomService->id,
                'brand_id' => $logitech->id, // Using logitech as placeholder brand if Poly not exists, or adjust
                'name' => 'Poly Studio X50',
                'description' => 'Radically simple video bar for medium-sized conference rooms.',
                'image_path' => '/assets/img/product/product_1_4.png',
                'sku' => '2200-85970-001',
                'solution_type' => 'Video Bar',
                'tags' => ['Poly', 'Video Bar', '4K'],
                'specs' => [
                    ['name' => 'Brand', 'value' => 'Poly'],
                    ['name' => 'Camera', 'value' => '4K, 5x Zoom'],
                    ['name' => 'Audio', 'value' => 'Stereo speakers'],
                ],
                'is_active' => true,
            ]
        );

        // Dell PowerEdge R750 (Server / Database / Rack)
        $r750 = Product::firstOrCreate(
            ['slug' => 'dell-poweredge-r750'],
            [
                'service_id' => $serverService->id,
                'brand_id' => $dell->id,
                'name' => 'Dell PowerEdge R750',
                'description' => 'Full-featured enterprise server delivering outstanding performance for the most demanding workloads.',
                'image_path' => '/assets/img/product/r750.jpg',
                'specs' => [
                    'Processor' => 'Up to 2x 3rd Gen Intel Xeon Scalable processors',
                    'Memory' => 'Up to 32 DDR4 DIMM slots',
                    'Storage' => 'Up to 24x 2.5-inch drives',
                    'Form Factor' => '2U Rack'
                ],
                'is_active' => true,
            ]
        );
        if ($r750->wasRecentlyCreated) {
            $r750->criteria()->syncWithoutDetaching([$dbScenario->id, $rackForm->id]);
        }

        // Hikvision Dome (Surveillance / Retail / Dome)
        $hikDome = Product::firstOrCreate(
            ['slug' => 'hikvision-4k-dome'],
            [
                'service_id' => $surveillanceService->id,
                'brand_id' => $hikvision->id,
                'name' => 'Hikvision 4K Dome Network Camera',
                'description' => 'High quality imaging with 8 MP resolution for retail environments.',
                'image_path' => '/assets/img/product/hikvision_dome.jpg', // Ensure this image exists or use placeholder
                'specs' => [
                    'Resolution' => '3840 × 2160',
                    'Lens' => '2.8 mm fixed lens',
                    'IR Range' => 'Up to 30 m',
                    'Protection' => 'IP67, IK10'
                ],
                'is_active' => true,
            ]
        );
        if ($hikDome->wasRecentlyCreated) {
            $hikDome->criteria()->syncWithoutDetaching([$retailEnv->id, $cameraDome->id]);
        }
    }
}
