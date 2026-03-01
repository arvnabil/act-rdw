<?php

namespace Modules\Services\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class ServicesServiceProvider extends ServiceProvider
{
    protected $moduleName = 'Services';
    protected $moduleNameLower = 'services';

    public function register()
    {
        //
    }

    public function boot()
    {
        $this->registerConfig();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->registerRoutes();
    }

    protected function registerRoutes()
    {
        Route::middleware('web')
            ->group(__DIR__ . '/../Routes/web.php');
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
