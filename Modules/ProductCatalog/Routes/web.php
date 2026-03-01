<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Modules\ProductCatalog\Models\Product;
use Modules\ProductCatalog\Models\Brand;
use Modules\Services\Models\Service;
use Modules\Services\Models\ServiceSolution;
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
Route::get('/products', [\Modules\ProductCatalog\Http\Controllers\ProductController::class, 'index'])->name('products');

// Fallback detail product route
Route::get('/products/{slug}', [\Modules\ProductCatalog\Http\Controllers\ProductController::class, 'show'])->name('products.show');

// Configurator API for Modal
Route::get('/configurator/product-details/{id}', function ($id) {
    return Product::with(['brand', 'service'])->where('is_active', true)->findOrFail($id);
});

// Brand Product List Page
// Brand Landing Page is now handled by the Global Dynamic Resolver (DynamicResolverController)
// which delegates to BrandController.

// Brand Product List Route moved to main routes/web.php to avoid catch-all conflict.
