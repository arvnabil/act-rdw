<?php

namespace Modules\SEO\Services\Seo;

use Illuminate\Database\Eloquent\Model;
use Modules\News\Models\News;
use Modules\Projects\Models\Project;
use Modules\CMS\Models\Page;
use Modules\Services\Models\Service;
use Modules\Services\Models\ServiceSolution;
use Modules\ProductCatalog\Models\Product;
use Modules\ProductCatalog\Models\Brand;
use Modules\Clients\Models\Client;
use Illuminate\Support\Str;

class SeoService
{
    public function extract(Model $model): array
    {
        $seo = $model->seo;
        $baseUrl = config('app.url');

        $data = [
            'title' => $seo?->title ?? $model->title ?? $model->name ?? config('app.name'),
            'description' => $this->cleanDesc($seo?->description ?? $model->excerpt ?? $model->description ?? ''),
            'url' => $this->resolveUrl($model, $baseUrl),
            'og_image' => $this->resolveImage($model),
            'keywords' => $this->resolveKeywords($seo?->keywords),
            'type' => $this->resolveType($model),
        ];

        // Model specific data
        if ($model instanceof News) {
            $data['article'] = [
                'datePublished' => $model->published_at?->toIso8601String() ?? $model->created_at->toIso8601String(),
                'dateModified' => $model->updated_at->toIso8601String(),
                'section' => $model->categories->pluck('name')->first(),
                'wordCount' => str_word_count(strip_tags($model->content ?? $model->description ?? '')),
                'keywords' => array_merge($data['keywords'], $model->tags->pluck('name')->toArray())
            ];
        }

        if ($model instanceof Service) {
            $data['service'] = [
                'offers' => $model->solutions->map(fn($s) => [
                    'name' => $s->title,
                    'description' => Str::limit(strip_tags($s->description ?? ''), 160)
                ])->toArray()
            ];
        }

        return $data;
    }

    protected function cleanDesc(string $desc): string
    {
        return Str::limit(strip_tags($desc), 160);
    }

    protected function resolveUrl(Model $model, string $baseUrl): string
    {
        if ($model instanceof Page && $model->is_homepage) {
            return $baseUrl;
        }

        $slug = $model->slug ?? '';
        $path = match(true) {
            $model instanceof News => 'news/' . ltrim($slug, '/'),
            $model instanceof Service => 'services/' . ltrim($slug, '/'),
            $model instanceof Project => 'projects/' . ltrim($slug, '/'),
            default => ltrim($slug, '/')
        };

        return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
    }

    protected function resolveImage(Model $model): ?string
    {
        // 1. Priority: Manual SEO og_image
        $path = $model->seo?->og_image;

        // 2. Fallbacks based on Model type
        if (!$path) {
            $path = match(true) {
                $model instanceof Product         => $model->image_path,
                $model instanceof Client          => $model->logo,
                $model instanceof Brand           => $model->image ?: $model->logo_path,
                $model instanceof News            => $model->thumbnail,
                $model instanceof Project         => $model->thumbnail,
                $model instanceof Service         => $model->thumbnail ?: $model->featured_image,
                $model instanceof ServiceSolution => $model->thumbnail,
                $model instanceof Page            => $model->breadcrumb_image,
                default                           => $model->thumbnail ?? $model->featured_image ?? $model->image_path
            };
        }

        // 3. Absolute URL resolution or Global Default
        if ($path) {
            return asset('storage/' . $path);
        }

        // 4. Global fallback to site logo or branding
        $defaultOg = \Modules\Settings\Models\Setting::getValue('seo_default_og_image');
        return $defaultOg ? asset('storage/' . $defaultOg) : asset('assets/img/logo/logo.png');
    }

    protected function resolveType(Model $model): string
    {
        return match(true) {
            $model instanceof News => 'Article',
            $model instanceof Service => 'Service',
            default => 'WebPage'
        };
    }
    protected function resolveKeywords(mixed $keywords): array
    {
        if (is_array($keywords)) {
            return $keywords;
        }

        if (is_string($keywords)) {
            // Check if it's a JSON string
            $decoded = json_decode($keywords, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }

            // Otherwise treat as comma-separated string
            return array_map('trim', explode(',', $keywords));
        }

        return [];
    }
}
