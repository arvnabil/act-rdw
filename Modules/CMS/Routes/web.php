<?php

use Illuminate\Support\Facades\Route;
use Modules\CMS\Http\Controllers\PageController;
use Modules\CMS\Http\Controllers\DynamicResolverController;
use Modules\CMS\Http\Controllers\Admin\PageBuilderController;

// ─── Page Builder (Admin) ───────────────────────────────
Route::middleware(['auth', 'verified'])->prefix('activioncms')->group(function () {
    Route::get('/page-builder/{page}', [PageBuilderController::class, 'edit'])->name('admin.page-builder.edit');
    Route::post('/page-builder/{page}/save', [PageBuilderController::class, 'update'])->name('admin.page-builder.update');
    Route::post('/upload-media', [PageBuilderController::class, 'upload'])->name('admin.upload-media');
});

// ─── Homepage & Catch-All (MUST BE LAST) ────────────────
// Moved to global routes/web.php to ensure it runs LAST after all other routes

