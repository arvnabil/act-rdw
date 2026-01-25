<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Core\Models\Product;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with(['brand', 'service', 'category'])
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
            $query->where('product_category_id', $request->input('category'));
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
        $brands = \Modules\Core\Models\Brand::orderBy('name')->get(['id', 'name']);
        $solutions = \Modules\ServiceSolutions\Models\ServiceSolution::orderBy('title')->get(['id', 'title']);
        $categories = \Modules\Core\Models\ProductCategory::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Products', [
            'products' => $products,
            'brands' => $brands,
            'solutions' => $solutions,
            'categories' => $categories,
            'filters' => $request->only(['search', 'orderby', 'brand', 'solution', 'category']),
        ]);
    }

    /**
     * Display the specified product.
     */
    public function show($slug)
    {
        $product = Product::with(['brand', 'service', 'solutions', 'category'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Get related products from the same service or brand
        $relatedProducts = Product::where('service_id', $product->service_id)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get()
            ->map(function ($related) {
                return [
                    'id' => $related->id, // Ideally slug, but using ID for now to match file or existing logic
                    'name' => $related->name,
                    'image' => $related->image_path,
                    'tag' => $related->tags ? $related->tags[0] ?? null : null,
                    'category' => $related->service->name,
                    'slug' => $related->slug,
                    // Map price for ProductCard
                    'price' => $related->price,
                    // Map brand for ProductCard
                    'brand' => [
                         'name' => $related->brand->name ?? '',
                         'logo' => $related->brand->logo ?? ''
                    ]
                ];
            });

        // Transform product for the view
        $productData = [
            'name' => $product->name,
            'image' => $product->image_path ? "/storage/" . $product->image_path : null,
            'sku' => $product->sku,
            'solution_type' => $product->solutions->pluck('title')->join(', ') ?: $product->solution_type,
            'datasheet_url' => $product->datasheet_url,
            'description' => $product->description,
            'category' => $product->category->name ?? $product->service->name,
            'brand' => [
                'name' => $product->brand->name,
                'logo' => $product->brand->logo,
            ],
            'tags' => $product->tags ?? [],
            'specification' => collect($product->specs ?? [])->map(function($value, $key) {
                return ['name' => $key, 'value' => $value];
            })->values()->all(),
            'specification_text' => $product->specification_text,
            'features' => $product->features ?? [], // Features is now a Repeater (Array of Objects), so we pass it directly.
            // If legacy data exists (KeyValue format), the frontend might need to handle it or we migrate it.
            // For now, assuming new data will be Repeater format. If it's an array of objects, passing directly is fine.
            // If it's the old KeyValue object {"Key": "Value"}, we might want to normalize it, but let's assume valid data for now.
            'features_text' => $product->features_text,
            'related_products' => $relatedProducts,
            'link_accommerce' => $product->link_accommerce,
            'whatsapp_note' => $product->whatsapp_note,
        ];

        return Inertia::render('ProductDetail', [
            'product' => $productData,
        ]);
    }
}
