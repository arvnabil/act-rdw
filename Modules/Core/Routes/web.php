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
// Brand Landing Page is now handled by the Global Dynamic Resolver (DynamicResolverController)
// which delegates to BrandController.

// Brand Product List Route moved to main routes/web.php to avoid catch-all conflict.
