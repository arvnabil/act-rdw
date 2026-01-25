<?php

namespace App\Services;

use App\Models\News;
use App\Models\Page;
use App\Models\Project;
use App\Models\Client;
use Modules\Core\Models\Brand;
use Modules\ServiceSolutions\Models\Service;
use App\Services\Seo\JsonLdOptimizer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class JsonLdGenerator
{
    const ORGANIZATION_ID = '#organization';

    public function __construct(
        protected JsonLdOptimizer $optimizer
    ) {}

    /**
     * Generate Enterprise-grade JSON-LD schema based on model type.
     */
    public function generate(Model $model, bool $force = false): array
    {
        // 1. CRITICAL GUARDS
        if (!$force && $this->shouldBlockJsonLd($model)) {
            return [];
        }

        $schemas = [];
        $baseUrl = config('app.url'); // Must use APP_URL, never request()->url()

        // 2. Organization Schema (Rendered ONLY on Homepage)
        if ($this->isHomepage($model)) {
            $schemas[] = $this->generateOrganization($baseUrl);
        }

        // 3. Entity Auto-Mapping
        $mainSchema = match (true) {
            $model instanceof News => $this->generateArticle($model, $baseUrl),
            $model instanceof Project => $this->generateCreativeWork($model, $baseUrl),
            $model instanceof Brand => $this->generateBrand($model, $baseUrl),
            $model instanceof Client => $this->generateClientOrganization($model, $baseUrl),
            $model instanceof Service => $this->generateService($model, $baseUrl),
            default => $this->generateWebPage($model, $baseUrl),
        };

        if ($mainSchema) {
            $schemas[] = $mainSchema;
        }

        // 4. BreadcrumbList (For Services and structured pages)
        if ($model instanceof Service) {
            $schemas[] = $this->generateBreadcrumbs($model, $baseUrl);
        }

        // 5. Validation & Optimization Pipeline
        return $this->optimizer->optimize($schemas);
    }

    /**
     * SECURITY & COMPLIANCE GUARD
     * Returns TRUE if JSON-LD should NOT be rendered.
     */
    protected function shouldBlockJsonLd(Model $model): bool
    {
        $request = request();

        // 1. URL Pattern Guards
        if ($request->is('admin/*') || $request->is('livewire/*') || $request->is('*/preview')) {
            return true;
        }

        // 2. Builder Header Guard
        if ($request->header('X-Builder-Preview')) {
            return true;
        }

        // 3. User Preference (NoIndex)
        if (optional($model->seo)->noindex) {
            return true;
        }

        // 4. Content Status Guard
        // Ensure we don't leak Draft content schema
        if (isset($model->status) && $model->status !== 'published') {
            return true;
        }

        return false;
    }

    /**
     * GLOBAL ORGANIZATION ENTITY
     * Stable @id, rendered only once.
     */
    protected function generateOrganization(string $baseUrl): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            '@id' => $baseUrl . self::ORGANIZATION_ID,
            'name' => config('app.name', 'ACTiV'),
            'url' => $baseUrl,
            'logo' => [
                '@type' => 'ImageObject',
                'url' => asset('assets/img/logo/logo.png'),
                'width' => 512,
                'height' => 512
            ],
            // Optional: ContactPoint if needed
        ];
    }

    /**
     * TEMPLATE: WebPage (Default / Static Pages)
     */
    protected function generateWebPage(Model $model, string $baseUrl): array
    {
        $url = $this->generateCanonicalUrl($model, $baseUrl);
        $title = $this->getMetaTitle($model);

        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            '@id' => $url . '#webpage',
            'url' => $url,
            'name' => $title,
            'description' => $this->getMetaDescription($model),
            'isPartOf' => ['@id' => $baseUrl . self::ORGANIZATION_ID],
            'inLanguage' => 'id-ID',
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $url
            ]
        ];
    }

    /**
     * TEMPLATE: Article (News)
     */
    protected function generateArticle(News $model, string $baseUrl): array
    {
        $url = $this->generateCanonicalUrl($model, $baseUrl);

        return [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            '@id' => $url . '#article',
            'headline' => $this->getMetaTitle($model),
            'description' => $this->getMetaDescription($model),
            'image' => $this->getImages($model),
            'datePublished' => $model->published_at?->toIso8601String(),
            'dateModified' => $model->updated_at?->toIso8601String(),
            'author' => [
                '@type' => 'Organization',
                '@id' => $baseUrl . self::ORGANIZATION_ID
            ],
            'publisher' => [
                '@type' => 'Organization',
                '@id' => $baseUrl . self::ORGANIZATION_ID
            ],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $url
            ]
        ];
    }

    /**
     * TEMPLATE: CreativeWork (Projects/Portfolio)
     */
    protected function generateCreativeWork(Project $model, string $baseUrl): array
    {
        $url = $this->generateCanonicalUrl($model, $baseUrl);

        return [
            '@context' => 'https://schema.org',
            '@type' => 'CreativeWork',
            '@id' => $url . '#project',
            'name' => $this->getMetaTitle($model),
            'description' => $this->getMetaDescription($model),
            'image' => $this->getImages($model),
            'datePublished' => $model->created_at?->toIso8601String(),
            'dateModified' => $model->updated_at?->toIso8601String(),
            'isPartOf' => ['@id' => $baseUrl . self::ORGANIZATION_ID],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $url
            ]
        ];
    }

    /**
     * TEMPLATE: Brand
     */
    protected function generateBrand(Brand $model, string $baseUrl): array
    {
        $url = $this->generateCanonicalUrl($model, $baseUrl);

        return [
            '@context' => 'https://schema.org',
            '@type' => 'Brand',
            '@id' => $url . '#brand',
            'name' => $model->name,
            'description' => $model->description,
            'logo' => $model->logo_path ? asset('storage/' . $model->logo_path) : null,
            'url' => $model->website_url ?? $url,
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $url
            ]
        ];
    }

    /**
     * TEMPLATE: Organization (Client)
     */
    protected function generateClientOrganization(Client $model, string $baseUrl): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $model->name,
            'url' => $model->website_url,
            'logo' => $model->logo ? asset('storage/' . $model->logo) : null,
        ];
    }

    /**
     * TEMPLATE: Service
     */
    protected function generateService(Service $model, string $baseUrl): array
    {
        $url = $this->generateCanonicalUrl($model, $baseUrl);
        $images = $this->getImages($model);
        $solutions = $model->solutions;

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            '@id' => $url . '#service',
            'name' => $this->getMetaTitle($model),
            'description' => $this->getMetaDescription($model),
            'image' => !empty($images) ? [
                '@type' => 'ImageObject',
                'url' => $images[0],
                'width' => 1200,
                'height' => 630
            ] : null,
            'provider' => [
                '@type' => 'Organization',
                '@id' => $baseUrl . self::ORGANIZATION_ID
            ],
            'areaServed' => [
                '@type' => 'Country',
                'name' => 'Indonesia'
            ],
            'serviceType' => $model->name,
            'hasOfferCatalog' => $solutions->isNotEmpty() ? [
                '@type' => 'OfferCatalog',
                'name' => "{$model->name} Solutions",
                'itemListElement' => $solutions->map(fn($sol) => [
                    '@type' => 'Offer',
                    'name' => $sol->title,
                    'url' => "{$url}#{$sol->slug}",
                    'availability' => 'https://schema.org/InStock',
                    'itemOffered' => [
                        '@type' => 'Service',
                        'name' => $sol->title,
                        'description' => Str::limit(strip_tags($sol->description ?? ''), 160)
                    ]
                ])->toArray()
            ] : null,
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $url
            ]
        ];

        return $this->clean($schema);
    }

    /**
     * HELPER: Generate Safe Canonical URL
     */
    protected function generateCanonicalUrl(Model $model, string $baseUrl): string
    {
        // 1. Explicit SEO Canonical Override
        if (optional($model->seo)->canonical_url) {
            return $model->seo->canonical_url;
        }

        // 2. Homepage Exception
        if ($this->isHomepage($model)) {
            return $baseUrl;
        }

        // 3. Model Slug Construction
        // Clean leading slashes to prevent double slashes
        $slug = match(true) {
            isset($model->slug) => $model->slug,
            default => ''
        };

        // Handle specific route prefixes if necessary (e.g. /news/slug)
        // For now assuming models have full slugs or root usage
        // Ideally this should map to named routes, but user requested 'config(app.url) . slug' pattern

        $path = match(true) {
             $model instanceof News => 'news/' . ltrim($slug, '/'),
             $model instanceof Service => 'services/' . ltrim($slug, '/'),
             // Add other prefixes here if models are not root-level
             default => ltrim($slug, '/')
        };

        return rtrim($baseUrl, '/') . '/' . $path;
    }

    protected function getMetaTitle(Model $model): string
    {
        return $model->seo?->title ?? $model->title ?? $model->name ?? config('app.name');
    }

    protected function getMetaDescription(Model $model): string
    {
        $desc = $model->seo?->description ?? $model->excerpt ?? $model->description ?? '';
        return Str::limit(strip_tags($desc), 160);
    }

    protected function getImages(Model $model): array
    {
        $images = [];

        // 1. SEO Image
        if ($model->seo?->og_image) {
            $images[] = asset('storage/' . $model->seo->og_image);
        }

        // 2. Featured/Thumbnail
        $featured = $model->featured_image ?? $model->thumbnail ?? $model->image_path ?? null;
        if ($featured) {
            $images[] = asset('storage/' . $featured);
        }

        return array_unique($images);
    }

    protected function isHomepage(Model $model): bool
    {
        return $model instanceof Page && $model->is_homepage;
    }

    /**
     * HELPER: Recursively remove empty values
     */
    protected function clean(array $data): array
    {
        return array_filter($data, function ($v) {
            if (is_array($v)) {
                $v = $this->clean($v);
                return !empty($v);
            }
            return !empty($v) || $v === 0 || $v === '0';
        });
    }

    /**
     * HELPER: Generate BreadcrumbList
     */
    protected function generateBreadcrumbs(Model $model, string $baseUrl): array
    {
        $items = [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Home',
                'item' => $baseUrl
            ]
        ];

        if ($model instanceof Service) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => 'Services',
                'item' => $baseUrl . '/services'
            ];
            $items[] = [
                '@type' => 'ListItem',
                'position' => 3,
                'name' => $model->name,
                'item' => $this->generateCanonicalUrl($model, $baseUrl)
            ];
        }

        // Add other models here if needed in future

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items
        ];
    }
}
