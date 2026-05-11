<?php

use Illuminate\Support\Facades\Route;
use Modules\ProductCatalog\Http\Controllers\Api\ProductApiController;

Route::group(['prefix' => 'products'], function () {
    // Public routes (Read-only)
    Route::get('/', [ProductApiController::class, 'index']);
    Route::get('/{slug}', [ProductApiController::class, 'show']);

    // Protected routes
    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('/import', [ProductApiController::class, 'import']);
    });
});
