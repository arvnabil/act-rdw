<?php

namespace Modules\Events\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Events\Models\Event;
use Modules\Events\Models\EventRegistration;
use Modules\Events\Models\EventUserCertificate;

class EventOverviewStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Active Events', Event::where('is_active', true)->count())
                ->description('Total events currently active')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('success'),
            
            Stat::make('Total Registrations', EventRegistration::count())
                ->description('All time registrations')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary')
                ->chart([7, 2, 10, 3, 15, 4, 17]), // Dummy chart for visuals or query actual trend if flexible

            Stat::make('Total Revenue', 'IDR ' . number_format(EventRegistration::sum('amount'), 0, ',', '.'))
                ->description('Gross revenue from paid events')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Certificates Issued', EventUserCertificate::count())
                ->description('Total certificates generated')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('warning'),
        ];
    }
}
