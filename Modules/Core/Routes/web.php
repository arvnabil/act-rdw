<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Modules\Core\Models\Product;
use Modules\Core\Models\Brand;
use Modules\ServiceSolutions\Models\Service;
use Modules\ServiceSolutions\Models\ServiceSolution;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Products List
Route::get('/products', [App\Http\Controllers\ProductController::class, 'index'])->name('products');

// Product Detail
Route::get('/products/{slug}', [App\Http\Controllers\ProductController::class, 'show'])->name('products.show');

// Configurator API for Modal
Route::get('/configurator/product-details/{id}', function ($id) {
    return Product::with(['brand', 'service'])->findOrFail($id);
});

// Brand Product List Page
Route::get('/{brandSlug}/products', function ($brandSlug) {
    // 1. Fetch Brand
    $brand = Brand::where('slug', $brandSlug)
        ->orWhere('name', 'LIKE', str_replace('-', ' ', $brandSlug))
        ->first();

    if (!$brand) {
        abort(404);
    }

    // 2. Base Query
    $query = $brand->products()->where('is_active', true)->with('service');

    // 3. Filter by Category (Service)
    if (request()->has('category')) {
        $categorySlug = request()->input('category');
        $query->whereHas('service', function ($q) use ($categorySlug) {
             $q->where('slug', $categorySlug)
               ->orWhere('name', 'LIKE', $categorySlug);
        });
    }

    // Filter by Service Item (ServiceSolution)
    if (request()->has('service_item')) {
        $solutionSlug = request()->input('service_item');
        $solution = ServiceSolution::where('slug', $solutionSlug)->first();
        if ($solution) {
             $query->whereJsonContains('tags', $solution->title);
        }
    }

    // 4. Sort
    $sort = request()->input('sort', 'newest');
    if ($sort === 'price_asc') {
        $query->orderBy('price', 'asc');
    } elseif ($sort === 'price_desc') {
         $query->orderBy('price', 'desc');
    } else {
        $query->orderBy('created_at', 'desc');
    }

    $products = $query->paginate(12)->withQueryString();

    // 5. Get filtering options (Services available for this brand)
    $categories = Service::whereHas('products', function ($q) use ($brand) {
        $q->where('brand_id', $brand->id)->where('is_active', true);
    })->get()->map(function($service) use ($brand) {
        return [
            'id' => $service->id,
            'name' => $service->name,
            'slug' => $service->slug ?? \Illuminate\Support\Str::slug($service->name),
            'count' => $service->products()->where('brand_id', $brand->id)->where('is_active', true)->count()
        ];
    });

    // 6. Get Service Items (Solutions) available for this brand
    $serviceSolutions = collect([]);
    $serviceItemLabel = 'Solutions';

    $categorySlug = request()->input('category');
    if ($categorySlug) {
        $selectedService = Service::where('slug', $categorySlug)
            ->orWhere('name', 'LIKE', $categorySlug)
            ->first();

        if ($selectedService) {
             $serviceItemLabel = $selectedService->name . ' Solutions';

             $serviceSolutions = ServiceSolution::where('service_id', $selectedService->id)
                ->whereHas('brands', function($q) use ($brand) {
                    $q->where('brands.id', $brand->id);
                })
                ->get()
                ->map(function($sol) use ($brand) {
                      $count = Product::where('brand_id', $brand->id)
                            ->where('is_active', true)
                            ->whereJsonContains('tags', $sol->title)
                            ->count();
                      return [
                          'id' => $sol->id,
                          'name' => $sol->title,
                          'slug' => $sol->slug,
                          'count' => $count
                      ];
                })
                ->filter(function($item) { return $item['count'] > 0; })
                ->values();
        }
    }

    return Inertia::render('BrandProductList', [
        'brand' => $brand,
        'products' => $products,
        'categories' => $categories,
        'serviceSolutions' => $serviceSolutions,
        'serviceItemLabel' => $serviceItemLabel,
        'filters' => request()->all()
    ]);
})->name('brand.products');

