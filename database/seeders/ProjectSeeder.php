<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $titles = [
            'Cloud Infrastructure Migration',
            'Enterprise ERP Implementation',
            'Cybersecurity Audit & Hardening',
            'Mobile App Development for Retail',
            'AI-Powered Analytics Dashboard',
            'IoT Smart Building Solution',
            'Blockchain Supply Chain Platform',
            'DevOps Pipeline Automation',
            'Data Center Virtualization'
        ];

        foreach ($titles as $index => $title) {
            Project::create([
                'title' => $title,
                'slug' => Str::slug($title),
                'excerpt' => 'Implementing a robust and scalable solution to meet the complex digital demands of modern enterprise.',
                'content' => '<p>The client faced significant challenges with legacy systems that were hindering scalability and performance. Key issues included data silos, slow processing times, and security vulnerabilities that needed immediate attention to prevent potential data breaches and operational downtime.</p><p>Our solution involved a complete overhaul of the infrastructure, leveraging cloud-native technologies and microservices architecture.</p>',
                'thumbnail' => '/assets/img/project/project-inner' . ($index % 8 + 1) . '.jpg',
                'status' => 'published',
                'published_at' => now()->subDays($index * 5),
            ]);
        }
    }
}
