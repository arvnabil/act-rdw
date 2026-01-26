<?php

namespace App\Services;

use App\Models\News;
use App\Models\NewsCategory;
use App\Models\Page;
use App\Models\Project;
use App\Models\SeoMeta;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Modules\Core\Models\Brand;
use Modules\Core\Models\Product;
use Modules\ServiceSolutions\Models\Service;
use Modules\ServiceSolutions\Models\ServiceSolution;

class ActivePublicRouteDetector
{
    /**
     * Detect all public, indexable, and active routes.
     *
     * @param bool $includeNoindex Whether to include routes marked as noindex.
     * @return Collection
     */
    public function detect(bool $includeNoindex = false): Collection
    {
        $detectedPaths = collect();
        $results = collect();

        // 1. Static Principal Routes
        $this->getStaticRoutes()->each(function ($route) use (&$detectedPaths, &$results) {
            // Check if route exists or is a valid URL
            if (str_starts_with($route['url'], 'http') && $this->isIndexable($route['url'])) {
                $results->push($route);
                $detectedPaths->push($this->normalizePath($route['url']));
            }
        });

        // 2. Model-Driven Dynamic Routes (Catch-all /{slug})
        // Priority: Page > Brand > News > Project
        $this->detectCatchAllRoutes($detectedPaths, $results, $includeNoindex);

        // 3. Structured Dynamic Routes
        $this->detectStructuredRoutes($detectedPaths, $results, $includeNoindex);

        return $results->values();
    }

    /**
     * Core static routes.
     */
    protected function getStaticRoutes(): Collection
    {
        $namedRoutes = [
            ['name' => 'home', 'model' => 'Static:Home', 'priority' => 1.0, 'changefreq' => 'daily'],
            ['name' => 'about', 'model' => 'Static:About', 'priority' => 0.8, 'changefreq' => 'weekly'],
            ['name' => 'services.index', 'model' => 'Static:Services', 'priority' => 0.8, 'changefreq' => 'weekly'],
            ['name' => 'projects.index', 'model' => 'Static:Projects', 'priority' => 0.7, 'changefreq' => 'weekly'],
            ['name' => 'news.index', 'model' => 'Static:News', 'priority' => 0.7, 'changefreq' => 'weekly'],
            ['name' => 'partners', 'model' => 'Static:Partners', 'priority' => 0.7, 'changefreq' => 'weekly'],
            ['name' => 'products', 'model' => 'Static:Products', 'priority' => 0.7, 'changefreq' => 'weekly'],
        ];

        return collect($namedRoutes)->map(function ($r) {
            try {
                if (\Illuminate\Support\Facades\Route::has($r['name'])) {
                    $r['url'] = route($r['name']);
                    return $r;
                }
            } catch (\Exception $e) {
                // Ignore missing routes
            }
            return null;
        })->filter();
    }

    /**
     * Handle catch-all priority: Page > Brand > News > Project.
     */
    protected function detectCatchAllRoutes(Collection &$detectedPaths, Collection &$results, bool $includeNoindex): void
    {
        $models = [
            ['class' => Page::class, 'priority' => 0.8, 'freq' => 'weekly'],
            ['class' => Brand::class, 'priority' => 0.6, 'freq' => 'monthly'],
            ['class' => News::class, 'priority' => 0.7, 'freq' => 'weekly'],
            ['class' => Project::class, 'priority' => 0.6, 'freq' => 'monthly'],
        ];

        foreach ($models as $config) {
            $config['class']::query()
                ->where(function ($q) {
                    if (in_array('status', $q->getModel()->getFillable())) {
                        $q->where('status', 'published');
                    }
                })
                ->with('seo')
                ->get()
                ->each(function ($model) use ($config, &$detectedPaths, &$results, $includeNoindex) {
                    $slug = $model->slug;
                    if (!$slug || $detectedPaths->contains($slug)) return;

                    // Check NOINDEX
                    if (!$includeNoindex && ($model->seo && $model->seo->noindex)) return;

                    try {
                        if (Route::has('dynamic.resolve')) {
                            $url = route('dynamic.resolve', ['slug' => $slug]);

                            $results->push([
                                'id' => $model->id,
                                'url' => $url,
                                'model' => class_basename($model),
                                'priority' => $config['priority'],
                                'changefreq' => $config['freq'],
                                'updated_at' => $model->updated_at,
                                'is_noindex' => (bool) ($model->seo->noindex ?? false),
                            ]);
                            $detectedPaths->push($slug);
                        }
                    } catch (\Exception $e) {
                    }
                });
        }
    }

