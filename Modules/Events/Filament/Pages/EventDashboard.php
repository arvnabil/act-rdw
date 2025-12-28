<?php

namespace Modules\Events\Filament\Pages;

use Filament\Pages\Page;
use Modules\Events\Filament\Widgets\EventOverviewStats;
use Modules\Events\Filament\Widgets\LatestRegistrationsTable;
use Modules\Events\Filament\Widgets\PopularEventsChart;
use Modules\Events\Filament\Widgets\RegistrationTrendChart;

use BackedEnum;
use UnitEnum;

class EventDashboard extends Page
{
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static string | UnitEnum | null $navigationGroup = 'Event Management';
    
    protected static ?string $slug = 'event-dashboard';
    
    protected static ?string $navigationLabel = 'Event Dashboard';

    protected static ?string $title = 'Event Dashboard';

    protected string $view = 'events::filament.pages.event-dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            EventOverviewStats::class,
            RegistrationTrendChart::class,
            PopularEventsChart::class,
            LatestRegistrationsTable::class,
        ];
    }
}
