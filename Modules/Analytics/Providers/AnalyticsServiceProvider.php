<?php

namespace Modules\Analytics\Providers;

use Illuminate\Support\ServiceProvider;

class AnalyticsServiceProvider extends ServiceProvider
{
    protected $moduleName = 'Analytics';
    protected $moduleNameLower = 'analytics';

    public function register()
    {
        //
    }

    public function boot()
    {
        $this->registerConfig();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
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
