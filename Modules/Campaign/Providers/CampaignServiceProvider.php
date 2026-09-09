<?php

namespace Modules\Campaign\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class CampaignServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Campaign';
    protected string $moduleNameLower = 'campaign';

    public function boot(): void
    {
        $this->registerConfig();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', $this->moduleNameLower);

        Route::middleware('web')
            ->group(__DIR__ . '/../Routes/web.php');
    }

    public function register(): void
    {
        //
    }

    protected function registerConfig(): void
    {
        $this->publishes([
            __DIR__ . '/../Config/config.php' => config_path($this->moduleNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/config.php', $this->moduleNameLower
        );
    }
}