    /**
     * Handle nested or structured routes (Products, Services, etc.).
     */
    protected function detectStructuredRoutes(Collection &$detectedPaths, Collection &$results, bool $includeNoindex): void
    {
        // Products
        Product::where('is_active', true)->with('seo')->get()->each(function ($product) use (&$results, $includeNoindex) {
            if (!$includeNoindex && ($product->seo && $product->seo->noindex)) return;
            try {
                if (Route::has('products.show')) {
                    $results->push([
                        'id' => $product->id,
                        'url' => route('products.show', $product->slug),
                        'model' => 'Product',
                        'priority' => 0.8,
                        'changefreq' => 'weekly',
                        'updated_at' => $product->updated_at,
                        'is_noindex' => (bool) ($product->seo->noindex ?? false),
                    ]);
                }
            } catch (\Exception $e) {
            }
        });

        // Services (Main)
        Service::with('seo')->get()->each(function ($service) use (&$results, $includeNoindex) {
            if (!$includeNoindex && ($service->seo && $service->seo->noindex)) return;
            try {
                if (Route::has('services.detail')) {
                    $results->push([
                        'id' => $service->id,
                        'url' => route('services.detail', $service->slug),
                        'model' => 'Service',
                        'priority' => 0.8,
                        'changefreq' => 'weekly',
                        'updated_at' => $service->updated_at,
                        'is_noindex' => (bool) ($service->seo->noindex ?? false),
                    ]);
                }
            } catch (\Exception $e) {
            }
        });

        // Service Solutions (Sub-services)
        ServiceSolution::with(['seo', 'service'])->get()->each(function ($solution) use (&$results, $includeNoindex) {
            if (!$includeNoindex && ($solution->seo && $solution->seo->noindex)) return;
            try {
                if ($solution->service && Route::has('services.item.detail')) {
                    $results->push([
                        'id' => $solution->id,
                        'url' => route('services.item.detail', [$solution->service->slug, $solution->slug]),
                        'model' => 'ServiceSolution',
                        'priority' => 0.7,
                        'changefreq' => 'weekly',
                        'updated_at' => $solution->updated_at,
                        'is_noindex' => (bool) ($solution->seo->noindex ?? false),
                    ]);
                }
            } catch (\Exception $e) {
            }
        });

        // News Categories
        NewsCategory::with('seo')->get()->each(function ($cat) use (&$results, $includeNoindex) {
            if (!$includeNoindex && ($cat->seo && $cat->seo->noindex)) return;
            try {
                if (Route::has('news.category')) {
                    $results->push([
                        'id' => $cat->id,
                        'url' => route('news.category', $cat->slug),
                        'model' => 'NewsCategory',
                        'priority' => 0.5,
                        'changefreq' => 'monthly',
                        'updated_at' => $cat->updated_at,
                        'is_noindex' => (bool) ($cat->seo->noindex ?? false),
                    ]);
                }
            } catch (\Exception $e) {
            }
        });
    }

    /**
     * Check if a URL should be indexed based on global rules.
     */
    protected function isIndexable(string $url): bool
    {
        $path = parse_url($url, PHP_URL_PATH) ?: '/';

        $blacklisted = [
            '/admin',
            '/login',
            '/logout',
            '/form/',
            '/events/dashboard',
            '/api/',
            '/storage/',
        ];

        foreach ($blacklisted as $pattern) {
            if (str_starts_with($path, $pattern)) return false;
        }

        return true;
    }

    /**
     * Normalize path for collision detection.
     */
    protected function normalizePath(string $url): string
    {
        $path = parse_url($url, PHP_URL_PATH) ?: '/';
        return trim($path, '/');
    }
}
