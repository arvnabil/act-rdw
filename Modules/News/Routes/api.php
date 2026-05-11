<?php

use Illuminate\Support\Facades\Route;
use Modules\News\Http\Controllers\Api\NewsApiController;

Route::group(['prefix' => 'news'], function () {
    // Public routes (Read-only)
    Route::get('/', [NewsApiController::class, 'index']);
    Route::get('/{slug}', [NewsApiController::class, 'show']);

    // Protected routes
    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('/import', [NewsApiController::class, 'import']);
    });
});
