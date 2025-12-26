<?php

use Illuminate\Support\Facades\Route;
use Modules\Events\Http\Controllers\EventController;
use Modules\Events\Http\Controllers\AuthController;
use Modules\Events\Http\Controllers\DashboardController;
use Modules\Events\Http\Controllers\EventRegistrationController;

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

// Public Event Routes
Route::prefix('events')->name('events.')->group(function() {
    Route::get('/', [EventController::class, 'index'])->name('index');
    Route::get('/list', [EventController::class, 'list_page'])->name('list'); // Keep legacy if needed or redirect


    // Auth Routes
    Route::middleware('guest:event')->group(function () {
        Route::get('/auth/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/auth/login', [AuthController::class, 'login']);
        Route::get('/auth/register', [AuthController::class, 'showRegister'])->name('register');
        Route::post('/auth/register', [AuthController::class, 'register']);
    });

    // Dashboard Routes (Protected)
    Route::middleware('auth:event')->prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/my-events', [DashboardController::class, 'myEvents'])->name('my-events');
        Route::get('/payments', [DashboardController::class, 'payments'])->name('payments');
        Route::get('/certificates', [DashboardController::class, 'certificates'])->name('certificates');
        Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
        Route::post('/profile', [DashboardController::class, 'updateProfile'])->name('profile.update');
        Route::post('/certificates/{event}/claim', [DashboardController::class, 'claimCertificate'])->name('certificates.claim');
        Route::get('/my-events/{slug}', [DashboardController::class, 'myEventDetail'])->name('my-event-detail');
        Route::get('/room/{slug}', [DashboardController::class, 'eventRoom'])->name('room');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });

    Route::get('/{event:slug}', [EventController::class, 'show'])->name('detail');
    Route::post('/{event:slug}/join', [EventRegistrationController::class, 'join'])->name('join');
});
