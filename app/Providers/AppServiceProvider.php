<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Services\Seo\SeoManager::class);
    }


    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::DefaultStringLength(191);

        view()->composer('app', \App\View\Composers\SeoViewComposer::class);
    }

}
