<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\Activioncms\Pages\Dashboard;
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
            ->databaseNotifications()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: base_path('Modules/CMS/Filament/Resources'), for: 'Modules\\CMS\\Filament\\Resources')
            ->discoverResources(in: base_path('Modules/Events/Filament/Resources'), for: 'Modules\\Events\\Filament\\Resources')
            ->discoverResources(in: base_path('Modules/Services/Filament/Resources'), for: 'Modules\\Services\\Filament\\Resources')
            ->discoverResources(in: base_path('Modules/ProductCatalog/Filament/Resources'), for: 'Modules\\ProductCatalog\\Filament\\Resources')
            ->discoverResources(in: base_path('Modules/Analytics/Filament/Resources'), for: 'Modules\\Analytics\\Filament\\Resources')
            ->discoverResources(in: base_path('Modules/Settings/Filament/Resources'), for: 'Modules\\Settings\\Filament\\Resources')
            ->discoverResources(in: base_path('Modules/SEO/Filament/Resources'), for: 'Modules\\SEO\\Filament\\Resources')
            ->discoverResources(in: base_path('Modules/Menu/Filament/Resources'), for: 'Modules\\Menu\\Filament\\Resources')
            ->discoverResources(in: base_path('Modules/FormBuilder/Filament/Resources'), for: 'Modules\\FormBuilder\\Filament\\Resources')
            ->discoverResources(in: base_path('Modules/News/Filament/Resources'), for: 'Modules\\News\\Filament\\Resources')
            ->discoverResources(in: base_path('Modules/Projects/Filament/Resources'), for: 'Modules\\Projects\\Filament\\Resources')
            ->discoverResources(in: base_path('Modules/Clients/Filament/Resources'), for: 'Modules\\Clients\\Filament\\Resources')
            ->discoverPages(in: base_path('Modules/CMS/Filament/Pages'), for: 'Modules\\CMS\\Filament\\Pages')
            ->discoverPages(in: app_path('Filament/Activioncms/Pages'), for: 'App\Filament\Activioncms\Pages')
            ->discoverPages(in: base_path('Modules/Events/Filament/Pages'), for: 'Modules\\Events\\Filament\\Pages')
            ->discoverPages(in: base_path('Modules/SEO/Filament/Pages'), for: 'Modules\\SEO\\Filament\\Pages')
            ->pages([
                Dashboard::class,
                EventDashboard::class,
            ])
            ->discoverWidgets(in: base_path('Modules/Events/Filament/Widgets'), for: 'Modules\Events\Filament\Widgets')
            ->discoverWidgets(in: base_path('Modules/Analytics/Filament/Widgets'), for: 'Modules\\Analytics\\Filament\\Widgets')
            ->discoverWidgets(in: base_path('Modules/SEO/Filament/Widgets'), for: 'Modules\\SEO\\Filament\\Widgets')
            ->discoverWidgets(in: base_path('Modules/FormBuilder/Filament/Widgets'), for: 'Modules\\FormBuilder\\Filament\\Widgets')
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
            ->navigationGroups([
                'Dashboard',
                'Analytics',
                'Product Catalog',
                'Service Management',
                'Project Management',
                'Client Management',
                'News Management',
                'Form Management',
                'Menu Management',
                'Seo Management',
                'Site Management',
                'Event Manage Data',
                'Event Management',
                'Settings',
            ])
            ->renderHook(
                'panels::body.end',
                fn () => \Illuminate\Support\Facades\Blade::render("@viteReactRefresh\n@vite(['resources/js/filament-serp.jsx'])")
            );
    }

    public function boot(): void
    {
        try {
            // Only try to fetch settings if the application is not running in CLI (unless it's a specific command we want)
            // Or better, just catch the exception if the table doesn't exist yet.
            $settings = \Modules\Settings\Models\Setting::whereIn('key', [
                'seo_ga4_property_id',
                'seo_ga4_service_account_json'
            ])->pluck('value', 'key');

            if (isset($settings['seo_ga4_property_id'])) {
                config(['analytics.property_id' => $settings['seo_ga4_property_id']]);
            }

            if (isset($settings['seo_ga4_service_account_json'])) {
                $json = json_decode($settings['seo_ga4_service_account_json'], true);
                if ($json) {
                    config(['analytics.service_account_credentials_json' => $json]);
                }
            }
        } catch (\Exception $e) {
            // Silently fail if database is not ready or table doesn't exist
        }
    }
}
