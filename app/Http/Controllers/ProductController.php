<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with(['brand', 'service'])
            ->where('is_active', true);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
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

        return Inertia::render('Products', [
            'products' => $products,
            'filters' => $request->only(['search', 'orderby']),
        ]);
    }

    /**
     * Display the specified product.
     */
    public function show($slug)
    {
        $product = Product::with(['brand', 'service'])
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
                ];
            });

        // Transform product for the view
        $productData = [
            'name' => $product->name,
            'image' => $product->image_path,
            'sku' => $product->sku,
            'solution_type' => $product->solution_type,
            'datasheet_url' => $product->datasheet_url,
            'description' => $product->description,
            'category' => $product->service->name,
            'tags' => $product->tags ?? [],
            'specification' => $product->specs ?? [], // Map specs column to specification
            'specification_text' => $product->specification_text,
            'features' => $product->features ?? [],
            'features_text' => $product->features_text,
            'related_products' => $relatedProducts,
        ];

        return Inertia::render('ProductDetail', [
            'product' => $productData,
        ]);
    }
}
