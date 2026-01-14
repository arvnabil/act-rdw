<?php

namespace Modules\ServiceSolutions\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\ServiceSolutions\Models\Configurator;
use Modules\ServiceSolutions\Models\ConfiguratorStep;
use Modules\ServiceSolutions\Models\ConfiguratorQuestion;
use Modules\ServiceSolutions\Models\ConfiguratorOption;

class ProductConfiguratorSeeder extends Seeder
{
    public function run(): void
    {
        // Clean up
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Configurator::truncate();
        ConfiguratorStep::truncate();
        ConfiguratorQuestion::truncate();
        ConfiguratorOption::truncate();
        DB::table('product_configurator_option')->truncate();

        // Ensure Brands exist
        $brandLogi = \Modules\Core\Models\Brand::firstOrCreate(['slug' => 'logitech'], ['name' => 'Logitech']);
        $brandYealink = \Modules\Core\Models\Brand::firstOrCreate(['slug' => 'yealink'], ['name' => 'Yealink']);
        $brandJabra = \Modules\Core\Models\Brand::firstOrCreate(['slug' => 'jabra'], ['name' => 'Jabra']);

        // Ensure Service exists
        $svcVC = \Modules\ServiceSolutions\Models\Service::firstOrCreate(
            ['slug' => 'video-conference'],
            ['name' => 'Video Conference']
        );

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Create the Video Conference Configurator
        $conf = Configurator::create([
            'name' => 'Video Conference',
            'slug' => 'video-conference',
            'description' => 'Build your ideal meeting room setup.',
            'image' => '/assets/img/service/service_6_1.jpg',
            'is_active' => true,
        ]);

        // --- Step 1: Room & Brand ---
        $step1 = $conf->steps()->create([
            'name' => 'Room & Brand',
            'title' => 'Select Room Type & Brand',
            'description' => 'Start by choosing your room size and preferred brand.',
            'sort_order' => 1,
        ]);

        // Question 1.1: Room Type (Structure from RoomConfigurator.jsx metaData.roomTypes)
        $qRoom = $step1->questions()->create([
            'label' => 'Room Type',
            'variable_name' => 'roomType',
            'type' => 'card_selection',
            'is_mandatory' => true,
            'sort_order' => 1,
        ]);

        $qRoom->options()->createMany([
            ['label' => 'Huddle Room', 'value' => 'huddle_room', 'metadata' => ['description' => '1-5 People', 'image' => '/assets/img/icon/feature_1_1.svg']],
            ['label' => 'Small Room', 'value' => 'small_room', 'metadata' => ['description' => '4-6 People', 'image' => '/assets/img/icon/feature_1_2.svg']],
            ['label' => 'Medium Room', 'value' => 'medium_room', 'metadata' => ['description' => '6-10 People', 'image' => '/assets/img/icon/feature_1_3.svg']],
        ]);

        // Question 1.2: Brand (Structure from RoomConfigurator.jsx metaData.brands)
        $qBrand = $step1->questions()->create([
            'label' => 'Brand',
            'variable_name' => 'brand',
            'type' => 'card_selection',
            'is_mandatory' => true,
            'sort_order' => 2,
        ]);

        $qBrand->options()->createMany([
            ['label' => 'Logitech', 'value' => 'logitech', 'metadata' => ['image' => '/assets/img/brand/brand_1_1.svg']],
            ['label' => 'Yealink', 'value' => 'yealink', 'metadata' => ['image' => '/assets/img/brand/brand_1_2.svg']],
            ['label' => 'Jabra', 'value' => 'jabra', 'metadata' => ['image' => '/assets/img/brand/brand_1_3.svg']],
        ]);

        // --- Step 2: Platform --- (Structure from RoomConfigurator.jsx metaData.platforms)
        $step2 = $conf->steps()->create([
            'name' => 'Platform',
            'title' => 'Select Conferencing Platform',
            'description' => 'Choose the software platform you use.',
            'sort_order' => 2,
        ]);

        $qPlatform = $step2->questions()->create([
            'label' => 'Platform',
            'variable_name' => 'platform',
            'type' => 'card_selection',
            'is_mandatory' => true,
        ]);

        $qPlatform->options()->createMany([
            ['label' => 'Google Meet', 'value' => 'google', 'metadata' => ['image' => '/assets/img/brand/brand_1_4.svg']],
            ['label' => 'Zoom', 'value' => 'zoom', 'metadata' => ['image' => '/assets/img/brand/brand_1_5.svg']],
            ['label' => 'Microsoft Teams', 'value' => 'teams', 'metadata' => ['image' => '/assets/img/brand/brand_1_6.svg']],
            ['label' => 'BYOD', 'value' => 'byod', 'metadata' => ['image' => '/assets/img/icon/feature_1_4.svg']],
        ]);

        // --- Step 3: Deployment --- (Structure from RoomConfigurator.jsx metaData.deploymentTypes)
        $step3 = $conf->steps()->create([
            'name' => 'Deployment',
            'title' => 'Deployment Method',
            'description' => 'How would you like to deploy?',
            'sort_order' => 3,
            'conditions' => ['platform' => ['operator' => '!=', 'value' => 'byod']], // Skip if BYOD
        ]);

        $qDeploy = $step3->questions()->create([
            'label' => 'Deployment Mode',
            'variable_name' => 'deployment',
            'type' => 'card_selection',
            'is_mandatory' => true,
        ]);

        $qDeploy->options()->createMany([
            ['label' => 'Appliance Mode', 'value' => 'appliance', 'metadata' => ['description' => 'Runs natively on the device (Android/CollabOS). No PC required.', 'image' => '/assets/img/icon/service-2-1.svg']],
            ['label' => 'PC Based', 'value' => 'pc-based', 'metadata' => ['description' => 'Requires a dedicated meeting room PC (Windows/Mac).', 'image' => '/assets/img/icon/service-2-2.svg']],
        ]);

        // --- Step 4: Main Device (Camera) --- (Matching productsData from RoomConfigurator.jsx)
        $step4 = $conf->steps()->create([
            'name' => 'Main Device',
            'title' => 'Select Camera / Video Bar',
            'description' => 'Choose your primary device.',
            'sort_order' => 4,
        ]);

        $qDevice = $step4->questions()->create([
            'label' => 'Device',
            'variable_name' => 'main_device',
            'type' => 'card_selection', // Single select main camera
            'is_mandatory' => true,
        ]);

        // -- Logitech Huddle Room Options --
        $optMeetUp2 = $qDevice->options()->create([
            'label' => 'Logitech MeetUp 2',
            'value' => 'meetup-2',
            'metadata' => [
                'price' => 9500000,
                'image' => '/assets/img/product/product_1_3.png',
                'description' => 'USB conference camera with built-in AI',
                // Generic Attributes Example
                'attributes' => [
                    ['type' => 'badge', 'text' => 'New AI Camera', 'color' => 'success'],
                    ['type' => 'icon_text', 'text' => 'Up to 6 People', 'icon' => 'fa-users'],
                    ['type' => 'separator'],
                    ['type' => 'list', 'items' => ['4K Sensor', 'Motorized Pan/Tilt', 'RightSense AI']],
                    ['type' => 'link', 'text' => 'View Datasheet', 'url' => '#', 'icon' => 'fa-file-pdf', 'style' => 'text_link'],
                ],
                'accessories' => [
                    ['id' => 'meetup-tv-mount', 'name' => 'TV Mount for MeetUp', 'description' => 'Mount MeetUp 2 to a TV', 'price' => 500000, 'product_id' => 1]
                ]
            ],
            'conditions' => ['brand' => 'logitech', 'roomType' => 'huddle_room']
        ]);

        // Create Dummy Product & Link
        $prodMeetUp2 = \Modules\Core\Models\Product::where('slug', 'meetup-2-prod')->first();
        if ($prodMeetUp2) {
            $optMeetUp2->products()->attach($prodMeetUp2->id);
        }

        // Create Accessory Products first
        $prodTVMount = \Modules\Core\Models\Product::updateOrCreate(
            ['slug' => 'tv-mount-video-bar-prod'],
            ['name' => 'TV Mount for Video Bars', 'price' => 800000, 'description' => 'Securely mount your Video Bar to a TV', 'service_id' => $svcVC->id, 'brand_id' => $brandLogi->id]
        );
        $prodWallMount = \Modules\Core\Models\Product::updateOrCreate(
            ['slug' => 'wall-mount-video-bar-prod'],
            ['name' => 'Wall Mount for Video Bars', 'price' => 600000, 'description' => 'Securely mount your Video Bar to a wall', 'service_id' => $svcVC->id, 'brand_id' => $brandLogi->id]
        );

        $optRallyBarMini = $qDevice->options()->create([
            'label' => 'Logitech Rally Bar Mini',
            'value' => 'rally-bar-mini',
            'metadata' => [
                'price' => 25000000,
                'image' => '/assets/img/product/product_1_1.png',
                'description' => 'Premier Video Bar for Small Rooms',
                'accessories' => [
                    ['id' => 'tv-mount-video-bar', 'name' => 'TV Mount for Video Bars', 'price' => 800000, 'recommended' => true, 'product_id' => $prodTVMount->id],
                    ['id' => 'wall-mount-video-bar', 'name' => 'Wall Mount for Video Bars', 'price' => 600000, 'product_id' => $prodWallMount->id]
                ]
            ],
            'conditions' => ['brand' => 'logitech', 'roomType' => 'huddle_room']
        ]);
        $prodRallyBarMini = \Modules\Core\Models\Product::updateOrCreate(
            ['slug' => 'rally-bar-mini-prod'],
            [
                'name' => 'Logitech Rally Bar Mini',
                'sku' => '960-001336',
                'solution_type' => 'Video Bar',
                'price' => 25000000,
                'description' => 'Simplicity meets versatility with Rally Bar Mini, Logitech’s most advanced all-in-one video bar for small rooms and huddle spaces. With studio-quality audio and video, AI-driven performance, and flexible deployment options, Rally Bar Mini sets a new standard for small room collaboration.',
                'service_id' => $svcVC->id,
                'brand_id' => $brandLogi->id,
                'tags' => ['Small Room', '4K', 'AI-Powered', 'All-in-One'],
                'datasheet_url' => 'https://www.logitech.com/assets/66044/vc-product-portfolio-brochure.pdf',
                'specs' => [
                    ['name' => 'Camera', 'value' => '4K, 120° FOV, 4x Digital Zoom'],
                    ['name' => 'Audio', 'value' => 'Ultra-low distortion speakers, 6 Beamforming Mics'],
                    ['name' => 'Connectivity', 'value' => 'USB-A, USB-C, HDMI Out (2), HDMI In (1)'],
                    ['name' => 'Dimensions', 'value' => '91.4 mm x 719 mm x 101 mm'],
                    ['name' => 'Warranty', 'value' => '2-Year Limited Hardware Warranty'],
                ],
                'features' => [
                    [
                        'title' => 'Crystal Clear Video',
                        'description' => 'Sharp video quality with 4K resolution and advanced optics.',
                        'image' => '/assets/img/icon/feature_1_1.svg'
                    ],
                    [
                        'title' => 'Studio-Quality Audio',
                        'description' => 'Ultra-low distortion speakers and adaptive beamforming mics.',
                        'image' => '/assets/img/icon/feature_1_2.svg'
                    ],
                    [
                        'title' => 'AI Viewfinder',
                        'description' => 'Persistent awareness of room occupancy and framing.',
                        'image' => '/assets/img/icon/feature_1_3.svg'
                    ]
                ]
            ]
        );
        $optRallyBarMini->products()->attach($prodRallyBarMini->id);

        $optBCC950 = $qDevice->options()->create([
            'label' => 'Logitech BCC950',
            'value' => 'bcc950',
            'metadata' => [
                'price' => 3500000,
                'image' => '/assets/img/product/product_1_4.png',
                'description' => 'Desktop Video Conferencing',
                'accessories' => []
            ],
            'conditions' => ['brand' => 'logitech', 'roomType' => 'huddle_room']
        ]);
        $prodBCC950 = \Modules\Core\Models\Product::updateOrCreate(
            ['slug' => 'bcc950-prod'],
            [
                'name' => 'Logitech BCC950',
                'sku' => '960-000939',
                'solution_type' => 'Desktop VC',
                'price' => 3500000,
                'description' => 'Small groups can run their own video conferences anytime, anywhere. BCC950 video conferencing system, with its high-definition, plug-and-play webcam and speakerphone, is ideal for small rooms or teams of 1-4 people. Set it up quickly and easily at an office desk or on a conference table.',
                'service_id' => $svcVC->id,
                'brand_id' => $brandLogi->id,
                'tags' => ['Desktop', '1080p', 'Portable'],
                'datasheet_url' => 'https://www.logitech.com/assets/66044/vc-product-portfolio-brochure.pdf',
                'specs' => [
                    ['name' => 'Camera', 'value' => '1080p, 78° FOV, 1.2x HD Zoom'],
                    ['name' => 'Audio', 'value' => 'Omnidirectional mic, 8ft pickup range'],
                    ['name' => 'Connectivity', 'value' => 'USB Plug-and-Play'],
                    ['name' => 'Warranty', 'value' => '2-Year Limited Hardware Warranty'],
                ],
                'features' => [
                     [
                        'title' => 'All-in-One Design',
                        'description' => 'Combines HD video and high-quality audio in a simple device.',
                        'image' => '/assets/img/icon/feature_1_1.svg'
                    ],
                     [
                        'title' => 'Perfect for Small Groups',
                        'description' => 'Ideal for teams of 1-4 people.',
                        'image' => '/assets/img/icon/feature_1_3.svg'
                    ]
                ]
            ]
        );
        $optBCC950->products()->attach($prodBCC950->id);

         $optConnect = $qDevice->options()->create([
            'label' => 'Logitech Connect',
            'value' => 'connect',
            'metadata' => [
                'price' => 7500000,
                'image' => '/assets/img/product/product_1_5.png',
                'description' => 'Portable ConferenceCam',
                'accessories' => []
            ],
            'conditions' => ['brand' => 'logitech', 'roomType' => 'huddle_room']
        ]);
        $prodConnect = \Modules\Core\Models\Product::updateOrCreate(
            ['slug' => 'connect-prod'],
            [
                'name' => 'Logitech Connect',
                'sku' => '960-001035',
                'solution_type' => 'Portable VC',
                'price' => 7500000,
                'description' => 'Simplify video conferencing so anyone can set it up and run a meeting anywhere. Affordable so you can outfit every conference room with video. Designed for huddle rooms and home offices, Logitech Connect is also compact and mobile so you can take it throughout the workplace and around the world.',
                'service_id' => $svcVC->id,
                'brand_id' => $brandLogi->id,
                'tags' => ['Portable', '1080p', 'Battery Powered'],
                'datasheet_url' => 'https://www.logitech.com/assets/66044/vc-product-portfolio-brochure.pdf',
                'specs' => [
                    ['name' => 'Camera', 'value' => '1080p, 90° FOV, 4x Digital Zoom'],
                    ['name' => 'Audio', 'value' => '360° Sound, 12ft Mic Pickup'],
                    ['name' => 'Battery', 'value' => 'Up to 3 hours video, 15 hours audio'],
                    ['name' => 'Warranty', 'value' => '2-Year Limited Hardware Warranty'],
                ],
                'features' => [
                     [
                        'title' => 'Portable Design',
                        'description' => 'Rechargeable battery and compact footprint.',
                        'image' => '/assets/img/icon/feature_1_1.svg'
                    ],
                     [
                        'title' => 'Premium Lens',
                        'description' => 'Logitech glass lens provides bright, sharp video.',
                        'image' => '/assets/img/icon/feature_1_2.svg'
                    ]
                ]
            ]
        );
        $optConnect->products()->attach($prodConnect->id);


        // -- Logitech Small Room Options --
        $optRallyBar = $qDevice->options()->create([
            'label' => 'Logitech Rally Bar',
            'value' => 'rally-bar',
            'metadata' => [
                'price' => 45000000,
                'image' => '/assets/img/product/product_1_2.png',
                'description' => 'All-in-One Video Bar',
                'accessories' => [
                   ['id' => 'tv-mount-video-bar', 'name' => 'TV Mount', 'price' => 800000, 'recommended' => true, 'product_id' => $prodTVMount->id],
                ]
            ],
            'conditions' => ['brand' => 'logitech', 'roomType' => 'small_room']
        ]);
        $prodRallyBar = \Modules\Core\Models\Product::updateOrCreate(
            ['slug' => 'rally-bar-prod'],
            [
                'name' => 'Logitech Rally Bar',
                'sku' => '960-001308',
                'solution_type' => 'Video Bar',
                'price' => 45000000,
                'description' => 'Rally Bar sets a new standard for video conferencing in mid-sized rooms, with expansion options for larger groups. It’s remarkably simple to use, manage, and deploy at scale, delivering cinema-quality video and audio in a sleek, all-in-one form factor. Plug and play Rally Bar with any PC or Mac, or leverage the built-in appliance mode.',
                'service_id' => $svcVC->id,
                'brand_id' => $brandLogi->id,
                'tags' => ['Medium Room', '4K', 'Optical Zoom', 'AI-Powered'],
                'datasheet_url' => 'https://www.logitech.com/assets/66044/vc-product-portfolio-brochure.pdf',
                'specs' => [
                    ['name' => 'Camera', 'value' => '4K, 15x HD Zoom (5x Optical), 90° FOV'],
                    ['name' => 'Audio', 'value' => 'Large ultra-low distortion speakers, 6 Beamforming Mics'],
                    ['name' => 'Connectivity', 'value' => 'USB-A, USB-C, HDMI Out (2), HDMI In (1)'],
                    ['name' => 'Dimensions', 'value' => '164 mm x 910 mm x 130.5 mm'],
                    ['name' => 'Warranty', 'value' => '2-Year Limited Hardware Warranty'],
                ],
                'features' => [
                    [
                        'title' => 'Cinema-Quality Video',
                        'description' => 'Motorized PTZ lens with 15x HD zoom.',
                        'image' => '/assets/img/icon/feature_1_1.svg'
                    ],
                    [
                        'title' => 'Room-Filling Sound',
                        'description' => 'Large ultra-low distortion speakers for rich audio.',
                        'image' => '/assets/img/icon/feature_1_2.svg'
                    ],
                    [
                        'title' => 'AI-Driven Performance',
                        'description' => 'RightSense technologies automate video and audio control.',
                        'image' => '/assets/img/icon/feature_1_3.svg'
                    ]
                ]
            ]
        );
        $optRallyBar->products()->attach($prodRallyBar->id);

        // Re-add Connect & Group for Small Room as per dummy data
        $optConnectSmall = $qDevice->options()->create([
            'label' => 'Logitech Connect',
            'value' => 'connect-small', // Unique value
            'metadata' => [
                'price' => 7500000,
                'image' => '/assets/img/product/product_1_5.png',
                'description' => 'Portable ConferenceCam',
                'accessories' => []
            ],
            'conditions' => ['brand' => 'logitech', 'roomType' => 'small_room']
        ]);
        $prodConnect = \Modules\Core\Models\Product::updateOrCreate(
            ['slug' => 'connect-prod'],
            ['name' => 'Logitech Connect', 'price' => 7500000, 'description' => 'Portable ConferenceCam']
        );
        $optConnectSmall->products()->attach($prodConnect->id);

        $optGroup = $qDevice->options()->create([
            'label' => 'Logitech Group',
            'value' => 'group',
            'metadata' => [
                'price' => 15000000,
                'image' => '/assets/img/product/product_1_7.png',
                'description' => 'Affordable Video Conferencing',
                'accessories' => [
                     ['id' => 'group-mic', 'name' => 'Expansion Mics', 'price' => 4000000, 'product_id' => \Modules\Core\Models\Product::updateOrCreate(['slug' => 'logitech-group-mic-prod'], ['name' => 'Expansion Mics', 'price' => 4000000, 'description' => 'Expand the conversation area for Logitech Group', 'service_id' => $svcVC->id, 'brand_id' => $brandLogi->id])->id]
                ]
            ],
            'conditions' => ['brand' => 'logitech', 'roomType' => 'small_room']
        ]);
        $prodGroup = \Modules\Core\Models\Product::updateOrCreate(
            ['slug' => 'group-prod'],
            ['name' => 'Logitech Group', 'price' => 15000000, 'description' => 'Affordable Video Conferencing', 'service_id' => $svcVC->id, 'brand_id' => $brandLogi->id]
        );
        $optGroup->products()->attach($prodGroup->id);

        // -- Logitech Medium Room Options --
        $optRallyPlus = $qDevice->options()->create([
            'label' => 'Logitech Rally Plus',
            'value' => 'rally-plus',
            'metadata' => [
                'price' => 65000000,
                'image' => '/assets/img/product/product_1_2.png',
                'description' => 'Modular Video Conferencing System',
                'accessories' => [
                     ['id' => 'rally-mic-pod-hub', 'name' => 'Mic Pod Hub', 'price' => 2500000, 'product_id' => \Modules\Core\Models\Product::updateOrCreate(['slug' => 'rally-mic-hub-prod'], ['name' => 'Rally Mic Pod Hub', 'price' => 2500000, 'description' => 'Flexible microphone placement', 'service_id' => $svcVC->id, 'brand_id' => $brandLogi->id])->id],
                     ['id' => 'rally-speakermount', 'name' => 'Speaker Mount', 'price' => 500000, 'recommended' => true, 'product_id' => \Modules\Core\Models\Product::updateOrCreate(['slug' => 'rally-speaker-mount-prod'], ['name' => 'Rally Speaker Mount', 'price' => 500000, 'description' => 'Secure mounting for Rally speakers', 'service_id' => $svcVC->id, 'brand_id' => $brandLogi->id])->id]
                ]
            ],
            'conditions' => ['brand' => 'logitech', 'roomType' => 'medium_room']
        ]);
        $prodRallyPlus = \Modules\Core\Models\Product::updateOrCreate(
            ['slug' => 'rally-plus-prod'],
            ['name' => 'Logitech Rally Plus', 'price' => 65000000, 'description' => 'Modular Video Conferencing System', 'service_id' => $svcVC->id, 'brand_id' => $brandLogi->id]
        );
        $optRallyPlus->products()->attach($prodRallyPlus->id);

        $optGroupMedium = $qDevice->options()->create([
            'label' => 'Logitech Group',
            'value' => 'group-medium',
            'metadata' => [
                'price' => 15000000,
                'image' => '/assets/img/product/product_1_7.png',
                'description' => 'Affordable Video Conferencing',
                'accessories' => [
                     ['id' => 'group-mic', 'name' => 'Expansion Mics', 'price' => 4000000, 'product_id' => \Modules\Core\Models\Product::where('slug', 'logitech-group-mic-prod')->first()->id]
                ]
            ],
            'conditions' => ['brand' => 'logitech', 'roomType' => 'medium_room']
        ]);
        // Reuse prodGroup
        $optGroupMedium->products()->attach($prodGroup->id);

        $optRallyCamera = $qDevice->options()->create([
            'label' => 'Logitech Rally Camera',
            'value' => 'rally-camera',
             'metadata' => [
                'price' => 22000000,
                'image' => '/assets/img/product/product_1_6.png',
                'description' => 'Premium PTZ Camera',
                 'accessories' => [
                     ['id' => 'rally-mount', 'name' => 'Camera Mount', 'price' => 500000, 'product_id' => \Modules\Core\Models\Product::updateOrCreate(['slug' => 'rally-camera-mount-prod'], ['name' => 'Rally Camera Mount', 'price' => 500000, 'description' => 'Secure mounting for Rally Camera', 'service_id' => $svcVC->id, 'brand_id' => $brandLogi->id])->id]
                ]
            ],
            'conditions' => ['brand' => 'logitech', 'roomType' => 'medium_room']
        ]);
        $prodRallyCamera = \Modules\Core\Models\Product::updateOrCreate(
            ['slug' => 'rally-camera-prod'],
            ['name' => 'Logitech Rally Camera', 'price' => 22000000, 'description' => 'Premium PTZ Camera', 'service_id' => $svcVC->id, 'brand_id' => $brandLogi->id]
        );
        $optRallyCamera->products()->attach($prodRallyCamera->id);

        // -- Yealink and Jabra --
        // Yealink Medium
        $optMVC840 = $qDevice->options()->create([
            'label' => 'Yealink MVC840',
            'value' => 'mvc840',
            'metadata' => ['price' => 45000000, 'image' => '/assets/img/product/product_1_6.png', 'description' => 'Microsoft Teams Room System'],
             'conditions' => ['brand' => 'yealink', 'roomType' => 'medium_room']
        ]);
        $prodMVC840 = \Modules\Core\Models\Product::updateOrCreate(
            ['slug' => 'mvc840-prod'],
            ['name' => 'Yealink MVC840', 'price' => 45000000, 'description' => 'Microsoft Teams Room System', 'service_id' => $svcVC->id, 'brand_id' => $brandYealink->id]
        );
        $optMVC840->products()->attach($prodMVC840->id);
        // Jabra Medium
        $optPanaCast50 = $qDevice->options()->create([
             'label' => 'Jabra PanaCast 50',
            'value' => 'panacast-50',
            'metadata' => ['price' => 42000000, 'image' => '/assets/img/product/product_1_5.png', 'description' => 'Intelligent Video Bar System'],
             'conditions' => ['brand' => 'jabra', 'roomType' => 'medium_room']
        ]);

        // Create Dummy Product & Link (Jabra)
        $prodPanaCast = \Modules\Core\Models\Product::where('slug', 'panacast-50-prod')->first();
        if ($prodPanaCast) {
            $optPanaCast50->products()->attach($prodPanaCast->id);
        }

        // --- Question 4.2: Computing Device (If PC-Based) ---
        $qPC = $step4->questions()->create([
            'label' => 'Select Computing Device',
            'variable_name' => 'pc',
            'type' => 'card_selection',
            'is_mandatory' => true,
            'sort_order' => 2,
            'conditions' => ['deployment' => 'pc-based']
        ]);

        $optASUS = $qPC->options()->create([
            'label' => 'ASUS Mini PC',
            'value' => 'asus_mini_pc',
            'metadata' => [
                'price' => 12000000,
                'image' => '/assets/img/product/product_1_8.png',
                'description' => 'Compact & Powerful (Intel Core i7, 16GB RAM)'
            ]
        ]);
        $prodASUS = \Modules\Core\Models\Product::updateOrCreate(
            ['slug' => 'asus-mini-pc-prod'],
            ['name' => 'ASUS Mini PC', 'price' => 12000000, 'description' => 'Compact & Powerful (Intel Core i7, 16GB RAM)', 'service_id' => $svcVC->id, 'brand_id' => $brandLogi->id]
        );
        $optASUS->products()->attach($prodASUS->id);

        $optLenovo = $qPC->options()->create([
            'label' => 'Lenovo ThinkCentre',
            'value' => 'lenovo_mini_pc',
            'metadata' => [
                'price' => 13500000,
                'image' => '/assets/img/product/product_1_8.png',
                'description' => 'Enterprise Grade (Intel Core i5 vPro, 16GB RAM)'
            ]
        ]);
        $prodLenovo = \Modules\Core\Models\Product::updateOrCreate(
            ['slug' => 'lenovo-thinkcentre-prod'],
            ['name' => 'Lenovo ThinkCentre', 'price' => 13500000, 'description' => 'Enterprise Grade (Intel Core i5 vPro, 16GB RAM)', 'service_id' => $svcVC->id, 'brand_id' => $brandLogi->id]
        );
        $optLenovo->products()->attach($prodLenovo->id);


        // --- Step 5: Audio --- (Matching metaData.audioProducts)
        $step5 = $conf->steps()->create([
            'name' => 'Audio',
            'title' => 'Expansion Audio',
            'description' => 'Add extra audio devices for better coverage.', // Added desc
            'sort_order' => 5,
        ]);

        $qAudio = $step5->questions()->create([
            'label' => 'Audio Add-ons',
            'variable_name' => 'audio',
            'type' => 'card_multi_selection', // Allow multiple authio
            'is_mandatory' => false,
        ]);

        $optMicPod = $qAudio->options()->create([
            'label' => 'Expansion Mic Pod',
            'value' => 'mic-pod',
            'metadata' => ['price' => 3500000, 'image' => '/assets/img/icon/feature_1_1.svg', 'description' => 'Extend audio coverage'],
             'conditions' => ['brand' => 'logitech']
        ]);
        $prodMicPod = \Modules\Core\Models\Product::updateOrCreate(['slug' => 'mic-pod-prod'], ['name' => 'Expansion Mic Pod', 'price' => 3500000, 'description' => 'Extend audio coverage', 'service_id' => $svcVC->id, 'brand_id' => $brandLogi->id]);
        $optMicPod->products()->attach($prodMicPod->id);

        $optMicHub = $qAudio->options()->create([
            'label' => 'Mic Pod Hub',
            'value' => 'mic-pod-hub',
            'metadata' => ['price' => 2500000, 'image' => '/assets/img/icon/feature_1_1.svg', 'description' => 'Clean cable management'],
             'conditions' => ['brand' => 'logitech']
        ]);
        $prodMicHub = \Modules\Core\Models\Product::updateOrCreate(['slug' => 'mic-pod-hub-prod'], ['name' => 'Mic Pod Hub', 'price' => 2500000, 'description' => 'Clean cable management', 'service_id' => $svcVC->id, 'brand_id' => $brandLogi->id]);
        $optMicHub->products()->attach($prodMicHub->id);

        $optSpkBar = $qAudio->options()->create([
            'label' => 'External Speaker Bar',
            'value' => 'spk-bar',
            'metadata' => ['price' => 5000000, 'image' => '/assets/img/icon/feature_1_1.svg', 'description' => 'Enhanced room audio'],
             'conditions' => ['brand' => 'logitech']
        ]);
        $prodSpkBar = \Modules\Core\Models\Product::updateOrCreate(['slug' => 'spk-bar-prod'], ['name' => 'External Speaker Bar', 'price' => 5000000, 'description' => 'Enhanced room audio', 'service_id' => $svcVC->id, 'brand_id' => $brandLogi->id]);
        $optSpkBar->products()->attach($prodSpkBar->id);


        // --- Step 6: Controller --- (Matching metaData.controllers + controllerAccessories)
        $step6 = $conf->steps()->create([
            'name' => 'Controller',
            'title' => 'Choose your meeting controller',
            'description' => 'Control meetings with a dedicated touch panel.',
            'sort_order' => 6,
            'conditions' => ['platform' => ['operator' => '!=', 'value' => 'byod']], // Skip if BYOD
        ]);

        $qCtrl = $step6->questions()->create([
            'label' => 'Controller',
            'variable_name' => 'controller',
            'type' => 'card_selection', // Single select for main controller
            'is_mandatory' => true,
        ]);

        // Options with Nested Accessories
        $optTap = $qCtrl->options()->create([
            'label' => 'Logitech Tap',
            'value' => 'tap-usb',
            'metadata' => [
                'price' => 10000000,
                'image' => '/assets/img/icon/feature_1_1.svg',
                'description' => 'USB-connected room controller with Cat5e kit'
            ]
        ]);
        $prodTap = \Modules\Core\Models\Product::firstOrCreate(
            ['slug' => 'tap-usb-prod'],
            ['name' => 'Logitech Tap', 'price' => 10000000, 'description' => 'USB-connected room controller', 'service_id' => $svcVC->id, 'brand_id' => $brandLogi->id]
        );
        $optTap->products()->attach($prodTap->id);

        // Create Tap Mount Products
        $prodTapTableMount = \Modules\Core\Models\Product::firstOrCreate(
            ['slug' => 'tap-table-mount-prod'],
            ['name' => 'Tap Table Mount', 'price' => 500000, 'description' => 'Secure Tap controllers to the table', 'service_id' => $svcVC->id, 'brand_id' => $brandLogi->id]
        );
        $prodTapRiserMount = \Modules\Core\Models\Product::firstOrCreate(
            ['slug' => 'tap-riser-mount-prod'],
            ['name' => 'Tap Riser Mount', 'price' => 600000, 'description' => 'Elevated mounting for Tap', 'service_id' => $svcVC->id, 'brand_id' => $brandLogi->id]
        );
        $prodTapWallMount = \Modules\Core\Models\Product::firstOrCreate(
            ['slug' => 'tap-wall-mount-prod'],
            ['name' => 'Tap Wall Mount', 'price' => 500000, 'description' => 'Wall mounting for Tap', 'service_id' => $svcVC->id, 'brand_id' => $brandLogi->id]
        );

        $optTapIP = $qCtrl->options()->create([
            'label' => 'Logitech Tap IP',
            'value' => 'tap-ip',
            'metadata' => [
                'price' => 12000000,
                'image' => '/assets/img/icon/feature_1_1.svg',
                'description' => 'Network-connected room controller',
                'accessories' => [
                    ['id' => 'tap-table-mount', 'name' => 'Tap Table Mount', 'description' => 'Secure Tap controllers to the table', 'price' => 500000, 'product_id' => $prodTapTableMount->id],
                    ['id' => 'tap-riser-mount', 'name' => 'Tap Riser Mount', 'description' => 'Elevated mounting for Tap', 'price' => 600000, 'product_id' => $prodTapRiserMount->id],
                    ['id' => 'tap-wall-mount', 'name' => 'Tap Wall Mount', 'description' => 'Wall mounting for Tap', 'price' => 500000, 'product_id' => $prodTapWallMount->id],
                ]
            ]
        ]);
        $prodTapIP = \Modules\Core\Models\Product::firstOrCreate(
            ['slug' => 'tap-ip-prod'],
            ['name' => 'Logitech Tap IP', 'price' => 12000000, 'description' => 'Network-connected room controller', 'service_id' => $svcVC->id, 'brand_id' => $brandLogi->id]
        );
        $optTapIP->products()->attach($prodTapIP->id);

        // Controller Accessories (New Question)
        $qCtrlAcc = $step6->questions()->create([
            'label' => 'Controller Accessories',
            'variable_name' => 'controller_accessories',
            'type' => 'card_multi_selection',
            'is_mandatory' => false,
            'sort_order' => 2
        ]);

        // Create Standalone Options for Controller Accessories
        $optAccTable = $qCtrlAcc->options()->create(['label' => 'Tap Table Mount', 'value' => 'tap-table-mount', 'metadata' => ['price' => 500000, 'image' => '/assets/img/icon/feature_1_1.svg', 'description' => 'Secure Tap controllers to the table']]);
        $optAccTable->products()->attach($prodTapTableMount->id);

        $optAccRiser = $qCtrlAcc->options()->create(['label' => 'Tap Riser Mount', 'value' => 'tap-riser-mount', 'metadata' => ['price' => 600000, 'image' => '/assets/img/icon/feature_1_1.svg', 'description' => 'Elevated mounting for Tap']]);
        $optAccRiser->products()->attach($prodTapRiserMount->id);

        $optAccWall = $qCtrlAcc->options()->create(['label' => 'Tap Wall Mount', 'value' => 'tap-wall-mount', 'metadata' => ['price' => 500000, 'image' => '/assets/img/icon/feature_1_1.svg', 'description' => 'Wall mounting for Tap']]);
        $optAccWall->products()->attach($prodTapWallMount->id);


        // --- Step 7: Add-Ons --- (Matching metaData.accessories)
        $step7 = $conf->steps()->create([
            'name' => 'Add-Ons',
            'title' => 'Choose your add-ons',
            'description' => 'Enhance the meeting experience.',
            'sort_order' => 7,
        ]);

        $qAddons = $step7->questions()->create([
            'label' => 'Add-Ons',
            'variable_name' => 'accessories', // Array
            'type' => 'card_multi_selection',
            'is_mandatory' => false,
        ]);

        $qAddons->options()->createMany([
            ['label' => 'Tap Scheduler', 'value' => 'tap-scheduler', 'metadata' => ['price' => 8000000, 'image' => '/assets/img/icon/feature_1_1.svg', 'description' => 'Room scheduling panel']],
            ['label' => 'Logitech Scribe', 'value' => 'scribe', 'metadata' => ['price' => 15000000, 'image' => '/assets/img/icon/feature_1_1.svg', 'description' => 'AI-powered whiteboard camera']],
            ['label' => 'Logitech Swytch', 'value' => 'swytch', 'metadata' => ['price' => 6000000, 'image' => '/assets/img/icon/feature_1_1.svg', 'description' => 'Bring-your-own-laptop connection', 'recommended' => true]],
            ['label' => 'TV Mount', 'value' => 'tv-mount', 'metadata' => ['price' => 1000000, 'image' => '/assets/img/icon/feature_1_1.svg', 'description' => 'Secure mounting above/below TV']],
            ['label' => 'Wall Mount', 'value' => 'wall-mount', 'metadata' => ['price' => 800000, 'image' => '/assets/img/icon/feature_1_1.svg', 'description' => 'Space saving wall installation']],
        ]);


        // --- Step 8: Services --- (Matching metaData.serviceQuestions completely)
        $step8 = $conf->steps()->create([
            'name' => 'Services',
            'title' => 'Finish by adding services',
            'description' => 'Ensure the deployment is always up and running.',
            'sort_order' => 8,
        ]);

        // Q1: Expertise
        $qS1 = $step8->questions()->create([
            'label' => 'What’s your support team’s level of AV/IT expertise?',
            'variable_name' => 'service_expertise',
            'type' => 'service_checklist',
            'is_mandatory' => true,
            'sort_order' => 1,
        ]);
        $qS1->options()->createMany([
            ['label' => 'No expertise on staff', 'value' => 'none'],
            ['label' => 'Qualified internal or partner team', 'value' => 'qualified'],
        ]);

        // Q2: Resources
        $qS2 = $step8->questions()->create([
            'label' => 'What additional resources are needed for ongoing operations?',
            'variable_name' => 'service_resources',
            'type' => 'service_checklist',
            'is_mandatory' => true,
            'sort_order' => 2,
        ]);
        $qS2->options()->createMany([
            ['label' => 'Ongoing service management', 'value' => 'management'],
            ['label' => 'Advanced, proactive software tools', 'value' => 'tools'],
            ['label' => 'Tech support when I need it', 'value' => 'support'],
        ]);

         // Q3: Troubleshooting
        $qS3 = $step8->questions()->create([
            'label' => 'How do you manage troubleshooting?',
            'variable_name' => 'service_troubleshooting',
            'type' => 'service_checklist',
            'is_mandatory' => true,
            'sort_order' => 3,
        ]);
        $qS3->options()->createMany([
            ['label' => 'We handle it internally', 'value' => 'internal'],
            ['label' => 'We rely on external support', 'value' => 'external'],
            ['label' => 'Ad-hoc basis', 'value' => 'adhoc'],
        ]);

         // Q4: Hours
        $qS4 = $step8->questions()->create([
            'label' => 'What are the hours of support you’re expected to deliver?',
            'variable_name' => 'service_hours',
            'type' => 'service_checklist',
            'is_mandatory' => true,
            'sort_order' => 4,
        ]);
        $qS4->options()->createMany([
            ['label' => '8/5 Standard Business Hours', 'value' => '8x5'],
            ['label' => '24/7 Critical Support', 'value' => '24x7'],
        ]);

         // Q5: Replacement
        $qS5 = $step8->questions()->create([
            'label' => 'When you need to replace faulty products, how quickly does it need to arrive?',
            'variable_name' => 'service_replacement',
            'type' => 'service_checklist',
            'is_mandatory' => true,
            'sort_order' => 5,
        ]);
        $qS5->options()->createMany([
            ['label' => 'Next Business Day', 'value' => 'nbd'],
            ['label' => 'Standard Shipping (3-5 Days)', 'value' => 'standard'],
        ]);
    }
}
