<?php

namespace Modules\Search\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class SearchServiceProvider extends ServiceProvider
{
    protected $moduleName = 'Search';
    protected $moduleNameLower = 'search';

    public function boot()
    {
        $this->registerConfig();
        
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
