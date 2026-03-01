<?php

use Illuminate\Support\Facades\Route;
use Modules\FormBuilder\Http\Controllers\FormSubmissionController;

Route::post('/form/submit', [FormSubmissionController::class, 'store'])->name('form.submit')->middleware('throttle:10,1');
Route::post('/form/view', [FormSubmissionController::class, 'trackView'])->name('form.view');
