<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;



Route::get('/login', function () {
    return redirect()->route('filament.activioncms.auth.login');
})->name('login');

Route::get('/search', [\App\Http\Controllers\SearchController::class, 'index'])->name('search');

Route::get('/about', function () {
    $page = \App\Models\Page::where('slug', 'about')->first();
    return Inertia::render('About', [
        'breadcrumb_image' => $page?->breadcrumb_image,
        'show_breadcrumb' => $page?->show_breadcrumb ?? true,
        'seo' => \App\Services\SeoResolver::staticPage('About Us', 'Akses cepat ke fitur-fitur penting sistem, termasuk dasbor untuk gambaran umum operasional.')
    ]);
})->name('about');

Route::post('/form/submit', [\App\Http\Controllers\FormSubmissionController::class, 'store'])->name('form.submit')->middleware('throttle:10,1');
Route::post('/form/view', [\App\Http\Controllers\FormSubmissionController::class, 'trackView'])->name('form.view');

// WhatsApp Redirect Route
Route::get('/wa', [\App\Http\Controllers\WhatsAppController::class, 'redirect'])->name('whatsapp.redirect');

// Services routes are now handled by Modules/ServiceSolutions

Route::get('/projects', function () {
    $page = \App\Models\Page::where('slug', 'projects')->first();
    return Inertia::render('Projects', [
        'breadcrumb_image' => $page?->breadcrumb_image,
        'show_breadcrumb' => $page?->show_breadcrumb ?? true,
    ]);
})->name('projects');

Route::get('/partners', function () {
    $brands = \Modules\Core\Models\Brand::all()->map(function($b) {
        $resolvePath = function($path) {
            if (!$path) return null;
            if (str_starts_with($path, 'http')) return $path;
            if (str_starts_with($path, 'assets') || str_starts_with($path, '/assets')) {
                return str_starts_with($path, '/') ? $path : "/{$path}";
            }
            return "/storage/{$path}";
        };
        // Normalize categories to array
        $cats = $b->category;
        if (is_string($cats)) {
            // First try JSON, then try comma split
            $decoded = json_decode($cats, true);
            if (is_array($decoded)) {
                $cats = $decoded;
            } else {
                // If it's a plain string, we treat it as a SINGLE category
                // This allows commas in category names (requested by user)
                $cats = [$cats];
            }
        }
        if (empty($cats)) {
            $cats = ['General'];
        }

        return [
            'id' => $b->id,
            'name' => $b->name,
            'slug' => $b->slug,
            'image' => $resolvePath($b->thumbnail ?? $b->logo_path ?? $b->image),
            'website_url' => $b->website_url,
            'categories' => $cats, // Changed from 'category' to 'categories'
            'is_featured' => (bool)$b->is_featured
        ];
    });

    // Flatten all categories from all brands to get unique ones with counts
    $allMappedCats = [];
    foreach ($brands as $brand) {
        foreach ($brand['categories'] as $catName) {
            if (!isset($allMappedCats[$catName])) {
                $allMappedCats[$catName] = 0;
            }
            $allMappedCats[$catName]++;
        }
    }

    $categories = collect($allMappedCats)->map(function ($count, $name) {
        return [
            'name' => $name,
            'count' => $count,
        ];
    })->values();

    $page = \App\Models\Page::where('slug', 'partners')->first();
    return Inertia::render('Partners', [
        'brands' => $brands,
        'categories' => $categories,
        'breadcrumb_image' => $page?->breadcrumb_image,
        'show_breadcrumb' => $page?->show_breadcrumb ?? true,
        'seo' => \App\Services\SeoResolver::staticPage('Our Partners', 'Discover our authorized partners and brands.')
    ]);
})->name('partners');

