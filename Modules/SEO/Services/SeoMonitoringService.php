<?php

namespace Modules\SEO\Services;

use Modules\SEO\Models\SeoMeta;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class SeoMonitoringService
{
    /**
     * Sync data from detector to database.
     */
    public function sync(): void
    {
        $detector = app(ActivePublicRouteDetector::class);
        $activeRoutes = $detector->detect(true);
        $sitemapUrls = $this->getSitemapUrls();

        \Illuminate\Support\Facades\DB::transaction(function () use ($activeRoutes, $sitemapUrls) {
            \Modules\SEO\Models\SeoMonitoringRecord::query()->delete();

            foreach ($activeRoutes as $route) {
                $path = parse_url($route['url'], PHP_URL_PATH) ?: '/';
                $score = (int) ($route['seo_score'] ?? 0);

                // Self-healing: If score is 0 and it's a model, try a fresh calculation
                if ($score === 0 && isset($route['id']) && !empty($route['model']) && !str_contains($route['model'], 'Static:')) {
                    $score = $this->repairSeoScore($route);
                }

                \Modules\SEO\Models\SeoMonitoringRecord::create([
                    'url' => $route['url'],
                    'path' => $path,
                    'model' => $route['model'],
                    'model_id' => $route['id'] ?? null,
                    'is_noindex' => $route['is_noindex'] ?? false,
                    'in_sitemap' => $sitemapUrls->contains($route['url']),
                    'canonical_valid' => !empty($route['canonical_url']),
                    'seo_score' => $score,
                    'priority' => $route['priority'] ?? 0.5,
                    'changefreq' => $route['changefreq'] ?? 'weekly',
                    'last_modified' => $route['updated_at'] ?? now(),
                ]);
            }
        });

        $this->clearCache();
    }

    /**
     * Attempt to recalculate and persist SEO score if missing.
     */
    protected function repairSeoScore(array $route): int
    {
        $modelMap = [
            'Page' => \Modules\CMS\Models\Page::class,
            'Brand' => \Modules\ProductCatalog\Models\Brand::class,
            'News' => \Modules\News\Models\News::class,
            'Project' => \Modules\Projects\Models\Project::class,
            'Product' => \Modules\ProductCatalog\Models\Product::class,
            'Service' => \Modules\Services\Models\Service::class,
            'ServiceSolution' => \Modules\Services\Models\ServiceSolution::class,
            'NewsCategory' => \Modules\News\Models\NewsCategory::class,
        ];

        $modelClass = $modelMap[$route['model']] ?? null;
        if (!$modelClass) return 0;

        $seo = \Modules\SEO\Models\SeoMeta::where('seoable_type', $modelClass)
            ->where('seoable_id', $route['id'])
            ->first();

        if (!$seo || !$seo->seoable) return 0;

        /** @var \Modules\SEO\Services\Seo\SeoAuditService $auditService */
        $auditService = app(\Modules\SEO\Services\Seo\SeoAuditService::class);
        
        // Calculate using the main audit service
        $result = $auditService->audit($seo->seoable);

        // Persist the fix to the main SEO table too
        $seo->update(['seo_score' => $result['score']]);

        return $result['score'];
    }

    /**
     * Get aggregate SEO coverage data from database.
     *
     * @return Collection
     */
    public function getCoverageData(): Collection
    {
        return \Modules\SEO\Models\SeoMonitoringRecord::all();
    }

    /**
     * Parse public/sitemap.xml to get all registered URLs.
     */
    protected function getSitemapUrls(): Collection
    {
        $path = public_path('sitemap.xml');
        if (!File::exists($path)) {
            return collect();
        }

        try {
            $xml = simplexml_load_file($path);
            $urls = collect();

            foreach ($xml->url as $url) {
                $urls->push((string) $url->loc);
            }

            return $urls;
        } catch (\Exception $e) {
            return collect();
        }
    }

    public function clearCache(): void
    {
        Cache::forget('seo_coverage_data');
    }
}
