<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\NewsTag;

class NewsTaxonomySeeder extends Seeder
{
    public function run()
    {
        // 1. Create Categories
        $categories = [
            'IT Strategy & Planning',
            'Web Developments',
            'Cloud Consulting',
            'Machine Learning',
            'Database Security',
            'IT Management'
        ];

        $categoryIds = [];
        foreach ($categories as $catName) {
            $cat = NewsCategory::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($catName)],
                ['name' => $catName]
            );
            $categoryIds[] = $cat->id;
        }

        // 2. Create Tags
        $tags = [
            'Advice', 'Technology', 'Atek', 'Ux/Ui',
            'Consulting', 'Solution', 'Health',
            'IT Solution', 'Cloud', 'Cybersecurity', 'AI'
        ];

        $tagIds = [];
        foreach ($tags as $tagName) {
            $tag = NewsTag::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($tagName)],
                ['name' => $tagName]
            );
            $tagIds[] = $tag->id;
        }

        // 3. Attach to existing News
        $allNews = News::all();
        if ($allNews->isEmpty()) {
            return;
        }

        foreach ($allNews as $news) {
            // Attach 1-3 random categories
            $news->categories()->sync(collect($categoryIds)->random(rand(1, 3)));

            // Attach 2-5 random tags
            $news->tags()->sync(collect($tagIds)->random(rand(2, 5)));
        }
    }
}
