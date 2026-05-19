<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('seo:generate-sitemap')->dailyAt('03:00');

Schedule::command('queue:work --stop-when-empty')
    ->everyMinute()
    ->withoutOverlapping();

Schedule::call(function () {
    Log::info('Supabase Keep-Alive: Sending ping request to prevent auto-pausing.');
    try {
        $vectorService = app(\Modules\AI\Services\VectorService::class);
        // Dimensi embedding text-embedding-005 default adalah 768.
        $dummyVector = array_fill(0, 768, 0.0);
        $vectorService->search($dummyVector, 1);
        Log::info('Supabase Keep-Alive: Ping request completed.');
    } catch (\Exception $e) {
        Log::warning('Supabase Keep-Alive: Ping failed: ' . $e->getMessage());
    }
})->cron('0 0 * * 1,4')->name('supabase-keep-alive');

