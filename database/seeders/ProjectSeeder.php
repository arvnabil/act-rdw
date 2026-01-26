<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            [
                'title' => 'E-Commerce Platform',
                'category' => 'Web Development',
                'thumbnail' => '/assets/img/project/project_1_1.jpg',
                'description' => 'A scalable e-commerce solution designed for a leading retail brand, featuring advanced inventory management and AI-powered recommendations.',
            ],
            [
                'title' => 'Corporate Branding',
                'category' => 'Design',
                'thumbnail' => '/assets/img/project/project_2_1.jpg',
                'description' => 'A complete corporate identity overhaul including logo design, brand guidelines, and marketing collateral for a fintech startup.',
            ],
            [
                'title' => 'Mobile App Redesign',
                'category' => 'UI/UX',
                'thumbnail' => '/assets/img/project/project_3_1.jpg',
                'description' => 'User-centric redesign of a mobile banking app, focusing on accessibility, streamlined navigation, and modern aesthetic.',
            ],
            [
                'title' => 'Marketing Campaign',
                'category' => 'Digital Marketing',
                'thumbnail' => '/assets/img/project/project_4_1.jpg',
                'description' => 'A comprehensive multi-channel marketing campaign that increased user engagement by 40% through targeted social media and email strategies.',
            ],
            [
                'title' => 'Cloud Infrastructure',
                'category' => 'DevOps',
                'thumbnail' => '/assets/img/project/project_3_9.jpg',
                'description' => 'Migration of legacy systems to a cloud-native infrastructure, optimizing performance, scalability, and security for a global enterprise.',
            ],
        ];

        foreach ($projects as $project) {
            Project::create([
                'title' => $project['title'],
                'slug' => Str::slug($project['title']),
                'category' => $project['category'],
                'excerpt' => $project['description'],
                'content' => '<p>' . $project['description'] . '</p><p>This project involved extensive research and collaboration to deliver a high-quality solution that meets the client\'s specific needs. Our team utilized the latest technologies and best practices to ensure success.</p>',
                'thumbnail' => $project['thumbnail'],
                'status' => 'published',
                'published_at' => now(),
            ]);
        }
    }
}
