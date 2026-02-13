<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Project;
use Modules\Core\Models\Product;
use Modules\Core\Models\Brand;
use Modules\Events\Models\Event;
use Modules\ServiceSolutions\Models\Service;
use Modules\ServiceSolutions\Models\ServiceSolution;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use App\Services\SeoResolver;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');
        $groupedResults = [];

        if ($query) {
            // Search News (Articles)
            $news = News::where('status', 'published')
                ->where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                        ->orWhere('excerpt', 'like', "%{$query}%")
                        ->orWhere('content', 'like', "%{$query}%");
                })
                ->limit(20)
                ->get()
                ->map(fn($item) => $this->formatResult($item, 'Article'));
            if ($news->isNotEmpty()) $groupedResults['Articles'] = $news;

            // Search Products
            $products = Product::where('is_active', true)
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%")
                        ->orWhere('features_text', 'like', "%{$query}%")
                        ->orWhere('specification_text', 'like', "%{$query}%");
                })
                ->limit(20)
                ->get()
                ->map(fn($item) => $this->formatResult($item, 'Product'));
            if ($products->isNotEmpty()) $groupedResults['Products'] = $products;

            // Search Events
            $events = Event::where('is_active', true)
                ->where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%");
                })
                ->limit(20)
                ->get()
                ->map(fn($item) => $this->formatResult($item, 'Event'));
            if ($events->isNotEmpty()) $groupedResults['Events'] = $events;

            // Search Services
            $services = Service::where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhere('content', 'like', "%{$query}%");
            })
                ->limit(20)
                ->get()
                ->map(fn($item) => $this->formatResult($item, 'Service'));
            if ($services->isNotEmpty()) $groupedResults['Services'] = $services;

            // Search Service Solutions
            $solutions = ServiceSolution::with('service')
                ->where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                        ->orWhere('subtitle', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%");
                })
                ->limit(20)
                ->get()
                ->map(fn($item) => $this->formatResult($item, 'Service Solution'));
            if ($solutions->isNotEmpty()) $groupedResults['Service Solutions'] = $solutions;

            // Search Brands
            $brands = Brand::where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('desc', 'like', "%{$query}%");
            })
                ->limit(20)
                ->get()
                ->map(fn($item) => $this->formatResult($item, 'Brand'));
            if ($brands->isNotEmpty()) $groupedResults['Brands'] = $brands;

            // Search Projects
            $projects = Project::where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('content', 'like', "%{$query}%");
            })
                ->limit(20)
                ->get()
                ->map(fn($item) => $this->formatResult($item, 'Project'));
            if ($projects->isNotEmpty()) $groupedResults['Projects'] = $projects;
        }

        return Inertia::render('Search', [
            'groupedResults' => $groupedResults,
            'query' => $query,
            'seo' => SeoResolver::staticPage('Search Results', "Results for: {$query}")
        ]);
    }

    private function formatResult($item, $type)
    {
        $resolvePath = function ($path) {
            if (!$path) return null;
            if (str_starts_with($path, 'http')) return $path;
            if (str_starts_with($path, 'assets') || str_starts_with($path, '/assets')) {
                return str_starts_with($path, '/') ? $path : "/{$path}";
            }
            return "/storage/{$path}";
        };

        $title = $item->title ?? $item->name;
        $slug = $item->slug;
        $image = $resolvePath($item->thumbnail ?? $item->image_path ?? $item->featured_image ?? $item->image ?? $item->logo_path ?? null);
        if (!$image) {
            $image = '/assets/img/logo-icon3.svg';
        }
        
        $content = $item->excerpt ?? $item->description ?? $item->subtitle ?? $item->desc ?? $item->content ?? '';
        $excerpt = Str::words(strip_tags($content), 20);

        $url = '#';
        switch ($type) {
            case 'Article':
                $url = "/{$slug}"; 
                break;
            case 'Product':
                $url = "/products/{$slug}";
                break;
            case 'Event':
                $url = "/events/{$slug}";
                break;
            case 'Service':
                $url = "/services/{$slug}";
                break;
            case 'Service Solution':
                $serviceSlug = $item->service?->slug ?? 'general';
                $url = "/services/{$serviceSlug}/{$slug}";
                break;
            case 'Brand':
                $url = "/{$slug}/products";
                break;
            case 'Project':
                $url = "/projects/{$slug}";
                break;
        }

        return [
            'id' => $item->id,
            'type' => $type,
            'title' => $title,
            'slug' => $slug,
            'image' => $image,
            'excerpt' => $excerpt,
            'url' => $url,
            'created_at' => $item->created_at
        ];
    }
}
