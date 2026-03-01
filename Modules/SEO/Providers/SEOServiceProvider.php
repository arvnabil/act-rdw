<?php

namespace Modules\SEO\Providers;

use Illuminate\Support\ServiceProvider;

class SEOServiceProvider extends ServiceProvider
{
    protected $moduleName = 'SEO';
    protected $moduleNameLower = 'seo';

    public function boot()
    {
        $this->registerConfig();
        // SEO module migrations are typically in the main database/migrations folder since they affect many tables, 
        // but if there are specific migrations here, we load them:
        if (is_dir(__DIR__ . '/../Database/Migrations')) {
            $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        }
    }

    public function register()
    {
        //
    }

    protected function registerConfig()
    {
        $this->publishes([
            __DIR__ . '/../Config/config.php' => config_path($this->moduleNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/config.php', $this->moduleNameLower
        );
    }
}
