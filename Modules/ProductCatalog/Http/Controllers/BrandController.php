<?php

namespace Modules\ProductCatalog\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\ProductCatalog\Models\Brand;
use Modules\ProductCatalog\Models\Product;
use Modules\Services\Models\Service;
use Modules\Services\Models\ServiceSolution;
use Modules\SEO\Services\SeoResolver;

class BrandController extends Controller
{
    public function show($slug)
    {
        // 1. Fetch Brand (by slug or name)
        $brand = Brand::where('slug', $slug)
            ->orWhere('name', 'LIKE', str_replace('-', ' ', $slug))
            ->orWhere('name', 'LIKE', $slug)
            ->first();

        if (!$brand) {
            abort(404);
        }

        // 2. Real "Categories" (Services that have products for this brand)
        // Frontend expects: { name, slug, image }
        // 2. Real "Categories" (Device Types)
        // Frontend expects: { name, slug, image }
        $categories = \Modules\ProductCatalog\Models\ProductCategory::whereHas('products', function ($q) use ($brand) {
            $q->where('brand_id', $brand->id)->where('is_active', true);
        })->orderBy('sort_order')->get()->map(function ($cat) {
            return (object) [
                'name' => $cat->name,
                'slug' => $cat->slug,
                // Fallback image logic if icon is missing
                'image' => $cat->icon ?? '/assets/default.png'
            ];
        });

        // 3. Dynamic Service Solutions (Existing logic preserved & optimized)
        $serviceSolutions = $brand->serviceSolutions()->with('service')->orderBy('sort_order')->get();

        $groupedServices = $serviceSolutions->groupBy('service_id')->map(function ($group) {
            $firstItem = $group->first();
            if (!$firstItem || !$firstItem->service) {
                return null;
            }
            $service = $firstItem->service;
            return [
                'id' => $service->id,
                'name' => $service->name,
                'slug' => $service->slug ?? \Illuminate\Support\Str::slug($service->name),
                'image' => $service->thumbnail,
                'title' => $service->name . ' Products',
                'sub_title' => 'Product By ' . $service->name,
                'solutions' => $group->map(function ($sol) {
                    return [
                        'title' => $sol->title,
                        'slug' => $sol->slug,
                        'image' => $sol->thumbnail,
                        'desc' => $sol->subtitle ?? \Illuminate\Support\Str::limit($sol->description, 50)
                    ];
                })->values()
            ];
        })->filter()->values();

        // 4. Real "Latest Products" (Filtered by New Arrival)
        // Frontend expects: { name, image_path, price, category (service name), is_active, is_new }
        $products = Product::where('brand_id', $brand->id)
            ->where('is_active', true)
            ->where('is_new', true) // Only New Arrivals
            ->with('categories') // Eager load categories
            ->latest()
            ->take(8) // Limit to 8 as per UI design
            ->get()
            ->map(function ($product) {
                // Get first category name
                $categoryName = $product->categories->first()?->name ?? 'General';
                
                return (object) [
                    'id' => $product->id,
                    'name' => $product->name,
                    'image_path' => $product->image_path ?? '/assets/default.png', // Fallback
                    'price' => $product->price ?? 0,
                    'category' => $categoryName,
                    'is_active' => $product->is_active,
                    'is_new' => $product->is_new,
                    'slug' => $product->slug
                ];
            });

        // 5. SEO
        $seo = SeoResolver::for($brand);

        return Inertia::render('Partners/BrandLanding', [
            'brand' => $brand,
            'products' => $products,
            'categories' => $categories,
            'relatedServices' => $groupedServices,
            'breadcrumb_image' => $brand->breadcrumb_image,
            'show_breadcrumb' => $brand->show_breadcrumb,
            'seo' => $seo
        ]);
    }

    /**
     * Brand Product List Page (/{brandSlug}/products)
     */
    public function productList(Request $request, string $brandSlug)
    {
        \Log::info("BrandController@productList HIT with: " . $brandSlug);
        $brand = Brand::where('slug', $brandSlug)
            ->orWhere('name', 'LIKE', str_replace('-', ' ', $brandSlug))
            ->first();

        if (!$brand) {
            abort(404);
        }

        // Base Query
        $query = $brand->products()->where('is_active', true)->with(['service', 'categories']);

        // Filter by Search Term
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Filter by Device Category (ProductCategory)
        if ($request->filled('category')) {
            $categorySlug = $request->input('category');
            $query->whereHas('categories', function ($q) use ($categorySlug) {
                 $q->where('slug', $categorySlug);
            });
        }

        // Filter by Service Item (ServiceSolution)
        if ($request->filled('service_item')) {
            $solutionSlug = $request->input('service_item');
            $query->whereHas('solutions', function ($q) use ($solutionSlug) {
                 $q->where('slug', $solutionSlug);
            });
        }

        // Sort
        $sort = $request->input('sort', 'newest');
        if ($sort === 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($sort === 'price_desc') {
             $query->orderBy('price', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(12)->withQueryString();

        // Device Categories available for this brand
        $categories = \Modules\ProductCatalog\Models\ProductCategory::whereHas('products', function ($q) use ($brand) {
            $q->where('brand_id', $brand->id)->where('is_active', true);
        })->orderBy('sort_order')->get()->map(function ($cat) use ($brand) {
            return (object) [
                'name' => $cat->name,
                'slug' => $cat->slug,
                'image' => $cat->icon,
                'count' => $cat->products()->where('brand_id', $brand->id)->where('is_active', true)->count()
            ];
        });

        // Service Solutions available for this brand
        $serviceSolutions = ServiceSolution::whereHas('products', function($q) use ($brand, $request) {
              $q->where('brand_id', $brand->id)->where('is_active', true);
              if ($request->filled('category')) {
                  $q->whereHas('categories', fn($c) => $c->where('slug', $request->input('category')));
              }
        })
        ->get()
        ->map(function($sol) use ($brand, $request) {
            $countQuery = Product::where('brand_id', $brand->id)->where('is_active', true);
            $countQuery->whereHas('solutions', fn($s) => $s->where('service_solutions.id', $sol->id));

            if ($request->filled('category')) {
                $countQuery->whereHas('categories', fn($c) => $c->where('slug', $request->input('category')));
            }

            return [
               'id' => $sol->id,
               'name' => $sol->title,
               'slug' => $sol->slug,
               'count' => $countQuery->count()
            ];
        })
        ->filter(function($item) { return $item['count'] > 0; })
        ->values();

        return Inertia::render('Partners/BrandProductList', [
            'brand' => $brand,
            'products' => $products,
            'categories' => $categories,
            'serviceSolutions' => $serviceSolutions,
            'serviceItemLabel' => 'Solutions',
            'filters' => $request->all(),
            'seo' => SeoResolver::for($brand)
        ]);
    }
}
