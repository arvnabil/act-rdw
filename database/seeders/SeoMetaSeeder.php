<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\SeoMeta;
use App\Services\JsonLdGenerator;
use Illuminate\Database\Seeder;

class SeoMetaSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Pages
        $pages = Page::all();

        foreach ($pages as $page) {
            // Check if SEO exists
            if ($page->seo()->exists()) {
                continue;
            }

            // Create Default SEO
            $page->seo()->create([
                'title' => $page->title,
                'description' => $this->getFallbackDescription($page),
                'noindex' => false,
                // We leave schema_override NULL so the dynamic generator takes over by default
                // unless we want to "snapshot" it now. Let's leave it null for dynamic flexibility.
                'schema_override' => null,
            ]);
        }
    }

    protected function getFallbackDescription(Page $page): string
    {
         $heroSection = $page->sections->firstWhere('section_key', 'slider');
         if ($heroSection && isset($heroSection->config['slides'][0]['description'])) {
            return \Illuminate\Support\Str::limit(strip_tags($heroSection->config['slides'][0]['description']), 160);
         }
         return "Activia RDW - " . $page->title;
    }
}
