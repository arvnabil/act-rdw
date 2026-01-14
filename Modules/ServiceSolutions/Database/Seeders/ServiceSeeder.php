<?php

namespace Modules\ServiceSolutions\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\ServiceSolutions\Models\Service;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks to avoid errors during truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Service::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Service::create([
            'name' => 'Video Conference',
            'slug' => 'video-conference',
            'description' => 'High-quality AV solutions for seamless collaboration in meetings.',
            'thumbnail' => '/assets/img/service/service_6_1.jpg', // Using 'thumbnail' as mapped in Model
            'icon' => '/assets/img/icon/sv-6-1.svg',
            'sort_order' => 1,
        ]);

        Service::create([
            'name' => 'Server & Storage',
            'slug' => 'server-storage',
            'description' => 'Robust and secure infrastructure for high-performance data management.',
            'thumbnail' => '/assets/img/service/service_2_2.jpg',
            'icon' => '/assets/img/icon/sv-6-2.svg',
            'sort_order' => 2,
        ]);

        Service::create([
            'name' => 'Smart Surveillance',
            'slug' => 'smart-surveillance',
            'description' => 'Advanced 24/7 security monitoring and CCTV systems.',
            'thumbnail' => '/assets/img/service/service_2_3.jpg',
            'icon' => '/assets/img/icon/sv-6-3.svg',
            'sort_order' => 3,
        ]);
    }
}
