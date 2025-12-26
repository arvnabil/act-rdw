<?php

namespace Modules\Events\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Events\Models\Event;
use Modules\Events\Models\EventCategory;
use Illuminate\Support\Str;

class EventSeeder extends Seeder
{
    public function run()
    {
        // 1. Create Requested Categories
        $categories = ['Talk Show', 'Seminar', 'Workshop', 'Business', 'Technology'];
        $categoryModels = [];

        foreach ($categories as $cat) {
            $categoryModels[$cat] = EventCategory::firstOrCreate(
                ['slug' => Str::slug($cat)],
                ['name' => $cat, 'is_active' => true]
            );
        }

        // 2. Create Requested Organizers
        $organizersData = [
            [
                'name' => 'ACTIV',
                'phone' => '08782323123',
                'website' => 'https://activ.co.id',
                'email' => 'contact@activ.co.id', // inferred
                'is_active' => true,
            ],
            [
                'name' => 'Accocommerce',
                'phone' => '087823232',
                'website' => 'https://accommerce.id',
                'email' => 'contact@accommerce.id', // inferred
                'is_active' => true,
            ]
        ];

        $organizerModels = [];
        foreach ($organizersData as $orgData) {
            $organizerModels[$orgData['name']] = \Modules\Events\Models\Organizer::firstOrCreate(
                ['slug' => Str::slug($orgData['name'])],
                $orgData
            );
        }

        // 3. Create Events with Valid Relations
        $eventsData = [
            [
                'title' => 'Global AI Summit 2025',
                'category' => 'Technology', // Will map to ID
                'organizer_name' => 'ACTIV', // Will map to ID
                'description' => 'Join industry leaders and tech enthusiasts for an immersive experience exploring the latest trends and innovations in the technology sector.',
                'start_date' => '2025-05-15 09:00:00',
                'end_date' => '2025-05-15 17:00:00',
                'location' => 'Grand Hall, Jakarta',
                'price' => 1500000,
                'quota' => 200,
                'speakers' => [
                    ['name' => 'Sarah Johnson', 'role' => 'AI Researcher', 'image' => null],
                    ['name' => 'David Lee', 'role' => 'Tech Lead', 'image' => null]
                ]
            ],
            [
                'title' => 'Digital Marketing Workshop',
                'category' => 'Workshop',
                'organizer_name' => 'Accocommerce',
                'description' => 'Hands-on workshop to master digital marketing channels.',
                'start_date' => '2025-06-22 09:00:00',
                'end_date' => '2025-06-22 16:00:00',
                'location' => 'Online',
                'price' => 500000,
                'quota' => 50,
                'speakers' => []
            ],
            [
                'title' => 'Future of E-Commerce Talk Show',
                'category' => 'Talk Show',
                'organizer_name' => 'ACTIV',
                'description' => 'Insightful talk show about the future of online retail.',
                'start_date' => '2025-07-05 10:00:00',
                'end_date' => '2025-07-05 12:00:00',
                'location' => 'ACTIV HQ, Bandung',
                'price' => 0, // Free
                'quota' => 100,
                'speakers' => []
            ]
        ];

        foreach ($eventsData as $data) {
            $catName = $data['category'];
            $orgName = $data['organizer_name'];

            unset($data['category']);
            unset($data['organizer_name']);

            $data['slug'] = Str::slug($data['title']);
            $data['is_active'] = true;
            $data['event_category_id'] = $categoryModels[$catName]->id ?? $categoryModels['Technology']->id;
            $data['organizer_id'] = $organizerModels[$orgName]->id ?? $organizerModels['ACTIV']->id;
            // Default images/other fields if needed can be handled here

            Event::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }
    }
}
