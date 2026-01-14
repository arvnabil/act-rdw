<?php

namespace Modules\ServiceSolutions\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class ServiceSolutionsServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->registerRoutes();
    }

    protected function registerRoutes()
    {
        Route::middleware('web')
            ->group(__DIR__ . '/../Routes/web.php');
    }
}
