<?php

namespace Modules\AI\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class AIServiceProvider extends ServiceProvider
{
    protected $moduleName = 'AI';
    protected $moduleNameLower = 'ai';

    public function boot()
    {
        \Log::info('AIServiceProvider Booting...');
        
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', $this->moduleNameLower);

        Route::middleware('web')
            ->group(__DIR__ . '/../Routes/web.php');

        Route::prefix('api/ai')
            ->middleware('api')
            ->group(__DIR__ . '/../Routes/api.php');

        if ($this->app->runningInConsole()) {
            $this->commands([
                \Modules\AI\Console\SyncProductEmbeddings::class,
            ]);
        }
    }




    public function register()
    {
        //
    }
}
