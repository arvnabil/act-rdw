<?php

namespace App\Services;

use App\Models\SeoMeta;
use App\Services\JsonLdGenerator;

class SeoResolver
{
    public static function for($model): array
    {
        // Default to app name if no title
        $siteName = config('app.name', 'ACTiV');

        // Eager load if possible/needed, but usually called on a loaded model
        $seo = $model->seo;

        $metaTitle = $seo?->title ?? $model->title ?? $siteName;
        $metaDesc = $seo?->description ?? $model->excerpt ?? $model->description ?? '';

        // JSON-LD Generation
        $jsonLd = [];
        if (!empty($seo?->schema_override)) {
             $jsonLd = json_decode($seo->schema_override, true);
        }

        if (empty($jsonLd)) {
            /** @var JsonLdGenerator $generator */
            $generator = app(JsonLdGenerator::class);
            $jsonLd = $generator->generate($model);
        }

        return [
            'tags' => [
                'title' => $metaTitle,
                'description' => $metaDesc,
                'keywords' => $seo?->keywords ?? '',
                'canonical_url' => $seo?->canonical_url ?? url()->current(),
                'og_title' => $seo?->og_title ?? $metaTitle,
                'og_description' => $seo?->og_description ?? $metaDesc,
                'og_image' => $seo?->og_image ? asset('storage/' . $seo->og_image) : ($model->thumbnail ?? $model->featured_image ?? $model->image ?? null),
                'og_type' => $seo?->og_type ?? 'website',
                'twitter_card' => $seo?->twitter_card ?? 'summary_large_image',
                'noindex' => (bool)$seo?->noindex,
            ],
            'meta' => [
                ['name' => 'description', 'content' => $metaDesc],
                ['property' => 'og:title', 'content' => $seo?->og_title ?? $metaTitle],
                ['property' => 'og:description', 'content' => $seo?->og_description ?? $metaDesc],
                ['property' => 'og:image', 'content' => $seo?->og_image ? asset('storage/' . $seo->og_image) : ($model->thumbnail ?? $model->featured_image ?? $model->image ?? null)],
                ['name' => 'twitter:card', 'content' => $seo?->twitter_card ?? 'summary_large_image'],
                ['name' => 'robots', 'content' => $seo?->noindex ? 'noindex, nofollow' : 'index, follow'],
                ['rel' => 'canonical', 'href' => $seo?->canonical_url ?? url()->current()],
            ],
            'json_ld' => $jsonLd,
        ];
    }

    public static function staticPage(string $title, string $description = ''): array
    {
        $siteName = config('app.name', 'ACTiV');
        $fullTitle = $title ? "{$title} | {$siteName}" : $siteName;
        $currentUrl = url()->current();

        /** @var JsonLdGenerator $generator */
        $generator = app(JsonLdGenerator::class);
        $json_ld = $generator->generateStatic($fullTitle, $description, $currentUrl);

        return [
            'tags' => [
                'title' => $fullTitle,
                'description' => $description,
                'canonical_url' => $currentUrl,
                'og_title' => $fullTitle,
                'og_description' => $description,
                'og_type' => 'website',
                'twitter_card' => 'summary_large_image',
                'noindex' => false,
            ],
            'meta' => [
                ['name' => 'description', 'content' => $description],
            ],
            'json_ld' => $json_ld,
        ];
    }
}
