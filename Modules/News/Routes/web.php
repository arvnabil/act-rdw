<?php

use Illuminate\Support\Facades\Route;
use Modules\News\Http\Controllers\NewsController;

Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/category/{slug}', [NewsController::class, 'category'])->name('news.category');
Route::get('/news/tag/{slug}', [NewsController::class, 'tag'])->name('news.tag');
