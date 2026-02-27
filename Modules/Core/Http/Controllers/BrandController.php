<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Core\Models\Brand;
use Modules\Core\Models\Product;
use Modules\ServiceSolutions\Models\Service;
use Modules\ServiceSolutions\Models\ServiceSolution;
use App\Services\SeoResolver;

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
        $categories = \Modules\Core\Models\ProductCategory::whereHas('products', function ($q) use ($brand) {
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

        return Inertia::render('BrandLanding', [
            'brand' => $brand,
            'products' => $products,
            'categories' => $categories,
            'relatedServices' => $groupedServices,
            'breadcrumb_image' => $brand->breadcrumb_image,
            'show_breadcrumb' => $brand->show_breadcrumb,
            'seo' => $seo
        ]);
    }
}
