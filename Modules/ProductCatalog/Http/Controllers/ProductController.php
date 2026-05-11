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
    public function index(Request $request, \Modules\ProductCatalog\Services\ProductService $service)
    {
        $filters = $request->only(['search', 'orderby', 'brand', 'solution', 'category']);
        $data = $service->getIndexData($filters);

        return Inertia::render('Products/Index', array_merge($data, [
            'filters' => $filters,
            'seo' => \Modules\SEO\Services\SeoResolver::staticPage('Products', 'Browse our wide range of technology solutions from top brands.')
        ]));
    }

    /**
     * Display the specified product.
     */
    public function show($slug, \Modules\ProductCatalog\Services\ProductService $service)
    {
        $productData = $service->getDetailData($slug);
        
        // Find the raw model for the SEO resolver
        $productModel = Product::where('slug', $slug)->first();

        return Inertia::render('Products/Detail', [
            'product' => $productData,
            'seo' => \Modules\SEO\Services\SeoResolver::for($productModel),
        ]);
    }
}
