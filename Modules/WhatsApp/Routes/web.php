<?php

use Illuminate\Support\Facades\Route;
use Modules\WhatsApp\Http\Controllers\WhatsAppController;

Route::get('/wa', [WhatsAppController::class, 'redirect'])->name('whatsapp.redirect');
