<?php

namespace Modules\CMS\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class CMSServiceProvider extends ServiceProvider
{
    protected $moduleName = 'CMS';
    protected $moduleNameLower = 'cms';

    public function boot()
    {
        $this->registerConfig();
        
        Route::middleware('web')
            ->group(__DIR__ . '/../Routes/web.php');
            
        // Load CMS migrations if any
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
