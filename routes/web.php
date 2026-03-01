<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ─── Auth ───────────────────────────────────────────────
Route::get('/login', function () {
    return redirect()->route('filament.activioncms.auth.login');
})->name('login');

// ─── Search ─────────────────────────────────────────────
// Moved to Modules/Search/Routes/web.php

// ─── Form Submission ────────────────────────────────────
// Moved to Modules/FormBuilder/Routes/web.php

// ─── WhatsApp ───────────────────────────────────────────
// Moved to Modules/WhatsApp/Routes/web.php

// ─── Projects ───────────────────────────────────────────
// Moved to Modules/Projects/Routes/web.php

// ─── Clients ────────────────────────────────────────────
// Moved to Modules/Clients/Routes/web.php

// ─── News ───────────────────────────────────────────────
// Moved to Modules/News/Routes/web.php

// ─── Static Configurators ───────────────────────────────
Route::get('/room-configurator', function () {
    return Inertia::render('Configurator/RoomConfigurator');
})->name('room.configurator');

Route::match(['get', 'post'], '/room-configurator/complete', [\Modules\Services\Http\Controllers\ConfiguratorController::class, 'simpleComplete'])->name('room.configurator.complete');

Route::get('/server-configurator', function () {
    return Inertia::render('Configurator/ServerConfigurator');
})->name('server.configurator');

Route::match(['get', 'post'], '/server-configurator/complete', [\Modules\Services\Http\Controllers\ConfiguratorController::class, 'simpleComplete'])->name('server.configurator.complete');

Route::get('/surveillance-configurator', function () {
    return Inertia::render('Configurator/SurveillanceConfigurator');
})->name('surveillance.configurator');

Route::match(['get', 'post'], '/surveillance-configurator/complete', [\Modules\Services\Http\Controllers\ConfiguratorController::class, 'simpleComplete'])->name('surveillance.configurator.complete');

// ─── Dynamic Configurator ───────────────────────────────
Route::get('/configurator/{slug}', [\Modules\Services\Http\Controllers\DynamicConfiguratorController::class, 'show'])->name('configurator.show');
Route::match(['get', 'post'], '/configurator/complete', [\Modules\Services\Http\Controllers\ConfiguratorController::class, 'complete'])->name('configurator.complete');

// ─── Brand Product List ─────────────────────────────────
Route::get('/{brandSlug}/products', [\Modules\ProductCatalog\Http\Controllers\BrandController::class, 'productList'])->name('brand.products');

// ─── Page Builder (Admin) ───────────────────────────────
// Moved to Modules/CMS/Routes/web.php

// ─── Homepage & Catch-All (MUST BE LAST) ────────────────
Route::get('/', [\Modules\CMS\Http\Controllers\PageController::class, 'resolveHomepage'])->name('home');
Route::get('/{slug}', [\Modules\CMS\Http\Controllers\DynamicResolverController::class, 'resolve'])
    ->name('dynamic.resolve')
    ->where('slug', '^(?!events|services|products|configurator|activioncms|admin|nova|api|storage|build|assets|favicon).*$');
