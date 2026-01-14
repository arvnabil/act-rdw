<?php

namespace Modules\ServiceSolutions\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\ServiceSolutions\Models\Service;
use Modules\ServiceSolutions\Models\ServiceSolution;
use Modules\ServiceSolutions\Models\ServiceCategory;
use Illuminate\Support\Facades\DB;

class ServiceSolutionSeeder extends Seeder
{
    public function run()
    {
        // cleanup
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        ServiceSolution::truncate();
        ServiceCategory::truncate();
        DB::table('service_solution_category')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $svcVC = Service::where('slug', 'video-conference')->first();

        if (!$svcVC) {
            $this->command->warn('Service Video Conference not found. Run ServiceSeeder first.');
            return;
        }

        // 1. Create Categories (Service Items)
        $catWorkplace = ServiceCategory::create(['service_id' => $svcVC->id, 'label' => 'Modern Workplace', 'value' => 'workplace']);
        $catEdu = ServiceCategory::create(['service_id' => $svcVC->id, 'label' => 'Education', 'value' => 'education']);
        $catHealth = ServiceCategory::create(['service_id' => $svcVC->id, 'label' => 'Healthcare', 'value' => 'healthcare']);

        // 2. Create Solutions (Aligned with Configurator Options)
        // 2. Create Solutions (Aligned with Configurator Options)
        // 2. Create Solutions (Aligned with Configurator Options)
        $solHuddle = ServiceSolution::create([
            'service_id' => $svcVC->id,
            'title' => 'Huddle Room',
            'slug' => 'huddle-room-solution',
            'subtitle' => 'Perfect for 2-4 People',
            'description' => 'Get a first-class experience and create inclusive experiences for all in your huddle meeting rooms.',
            'thumbnail' => '/assets/img/rooms/huddle-room.jpg',
            'features' => [
                ['icon' => 'assets/img/icon/shield.svg', 'title' => 'People support', 'text' => '2-4 People capacity for huddle meeting rooms.'],
                ['icon' => 'assets/img/icon/shield.svg', 'title' => 'Size', 'text' => '8-12m² / 85-130ft²'],
            ],
            'configurator_slug' => 'video-conference', // Link to configurator
            'sort_order' => 1
        ]);
        $solHuddle->categories()->attach($catWorkplace->id);

        $solSmall = ServiceSolution::create([
            'service_id' => $svcVC->id,
            'title' => 'Small Room',
            'slug' => 'small-room-solution',
            'subtitle' => 'Smart, Simple, and Ready for Collaboration',
            'description' => 'Designed for 4–6 people, Small Room solutions deliver clear video, crisp audio, and an easy meeting experience in a compact space. With a streamlined setup of camera, mic, and speaker, your room becomes a modern collaboration hub that’s always ready to use.',
            'thumbnail' => '/assets/img/rooms/small-room.jpg',
            'features' => [
                ['icon' => 'assets/img/icon/shield.svg', 'title' => 'People support', 'text' => '4-6 People capacity for small meeting rooms.'],
                ['icon' => 'assets/img/icon/shield.svg', 'title' => 'Size', 'text' => '12-28m² / 130-300ft²'],
            ],
             'configurator_slug' => 'video-conference',
            'sort_order' => 2
        ]);
        $solSmall->categories()->attach($catWorkplace->id);

        $solMedium = ServiceSolution::create([
            'service_id' => $svcVC->id,
            'title' => 'Medium Room',
            'slug' => 'medium-room-solution',
            'subtitle' => 'Optimized for 6-10 People',
            'description' => 'Scalable audio and video solutions designed for mid-sized conference rooms, ensuring everyone is seen and heard clearly.',
            'thumbnail' => '/assets/img/rooms/medium-room.jpg',
            'features' => [
                ['icon' => 'assets/img/icon/shield.svg', 'title' => 'People support', 'text' => '6-10 People capacity for medium meeting rooms.'],
                ['icon' => 'assets/img/icon/shield.svg', 'title' => 'Size', 'text' => '14-36m² / 150-390ft²'],
            ],
             'configurator_slug' => 'video-conference',
            'sort_order' => 3
        ]);
        $solMedium->categories()->attach([$catWorkplace->id, $catEdu->id]);
    }
}
