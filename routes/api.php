<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthApiController::class, 'login']);
    
    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('/logout', [AuthApiController::class, 'logout']);
        Route::post('/refresh', [AuthApiController::class, 'refresh']);
        Route::get('/me', [AuthApiController::class, 'me']);
    });
});

// Automation Routes (Protected by Static API Key)
Route::group(['prefix' => 'automation', 'middleware' => 'api_key'], function () {
    Route::get('/leads', [\App\Http\Controllers\Api\AutomationApiController::class, 'getLeads']);
    Route::post('/leads', [\App\Http\Controllers\Api\AutomationApiController::class, 'pushLead']);
    Route::post('/wa-trigger', [\App\Http\Controllers\Api\AutomationApiController::class, 'trackWaTrigger']);
});
