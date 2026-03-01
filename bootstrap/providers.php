<?php

return [
    App\Providers\AppServiceProvider::class,
    Modules\Settings\Providers\SettingsServiceProvider::class,
    Modules\SEO\Providers\SEOServiceProvider::class,
    Modules\Menu\Providers\MenuServiceProvider::class,
    Modules\FormBuilder\Providers\FormBuilderServiceProvider::class,
    Modules\WhatsApp\Providers\WhatsAppServiceProvider::class,
    Modules\News\Providers\NewsServiceProvider::class,
    Modules\Projects\Providers\ProjectsServiceProvider::class,
    Modules\Clients\Providers\ClientsServiceProvider::class,
    Modules\Search\Providers\SearchServiceProvider::class,
    Modules\ProductCatalog\Providers\ProductCatalogServiceProvider::class,
    Modules\Events\Providers\EventsModuleServiceProvider::class,
    Modules\Services\Providers\ServicesServiceProvider::class,
    Modules\Analytics\Providers\AnalyticsServiceProvider::class,
    
    // CMS — MUST BE REGISTERED LAST for catch-all route to work properly
    Modules\CMS\Providers\CMSServiceProvider::class,
    
    App\Providers\Filament\ActivioncmsPanelProvider::class,
];
