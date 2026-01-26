<?php

namespace App\Services;

use App\Models\SeoMeta;
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
            \App\Models\SeoMonitoringRecord::query()->delete();

            foreach ($activeRoutes as $route) {
                $path = parse_url($route['url'], PHP_URL_PATH) ?: '/';

                \App\Models\SeoMonitoringRecord::create([
                    'url' => $route['url'],
                    'path' => $path,
                    'model' => $route['model'],
                    'model_id' => $route['id'] ?? null,
                    'is_noindex' => $route['is_noindex'] ?? false,
                    'in_sitemap' => $sitemapUrls->contains($route['url']),
                    'canonical_valid' => $this->verifyCanonical($route),
                    'seo_score' => $this->calculateSeoScore($route),
                    'priority' => $route['priority'] ?? 0.5,
                    'changefreq' => $route['changefreq'] ?? 'weekly',
                    'last_modified' => $route['updated_at'] ?? now(),
                ]);
            }
        });

        $this->clearCache();
    }

    /**
     * Get aggregate SEO coverage data from database.
     *
     * @return Collection
     */
    public function getCoverageData(): Collection
    {
        return \App\Models\SeoMonitoringRecord::all();
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

    /**
     * Placeholder/Simple calculation for SEO Score if not already present.
     */
    protected function calculateSeoScore(array $route): int
    {
        // If it's a model, we might want to fetch its actual SeoMeta record
        // but detector already has some info.
        // For now, return a placeholder or implement logic to fetch from database.

        // Let's assume the detector returns 'model' and 'id'.
        if (!isset($route['id'])) return 0;

        $modelClass = $this->resolveModelClass($route['model']);
        if (!$modelClass) return 0;

        $record = $modelClass::find($route['id']);
        if (!$record || !$record->seo) return 0;

        return $record->seo->seo_score ?? 0;
    }

    /**
     * Verify if canonical URL matches or is missing.
     */
    protected function verifyCanonical(array $route): bool
    {
        if (!isset($route['id'])) return false;

        $modelClass = $this->resolveModelClass($route['model']);
        if (!$modelClass) return false;

        $record = $modelClass::find($route['id']);
        if (!$record || !$record->seo) return false;

        return !empty($record->seo->canonical_url);
    }

    /**
     * Helper to resolve class name from short name.
     */
    protected function resolveModelClass(string $shortName): ?string
    {
        $map = [
            'Page' => \App\Models\Page::class,
            'Brand' => \Modules\Core\Models\Brand::class,
            'News' => \App\Models\News::class,
            'Project' => \App\Models\Project::class,
            'Product' => \Modules\Core\Models\Product::class,
            'Service' => \Modules\ServiceSolutions\Models\Service::class,
            'ServiceSolution' => \Modules\ServiceSolutions\Models\ServiceSolution::class,
            'NewsCategory' => \App\Models\NewsCategory::class,
        ];

        return $map[$shortName] ?? null;
    }

    /**
     * Clear Cache.
     */
    public function clearCache(): void
    {
        Cache::forget('seo_coverage_data');
    }
}
