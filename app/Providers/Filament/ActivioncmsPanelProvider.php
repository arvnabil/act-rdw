<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

use Modules\Events\Filament\Pages\EventDashboard;

class ActivioncmsPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('activioncms')
            ->path('activioncms')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Activioncms/Resources'), for: 'App\Filament\Activioncms\Resources')
            ->discoverResources(in: base_path('Modules/Events/Filament/Resources'), for: 'Modules\\Events\\Filament\\Resources')
            ->discoverResources(in: base_path('Modules/ServiceSolutions/Filament/Resources'), for: 'Modules\\ServiceSolutions\\Filament\\Resources')
            ->discoverResources(in: base_path('Modules/Core/Filament/Resources'), for: 'Modules\\Core\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Activioncms/Pages'), for: 'App\Filament\Activioncms\Pages')
            ->discoverPages(in: base_path('Modules/Events/Filament/Pages'), for: 'Modules\\Events\\Filament\\Pages')
            ->pages([
                Dashboard::class,
                EventDashboard::class,
            ])
//            ->discoverWidgets(in: app_path('Filament/Activioncms/Widgets'), for: 'App\Filament\Activioncms\Widgets')
//            ->discoverWidgets(in: base_path('Modules/Events/Filament/Widgets'), for: 'Modules\\Events\\Filament\\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->renderHook(
                'panels::body.end',
                fn () => \Illuminate\Support\Facades\Blade::render("@viteReactRefresh\n@vite(['resources/js/filament-serp.jsx'])")
            );
    }
}
