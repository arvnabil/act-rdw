<?php

namespace App\Services;

use App\Models\SeoMeta;
use App\Services\Seo\SeoService;

class SeoResolver
{
    public static function for($model): array
    {
        /** @var SeoService $seoService */
        $seoService = app(SeoService::class);
        $data = $seoService->extract($model);

        $seo = $model->seo;

        return [
            'tags' => [
                'title' => $data['title'],
                'description' => $data['description'],
                'keywords' => implode(', ', $data['keywords']),
                'canonical_url' => $seo?->canonical_url ?? $data['url'],
                'og_title' => $seo?->og_title ?? $data['title'],
                'og_description' => $seo?->og_description ?? $data['description'],
                'og_image' => $data['og_image'],
                'og_type' => $seo?->og_type ?? 'website',
                'twitter_card' => $seo?->twitter_card ?? 'summary_large_image',
                'noindex' => (bool)$seo?->noindex,
            ],
            'meta' => [
                ['name' => 'description', 'content' => $data['description']],
                ['property' => 'og:title', 'content' => $seo?->og_title ?? $data['title']],
                ['property' => 'og:description', 'content' => $seo?->og_description ?? $data['description']],
                ['property' => 'og:image', 'content' => $data['og_image']],
                ['name' => 'twitter:card', 'content' => $seo?->twitter_card ?? 'summary_large_image'],
                ['name' => 'robots', 'content' => $seo?->noindex ? 'noindex, nofollow' : 'index, follow'],
                ['rel' => 'canonical', 'href' => $seo?->canonical_url ?? $data['url']],
            ],
            'json_ld' => [], // Managed by SeoViewComposer now
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
                ['rel' => 'canonical', 'href' => $currentUrl],
            ],
            'json_ld' => [], // Managed by SeoViewComposer now
        ];
    }

}
