<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\Modules\SEO\Services\Seo\SeoManager::class);
    }


    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::DefaultStringLength(191);

        \Illuminate\Database\Eloquent\Relations\Relation::morphMap([
            'service'          => \Modules\Services\Models\Service::class,
            'service_solution' => \Modules\Services\Models\ServiceSolution::class,
            'product'          => \Modules\ProductCatalog\Models\Product::class,
            'brand'            => \Modules\ProductCatalog\Models\Brand::class,
            'news'             => \Modules\News\Models\News::class,
            'project'          => \Modules\Projects\Models\Project::class,
            'page'             => \Modules\CMS\Models\Page::class,
            'client'           => \Modules\Clients\Models\Client::class,
            'slider'           => \Modules\CMS\Models\Page::class,
            'global'           => \Modules\CMS\Models\Page::class,
            'contact'          => \Modules\CMS\Models\Page::class,
            'partnership'      => \Modules\CMS\Models\Page::class,
            
            // Legacy Aliases for database backwards compatibility
            'App\Models\News'    => \Modules\News\Models\News::class,
            'App\Models\Project' => \Modules\Projects\Models\Project::class,
            'App\Models\Page'    => \Modules\CMS\Models\Page::class,
            'App\Models\Client'  => \Modules\Clients\Models\Client::class,

            // Core Module Legacy Aliases (database backwards compatibility)
            'Modules\Core\Models\Product' => \Modules\ProductCatalog\Models\Product::class,
            'Modules\Core\Models\Brand'   => \Modules\ProductCatalog\Models\Brand::class,
        ]);

        view()->composer('app', \App\View\Composers\SeoViewComposer::class);

        \Filament\Forms\Components\FileUpload::configureUsing(function (\Filament\Forms\Components\FileUpload $component) {
            if ($component->getName() === 'file') {
                $component->acceptedFileTypes(['*']);
            }
        });

        // Smart Patch: Disable the acceptedFileTypes method for components named 'file'
        // to prevent ImportAction from overwriting our '*' configuration.
        \Filament\Forms\Components\FileUpload::macro('acceptedFileTypes', function ($types) {
            if ($this->getName() === 'file') {
                return $this; // Do nothing, keep the '*' from configureUsing
            }
            
            // For other components, we need a way to set the property.
            // Since we can't call the original method easily, we'll use reflection
            // only if it's not our target component.
            $property = new \ReflectionProperty(get_class($this), 'acceptedFileTypes');
            $property->setAccessible(true);
            $property->setValue($this, $types);

            return $this;
        });

        // Register API Documentation authorization gate (Dedoc Scramble)
        Gate::define('viewApiDocs', function (?User $user) {
            // In local development environment, anyone can access
            if (app()->environment('local')) {
                return true;
            }

            // In other environments (production, staging), user must be authenticated
            return $user !== null;
        });

        // Configure OpenAPI Document with X-API-KEY security scheme
        Scramble::configure()
            ->withDocumentTransformers(function (OpenApi $openApi) {
                $openApi->components->securitySchemes['ApiKey'] = SecurityScheme::apiKey('header', 'X-API-KEY');
                $openApi->secure(SecurityScheme::apiKey('header', 'X-API-KEY'));
            });
    }

}
