<?php

namespace Modules\ProductCatalog\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Modules\ProductCatalog\Models\Product;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with(['brand', 'service', 'categories'])
            ->where('is_active', true);

        // Filter by Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by Brand
        if ($request->filled('brand')) {
            $query->where('brand_id', $request->input('brand'));
        }

        // Filter by Solution
        if ($request->filled('solution')) {
            $query->whereHas('solutions', function($q) use ($request) {
                $q->where('service_solutions.id', $request->input('solution'));
            });
        }

        // Filter by Category
        if ($request->filled('category')) {
            $catId = $request->input('category');
            $query->whereHas('categories', function($q) use ($catId) {
                $q->where('product_categories.id', $catId);
            });
        }

        // Sorting
        $sort = $request->input('orderby', 'menu_order');
        if($sort === 'date') {
            $query->orderBy('created_at', 'desc');
        } elseif ($sort === 'name') {
            $query->orderBy('name', 'asc');
        } else {
             $query->orderBy('id', 'desc'); // Default
        }

        $products = $query->paginate(9)->withQueryString();

        // Fetch Filter Options
        $brands = \Modules\ProductCatalog\Models\Brand::orderBy('name')->get(['id', 'name']);
        $solutions = \Modules\Services\Models\ServiceSolution::orderBy('title')->get(['id', 'title']);
        $categories = \Modules\ProductCatalog\Models\ProductCategory::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        
        $page = \Modules\CMS\Models\Page::where('slug', 'products')->first();

        return Inertia::render('Products/Index', [
            'products' => $products,
            'brands' => $brands,
            'solutions' => $solutions,
            'categories' => $categories,
            'page_title' => $page?->title ?? 'All Products',
            'breadcrumb_image' => $page?->breadcrumb_image,
            'show_breadcrumb' => $page?->show_breadcrumb ?? true,
            'filters' => $request->only(['search', 'orderby', 'brand', 'solution', 'category']),
            'seo' => \Modules\SEO\Services\SeoResolver::staticPage('Products', 'Browse our wide range of technology solutions from top brands.')
        ]);
    }

    /**
     * Display the specified product.
     */
    public function show($slug)
    {
        $product = Product::with(['brand', 'service', 'solutions', 'categories'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Get related products from the same service or brand
        $relatedProducts = Product::with(['brand', 'service', 'categories'])
            ->where('service_id', $product->service_id)
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get()
            ->map(function ($related) {
                return [
                    'id' => $related->id, // Ideally slug, but using ID for now to match file or existing logic
                    'name' => $related->name,
                    'image_path' => $related->image_path,
                    'tag' => $related->tags ? $related->tags[0] ?? null : null,
                    'category' => $related->categories->pluck('name')->join(', ') ?: ($related->service?->name ?? 'General'),
                    'slug' => $related->slug,
                    // Map price for ProductCard
                    'price' => $related->price,
                    // Map brand for ProductCard
                    'brand' => [
                         'name' => $related->brand?->name ?? '',
                         'logo' => $related->brand?->logo ?? ''
                    ]
                ];
            });

        // Transform product for the view
        $productData = [
            'id' => $product->id,
            'slug' => $product->slug,
            'name' => $product->name,
            'subtitle' => $product->seo?->description,
            'cta_label' => "CTA Produk: {$product->name}",
            'image' => $product->image_path ? "/storage/" . $product->image_path : null,
            'image_path' => $product->image_path,
            'breadcrumb_image' => $product->breadcrumb_image ?: ($product->thumbnail_path ?: $product->image_path),
            'show_breadcrumb' => $product->show_breadcrumb ?? true,
            'sku' => $product->sku,
                'solution_type' => $product->solutions
                    ->where('service_id', $product->service_id)
                    ->pluck('title')
                    ->unique()
                    ->join(', ') ?: $product->solution_type,
                'solutions_list' => $product->solutions
                    ->where('service_id', $product->service_id)
                    ->unique('title')
                    ->map(function($s) {
                        return ['id' => $s->id, 'title' => $s->title, 'slug' => $s->slug];
                    })->values(),
                'datasheet_url' => $product->datasheet_url,
                'datasheet_rel' => \Modules\SEO\Helpers\SeoHelper::get_rel($product->datasheet_url),
                'description' => $product->description,
                'category' => $product->categories->pluck('name')->join(', ') ?: ($product->service?->name ?? 'General'),
                'categories_list' => $product->categories->map(function($c) {
                    return ['id' => $c->id, 'name' => $c->name, 'slug' => $c->slug];
                })->values(),
                'brand' => [
                'name' => $product->brand->name,
                'logo' => $product->brand->logo,
                'slug' => $product->brand->slug,
                'config' => $product->brand->landing_config,
            ],
            'tags' => $product->tags ?? [],
            'specification' => $product->specs,
            'specification_text' => $product->specification_text,
            'features' => $product->features ?? [],
            'features_text' => $product->features_text,
            'related_products' => $relatedProducts,
            'link_accommerce' => $product->link_accommerce,
            'link_accommerce_rel' => \Modules\SEO\Helpers\SeoHelper::get_rel($product->link_accommerce),
            'whatsapp_note' => $product->whatsapp_note,
        ];

        return Inertia::render('Products/Detail', [
            'product' => $productData,
            'seo' => \Modules\SEO\Services\SeoResolver::for($product),
        ]);
    }
}
