<?php

namespace Modules\FormBuilder\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class FormBuilderServiceProvider extends ServiceProvider
{
    protected $moduleName = 'FormBuilder';
    protected $moduleNameLower = 'formbuilder';

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