Route::get('/clients', function () {
    $category = request('category');

    $clientsQuery = \App\Models\Client::where('is_active', true);

    if ($category && $category !== 'All Clients') {
        $clientsQuery->where(function($q) use ($category) {
            $q->where('category', $category)
              ->orWhere('category', 'LIKE', '%"'.$category.'"%');
        });
    }

    $clients = $clientsQuery->orderBy('position')
        ->paginate(15)
        ->through(function($c) {
            $resolvePath = function($path) {
                if (!$path) return null;
                if (str_starts_with($path, 'http')) return $path;
                if (str_starts_with($path, 'assets') || str_starts_with($path, '/assets')) {
                    return str_starts_with($path, '/') ? $path : "/{$path}";
                }
                return "/storage/{$path}";
            };
            
            $cats = $c->category;
            if ($cats) {
                $decoded = json_decode($cats, true);
                if (is_array($decoded)) {
                    $cats = $decoded;
                } else {
                    $cats = [$cats];
                }
            } else {
                $cats = ['General'];
            }
            
            return [
                'id' => $c->id,
                'name' => $c->name,
                'image' => $resolvePath($c->logo),
                'website_url' => $c->website_url,
                'website_rel' => \App\Helpers\SeoHelper::get_rel($c->website_url),
                'categories' => $cats, 
                'is_featured' => true 
            ];
        });

    // Get categories count from ALL active clients for the sidebar
    $allActiveClients = \App\Models\Client::where('is_active', true)->get();
    $allMappedCats = [];
    foreach ($allActiveClients as $client) {
        $cats = $client->category;
        if ($cats) {
            $decoded = json_decode($cats, true);
            $catsArray = is_array($decoded) ? $decoded : [$cats];
        } else {
            $catsArray = ['General'];
        }

        foreach ($catsArray as $catName) {
            if (!isset($allMappedCats[$catName])) {
                $allMappedCats[$catName] = 0;
            }
            $allMappedCats[$catName]++;
        }
    }

    $categories = collect($allMappedCats)->map(function ($count, $name) {
        return [
            'name' => $name,
            'count' => $count,
        ];
    })->values();

    $page = \App\Models\Page::where('slug', 'clients')->first();
    return Inertia::render('Clients', [
        'clients' => $clients,
        'categories' => $categories,
        'filters' => [
            'category' => $category
        ],
        'page_title' => $page?->title ?? 'Our Clients',
        'breadcrumb_image' => $page?->breadcrumb_image,
        'show_breadcrumb' => $page?->show_breadcrumb ?? true,
        'seo' => \App\Services\SeoResolver::staticPage('Our Clients', 'Our trusted clients across various industries.')
    ]);
})->name('clients');

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

// Product and Configurator API routes are now handled by Modules/Core

// Projects Page
Route::get('/projects', [\App\Http\Controllers\ProjectController::class, 'index'])->name('projects.index');

// News Page
Route::get('/news', [\App\Http\Controllers\NewsController::class, 'index'])->name('news.index');
Route::get('/news/category/{slug}', [\App\Http\Controllers\NewsController::class, 'category'])->name('news.category');
Route::get('/news/tag/{slug}', [\App\Http\Controllers\NewsController::class, 'tag'])->name('news.tag');

// Room Configurator Page
Route::get('/room-configurator', function () {
    return Inertia::render('RoomConfigurator');
})->name('room.configurator');

Route::match(['get', 'post'], '/room-configurator/complete', function (\Illuminate\Http\Request $request) {
    return Inertia::render('ConfiguratorComplete', [
        'selection' => $request->input('selection'),
        'userInfo' => $request->input('userInfo'),
        'uuid' => $request->input('uuid')
    ]);
})->name('room.configurator.complete');

// Server Configurator Routes
Route::get('/server-configurator', function () {
    return Inertia::render('ServerConfigurator');
})->name('server.configurator');

Route::match(['get', 'post'], '/server-configurator/complete', function (\Illuminate\Http\Request $request) {
    return Inertia::render('ConfiguratorComplete', [
        'selection' => $request->input('selection'),
        'userInfo' => $request->input('userInfo'),
        'uuid' => $request->input('uuid')
    ]);
})->name('server.configurator.complete');

// Surveillance Configurator Routes
Route::get('/surveillance-configurator', function () {
    return Inertia::render('SurveillanceConfigurator');
})->name('surveillance.configurator');

