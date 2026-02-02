<?php

return [
    App\Providers\AppServiceProvider::class,
    Modules\Core\Providers\CoreServiceProvider::class,
    Modules\Events\Providers\EventsModuleServiceProvider::class,
    Modules\ServiceSolutions\Providers\ServiceSolutionsServiceProvider::class,
    App\Providers\Filament\ActivioncmsPanelProvider::class,
];
