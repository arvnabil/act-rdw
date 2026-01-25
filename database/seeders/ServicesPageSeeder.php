<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Database\Seeder;

class ServicesPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create or Update Page
        $page = Page::updateOrCreate(
            ['slug' => 'services'],
            [
                'title' => 'Our Services',
                'type' => 'dynamic',
                'status' => 'published',
                'show_breadcrumb' => true,
            ]
        );

        // 2. Clear existing sections for this page
        PageSection::where('page_id', $page->id)->delete();

        // 3. Define Sections
        $sections = [
            // 1. Service List (Breadcrumb handled by Page model)
            [
                'section_key' => 'service_list',
                'config' => [
                    'title' => 'Dealing in all professional IT services',
                    'subtitle' => 'What We’re Offering',
                    'description' => "IT solutions refer to a broad range of services and technologies designed to address specific business needs, streamline operations, and drive growth.",
                    'limit' => 12, // Show more services by default
                    'order' => 'asc',
                ],
            ],
            // 3. Clients
            [
                'section_key' => 'service_clients',
                'config' => [
                    'title' => 'Our Clients',
                    'subtitle' => 'Clients',
                    'limit' => 20,
                ],
            ],
            // 4. Solution
            [
                'section_key' => 'service_solution',
                'config' => [
                    'title' => 'Our Solution',
                    'subtitle' => 'Projects', // "Projects" based on HTML
                    'description' => "At ACTiV, we don't just develop technology; we engineer powerful digital solutions that drive real business transformation. Explore our portfolio to see how we leverage cutting-edge ICT to help our clients achieve greater productivity, visibility, and growth.",
                    'btn_text' => 'Explore Case Studies',
                    'btn_url' => '/contact',
                    'images' => [
                        '/assets/img/choose/choose_4_1.jpg',
                        '/assets/img/choose/choose_4_2.jpg',
                        '/assets/img/choose/choose_4_3.jpg',
                    ],
                    'features' => [
                        ['text' => 'Room Solution'],
                        ['text' => 'Server IP PBX'],
                        ['text' => 'Storage & Data Solution'],
                        ['text' => 'Meeting cloud'],
                        ['text' => 'CCTV & Access Control'],
                    ],
                ],
            ],
            // 5. CTA
            [
                'section_key' => 'service_cta',
                'config' => [
                    'title' => 'Have any project to work with us',
                    'subtitle' => 'Grab up to 35% off',
                    'description' => "Limited time offer, don't miss the opportunity",
                    'btn_text' => 'Contact With Us',
                    'btn_url' => '/contact',
                    'bg_image' => '/assets/img/normal/cta-img-6.jpg',
                ],
            ],
        ];

        // 4. Insert Sections
        foreach ($sections as $index => $section) {
            PageSection::create([
                'page_id' => $page->id,
                'section_key' => $section['section_key'],
                'position' => $index + 1,
                'is_active' => true,
                'config' => $section['config'],
            ]);
        }

        $this->command->info('Services Page Seeded Successfully!');
    }
}