Route::match(['get', 'post'], '/surveillance-configurator/complete', function (\Illuminate\Http\Request $request) {
    return Inertia::render('ConfiguratorComplete', [
        'selection' => $request->input('selection'),
        'userInfo' => $request->input('userInfo'),
        'uuid' => $request->input('uuid')
    ]);
})->name('surveillance.configurator.complete');

// Dynamic Configurator Route
Route::get('/configurator/{slug}', [\Modules\ServiceSolutions\Http\Controllers\DynamicConfiguratorController::class, 'show'])->name('configurator.show');

Route::match(['get', 'post'], '/configurator/complete', function (\Illuminate\Http\Request $request) {
    $selection = $request->input('selection', []);
    $configurator = $request->input('configurator');
    $summaryItems = collect();
    $quantities = $selection['quantities'] ?? [];

    if ($configurator && isset($configurator['steps'])) {
        foreach ($configurator['steps'] as $step) {
            if (!isset($step['questions'])) continue;

            foreach ($step['questions'] as $question) {
                // Check if this question has a selection
                $varName = $question['variable_name'];
                if (!isset($selection[$varName])) continue;

                $selectedValue = $selection[$varName];

                // If selection represents multiple values (checkboxes), handle array
                $selectedValues = is_array($selectedValue) ? $selectedValue : [$selectedValue];

                foreach ($selectedValues as $val) {
                    // Find the matching option
                    $option = collect($question['options'])->firstWhere('value', $val);

                    if ($option) {
                        // Priority 1: Linked Product
                        if (!empty($option['products'])) {
                            foreach ($option['products'] as $product) {
                                $summaryItems->push([
                                    'id' => $product['id'],
                                    'step_label' => $step['title'] ?? 'Config',
                                    'question_label' => $question['label'], // "Select your Room Size"
                                    'name' => $product['name'],
                                    'image' => $product['image_path'] ?? $product['image'] ?? null,
                                    'sku' => $product['sku'] ?? '',
                                    'quantity' => $quantities[$product['id']] ?? 1,
                                    'type' => 'product'
                                ]);
                            }
                        } else {
                            // Priority 2: Standard Option (Text/Card)
                            $summaryItems->push([
                                'id' => $option['id'] ?? uniqid(),
                                'step_label' => $step['title'] ?? 'Config',
                                'question_label' => $question['label'], // "Select your Room Size"
                                'name' => $option['label'],
                                'image' => $option['image_path'] ?? $option['image'] ?? null,
                                'sku' => '-', // No SKU for generic option
                                'quantity' => 1,
                                'type' => 'option'
                            ]);
                        }
                    }
                }
            }
        }
    } else {
        // Fallback for direct product ID selections (Legacy/Simple Mode)
        $ids = collect($selection)->flatten()->filter(function ($value) {
            return is_string($value) || is_int($value);
        })->unique()->values();

        if ($ids->isNotEmpty()) {
            $products = \Modules\Core\Models\Product::whereIn('id', $ids)->get();
            $summaryItems = $products->map(function ($product) use ($quantities) {
                return [
                    'id' => $product->id,
                    'step_label' => 'Product Selection',
                    'question_label' => 'Selected Item',
                    'name' => $product->name,
                    'image' => $product->image_path,
                    'sku' => $product->sku,
                    'quantity' => $quantities[$product->id] ?? 1,
                    'type' => 'product'
                ];
            });
        }
    }

    return Inertia::render('ConfiguratorComplete', [
        'selection' => $selection,
        'userInfo' => $request->input('userInfo'),
        'uuid' => $request->input('uuid'),
        'configurator' => $request->input('configurator'),
        'summaryItems' => $summaryItems
    ]);
})->name('configurator.complete');

