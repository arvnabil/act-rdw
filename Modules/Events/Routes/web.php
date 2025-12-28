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

// Certificate Designer Routes (Admin only, add middleware as needed)
Route::group(['prefix' => 'admin/events/certificates', 'middleware' => ['auth']], function () {
    Route::get('/{id}/design', [\Modules\Events\Http\Controllers\CertificateDesignerController::class, 'edit'])->name('events.certificates.design');
    Route::post('/{id}/design', [\Modules\Events\Http\Controllers\CertificateDesignerController::class, 'update'])->name('events.certificates.update-design');
    Route::post('/{id}/upload-media', [\Modules\Events\Http\Controllers\CertificateDesignerController::class, 'uploadMedia'])->name('events.certificates.upload-media');
    Route::get('/{id}/get-media', [\Modules\Events\Http\Controllers\CertificateDesignerController::class, 'indexMedia'])->name('events.certificates.get-media'); // GET list
    Route::post('/{id}/delete-media', [\Modules\Events\Http\Controllers\CertificateDesignerController::class, 'deleteMedia'])->name('events.certificates.delete-media');
    Route::post('/{id}/rename-media', [\Modules\Events\Http\Controllers\CertificateDesignerController::class, 'renameMedia'])->name('events.certificates.rename-media');
});

// Public Event Routes
Route::prefix('events')->name('events.')->group(function() {
    Route::get('/', [EventController::class, 'index'])->name('index');
    Route::get('/list', [EventController::class, 'list_page'])->name('list');

    // Certificate Validation
    Route::get('/certificates/verify/{code}', [\Modules\Events\Http\Controllers\CertificateValidationController::class, 'verify'])->name('certificates.verify');
    Route::get('/certificates/download/{code}', [\Modules\Events\Http\Controllers\CertificateValidationController::class, 'download'])->name('certificates.download');

    // Ticket Verification
    // Ticket Verification (Scan) - PROTECTED by Logic in Controller
    Route::get('/{slug}/{code}', [\Modules\Events\Http\Controllers\AttendanceController::class, 'verify'])
        ->where('code', '[A-Z0-9]{5,}') // Regex to ensure code format (e.g. 5 chars) to differentiate from slug
        ->name('verify_attender');

    // Legacy Redirect for URLs generated before removal of verify-ticket prefix
    Route::get('/verify-ticket/{slug}/{code}', function ($slug, $code) {
        return redirect()->route('events.verify_attender', ['slug' => $slug, 'code' => $code]);
    });



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
        Route::get('/ticket/{code}/download', [DashboardController::class, 'downloadTicket'])->name('ticket.download');
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
