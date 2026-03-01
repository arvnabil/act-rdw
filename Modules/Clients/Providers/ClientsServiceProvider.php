<?php

namespace Modules\Clients\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class ClientsServiceProvider extends ServiceProvider
{
    protected $moduleName = 'Clients';
    protected $moduleNameLower = 'clients';

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