// Brand routes are now handled by Modules/Core - MIGRATED TO MAIN DUE TO PRIORITY ISSUE
// Brand Product List Page
Route::get('/{brandSlug}/products', function ($brandSlug) {
    // 1. Fetch Brand
    $brand = \Modules\Core\Models\Brand::where('slug', $brandSlug)
        ->orWhere('name', 'LIKE', str_replace('-', ' ', $brandSlug))
        ->first();

    if (!$brand) {
        abort(404);
    }

    // 2. Base Query
    $query = $brand->products()->where('is_active', true)->with(['service', 'categories']);

    // 3. Filter by Search Term
    if (request()->filled('search')) {
        $searchTerm = request()->input('search');
        $query->where(function ($q) use ($searchTerm) {
            $q->where('name', 'LIKE', "%{$searchTerm}%")
              ->orWhere('description', 'LIKE', "%{$searchTerm}%");
        });
    }

    // 4. Filter by Device Category (ProductCategory)
    if (request()->filled('category')) {
        $categorySlug = request()->input('category');
        $query->whereHas('categories', function ($q) use ($categorySlug) {
             $q->where('slug', $categorySlug);
        });
    }

    // Filter by Service Item (ServiceSolution)
    if (request()->filled('service_item')) {
        $solutionSlug = request()->input('service_item');
        $query->whereHas('solutions', function ($q) use ($solutionSlug) {
             $q->where('slug', $solutionSlug);
        });
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
    // REFACTOR: Use ProductCategory here too? No, "Categories" in Sidebar usually means Product Type.
    // The previous code fetched 'Service' as Categories.
    // The user wanted "ProductCategory" (Device Type).
    $categories = \Modules\Core\Models\ProductCategory::whereHas('products', function ($q) use ($brand) {
        $q->where('brand_id', $brand->id)->where('is_active', true);
    })->orderBy('sort_order')->get()->map(function ($cat) use ($brand) {
        return (object) [
            'name' => $cat->name,
            'slug' => $cat->slug,
            'image' => $cat->icon,
            'count' => $cat->products()->where('brand_id', $brand->id)->where('is_active', true)->count()
        ];
    });

    // 6. Get Service Items (Solutions) available for this brand
    $serviceSolutions = collect([]);
    $serviceItemLabel = 'Solutions';

    // Enable dynamic solution filtering based on current context (Category + Brand)
    $serviceSolutions = \Modules\ServiceSolutions\Models\ServiceSolution::whereHas('products', function($q) use ($brand) {
          $q->where('brand_id', $brand->id)->where('is_active', true);
          if (request()->filled('category')) {
              $q->whereHas('categories', fn($c) => $c->where('slug', request()->input('category')));
          }
    })
    ->get()
    ->map(function($sol) use ($brand) {
        $countQuery = \Modules\Core\Models\Product::where('brand_id', $brand->id)->where('is_active', true);

        // FIX: Qualify 'id' to avoid ambiguity (SQL integrity violation 1052)
        $countQuery->whereHas('solutions', fn($s) => $s->where('service_solutions.id', $sol->id));

        if (request()->filled('category')) {
            $countQuery->whereHas('categories', fn($c) => $c->where('slug', request()->input('category')));
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

    return Inertia::render('BrandProductList', [
        'brand' => $brand,
        'products' => $products,
        'categories' => $categories,
        'serviceSolutions' => $serviceSolutions,
        'serviceItemLabel' => $serviceItemLabel, // "Solutions"
        'filters' => request()->all(),
        'seo' => \App\Services\SeoResolver::for($brand)
    ]);
})->name('brand.products');

// Page Builder Routes
Route::middleware(['auth', 'verified'])->prefix('admin')->group(function () {
    Route::get('/page-builder/{page}', [\App\Http\Controllers\Admin\PageBuilderController::class, 'edit'])->name('admin.page-builder.edit');
    Route::post('/page-builder/{page}/save', [\App\Http\Controllers\Admin\PageBuilderController::class, 'update'])->name('admin.page-builder.update');

    // Simple Media Upload Helper
    Route::post('/upload-media', [\App\Http\Controllers\Admin\PageBuilderController::class, 'upload'])->name('admin.upload-media');
});

// Dynamic Pages (CMS)
use App\Http\Controllers\PageController;

Route::get('/', [PageController::class, 'resolveHomepage'])->name('home');
Route::get('/{slug}', [\App\Http\Controllers\DynamicResolverController::class, 'resolve'])
    ->name('dynamic.resolve')
    ->where('slug', '^(?!events|services|products|configurator|admin|nova|api|storage|build|assets|favicon).*$');

