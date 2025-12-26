<?php

namespace Modules\Events\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class EventsModuleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'events');

        Route::middleware('web')
            ->group(__DIR__ . '/../Routes/web.php');
    }

    public function register(): void
    {
        // Register any application services.
    }
}
