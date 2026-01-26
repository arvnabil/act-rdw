<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\NewsCategory;
use App\Models\NewsTag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ActivNewsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure Categories exist
        $categories = [
            'Berita Terkini' => ['slug' => 'berita-terkini'],
            'Tips & Trik' => ['slug' => 'tips-trik'],
            'Maxhub' => ['slug' => 'maxhub'],
            'Logitech' => ['slug' => 'logitech'],
            'Video Conference' => ['slug' => 'video-conference'],
            'Yeastar' => ['slug' => 'yeastar'],
            'Zoom' => ['slug' => 'zoom'],
            'Event' => ['slug' => 'event'],
        ];

        $catModels = [];
        foreach ($categories as $name => $data) {
            $catModels[$name] = NewsCategory::firstOrCreate(
                ['slug' => $data['slug']],
                ['name' => $name]
            );
        }

        // 2. Articles Data
        $articles = [
            [
                'title' => 'ACTiV Raih Penghargaan Terbaik Logitech di Asia Tenggara',
                'date' => '2025-07-03',
                'cats' => ['Berita Terkini', 'Maxhub', 'Tips & Trik'],
                'image' => '/assets/img/blog/blog-1-1.jpg', // Placeholder recycled from template
            ],
            [
                'title' => 'Cara Kerja dan Manfaat Screen Sharing Dongle Maxhub',
                'date' => '2025-07-02',
                'cats' => ['Berita Terkini', 'Maxhub', 'Tips & Trik'],
                'image' => '/assets/img/blog/blog-1-2.jpg',
            ],
            [
                'title' => 'Solusi Meeting Modern Konferensi Maxhub',
                'date' => '2025-02-05',
                'cats' => ['Berita Terkini', 'Video Conference'],
                'image' => '/assets/img/blog/blog-1-3.jpg',
            ],
            [
                'title' => 'Permasalahan dalam Video Conference dan Solusinya',
                'date' => '2025-01-23',
                'cats' => ['Berita Terkini', 'Logitech'],
                'image' => '/assets/img/blog/blog-1-4.jpg',
            ],
            [
                'title' => 'Solusi Pemantauan dan Diagnostik Perangkat dengan Logitech Essential',
                'date' => '2025-01-15',
                'cats' => ['Berita Terkini', 'Yeastar'],
                'image' => '/assets/img/blog/blog-1-1.jpg',
            ],
            [
                'title' => 'Solusi Tepat Untuk Komunikasi Tim Anda',
                'date' => '2025-01-10',
                'cats' => ['Video Conference'],
                'image' => '/assets/img/blog/blog-1-2.jpg',
            ],
            [
                'title' => 'Solusi Video Conference Terdepan!',
                'date' => '2025-01-03',
                'cats' => ['Event'],
                'image' => '/assets/img/blog/blog-1-3.jpg',
            ],
            [
                'title' => 'Solusi Video Konferensi Inovatif',
                'date' => '2024-06-13',
                'cats' => ['Berita Terkini', 'Logitech'],
                'image' => '/assets/img/blog/blog-1-4.jpg',
            ],
            [
                'title' => 'Zone 305 Solusi Wireless Terbaik untuk Beban Kerja Bisnis Anda',
                'date' => '2024-06-07',
                'cats' => ['Berita Terkini', 'Tips & Trik', 'Zoom'],
                'image' => '/assets/img/blog/blog-1-1.jpg',
            ],
            [
                'title' => 'Cara menghapus akun zoom di web',
                'date' => '2024-05-20', // Estimated
                'cats' => ['Tips & Trik', 'Zoom'],
                'image' => '/assets/img/blog/blog-1-2.jpg',
            ],
        ];

        foreach ($articles as $data) {
            $slug = Str::slug($data['title']);

            $news = News::updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => $data['title'],
                    'content' => $this->generateDummyContent($data['title']),
                    'excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                    'status' => 'published',
                    'published_at' => Carbon::parse($data['date']),
                    'featured_image' => $data['image'],
                ]
            );

            // Update SEO Meta
            $news->seo()->updateOrCreate(
                [],
                [
                    'title' => $data['title'],
                    'description' => "Baca selengkapnya tentang {$data['title']} di ACTiV.",
                ]
            );

            // Sync Categories
            $catIds = [];
            foreach ($data['cats'] as $catName) {
                if (isset($catModels[$catName])) {
                    $catIds[] = $catModels[$catName]->id;
                }
            }
            $news->categories()->sync($catIds);

            // Add some random tags
            $this->attachRandomTags($news);
        }
    }

    private function generateDummyContent($title)
    {
        return "
            <p><strong>{$title}</strong> - Ini adalah artikel dummy yang digenerate untuk keperluan development.</p>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam in dui mauris. Vivamus hendrerit arcu sed erat molestie vehicula. Sed auctor neque eu tellus rhoncus ut eleifend nibh porttitor.</p>
            <h3>Poin Penting</h3>
            <ul>
                <li>Solusi inovatif untuk kebutuhan bisnis Anda.</li>
                <li>Teknologi terbaru yang efisien dan efektif.</li>
                <li>Didukung oleh tim profesional dari ACTiV.</li>
            </ul>
            <p>Untuk informasi lebih lanjut, silakan hubungi tim kami.</p>
        ";
    }

    private function attachRandomTags(News $news)
    {
        $tags = ['Bisnis', 'Teknologi', 'Inovasi', 'Digital', 'Solusi', 'Efisiensi'];
        $tagIds = [];
        foreach ($tags as $tagName) {
             $tag = NewsTag::firstOrCreate(
                ['slug' => Str::slug($tagName)],
                ['name' => $tagName]
            );
            $tagIds[] = $tag->id;
        }

        // Attach 2 random tags
        $news->tags()->sync(collect($tagIds)->random(2));
    }
}