// Brand Landing Page (Catch-all for top-level slugs)
Route::get('/{brandSlug}', function ($brandSlug) {
    // Attempt to match by slug or name
    $brand = Brand::where('slug', $brandSlug)
        ->orWhere('name', 'LIKE', str_replace('-', ' ', $brandSlug))
        ->orWhere('name', 'LIKE', $brandSlug)
        ->first();

    if (!$brand) {
        abort(404);
    }

    // Generic Categories for Brand Landing (Dummy Data)
    $categories = collect([
        ['name' => 'Keyboard', 'slug' => 'keyboard', 'image' => '/assets/img/product/product_1_1.png'],
        ['name' => 'Mouse', 'slug' => 'mouse', 'image' => '/assets/img/product/product_1_2.png'],
        ['name' => 'Kamera Ruang Konferensi', 'slug' => 'conference-camera', 'image' => '/assets/img/product/logitech meetup.jpg'],
        ['name' => 'Headset', 'slug' => 'headset', 'image' => '/assets/img/product/product_1_4.png'],
        ['name' => 'Keyboard Case iPad', 'slug' => 'ipad-keyboard', 'image' => '/assets/img/product/product_1_5.png'],
        ['name' => 'Mouse Pad', 'slug' => 'mouse-pad', 'image' => '/assets/img/product/product_1_6.png'],
        ['name' => 'Combo', 'slug' => 'combo', 'image' => '/assets/img/product/product_1_7.png'],
        ['name' => 'Speaker', 'slug' => 'speaker', 'image' => '/assets/img/product/product_1_8.png'],
        ['name' => 'Mengemudi', 'slug' => 'driving', 'image' => '/assets/img/product/product_1_3.png'],
        ['name' => 'Webcam', 'slug' => 'webcam', 'image' => '/assets/img/product/product_1_9.png'],
        ['name' => 'Gamepad', 'slug' => 'gamepad', 'image' => '/assets/img/product/product_1_1.png'],
    ])->map(function($cat) {
        return (object) $cat;
    });

    // Dynamic Service Solutions
    $serviceSolutions = $brand->serviceSolutions()->with('service')->orderBy('sort_order')->get();

    $groupedServices = $serviceSolutions->groupBy('service_id')->map(function($group) {
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
            'solutions' => $group->map(function($sol) {
                return [
                    'title' => $sol->title,
                    'slug' => $sol->slug,
                    'image' => $sol->thumbnail,
                    'desc' => $sol->subtitle ?? \Illuminate\Support\Str::limit($sol->description, 50)
                ];
            })->values()
        ];
    })->filter()->values();

    // Dummy Products for "Terbaru"
    $products = collect([
        [
            'name' => 'MX Master 3S',
            'image_path' => '/assets/img/benefit/benefit_bg_1_1.jpg',
            'price' => 1869000,
            'category' => 'Mouse',
            'is_active' => true
        ],
        [
            'name' => 'K580 Slim Multi-Device',
            'image_path' => '/assets/img/benefit/benefit_bg_1_2.jpg',
            'price' => 759000,
            'category' => 'Keyboard',
            'is_active' => true
        ],
        [
            'name' => 'Logitech Zone Vibe 100',
            'image_path' => '/assets/img/benefit/benefit_bg_1_3.jpg',
            'price' => 1449000,
            'category' => 'Headset',
            'is_active' => true
        ],
        [
            'name' => 'Lift Vertical Ergonomic',
            'image_path' => '/assets/img/benefit/benefit_bg_1_4.jpg',
            'price' => 1099000,
            'category' => 'Mouse',
            'is_active' => true
        ],
        [
            'name' => 'Pop Keys',
            'image_path' => '/assets/img/benefit/benefit_bg_1_1.jpg',
            'price' => 1589000,
            'category' => 'Keyboard',
            'is_active' => true
        ],
        [
            'name' => 'StreamCam',
            'image_path' => '/assets/img/benefit/benefit_bg_1_2.jpg',
            'price' => 2299000,
            'category' => 'Webcam',
            'is_active' => true
        ],
    ])->map(function($prod) {
        return (object) $prod;
    });

    return Inertia::render('BrandLanding', [
        'brand' => $brand,
        'products' => $products,
        'categories' => $categories,
        'relatedServices' => $groupedServices
    ]);
})->where('brandSlug', '^(?!services|products|news|partners|projects|events|admin|nova|api|storage|assets).*$')->name('brand.landing');
