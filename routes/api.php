<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\AutomationApiController;

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthApiController::class, 'login']);
    Route::post('/logout', [AuthApiController::class, 'logout']);
    Route::post('/refresh', [AuthApiController::class, 'refresh']);
    Route::get('/me', [AuthApiController::class, 'me']);
});

// Automation Routes (Middleware dipindahkan ke Controller tingkat kelas)
Route::group(['prefix' => 'automation'], function () {
    Route::get('/leads', [AutomationApiController::class, 'getLeads']);
    Route::post('/leads', [AutomationApiController::class, 'pushLead']);
    Route::post('/wa-trigger', [AutomationApiController::class, 'trackWaTrigger']);
});
