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

        // JSON-LD Generation (Delegated to Enterprise Generator)
        /** @var JsonLdGenerator $generator */
        $generator = app(JsonLdGenerator::class);
        $jsonLd = $generator->generate($model);

        return [
            'title' => $metaTitle,
            'meta' => [
                ['name' => 'description', 'content' => $metaDesc],
                ['property' => 'og:title', 'content' => $seo?->og_title ?? $metaTitle],
                ['property' => 'og:description', 'content' => $seo?->og_description ?? $metaDesc],
                ['property' => 'og:image', 'content' => $seo?->og_image ? asset('storage/' . $seo->og_image) : ($model->thumbnail ?? $model->featured_image ?? $model->image ?? null)],
                ['name' => 'twitter:card', 'content' => $seo?->twitter_card ?? 'summary_large_image'],
                ['name' => 'robots', 'content' => $seo?->noindex ? 'noindex, nofollow' : 'index, follow'],
                ['rel' => 'canonical', 'href' => $seo?->canonical_url ?? url()->current()], // Fallback to current if not custom, but JsonLdGen uses stricter canonicals
            ],
            'jsonLd' => $jsonLd,
        ];
    }

    public static function staticPage(string $title, string $description = ''): array
    {
        return [
            'title' => $title,
            'meta' => [
                ['name' => 'description', 'content' => $description],
            ]
        ];
    }
}
