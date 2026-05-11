<?php

namespace Modules\WhatsApp\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class WhatsAppServiceProvider extends ServiceProvider
{
    protected $moduleName = 'WhatsApp';
    protected $moduleNameLower = 'whatsapp';

    public function boot()
    {
        $this->registerConfig();

        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', $this->moduleNameLower);

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
