<?php

use Illuminate\Support\Facades\Route;
use Modules\Clients\Http\Controllers\ClientController;

Route::get('/clients', [ClientController::class, 'index'])->name('clients');
