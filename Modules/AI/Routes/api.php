<?php

use Illuminate\Support\Facades\Route;
use Modules\AI\Http\Controllers\ChatbotController;

Route::post('/chat', [ChatbotController::class, 'chat'])->name('ai.chat');
Route::post('/start-session', [ChatbotController::class, 'startSession'])->name('ai.start-session');
Route::get('/personas', [ChatbotController::class, 'personas'])->name('ai.personas');

Route::post('/summarize', [ChatbotController::class, 'summarize'])->name('ai.summarize');
Route::get('/get-history', [ChatbotController::class, 'getHistory'])->name('ai.get-history');
Route::get('/settings', [ChatbotController::class, 'settings'])->name('ai.settings');


