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
                $score = (int) ($route['seo_score'] ?? 0);

                // Self-healing: If score is 0 and it's a model, try a fresh calculation
                if ($score === 0 && isset($route['id']) && !empty($route['model']) && !str_contains($route['model'], 'Static:')) {
                    $score = $this->repairSeoScore($route);
                }

                \App\Models\SeoMonitoringRecord::create([
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
            'Page' => \App\Models\Page::class,
            'Brand' => \Modules\Core\Models\Brand::class,
            'News' => \App\Models\News::class,
            'Project' => \App\Models\Project::class,
            'Product' => \Modules\Core\Models\Product::class,
            'Service' => \Modules\ServiceSolutions\Models\Service::class,
            'ServiceSolution' => \Modules\ServiceSolutions\Models\ServiceSolution::class,
            'NewsCategory' => \App\Models\NewsCategory::class,
        ];

        $modelClass = $modelMap[$route['model']] ?? null;
        if (!$modelClass) return 0;

        $seo = \App\Models\SeoMeta::where('seoable_type', $modelClass)
            ->where('seoable_id', $route['id'])
            ->first();

        if (!$seo) return 0;

        $result = \App\Services\Seo\SeoScoreCalculator::calculate($seo);

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

    public function clearCache(): void
    {
        Cache::forget('seo_coverage_data');
    }
}
