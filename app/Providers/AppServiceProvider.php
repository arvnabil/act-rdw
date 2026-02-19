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

        \Illuminate\Database\Eloquent\Relations\Relation::morphMap([
            'service'          => \Modules\ServiceSolutions\Models\Service::class,
            'service_solution' => \Modules\ServiceSolutions\Models\ServiceSolution::class,
            'product'          => \Modules\Core\Models\Product::class,
            'brand'            => \Modules\Core\Models\Brand::class,
            'news'             => \App\Models\News::class,
            'project'          => \App\Models\Project::class,
            'page'             => \App\Models\Page::class,
        ]);

        view()->composer('app', \App\View\Composers\SeoViewComposer::class);
    }

}
