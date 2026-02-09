<?php

namespace App\Services;

use App\Models\SeoMeta;
use App\Services\JsonLdGenerator;

class SeoResolver
{
    public static function for($model): array
    {
        // Default to app name if no title
        $settings = \App\Models\Setting::whereIn('key', ['seo_default_title', 'seo_default_description', 'seo_default_og_image'])
            ->pluck('value', 'key');

        $siteName = $settings['seo_default_title'] ?? config('app.name', 'ACTiV');
        $defaultDesc = $settings['seo_default_description'] ?? '';
        $defaultOgImageVal = $settings['seo_default_og_image'] ?? null;
        $defaultOgImage = $defaultOgImageVal ? asset('storage/' . $defaultOgImageVal) : null;

        $seo = $model->seo;

        $metaTitle = $seo?->title ?? $model->title ?? $siteName;
        $metaDesc = $seo?->description ?? $model->excerpt ?? $model->description ?? $defaultDesc;

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
                'og_image' => $seo?->og_image ? asset('storage/' . $seo->og_image) : ($model->thumbnail ?? $model->featured_image ?? $model->image ?? $defaultOgImage),
                'og_type' => $seo?->og_type ?? 'website',
                'twitter_card' => $seo?->twitter_card ?? 'summary_large_image',
                'noindex' => (bool)$seo?->noindex,
            ],
            'meta' => [
                ['name' => 'description', 'content' => $metaDesc],
                ['property' => 'og:title', 'content' => $seo?->og_title ?? $metaTitle],
                ['property' => 'og:description', 'content' => $seo?->og_description ?? $metaDesc],
                ['property' => 'og:image', 'content' => $seo?->og_image ? asset('storage/' . $seo->og_image) : ($model->thumbnail ?? $model->featured_image ?? $model->image ?? $defaultOgImage)],
                ['name' => 'twitter:card', 'content' => $seo?->twitter_card ?? 'summary_large_image'],
                ['name' => 'robots', 'content' => $seo?->noindex ? 'noindex, nofollow' : 'index, follow'],
                ['rel' => 'canonical', 'href' => $seo?->canonical_url ?? url()->current()],
            ],
            'json_ld' => $jsonLd,
        ];
    }

    public static function staticPage(string $title, string $description = ''): array
    {
        $settings = \App\Models\Setting::whereIn('key', ['seo_default_title', 'seo_default_description', 'seo_default_og_image'])
            ->pluck('value', 'key');

        $siteName = $settings['seo_default_title'] ?? config('app.name', 'ACTiV');
        $defaultDesc = $settings['seo_default_description'] ?? '';
        $defaultOgImageVal = $settings['seo_default_og_image'] ?? null;
        $defaultOgImage = $defaultOgImageVal ? asset('storage/' . $defaultOgImageVal) : null;

        $fullTitle = $title ? "{$title} | {$siteName}" : $siteName;
        $metaDesc = $description ?: $defaultDesc;
        $currentUrl = url()->current();

        /** @var JsonLdGenerator $generator */
        $generator = app(JsonLdGenerator::class);
        $json_ld = $generator->generateStatic($fullTitle, $description, $currentUrl);

        return [
            'tags' => [
                'title' => $fullTitle,
                'description' => $metaDesc,
                'canonical_url' => $currentUrl,
                'og_title' => $fullTitle,
                'og_description' => $metaDesc,
                'og_image' => $defaultOgImage,
                'og_type' => 'website',
                'twitter_card' => 'summary_large_image',
                'noindex' => false,
            ],
            'meta' => [
                ['name' => 'description', 'content' => $metaDesc],
                ['property' => 'og:title', 'content' => $fullTitle],
                ['property' => 'og:description', 'content' => $metaDesc],
                ['property' => 'og:image', 'content' => $defaultOgImage],
            ],
            'json_ld' => $json_ld,
        ];
    }
}
