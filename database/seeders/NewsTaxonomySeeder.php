<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\NewsCategory;
use App\Models\NewsTag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NewsTaxonomySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Categories
        $categories = [
            'Technology',
            'Business',
            'Design',
            'Marketing',
            'Health',
            'Education'
        ];

        $categoryIds = [];
        foreach ($categories as $name) {
            $category = NewsCategory::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
            $categoryIds[] = $category->id;
        }

        // 2. Create Tags
        $tags = [
            'AI', 'Cloud', 'Cybersecurity', 'Startup',
            'UX/UI', 'Trends', 'Innovation', 'Growth',
            'Mobile', 'Web Development', 'Data Science'
        ];

        $tagIds = [];
        foreach ($tags as $name) {
            $tag = NewsTag::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
            $tagIds[] = $tag->id;
        }

        // 3. Assign to existing News
        $posts = News::all();

        if ($posts->count() > 0) {
            $this->command->info("Assigning taxonomy to {$posts->count()} existing posts...");

            foreach ($posts as $post) {
                // Attach 1-2 random categories
                $post->categories()->sync(collect($categoryIds)->random(rand(1, 2)));

                // Attach 2-4 random tags
                $post->tags()->sync(collect($tagIds)->random(rand(2, 4)));
            }
        } else {
            $this->command->warn("No news posts found. Created categories and tags only.");
        }
    }
}
