<?php

namespace Modules\Projects\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class ProjectsServiceProvider extends ServiceProvider
{
    protected $moduleName = 'Projects';
    protected $moduleNameLower = 'projects';

    public function boot()
    {
        $this->registerConfig();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        Route::middleware('web')
            ->group(__DIR__ . '/../Routes/web.php');
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
