<?php

use Illuminate\Support\Facades\Route;
use Modules\Campaign\Http\Controllers\CampaignPublicController;
use Modules\Campaign\Http\Controllers\CampaignPreviewController;

// ─── Public Campaign Route ────────────────────────────────────────────────────
// Uses /campaign/{slug} namespace — does NOT use root wildcard /{slug}
// Registered before CMS catch-all via provider registration order.
Route::get('/campaign/{slug}', [CampaignPublicController::class, 'show'])
    ->name('campaign.show');

// ─── Admin Preview (auth required) ───────────────────────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('/activioncms/campaigns/{id}/preview', [CampaignPreviewController::class, 'preview'])
        ->name('campaign.preview');
});