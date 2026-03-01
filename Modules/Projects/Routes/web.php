<?php

use Illuminate\Support\Facades\Route;
use Modules\Projects\Http\Controllers\ProjectController;

Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
